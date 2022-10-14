<?php
namespace DancePressTRWA\Controllers;

use \Exception as PHPException;

if (isset($wpdb)) {
    $wpdb->hide_errors();
}

/**
 * Controls public site registration page.
 *
 */
final class RegistrationController extends Controller
{
    public function registration()
    {
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;

        try {
            switch ($action) {
                case 'newuserreg':
                    $this->newuserreg();
                    break;
                case 'reg_stage2':
                    $this->reg_stage2();
                    break;
                case 'reg_stage3':
                    $this->reg_stage3();
                    break;
                case 'reg_stage4':
                    $this->reg_stage4();
                    break;
                case 'reg_stage5':
                    $this->reg_stage5();
                    break;
                default:
                    $this->render('registration__premium_only', array('sessionName' => $this->getSessionName()));
                    break;
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function newuserreg()
    {
        $options = new \DancePressTRWA\Models\Option();
        $countries = new \DancePressTRWA\Models\Countries();

        $country = $countries->findById($options->getContactAddressCountry());

        $this->render('reg_stage1__premium_only', array('countryName' => $country->name, 'sessionName' => $this->getSessionName()));
    }

    public function reg_stage2()
    {
        $_SESSION['stage1'] = json_encode($this->_REQUEST);

        if (is_user_logged_in()) {
            $objParent = new \DancePressTRWA\Models\Parents($this->sessionCondition);
            $objStudents = new \DancePressTRWA\Models\ClassStudents($this->sessionCondition);
            $parentId = $objParent->getParentIdByWPUserId();

            if ($parentId) {
                $students = $objStudents->getStudentsByParentId($parentId, 'excludeIfCannotReg', false);
                foreach ($students as &$s) {
                    $s->meta = json_decode($s->meta);
                }
            } else {
                die("We're sorry. Something isn't right. Please contact " . get_bloginfo('name') . " for assistance and quote 'Missing Parent ID error encountered'. <br/><br/>If you prefer, try restarting your browser and then registering a new account. (Do not login first.)");
            }

            $this->render('reg_stage2__premium_only', array('students' => $students));
            return;
        }

        $cc = new \DancePressTRWA\Models\ClassCategories($this->sessionCondition);
        $courseCategories = $cc->getCategories();

        $this->render('reg_stage2__premium_only', array('courseCategories' => $courseCategories));
    }

    public function reg_stage3()
    {
        $this->checkHTTPS();
        if (!isset($_SESSION['stage1'])) {
            $this->errors[] = "It appears your session has expired due to a long period of inactivity. Please enter student details again.";
            $this->render('registration__premium_only');
            return;
        }

        if (!isset($this->_REQUEST['returning_id']) && (!isset($this->_REQUEST['numberofdancers']) || !$this->_REQUEST['numberofdancers'])) {
            $this->errors[] = "Please complete details for at least one student.";
            $this->_REQUEST['stage1'] = json_decode($_SESSION['stage1']);
            $this->reg_stage2();
            return true;
        }

        $_SESSION['stage2'] = json_encode($this->_REQUEST);
        $stage2 = json_decode($_SESSION['stage2']);

        if (isset($stage2->child)) {
            $children = $stage2->child;
            $returning = new \StdClass();

            if (!empty($stage2->returning_id)) {
                $returning =  $stage2->returning_id;
            }

            //Combined the two arrays without losing any array keys fom $returning array.
            $combined = (array)$returning;

            foreach ($children as $k => $child) {
                $combined['_' . $k] = $child;
            }
        } else {
            $returning = [];

            if (!empty($stage2->returning_id)) {
                $returning = $stage2->returning_id;
            }

            $combined = (array)$returning;
        }

        $objClass = new \DancePressTRWA\Models\ClassManager($this->sessionCondition);

        if (current_user_can('moderate_comments')) {
            $excludeCompetitive = false;
            $excludeFull = false;
        } else {
            $excludeCompetitive = true;
            $excludeFull = false;
        }

        if (current_user_can('moderate_comments') && isset($this->_REQUEST['showall'])) {
            $classes = $objClass->getClassesUnNarrowed($combined);
        } else {
            //regular user or admin not wanting to see all classes
            $classes = $objClass->getClassesByChildAge($combined, $excludeCompetitive, 1);
        }

        $recommended = false;

        if (is_user_logged_in()) {
            $objParent = new \DancePressTRWA\Models\Parents($this->sessionCondition);
            $objStudents = new \DancePressTRWA\Models\ClassStudents($this->sessionCondition);
            $parentId = $objParent->getParentIdByWPUserId();
            $students = $objStudents->getStudentsByParentId($parentId, false, false);

            foreach ($students as $k => $s) {
                $students[$k]->meta = json_decode($s->meta);
            }

            foreach ($students as $st) {
                $recommended[$st->id] = $objClass->getRecommendedCourses($st->id);
            }
        }

        $weekdays = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5=> "Friday", 6=> "Saturday", 7=>"Sunday");

        foreach ($classes as $key => $course) {
            $dd = '';
            $days = json_decode($course->days);

            foreach ($days as $day) {
                $dd[$day] = $weekdays[$day];
            }

            $classes[$key]->days = $dd;

            if ($course->enrollment >= DANCEPRESSTRWA_CLASS_LIMIT && DANCEPRESSTRWA_CLASS_LIMIT != 0 && $excludeFull) {
                unset($classes[$key]);
            }
        }

        $this->render('reg_stage3__premium_only', array(
              'data' => $classes,
              'students' => $combined,
              'recommended' => $recommended,
              'sessionName' => $this->getSessionName()
        ));
    }

    public function reg_stage4()
    {
        if (!isset($_SESSION['stage1']) || !isset($_SESSION['stage2'])) {
            $this->errors[] = "It appears your session has expired due to a long period of inactivity. Please enter student details again.";
            $this->render('registration__premium_only');
            return;
        }

        //Check each returning student has been assigned Classes
        //If not, delete them from the data.
        $stage2 = json_decode($_SESSION['stage2']);
        if (isset($stage2->returning_id)) {
            foreach ($stage2->returning_id as $id => $data) {
                if (!isset($this->_REQUEST['recommended'][$id]) && !isset($this->_REQUEST['class'][$id])) {
                    unset($stage2->returning_id->{$id});
                    $_SESSION['stage2'] = json_encode($stage2);
                }
            }
        }

        $_SESSION['stage3'] = json_encode($this->_REQUEST);

        $objRegistration = new \DancePressTRWA\Models\Registration($this->sessionCondition);
        $objBilling = new \DancePressTRWA\Models\Billing($this->sessionCondition);
        $objParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);
        $objClasses = new \DancePressTRWA\Models\ClassManager($this->sessionCondition);

        $parentId = false;

        if (is_user_logged_in()) {
            $parentId = $objParents->getParentIdByWPUserId();
        }

        if (!$registered = $objRegistration->registerUser($_SESSION, $parentId)) {
            $this->errors[] = "An error was encountered. Did you select any classes? Please try again.";
            $this->render('registration__premium_only', array('sessionName' => $this->getSessionName()));
            return;
        }
        try {
            if (!$initiateBilling = $objBilling->initiateUnpaid($registered, $this->_REQUEST)) {
                $this->errors[] = "An error was encountered. Did you select any classes? Please try again.";
                $this->render('registration__premium_only', array('sessionName' => $this->getSessionName()));
                return;
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        $studentClasses = $objClasses->getClassesByStudentIds($objRegistration->studentIds);

        $_SESSION['stage4'] = array();
        $_SESSION['stage4']['transaction_id'] = $objBilling->transaction_id;
        $_SESSION['stage4']['studentClasses'] = $studentClasses;
        $_SESSION['stage4']['parent_id'] = $registered['parents']['parent'];

        $oldSess = json_decode($_SESSION['stage2']);
        $newSess = $oldSess;

        unset($oldSess->returning_id, $oldSess->child);

        foreach ($registered['students'] as $sv) {
            //FIXME Different names for same variable..
            if (isset($sv->student_id)) {
                @$newSess->returning_id->{$sv->student_id} = $sv;
            } else {
                @$newSess->returning_id->{$sv->id} = $sv;
            }
        }

        $_SESSION['stage2'] = json_encode($newSess);

        $paymentOptions['ds_stripe_install_enabled'] = get_option('ds_stripe_install_enabled');
        $paymentOptions['ds_stripe_advance_enabled'] = get_option('ds_stripe_advance_enabled');
        $paymentOptions['ds_cheque_payment_enabled'] = get_option('ds_cheque_payment_enabled');
        $paymentOptions['ds_nofees_enabled'] = get_option('ds_nofees_enabled');

        $this->render('reg_stage4__premium_only', array(
            'data' => $initiateBilling,
            'parents' => $objParents->getParents($registered['parents']),
            'classes' => $studentClasses,
            'paymentOptions' => $paymentOptions
        ));
    }

    public function reg_stage5()
    {
        $option = new \DancePressTRWA\Models\Option();
        $objParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);

        if ($this->_REQUEST['paymenttype'] == 'cheque') {
            for ($i=1; $i <= 4; $i++) {
                if (!isset($_SESSION['stage' . $i])) {
                    return;
                }
            }
            $this->sendConfirmation();
            unset($_SESSION['stage1'], $_SESSION['stage2'], $_SESSION['stage3'], $_SESSION['stage4'], $_SESSION['totalFees']);
            return;
        }

        if ($this->_REQUEST['paymenttype'] == 'nopay') {
            for ($i=1; $i <= 4; $i++) {
                if (!isset($_SESSION['stage' . $i])) {
                    return;
                }
            }
            $this->sendConfirmation();
            unset($_SESSION['stage1'], $_SESSION['stage2'], $_SESSION['stage3'], $_SESSION['stage4'], $_SESSION['totalFees']);
            return;
        }

        \Stripe::setApiKey($option->getStripeSecretKey());

        //Make the customer a Stripe Customer who can be charged again in future.
        try {
            $error = 0;

            // Get the credit card details submitted by the form
            $this->stripeToken = filter_var($this->_REQUEST['stripeToken'], FILTER_SANITIZE_STRING);
            $this->stripeEmail = $this->_REQUEST['stripeEmail'];

            if (! $this->stripeToken) {
                throw new \Exception("Stripe token not provided");
            }
            if (! filter_var($this->stripeEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Stripe email not valid");
            }

            $this->objNewCustomer = \Stripe_Customer::create(
                array(
                    "card" => $this->stripeToken,
                    "description" => get_bloginfo('name') . " Customer: " . $this->stripeEmail,
                    "email" => $this->stripeEmail,
                    "account_balance" => 0
                )
            );
        } catch (\Stripe_CardError $e) {
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];
            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n");
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");
            $error = 1;
        } catch (\tripe_InvalidRequestError $e) {// Invalid parameters were supplied to Stripe's API
            echo "Invalid Request " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_AuthenticationError $e) { // Authentication with Stripe's API failed // (maybe you changed API keys recently)
            echo "Authenication error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_ApiConnectionError $e) { // Network communication with Stripe failed
            echo "API connection error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_Error $e) { // Display a very generic error to the user, and maybe send // yourself an email
            "Stripe Error " . $e->getMessage();
            $error = 1;
        } catch (\Exception $e) { // Something else happened, completely unrelated to Stripe
            echo "Error: " . $e->getMessage();
            $error = 1;
        }

        //Charge anything payable now, if necessary.
        if (!$error) {
            if ($this->_REQUEST['paymenttype'] == 'advance') {
                if ($this->payInAdvance()) {
                    $this->sendConfirmation();

                    $parentId = $_SESSION['stage4']['parent_id'];
                    $objParents->activateParent($parentId);

                    unset($_SESSION['stage1'], $_SESSION['stage2'], $_SESSION['stage3'], $_SESSION['stage4'], $_SESSION['totalFees']);
                } else {
                    return false;
                }
            } elseif ($this->chargeRegistrationFee()) {
                $this->sendConfirmation();
                $parentId = $_SESSION['stage4']['parent_id'];
                $objParents->activateParent($parentId, true);
                //get Parent Id and call Parent->activateParent($parentId)
                unset($_SESSION['stage1'], $_SESSION['stage2'], $_SESSION['stage3'], $_SESSION['stage4'], $_SESSION['totalFees']);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function chargeRegistrationFee()
    {
        $objBilling = new \DancePressTRWA\Models\Billing($this->sessionCondition);
        $option = new \DancePressTRWA\Models\Option;

        $currencyCode = strtolower($option->getStripeCurrency());

        if (empty($currencyCode)) {
            throw new Exception("Currency not set in plugin");
        }

        $contactTelephone = $option->getContactTelephone();
        $error = 0;

        if (!$transaction = $objBilling->getTransactionById($_SESSION['stage4']['transaction_id'], 1)) {
            echo "<h2>e503: We're sorry - something appears to have gone wrong. Please click the back button and try again. You have not been charged. If the problem persists, contact " . get_bloginfo('name') . " at {$contactTelephone}</h2>";
            return false;
        }

        $stripeTransactionFee = ($transaction->registration_fee * 100);

        foreach ($transaction->installments as $installment) {
            if (time() > strtotime($installment->payment_date)) {
                $stripeTransactionFee += ($installment->amount * 100);
            }
        }

        if (!$stripeTransactionFee) {
            return true;
        }

        try {
            \Stripe_Charge::create(
                array(
                    "customer" => $this->objNewCustomer->id,
                    "amount" => $stripeTransactionFee,
                    "description" => "Registration Fee " . $this->objNewCustomer->email,
                    "currency" => $currencyCode
                )
            );
        } catch (\Stripe_CardError $e) {
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();

            $err = $body['error'];

            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n"); // param is '' in this case
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");
            $error = 1;
        } catch (\Stripe_InvalidRequestError $e) {// Invalid parameters were supplied to Stripe's API
            echo "Invalid Request " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_AuthenticationError $e) { // Authentication with \Stripe's API failed // (maybe you changed API keys recently)
            echo "Authenication error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_ApiConnectionError $e) { // Network communication with \Stripe failed
            echo "API connection error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_Error $e) { // Display a very generic error to the user, and maybe send // yourself an email
            echo "Stripe Error " . $e->getMessage();
            $error = 1;
        } catch (Exception $e) { // Something else happened, completely unrelated to Stripe
            echo $e->getMessage();
            $error = 1;
        }

        if ($error) {
            echo "<h2>Sorry! Something went wrong with the credit card process! Your registration fee could not be processed. Please contact " . get_bloginfo('name') . " for immediate assistance on {$contactTelephone}</h2>";
            return false;
        }

        return true;
    }

    private function payInAdvance()
    {
        $objBilling = new \DancePressTRWA\Models\Billing($this->sessionCondition);
        $option = new \DancePressTRWA\Models\Option;

        $currencyCode = strtolower($option->getStripeCurrency());

        if (empty($currencyCode)) {
            throw new Exception("Currency not set in plugin");
        }

        $contactTelephone = $option->getContactTelephone();
        $error = 0;

        if (!$objBilling->getTransactionById($_SESSION['stage4']['transaction_id'], 1)) {
            echo "<h2>e582: We're sorry - something appears to have gone wrong. Please click the back button and try again. You have not been charged. If the problem persists, contact " . get_bloginfo('name') . " at {$contactTelephone}</h2>";
            return false;
        }

        $amount = (int) ($_SESSION['totalFees']->grandTotal * 100);

        try {
            \Stripe_Charge::create(
                array(
                    "customer" => $this->objNewCustomer->id,
                    "amount" => $amount,
                    "description" => "Registration Fee " . $this->objNewCustomer->email,
                    "currency" => $currencyCode
                )
            );
        } catch (\Stripe_CardError $e) {
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];
            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n"); // param is '' in this case
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");
            $error = 1;
        } catch (\Stripe_InvalidRequestError $e) {// Invalid parameters were supplied to Stripe's API
            echo "Invalid Request " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_AuthenticationError $e) { // Authentication with Stripe's API failed // (maybe you changed API keys recently)
            echo "Authenication error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_ApiConnectionError $e) { // Network communication with \Stripe failed
            echo "API connection error " . $e->getMessage();
            $error = 1;
        } catch (\Stripe_Error $e) { // Display a very generic error to the user, and maybe send // yourself an email
            echo "Stripe Error " . $e->getMessage();
            $error = 1;
        } catch (Exception $e) { // Something else happened, completely unrelated to Stripe
            echo $e->getMessage();
            $error = 1;
        }

        if ($error) {
            echo "<h2>Sorry! Something went wrong with the credit card process! Your registration fee could not be processed. Please contact " . get_bloginfo('name') . " for immediate assistance on {$contactTelephone}</h2>";
            return false;
        }

        return true;
    }

    private function sendConfirmation()
    {
        if ($this->_REQUEST['paymenttype'] == 'cheque') {
            $view = "cheque__premium_only";
        }
        if ($this->_REQUEST['paymenttype'] == 'nopay') {
            $view = "nopay__premium_only";
        } elseif ($this->_REQUEST['paymenttype'] == 'advance') {
            $view = "advance__premium_only";
        } else {
            $view = "reg_stage5__premium_only";
        }

        if (!isset($this->objNewCustomer)) {
            $this->objNewCustomer = false;
        }

        $stage1 = json_decode($_SESSION['stage1']);
        $stage2 = json_decode($_SESSION['stage2']);
        $stage3 = json_decode($_SESSION['stage3']);
        $stage4 = $_SESSION['stage4'];

        $parent = $stage1;
        $objBilling = new \DancePressTRWA\Models\Billing($this->sessionCondition);
        $objRegistration = new \DancePressTRWA\Models\Registration($this->sessionCondition);

        if ($this->objNewCustomer) {
            $objBilling->saveVerifiedTransaction($this->objNewCustomer, $_SESSION, $this->_REQUEST['paymenttype']); //save transaction
            $objRegistration->validateParentsAndChildren($_SESSION['stage4']['transaction_id'], $stage2->returning_id, $stage4['parent_id']); //add users/students to admin area
            $confirmationMessage = $objRegistration->makeParentUser($parent); //Make them Wordpress users with access DB Login
        } elseif ($this->_REQUEST['paymenttype'] == 'cheque') {
            $objBilling->saveChequeTransaction($_SESSION['totalFees'], $_SESSION['stage4']['transaction_id']);
            $objRegistration->validateParentsAndChildren($_SESSION['stage4']['transaction_id'], $stage2->returning_id, $stage4['parent_id']); //add users/students to admin area
            $confirmationMessage = __("Your registration is complete. Please ensure your cheques reach " . get_bloginfo('name') . " within 5 business days. Upon receipt of all required payments & postdated cheques, your account will be activated and you will be provided with access to the DB Login area to monitor your account.");
        } else {
            $objBilling->saveChequeTransaction($_SESSION['totalFees'], $_SESSION['stage4']['transaction_id']);
            $objRegistration->validateParentsAndChildren($_SESSION['stage4']['transaction_id'], $stage2->returning_id, $stage4['parent_id']); //add users/students to admin area
            $confirmationMessage = __("Your registration is complete.");
        }

        $trans = $objBilling->getTransactionById($_SESSION['stage4']['transaction_id']);


        $students = $objRegistration->getStudentsByTransactionId($_SESSION['stage4']['transaction_id'], 'excludeNonRegisterable');

        ob_start();
        $this->render($view, array(
            'data' => array(
                $stage1, $stage2, $stage3, $stage4, $trans,
                $this->objNewCustomer,
                'confirmationMessage' => $confirmationMessage,
                'totalFees' => $_SESSION['totalFees'],
                'students' => $students
            )
        ));

        $email = ob_get_contents();
        ob_flush();

        $option = new \DancePressTRWA\Models\Option();
        $headers = [];
        $contactEmail = $option->getContactEmail();
        $blogName = get_bloginfo('name');
        $toEmail = $stage1->parent->email;


        $headers[] = "From: {$blogName} <{$contactEmail}>";
        $headers[] = "Content-type:text/html";

        wp_mail($toEmail, "Confirmation of Registration with {$blogName}", $email, $headers);
    }

    public function checkRegistrationDuplicate()
    {
        $field =  $this->_REQUEST['field'];
        $value  = $this->_REQUEST['value'];

        if (!$field) {
            echo json_encode(array('error' => array('message' => 'Field not provided')));
            die();
        } elseif (!$value) {
            echo json_encode(array('error' => array('message' => 'Value not provided')));
            die();
        }

        $objRegistration = new \DancePressTRWA\Models\Registration($this->sessionCondition);
        $status = false;

        switch ($field) {
            case 'name':
                $status = $objRegistration->checkDuplicateName($value);
            break;
            case 'email':
                $status = $objRegistration->checkDuplicateEmail($value);
            break;
            case 'additionalemail':
                $status = $objRegistration->checkDuplicateEmail($value);
            break;
            case 'address':
                $status = $objRegistration->checkDuplicateAddress($value);
            break;
        }

        echo json_encode(array('hasduplicate'=>(int)$status));
        die();
    }
}
