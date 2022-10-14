<?php
namespace DancePressTRWA\Controllers;

/**
 * Controls public site.
 *
 * This previously controlled all public pages. Now split into files controlling parent portal & regsitration.
 * Now mainly directs traffic to appropriate class/method.
 *
 * @since 1.0
 */
class DanceSchool extends Controller
{
    public $product;

    public function startDance()
    {
        load_plugin_textdomain('dancepress-trwa', false, dirname(plugin_basename(__FILE__)) . '/languages') ;

        $this->initializePublic();

        self::setAction(false);
        $this->offset =  0;
        $this->limit = 30;

        if (isset($this->_REQUEST['action'])) {
            self::setAction($this->_REQUEST['action']);
        }

        if (isset($this->_REQUEST['offset'])) {
            $this->offset =  $this->_REQUEST['offset'];
        }

        if (method_exists($this, self::getAction())) {
            call_user_func_array(array($this, self::getAction()), $this->_REQUEST);
        } else {
            $this->doDefault();
        }
    }

    public function startJDB()
    {
        if (!IS_AJAX) {
            return;
        }
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : false;

        switch ($page) {
            case 'checkregistrationduplicate':
                $this->checkRegistrationDuplicate();

                break;
        }
    }

    public function startParentPortal()
    {
        $pp = new \DancePressTRWA\Controllers\ParentPortalController();
        $pp->startParentPortal();
    }

    public function doDefault()
    {
        $objParent = new \DancePressTRWA\Models\Parents($this->sessionCondition);

        $user = wp_get_current_user();

        //Actions related to Parent Portal area. Logged in users only. Don't put registration code here.
        if (isset($_REQUEST['page']) && $_REQUEST['page'] != "") {
            $loggedInParent = $objParent->getParentIdByWPUserId();

            $_SESSION['loggedinparent'] = $loggedInParent;

            if (is_user_logged_in()) {
                get_sidebar('portal');
            }

            if (get_user_meta($user->ID, 'active', true) == 0) {
                die('Your account is currently inactive. Please <a href="/registration/">Register</a> for the current session to activate your account. Your account will be activated once your registration fee has been received. <br/><br/>Please <a href="/contact-us">Contact us</a> if you have any questions.');
            }

            switch ($_REQUEST['page']) {
                case 'viewclass':
                    $classId = 0;

                    if (!empty($_REQUEST['id'])) {
                        $classId = intval($_REQUEST['id']);
                    }

                    $objClassManager = new \DancePressTRWA\Models\ClassManager($this->sessionCondition);

                    if ($classId) {
                        $this->render($_REQUEST['page'], [$objClassManager->getClassById($classId), 'is_full_schedule' => false]);
                    } else {
                        $this->render('classcalendar', ['classes' => $objClassManager->getAllClassForFront('/parent-portal/dance-school-plugin-page/?page=viewclass'), 'is_full_schedule' => true]);
                    }
                    break;
                case 'childclasses':
                        $objClassStudents = new \DancePressTRWA\Models\ClassStudents($this->sessionCondition);
                        $studentId = $this->_REQUEST['id'];

                        if (!$studentId) {
                            $this->errors[] = "Student id not provided";
                            $this->render('empty');
                        }

                        $classes = $objClassStudents->getStudentClasses();
                        $student = $objClassStudents->getStudentById($studentId);

                        $student = is_array($student) ? $student[0] : $student;
                        $this->render('classcalendar', [
                              'classes' => $classes,
                              'student' => $student,
                              'is_full_schedule' => false]);

                    break;
                case 'parentchilds':
                    $objClassParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);
                    $this->render($_REQUEST['page'], $objClassParents->getChildsByParentId($_SESSION['loggedinparent'], 'activeonly'));
                    break;
                case 'parentprofile':

                    if ($loggedInParent == null) {
                        $this->errors[] = "Sorry - your profile details have not been found. Contact " . get_bloginfo('name') . " for assistance.";
                        return;
                    }

                    $ObjClassParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);

                    if (isset($this->_REQUEST['action']) && $this->_REQUEST['action']=='save') {
                        $data = array();
                        $email = $this->_REQUEST['email'];

                        $current_user = wp_get_current_user();
                        $user_login = $current_user->user_login;
                        $errCount = 0;


                        if (!is_email($email)) {
                            $this->errors[] = "New email/username is not a valid email address. Please correct.";
                            $errCount++;
                        } else {
                            if ($email != $user_login) {
                                if (!$ObjClassParents->updateWordpressUsername($email, $user_login)) {
                                    $this->errors[] = $ObjClassParents->getError();
                                    $errCount++;
                                } else {
                                    $this->messages[] = "Wordpress username updated;";
                                }
                            }
                        }

                        $address = array();
                        $address['address1'] = $this->_REQUEST['address1'];
                        $address['address2'] = $this->_REQUEST['address2'];
                        $address['city'] = $this->_REQUEST['city'];
                        $address['postal_code'] = $this->_REQUEST['postal_code'];
                        $address['phone_primary'] = $this->_REQUEST['phone_primary'];
                        $address['phone_secondary'] = $this->_REQUEST['phone_secondary'];
                        //
                        $data['firstname'] = $this->_REQUEST['firstname'];
                        $data['lastname'] = $this->_REQUEST['lastname'];
                        $data['email'] = $this->_REQUEST['email'];
                        $data['address_data'] = json_encode((object)$address);

                        if (!$data['firstname']) {
                            $this->errors[] = "First name not provided";
                        }

                        if (!$data['lastname']) {
                            $this->errors[] = "Last name not provided";
                        }

                        if ($this->errors) {
                            $this->render('empty');
                            return;
                        }

                        if (!$errCount) {
                            $ObjClassParents->updateParent($data, $_SESSION['loggedinparent']);
                            $this->messages[] = "User profile saved";
                        }
                    }

                    $parent = $ObjClassParents->getParentById($_SESSION['loggedinparent']);
                    $parent = $parent[0];

                    $addressInfo = json_decode($parent->address_data);

                    $parent->address1 = $addressInfo->address1;
                    $parent->address2 = $addressInfo->address2;
                    $parent->city = $addressInfo->city;
                    $parent->postal_code = $addressInfo->postal_code;
                    $parent->phone_primary = $addressInfo->phone_primary;
                    $parent->phone_secondary = $addressInfo->phone_secondary;

                    $this->render($_REQUEST['page'], $parent);

                    break;
                case 'parentbillinghistory':
                    $ObjBilling = new \DancePressTRWA\Models\Billing($this->sessionCondition);
                    $billingHistory = $ObjBilling->getBillingHistoryByParentId($_SESSION['loggedinparent']);

                    $this->render($_REQUEST['page'], array(
                        "billingHistory" => $billingHistory
                    ));
                    break;
                case 'ticketpurchases':
                    $objClassEventTicketSales = new \DancePressTRWA\Models\DancepressClassEventTicketSales($this->sessionCondition);
                    $ObjClassParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);

                    $parent = $ObjClassParents->getParentById__premium_only($_SESSION['loggedinparent']);

                    if (!$parent) {
                        $this->errors[] = "Parent account not found. This sometimes happens with accounts which were not created through DancePress.";
                    }

                    $parent = $parent[0];

                    $this->render($_REQUEST['page'], array(
                           "parent" => "",
                           "ticket_purchases" => $objClassEventTicketSales->findEventTicketSales(array('parent_id' => $parent->id))
                    ));

                    break;
                default:

                    break;
            }
        } else {
            $registration = new \DancePressTRWA\Controllers\RegistrationController();
            $registration->registration();
        }
    }

    public function existinguserlogin()
    {
        $session = new \DancePressTRWA\Models\Sessions($this->sessionId);
        $sessionName = $session->getSessionNameById($this->sessionId);
        if (!is_user_logged_in()) {
            $user = wp_signon();
            if (get_class($user) == 'WP_Error') {
                if (isset($user->errors['invalid_username'])) {
                    $this->errors[] = $user->errors['invalid_username'][0];
                } elseif (isset($user->errors['incorrect_password'])) {
                    $this->errors[] = $user->errors['incorrect_password'][0];
                } else {
                    $this->errors[] = '<strong>ERROR</strong>: Login details not recognised. <a href="http://dancersdev/wp-login.php?action=lostpassword">Lost your password?</a>';
                }
                $this->checkHTTPS();
                $this->render('registration__premium_only', ['sessionName' => $sessionName]);
                return;
            }
            //Redirect after successful login, so as to set correct cookie vars before proceeding further.
            header('location: ' . site_url() . '/registration/?action=existinguserlogin');
            die();
        }

        $objParent = new \DancePressTRWA\Models\Parents($this->sessionCondition);
        $parentId = $objParent->getParentIdByWPUserId();
        $parent = $objParent->getParentById__premium_only($parentId);

        if (!$objParent->isParentAlreadyActive($parentId)) {
            $this->errors[] = "According to our records you have already completed registration. If you would like to change your registered classes, or believe this message is in error, please <a href='/contact-us/'>contact us</a>";
            $this->render('registration__premium_only', array('sessionName' => $this->getSessionName()));
        } else {
            $parent[0] = isset($parent[0]) ? $parent[0] : new \stdClass();
            $parent[0]->meta = isset($parent[0]->meta) ?  json_decode($parent[0]->meta) : '';
            $parent[0]->address_data = isset($parent[0]->address_data) ? json_decode($parent[0]->address_data) : '';
            $this->render('reg_stage1__premium_only', array('parent' => $parent[0], 'sessionName' => $this->getSessionName()));
        }

        return;
    }
}
