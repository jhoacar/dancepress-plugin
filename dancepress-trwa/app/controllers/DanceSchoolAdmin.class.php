<?php

namespace DancePressTRWA\Controllers;

//PHP
use  Exception as PHPException ;
//DancePressTRWA
use  DancePressTRWA ;
/**
 * Controls admin interface.
 * TODO: Break up into separate controllers.
 * STATUS: Clients, Registration, Options moved to separate controllers. Remainder tbd.
 * @todo Hideous God object/spaghetti code in desperate need of refactoring.
 * @since 1.0
 */
final class DanceAdmin extends Controller
{
    public function startAdmin( $page = false, $overrideAction = false )
    {
        dancepresstrwa_register_session();
        //Allow output from here on.
        $this->output = true;
        if ( !$page ) {
            $this->page = ( isset( $this->_REQUEST['page'] ) ? $this->_REQUEST['page'] : false );
        }
        
        if ( !$overrideAction ) {
            $action = ( isset( $this->_REQUEST['action'] ) ? $this->_REQUEST['action'] : false );
            self::setAction( $action );
        }
        
        if ( $page || $overrideAction ) {
            $this->_REQUEST = [];
        }
        $this->pageNumber = 1;
        $this->search = false;
        if ( isset( $this->_REQUEST['p'] ) ) {
            $this->pageNumber = $this->_REQUEST['p'];
        }
        if ( isset( $this->_REQUEST['search'] ) ) {
            $this->search = $this->_REQUEST['search'];
        }
        try {
            switch ( self::$action ) {
                default:
                    $this->doHandleaction( $this->page, self::$action, $this->_REQUEST );
                    //Add,edit and remove  actions  method in App controller
                    break;
            }
            //FIXME REFACTOR this. Should have simple code to call correct class and method based on page and action/CRUD.
            //show the view and pages
            switch ( $this->page ) {
                case 'admin-addclass':
                    $this->render( $this->page, $this->_REQUEST );
                    //view for add class
                    break;
                case 'admin-editclass':
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    
                    if ( isset( $this->_REQUEST['editchild'] ) ) {
                        $classId = intval( $this->_REQUEST['id'] );
                        $this->render( 'admin-editchildclass', $objClassManager->getClassById( $classId ) );
                        //view for edit child class
                    } elseif ( isset( $this->_REQUEST['editchildren'] ) ) {
                        $parentId = intval( $this->_REQUEST['id'] );
                        $this->render( 'admin-listchildclass', $objClassManager->getClassChildrenById( $parentId ) );
                        //view for edit classes
                    } elseif ( isset( $this->_REQUEST['enrollment'] ) && self::$action == '' ) {
                        $classId = intval( $this->_REQUEST['id'] );
                        $averageAge = $objClassManager->getClassAverageAge( $classId );
                        $currentSelectedYear = ( isset( $this->_REQUEST['year'] ) ? $this->_REQUEST['year'] : date( 'Y', time() ) );
                        $currentSelectedMonth = ( isset( $this->_REQUEST['month'] ) ? $this->_REQUEST['month'] : date( 'm', time() ) );
                        $currentSelectedDay = ( isset( $this->_REQUEST['day'] ) ? $this->_REQUEST['day'] : date( 'd', time() ) );
                        $calculatedAverageAge = $objClassManager->getClassAverageAgeByTimestamp( $classId, mktime(
                            0,
                            0,
                            0,
                            $currentSelectedMonth,
                            $currentSelectedDay,
                            $currentSelectedYear
                        ) );
                        $this->render( 'admin-classenrollment', array(
                            'currentSelectedYear'  => $currentSelectedYear,
                            'currentSelectedMonth' => $currentSelectedMonth,
                            'currentSelectedDay'   => $currentSelectedDay,
                            'calculatedAverageAge' => $calculatedAverageAge,
                            "students"             => $objClassManager->getChildrenByClassId( $classId ),
                            "average_age"          => $averageAge,
                            'classId'              => $classId,
                        ) );
                        //view for edit classes
                    } else {
                        $classId = intval( $this->_REQUEST['id'] );
                        $this->render( $this->page, $objClassManager->getClassById( $classId ) );
                        //view for edit class
                    }
                    
                    break;
                case 'admin-listclass':
                    $this->manageClasses();
                    break;
                case 'admin-assignclass':
                    $objClassStudents = new \DancePressTRWA\Models\ClassStudents( $this->sessionCondition );
                    $classdays = $this->_REQUEST['classdays'];
                    
                    if ( isset( $classdays ) ) {
                        list( $classId, $weekDay ) = explode( '-', $classdays );
                        //hack, because classes require weekday provided
                        $classId = intval( $classId );
                        $weekDay = intval( $weekDay );
                        $studentIds = array();
                        if ( !empty($this->_REQUEST['studentids']) ) {
                            $studentIds = array_filter( $this->_REQUEST['studentids'], 'ctype_digit' );
                        }
                        foreach ( $studentIds as $studentId ) {
                            $objClassStudents->addStudentToClass( $classId, $weekDay, $studentId );
                        }
                        wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-assignclass' );
                    } else {
                        $start = ($this->pageNumber - 1) * $this->limit;
                        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                        $studentsClasses = $objClassStudents->getStudentsWithClasses( $start, $this->limit );
                        $classes = $objClassManager->getAllClass();
                        //assign class to students
                        $this->render( $this->page, array(
                            'studentsClasses' => $studentsClasses,
                            'classes'         => $classes,
                            'pageNumber'      => $this->pageNumber,
                            "weekdayMap"      => $objClassStudents->getWeekdays(),
                        ) );
                    }
                    
                    break;
                case 'admin-managestudents':
                    //Manage all elements relating to students
                    $this->manageStudents();
                    break;
                case 'admin-manageparents':
                    //Manage all elements relating to clients
                    $objClients = new ClientsController();
                    break;
                case 'admin-listclasscategories':
                    $objClassManager = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
                    $this->render( $this->page, $objClassManager->getClassCategories() );
                    //view for list classes
                    break;
                case 'admin-addclasscategory':
                    $this->render( $this->page, $this->_REQUEST );
                    //view for add class
                    break;
                case 'admin-editclasscategory':
                    $objClassManager = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
                    $categoryId = intval( $this->_REQUEST['id'] );
                    $this->render( $this->page, $objClassManager->getClassCategoryById( $categoryId ) );
                    //view for edit class
                    break;
                case 'admin-addclients':
                    $objClients = new ClientsController();
                    $objClients->addClients();
                    break;
                case 'admin-sendemail':
                    $this->sendEmail();
                    break;
                case 'admin-managegroups':
                    $this->manageGroups();
                    break;
                case 'admin-payments':
                    $billing = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
                    $currentYear = date( 'Y', time() );
                    $currentMonth = date( 'm', time() );
                    $receivedMonth = $billing->getReceivedPaymentsByMonth( $currentYear, $currentMonth );
                    $receivedYear = $billing->getReceivedPaymentsByYear( $currentYear );
                    $scheduledMonth = $billing->getScheduledPaymentsByMonth( $currentYear, $currentMonth );
                    $scheduledYear = $billing->getScheduledPaymentsByYear( $currentYear );
                    $totalMonth = $receivedMonth + $scheduledMonth;
                    $totalYear = $receivedYear + $scheduledYear;
                    $data = array(
                        'receivedMonth'  => $receivedMonth,
                        'receivedYear'   => $receivedYear,
                        'scheduledMonth' => $scheduledMonth,
                        'scheduledYear'  => $scheduledYear,
                        'totalMonth'     => $totalMonth,
                        'totalYear'      => $totalYear,
                    );
                    $this->render( 'admin-payments__premium_only', $data );
                    break;
                case 'admin-venues':
                    $this->venues();
                    break;
                case 'admin-events':
                    $this->events();
                    break;
                case 'admin-reports':
                    $this->reports();
                    break;
                case 'admin-options':
                    new OptionsController();
                    break;
                default:
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $this->render( 'admin-listclass', array(
                        'data' => $objClassManager->getAllClass(),
                        'rows' => $objClassManager->getAllClassForFront( '/wp-admin/admin.php?page=admin-editclass&editchild=1' ),
                    ) );
                    //view for list classes
                    break;
            }
        } catch ( PHPException $e ) {
            $this->errors[] = $e->getMessage();
            $this->render( $this->page );
        }
    }
    
    public function manageStudents()
    {
        $objClassStudents = new \DancePressTRWA\Models\ClassStudents( $this->sessionCondition );
        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
        
        if ( !isset( $this->_REQUEST['action'] ) ) {
            $numStudents = $objClassStudents->getCountRegisteredStudents();
            $this->render( $this->page, array(
                'numStudents' => $numStudents,
            ) );
            return;
        } elseif ( $this->_REQUEST['action'] == 'search' ) {
            $hideDeactivated = ( isset( $this->_REQUEST['showDeactivated'] ) ? false : true );
            $showDeactivated = ( isset( $this->_REQUEST['showDeactivated'] ) ? true : false );
            $search = $this->_REQUEST['search'];
            $this->render( $this->page, array(
                "students"        => $objClassStudents->findStudents( $search, $hideDeactivated ),
                'showDeactivated' => $showDeactivated,
            ) );
            return;
        } elseif ( $this->_REQUEST['action'] == 'delete' ) {
            $studentId = intval( $this->_REQUEST['id'] );
            $search = $this->_REQUEST['search'];
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $this->messages[] = $objClassStudents->deleteStudent( $studentId );
            $this->render( $this->page, $objClassStudents->findStudents( $search, true ) );
            return;
        } elseif ( $this->_REQUEST['action'] == 'edit' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $classes = $objClassStudents->getStudentClasses( $studentId );
            $addClasses = $objClassManager->getAllClass();
            $recommendations = $objClassManager->getRecommendedCourses( $studentId );
            $this->render( 'admin-editstudent', array(
                "student"         => $objClassStudents->getStudentById( $studentId ),
                "classes"         => $classes,
                "addClasses"      => $addClasses,
                "weekdayMap"      => $objClassStudents->getWeekdays(),
                'recommendations' => $recommendations,
            ) );
        } elseif ( $this->_REQUEST['action'] == 'updatestudent' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $firstname = $this->_REQUEST['firstname'];
            $lastname = $this->_REQUEST['lastname'];
            $birthdate = $this->_REQUEST['birthdate'];
            $gender = $this->_REQUEST['gender'];
            $address = json_encode( $this->_REQUEST['address'] );
            $schedule_available = $this->_REQUEST['schedule_available'];
            $custom_meta_key = $this->_REQUEST['custom_meta_key'];
            $custom_meta_value = $this->_REQUEST['custom_meta_value'];
            $oldMeta = $this->_REQUEST['meta'];
            if ( !$firstname ) {
                $this->errors[] = "First name not provided";
            }
            if ( !$lastname ) {
                $this->errors[] = "Last name not provided";
            }
            
            if ( !$birthdate ) {
                $this->errors[] = "Birthdate not provided";
            } elseif ( !strtotime( $birthdate ) ) {
                $this->errors[] = "Birthdate is not valid";
            }
            
            if ( !$gender ) {
                $this->errors[] = "Gender not provided";
            }
            if ( !$address ) {
                $this->errors[] = "Address not provided";
            }
            if ( !isset( $schedule_available ) ) {
                $schedule_available = false;
            }
            
            if ( empty($this->errors) ) {
                $data = array(
                    'firstname'          => $firstname,
                    'lastname'           => $lastname,
                    'birthdate'          => $birthdate,
                    'gender'             => $gender,
                    'address'            => $address,
                    'schedule_available' => $schedule_available,
                    'custom_meta_key'    => $custom_meta_key,
                    'custom_meta_value'  => $custom_meta_value,
                    'meta'               => $oldMeta,
                );
                if ( $objClassStudents->updateStudent( $data, $studentId ) ) {
                    $this->messages[] = "Student details updated";
                }
                $student = $objClassStudents->getStudentById( $studentId );
                wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=' . $studentId );
            }
        
        } elseif ( $this->_REQUEST['action'] == 'removerecommendation' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            $classId = intval( $this->_REQUEST['class_id'] );
            
            if ( !$studentId || !$classId ) {
                wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents' );
                exit;
            }
            
            $objCourseRecommendation = new \DancePressTRWA\Models\CourseRecommendation( $this->sessionCondition );
            $rec = $objCourseRecommendation->getRecommendationByStudentClass( $studentId, $classId );
            $objCourseRecommendation->deleteRecommendation( $rec->id );
            wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=' . $studentId );
        } elseif ( $this->_REQUEST['action'] == 'removeclass' ) {
            $classStudentId = intval( $this->_REQUEST['class_student_id'] );
            
            if ( $classStudentId ) {
                $studentId = $objClassStudents->getStudentIdByClassStudentsId( $classStudentId );
                $objClassStudents->removeStudentFromClass( $classStudentId );
                $this->messages[] = "Class deleted.";
                $classes = $objClassStudents->getStudentClasses( $studentId );
                $addClasses = $objClassManager->getAllClass();
                $this->render( 'admin-editstudent', array(
                    "student"    => $objClassStudents->getStudentById( $studentId ),
                    "classes"    => $classes,
                    "addClasses" => $addClasses,
                    "weekdayMap" => $objClassStudents->getWeekdays(),
                ) );
            } else {
                $this->errors[] = "Class not found. Has it already been deleted?";
            }
        
        } elseif ( $this->_REQUEST['action'] == 'addclasses' ) {
            $studentId = ( !empty($this->_REQUEST['student_id']) ? intval( $this->_REQUEST['student_id'] ) : 0 );
            $classDays = ( !empty($this->_REQUEST['classday']) ? $this->_REQUEST['classday'] : array() );
            //First add the class to the database $classId, $weekDay, $studentId
            $registeredClasses = [];
            
            if ( !$studentId || !$classDays ) {
                $this->errors[] = "Student id an/or class days not provided";
                $this->render( 'empty' );
                return;
            }
            
            foreach ( $classDays as $cdk => $cd ) {
                if ( $cd !== '' ) {
                    $registeredClasses[$cdk] = $cd;
                }
            }
            foreach ( $registeredClasses as $classId => $day ) {
                $objClassStudents->addStudentToClass( $classId, $day, $studentId );
            }
            $this->messages[] = "Classes updated successfully";
            $classes = $objClassStudents->getStudentClasses( $studentId );
            $addClasses = $objClassManager->getAllClass();
            $this->render( 'admin-editstudent', array(
                "student"    => $objClassStudents->getStudentById( $studentId ),
                "classes"    => $classes,
                "addClasses" => $addClasses,
                "weekdayMap" => $objClassStudents->getWeekdays(),
            ) );
        } elseif ( $this->_REQUEST['action'] == 'deactivatestudent' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
            $objClassParents->setStudentStatus( $studentId, 0 );
            wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=' . $studentId );
        } elseif ( $this->_REQUEST['action'] == 'deactivate-account' ) {
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
            $objClassParents->deactivateStudents( $studentIds );
            wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents&action=search' );
        } elseif ( $this->_REQUEST['action'] == 'activatestudent' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
            $objClassParents->setStudentStatus( $studentId, 1 );
            wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=' . $studentId );
        } elseif ( $this->_REQUEST['action'] == 'deactivatedstudents' ) {
            $students = $objClassStudents->findStudents( $this->search, false );
            $showDeactivated = true;
            $this->render( $this->page, array(
                'students'        => $students,
                'showDeactivated' => $showDeactivated,
            ) );
        } elseif ( $this->_REQUEST['action'] == 'datasheet' ) {
            $studentId = intval( $this->_REQUEST['student_id'] );
            
            if ( !$studentId ) {
                $this->errors[] = "Student id not provided";
                $this->render( 'empty' );
                return;
            }
            
            $student = $objClassStudents->getStudentById( $studentId );
            $this->render( 'admin-studentdatasheet', array(
                'student' => $student,
            ) );
        } elseif ( $this->_REQUEST['action'] == 'cleanmedicalfield' ) {
            $students = $objClassStudents->getAllStudents();
            $cleanValues = array(
                'na',
                'n/a',
                'n./a',
                'none',
                'n.a.',
                'non',
                'none known',
                'no',
                'nne',
                'hjkg',
                'non that i know of',
                'nka'
            );
            foreach ( $students as $sk => $s ) {
                $meta = json_decode( $s->meta );
                $changed = false;
                
                if ( $meta ) {
                    foreach ( $meta as $mk => $m ) {
                        if ( isset( $m->medical ) ) {
                            
                            if ( in_array( strtolower( $m->medical ), $cleanValues ) ) {
                                $meta->{$mk}->medical = '';
                                $changed = true;
                            }
                        
                        }
                    }
                    
                    if ( $changed ) {
                        $students[$sk]->meta = json_encode( $meta );
                        $objClassStudents->updateStudentMeta( $students[$sk]->id, $students[$sk]->meta );
                    }
                
                }
            
            }
        } elseif ( $this->_REQUEST['action'] == 'addrecommendedcourse-selectcourse' ) {
            $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
            $classes = $objClassManager->getAllClass();
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            $this->variables['classes'] = $classes;
            $this->variables['studentIds'] = $studentIds;
            $this->render( 'admin-addrecommendedcourse-selectcourse', $this->variables );
        } elseif ( $this->_REQUEST['action'] == 'addrecommendedcourse-add' ) {
            $objCourseRecommendation = new \DancePressTRWA\Models\CourseRecommendation( $this->sessionCondition );
            $studentIds = explode( ',', $this->_REQUEST['student_ids'] );
            $studentIds = array_filter( $studentIds, 'ctype_digit' );
            $courseIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            foreach ( $studentIds as $studentId ) {
                $objCourseRecommendation->add( $studentId, $courseIds );
            }
            $this->messages[] = 'Recommendations added';
            $search = $this->_REQUEST['search'];
            $this->render( $this->page, array(
                "students"        => $objClassStudents->findStudents( $search, $hideDeactivated ),
                'showDeactivated' => $showDeactivated,
            ) );
            $this->render( $this->page, $objClassStudents->findStudents( $search, true ) );
        } elseif ( $this->_REQUEST['action'] == 'addrecommendationtostudent' ) {
            $objCourseRecommendation = new \DancePressTRWA\Models\CourseRecommendation( $this->sessionCondition );
            $studentId = ( !empty($this->_REQUEST['student_id']) ? intval( $this->_REQUEST['student_id'] ) : 0 );
            $courseIds = ( !empty($this->_REQUEST['class_id']) ? array_filter( $this->_REQUEST['class_id'], 'ctype_digit' ) : array() );
            
            if ( $studentId && $courseIds ) {
                $objCourseRecommendation->add( $studentId, $courseIds );
                $this->messages[] = 'Recommendation added';
            } else {
                $this->errors[] = 'Recommendation not added';
            }
            
            $this->_REQUEST['action'] = 'edit';
            $this->manageStudents();
            return;
        } elseif ( $this->_REQUEST['action'] == 'enable-registration' ) {
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            if ( $objClassStudents->enableRegistrationsById( $studentIds ) ) {
                $this->messages[] = 'Registration(s) enabled';
            }
            $numStudents = $objClassStudents->getCountRegisteredStudents();
            $this->render( $this->page, array(
                'numStudents' => $numStudents,
            ) );
            return;
        } elseif ( $this->_REQUEST['action'] == 'disable-registration' ) {
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            if ( $objClassStudents->disableRegistrationsById( $studentIds ) ) {
                $this->messages[] = 'Registration(s) disabled';
            }
            $numStudents = $objClassStudents->getCountRegisteredStudents();
            $this->render( $this->page, array(
                'numStudents' => $numStudents,
            ) );
            return;
        } elseif ( $this->_REQUEST['action'] == 'disable-schedule' ) {
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            if ( $objClassStudents->disableScheduleById( $studentIds ) ) {
                $this->messages[] = 'Schedule viewing disabled';
            }
            $numStudents = $objClassStudents->getCountRegisteredStudents();
            $this->render( $this->page, array(
                'numStudents' => $numStudents,
            ) );
        } elseif ( $this->_REQUEST['action'] == 'enable-schedule' ) {
            $studentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            if ( $objClassStudents->enableScheduleById( $studentIds ) ) {
                $this->messages[] = 'Schedule viewing enabled';
            }
            $numStudents = $objClassStudents->getCountRegisteredStudents();
            $this->render( $this->page, array(
                'numStudents' => $numStudents,
            ) );
        }
    
    }
    
    public function addClients()
    {
        switch ( self::$action ) {
            case 'savenewclient':
                $objParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
                $objReg = new \DancePressTRWA\Models\Registration( $this->sessionCondition );
                $parent = ( !empty($this->_REQUEST['parent']) ? $this->_REQUEST['parent'] : array() );
                $email = $parent['email'];
                
                if ( !empty($email) && username_exists( $email ) ) {
                    $this->errors[] = "This username has already been taken, please choose another and/or check you are not duplicating an existing account.";
                    $this->render( $this->page );
                    return;
                }
                
                if ( empty($parent['firstname']) ) {
                    $this->errors[] = "First name not provided";
                }
                if ( empty($parent['lastname']) ) {
                    $this->errors[] = "Last name not provided";
                }
                
                if ( empty($parent['email']) ) {
                    $this->errors[] = "Email address not provided";
                } elseif ( !is_email( $parent['email'] ) ) {
                    $this->errors[] = "Email address is not valid";
                }
                
                
                if ( $this->errors ) {
                    $this->render( $this->page );
                    return;
                }
                
                $data = array(
                    'parent' => $parent,
                );
                $parentId = $objParents->addNewParent( $data );
                $this->_REQUEST['parent']['id'] = $parentId;
                $sendEmails = ( isset( $this->_REQUEST['sendnotifications'] ) && $this->_REQUEST['sendnotifications'] == 1 ? true : false );
                //Redirect to edit parent page
                $this->_REQUEST['page'] = 'admin-manageparents';
                $this->_REQUEST['parent_id'] = $parentId;
                $this->_REQUEST['action'] = 'edit';
                $this->startAdmin();
                exit;
                break;
            default:
                $this->render( $this->page );
                break;
        }
    }
    
    public function manageClasses()
    {
        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
        switch ( self::$action ) {
            case 'print':
                $classIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                foreach ( $classIds as $id ) {
                    $averageAge = $objClassManager->getClassAverageAge( $id );
                    $this->render( 'admin-classenrollment', array(
                        "students"    => $objClassManager->getChildrenByClassId( $id ),
                        "average_age" => $averageAge,
                    ) );
                }
                break;
            case 'print-advanced':
                $this->render( 'admin-createcustomclasslist', array(
                    "ids" => $this->_REQUEST['ids'],
                ) );
                break;
            case 'showadvancedclasslist':
                wp_enqueue_style( 'print-style' );
                $objListManager = new \DancePressTRWA\Models\ListManager( $this->sessionCondition );
                $classDetails = $objListManager->getListByCriteria( $this->_REQUEST );
                $this->render( 'admin-viewcustomclasslist', array(
                    "classDetails" => $classDetails,
                    "fields"       => $this->_REQUEST,
                ) );
                break;
            case 'disableregistration':
                $classIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $objClassManager->bulkDisableClasses( $classIds );
                $this->messages[] = "Classes disabled successfully.";
                $this->render( $this->page, array(
                    'data' => $objClassManager->getAllClass(),
                    'rows' => $objClassManager->getAllClassForFront( '/wp-admin/admin.php?page=admin-editclass&editchild=1' ),
                ) );
                break;
            case 'enableregistration':
                $classIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $objClassManager->bulkEnableClasses( $classIds );
                $this->messages[] = "Classes enabled successfully.";
                $this->render( $this->page, array(
                    'data' => $objClassManager->getAllClass(),
                    'rows' => $objClassManager->getAllClassForFront( '/wp-admin/admin.php?page=admin-editclass&editchild=1' ),
                ) );
                //view for list classes
                // no break
            //view for list classes
            // no break
            case 'delete':
                $classId = intval( $this->_REQUEST['id'] );
                
                if ( $classId ) {
                    $objClassManager->deleteClass( $classId );
                    $this->messages[] = "Class deleted successfully.";
                    $this->render( $this->page, array(
                        'data' => $objClassManager->getAllClass(),
                        'rows' => $objClassManager->getAllClassForFront( '/wp-admin/admin.php?page=admin-editclass&editchild=1' ),
                    ) );
                    //view for list classes
                }
                
                break;
            case 'emailselectedclasses':
                $this->page = 'admin-sendemail';
                $this->sendEmail();
                break;
            default:
                $this->render( $this->page, array(
                    'data' => $objClassManager->getAllClass(),
                    'rows' => $objClassManager->getAllClassForFront( '/wp-admin/admin.php?page=admin-editclass&editchild=1' ),
                ) );
                //view for list classes
                break;
        }
        return;
    }
    
    private function venues()
    {
        $objClassVenues = new \DancePressTRWA\Models\ClassVenues( $this->sessionCondition );
        switch ( self::$action ) {
            default:
                $this->render( $this->page, array(
                    "venues" => $objClassVenues->getAllVenues(),
                ) );
                break;
            case 'search':
                $search = array(
                    'search' => '',
                );
                
                if ( empty($this->_REQUEST['search']) ) {
                    $this->errors[] = "Search not provided";
                } else {
                    $search['search'] = $this->_REQUEST['search'];
                    $this->render( 'admin-venues', array(
                        "venues" => $objClassVenues->findVenues( $search ),
                    ) );
                }
                
                break;
            case 'add':
                $this->render( 'admin-addvenue', array(
                    'name'        => '',
                    'address1'    => '',
                    'address2'    => '',
                    'city'        => '',
                    'postal_code' => '',
                    'phone'       => '',
                ) );
                break;
            case 'edit':
                $venueId = intval( $this->_REQUEST['venue_id'] );
                
                if ( !$venueId ) {
                    $this->errors[] = "Venue id not provided";
                    break;
                }
                
                $this->render( 'admin-editvenue', array(
                    "venue" => $objClassVenues->getVenueById( $venueId ),
                ) );
                break;
            case 'addvenue':
                $name = $this->_REQUEST['name'];
                $address1 = $this->_REQUEST['address1'];
                $address2 = $this->_REQUEST['address2'];
                $city = $this->_REQUEST['city'];
                $postal_code = $this->_REQUEST['postal_code'];
                $phone = $this->_REQUEST['phone'];
                //
                $meta = array();
                $custom_meta_key = $this->_REQUEST['custom_meta_key'];
                $custom_meta_value = $this->_REQUEST['custom_meta_value'];
                
                if ( empty($name) ) {
                    $this->errors[] = "Name of the venue must be provided.";
                } elseif ( $objClassVenues->venueExists( $name ) ) {
                    $this->errors[] = "Venue with name '{$name}' already exists";
                } elseif ( empty($address1) ) {
                    $this->errors[] = "Address 1 of the venue must be provided.";
                } elseif ( empty($city) ) {
                    $this->errors[] = "City of the venue must be provided.";
                } elseif ( empty($phone) ) {
                    $this->errors[] = "Phone number of the venue must be provided.";
                }
                
                $data = array(
                    'name'              => $name,
                    'address1'          => $address1,
                    'address2'          => $address2,
                    'city'              => $city,
                    'postal_code'       => $postal_code,
                    'phone'             => $phone,
                    'meta'              => $meta,
                    'custom_meta_key'   => $custom_meta_key,
                    'custom_meta_value' => $custom_meta_value,
                );
                
                if ( $this->errors ) {
                    $this->render( 'admin-addvenue', $data );
                    break;
                }
                
                $venueId = $objClassVenues->addVenue( $data );
                
                if ( $venueId ) {
                    $this->messages[] = "Venue added";
                    $this->render( 'admin-editvenue', array(
                        "venue" => $objClassVenues->getVenueById( $venueId ),
                    ) );
                } else {
                    $this->errors[] = "Error adding venue. Please try again.";
                    $this->render( 'admin-addvenue', $data );
                }
                
                break;
            case 'updatevenue':
                $venueId = intval( $this->_REQUEST['venue_id'] );
                
                if ( !$venueId ) {
                    $this->errors[] = "Venue id not provided";
                    break;
                }
                
                $name = $this->_REQUEST['name'];
                $address1 = $this->_REQUEST['address1'];
                $address2 = $this->_REQUEST['address2'];
                $city = $this->_REQUEST['city'];
                $postal_code = $this->_REQUEST['postal_code'];
                $phone = $this->_REQUEST['phone'];
                //
                $meta = array();
                $custom_meta_key = $this->_REQUEST['custom_meta_key'];
                $custom_meta_value = $this->_REQUEST['custom_meta_value'];
                
                if ( empty($name) ) {
                    $this->errors[] = "Name of the venue must be provided.";
                } elseif ( empty($address1) ) {
                    $this->errors[] = "Address 1 of the venue must be provided.";
                } elseif ( empty($city) ) {
                    $this->errors[] = "City of the venue must be provided.";
                } elseif ( empty($phone) ) {
                    $this->errors[] = "Phone number of the venue must be provided.";
                } else {
                    $data = array(
                        'name'              => $name,
                        'address1'          => $address1,
                        'address2'          => $address2,
                        'city'              => $city,
                        'postal_code'       => $postal_code,
                        'phone'             => $phone,
                        'meta'              => $meta,
                        'custom_meta_key'   => $custom_meta_key,
                        'custom_meta_value' => $custom_meta_value,
                    );
                    $objClassVenues->updateVenue( $data, $venueId );
                    $this->messages[] = "Venue details updated";
                    $this->render( 'admin-editvenue', array(
                        "venue" => $objClassVenues->getVenueById( $venueId ),
                    ) );
                }
                
                break;
            case 'deletevenues':
                $venueIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                
                if ( !empty($venueIds) ) {
                    $deleted = 0;
                    $unableToDelete = 0;
                    foreach ( $venueIds as $venueId ) {
                        
                        if ( $objClassVenues->deleteVenue( $venueId ) ) {
                            $deleted++;
                        } else {
                            $unableToDelete++;
                        }
                    
                    }
                    if ( $deleted ) {
                        $this->messages[] = "Deleted {$deleted} venue(s).";
                    }
                    if ( $unableToDelete ) {
                        $this->errors[] = "Unable to delete {$unableToDelete} venue(s).";
                    }
                } else {
                    $this->errors[] = "IDs of venues not provided";
                }
                
                $this->render( 'admin-venues', array(
                    "venues" => $objClassVenues->getAllVenues(),
                ) );
                break;
            case 'delete':
                $venueId = intval( $this->_REQUEST['venue_id'] );
                
                if ( $venueId ) {
                    
                    if ( $objClassVenues->deleteVenue( $venueId ) ) {
                        $this->messages[] = "Venue deleted.";
                    } else {
                        $this->errors[] = "Unable to delete venue. Please try again.";
                    }
                
                } else {
                    $this->errors[] = "Venue ID not provided";
                }
                
                $this->render( 'admin-venues', array(
                    "venues" => $objClassVenues->getAllVenues(),
                ) );
                break;
        }
    }
    
    public function events()
    {
        $objClassEvents = new \DancePressTRWA\Models\ClassEvents( $this->sessionCondition );
        $objClassVenues = new \DancePressTRWA\Models\ClassVenues( $this->sessionCondition );
        switch ( self::$action ) {
            default:
                $this->render( $this->page, array(
                    "events" => $objClassEvents->getAllEvents(),
                ) );
                break;
            case 'past':
                $this->render( $this->page, array(
                    "events" => $objClassEvents->getPastEvents(),
                ) );
                break;
            case 'current':
                $this->render( $this->page, array(
                    "events" => $objClassEvents->getCurrentEvents(),
                ) );
                break;
            case 'upcoming':
                $this->render( $this->page, array(
                    "events" => $objClassEvents->getUpcomingEvents(),
                ) );
                break;
            case 'search':
                $search = array(
                    'search' => '',
                );
                
                if ( empty($this->_REQUEST['search']) ) {
                    $this->errors[] = "Search not provided";
                    break;
                }
                
                $search['search'] = $this->_REQUEST['search'];
                $this->render( 'admin-events', array(
                    "events" => $objClassEvents->findEvents( $search ),
                ) );
                break;
            case 'add':
                $venues = $objClassVenues->getAllVenues();
                
                if ( empty($venues) ) {
                    $this->errors[] = "You must create a venue before adding events";
                    $this->render( 'admin-addvenue', array(
                        'name'        => '',
                        'address1'    => '',
                        'address2'    => '',
                        'city'        => '',
                        'postal_code' => '',
                        'phone'       => '',
                    ) );
                } else {
                    $this->render( 'admin-addevent', array(
                        'name'         => '',
                        'starts'       => date( 'Y-m-d h:i A', time() + 1800 ),
                        'ends'         => date( 'Y-m-d h:i A', time() + 3600 ),
                        'ticket_price' => 0.0,
                        'max_ticksts'  => 50,
                        'venues'       => $objClassVenues->getAllVenues(),
                        'description'  => '',
                        'image_url'    => "",
                    ) );
                }
                
                break;
            case 'edit':
                $eventId = intval( $this->_REQUEST['event_id'] );
                
                if ( !$eventId ) {
                    $this->errors[] = "Event id not provided";
                    return;
                }
                
                $this->render( 'admin-editevent', array(
                    "event"  => $objClassEvents->getEventById( $eventId ),
                    'venues' => $objClassVenues->getAllVenues(),
                ) );
                break;
            case 'addevent':
                $name = $this->_REQUEST['name'];
                $starts = $this->_REQUEST['starts'];
                $ends = $this->_REQUEST['ends'];
                $ticket_price = $this->_REQUEST['ticket_price'];
                $max_tickets = $this->_REQUEST['max_tickets'];
                $description = $this->_REQUEST['description'];
                $image_url = $this->_REQUEST['image_url'];
                $venues = $objClassVenues->getAllVenues();
                $venueId = (int) $this->_REQUEST['venue_id'];
                //
                $meta = array();
                $custom_meta_key = $this->_REQUEST['custom_meta_key'];
                $custom_meta_value = $this->_REQUEST['custom_meta_value'];
                $data = array(
                    'name'              => $name,
                    'starts'            => $starts,
                    'ends'              => $ends,
                    'ticket_price'      => $ticket_price,
                    'max_tickets'       => $max_tickets,
                    'description'       => $description,
                    'image_url'         => $image_url,
                    'venues'            => $venues,
                    'venue_id'          => $venueId,
                    'meta'              => $meta,
                    'custom_meta_key'   => $custom_meta_key,
                    'custom_meta_value' => $custom_meta_value,
                );
                
                if ( !$name ) {
                    $this->errors[] = "Event name must be provided.";
                } elseif ( !$starts ) {
                    $this->errors[] = "Start datetime must be provided for event.";
                } elseif ( !$ends ) {
                    $this->errors[] = "End datetime must be provided for event.";
                } elseif ( !isset( $ticket_price ) ) {
                    $this->errors[] = "Specify the cost for an individual ticket.";
                } elseif ( strtotime( $ends ) < strtotime( $starts ) ) {
                    $this->errors[] = "Event end datetime must be greater than event start datetime.";
                } elseif ( strtotime( $ends ) == strtotime( $starts ) ) {
                    $this->errors[] = "Event cannot start and end at the same time.";
                } elseif ( !$max_tickets ) {
                    $this->errors[] = "Max tickets must be provided.";
                } elseif ( !$description ) {
                    $this->errors[] = "The event must have a description.";
                } elseif ( !$venueId ) {
                    $this->errors[] = "The event must have a venue.";
                } else {
                    $eventId = $objClassEvents->addEvent( $data );
                    
                    if ( $eventId ) {
                        $this->messages[] = "Event added.";
                        $this->render( 'admin-editevent', array(
                            "event"  => $objClassEvents->getEventById( $eventId ),
                            'venues' => $venues,
                        ) );
                    } else {
                        $this->errors[] = "Unable to add event details. Please try again.";
                    }
                
                }
                
                if ( !empty($this->errors) ) {
                    $this->render( 'admin-addevent', $data );
                }
                break;
            case 'updateevent':
                $eventId = $this->_REQUEST['event_id'];
                
                if ( !$eventId ) {
                    $this->errors[] = "Event id not provided";
                    break;
                }
                
                $event = $objClassEvents->getEventById( $eventId );
                
                if ( !$event ) {
                    $this->errors[] = "Event with id '{$eventId}' does not exist";
                    break;
                }
                
                $name = $this->_REQUEST['name'];
                $starts = $this->_REQUEST['starts'];
                $ends = $this->_REQUEST['ends'];
                $ticket_price = $this->_REQUEST['ticket_price'];
                $max_tickets = $this->_REQUEST['max_tickets'];
                $description = $this->_REQUEST['description'];
                $image_url = $this->_REQUEST['image_url'];
                $venues = $objClassVenues->getAllVenues();
                $venueId = (int) $this->_REQUEST['venue_id'];
                //
                $meta = array();
                $custom_meta_key = $this->_REQUEST['custom_meta_key'];
                $custom_meta_value = $this->_REQUEST['custom_meta_value'];
                $data = array(
                    'name'              => $name,
                    'starts'            => $starts,
                    'ends'              => $ends,
                    'ticket_price'      => $ticket_price,
                    'max_tickets'       => $max_tickets,
                    'description'       => $description,
                    'image_url'         => $image_url,
                    'venues'            => $venues,
                    'venue_id'          => $venueId,
                    'meta'              => $meta,
                    'custom_meta_key'   => $custom_meta_key,
                    'custom_meta_value' => $custom_meta_value,
                );
                
                if ( !$name ) {
                    $this->errors[] = "Event name must be provided.";
                } elseif ( !$starts ) {
                    $this->errors[] = "Start datetime must be provided for event.";
                } elseif ( !$ends ) {
                    $this->errors[] = "End datetime must be provided for event.";
                } elseif ( !isset( $ticket_price ) ) {
                    $this->errors[] = "Specify the cost for an individual ticket.";
                } elseif ( strtotime( $ends ) < strtotime( $starts ) ) {
                    $this->errors[] = "Event end datetime must be greater than event start datetime.";
                } elseif ( strtotime( $ends ) == strtotime( $starts ) ) {
                    $this->errors[] = "Event cannot start and end at the same time.";
                } elseif ( !$max_tickets ) {
                    $this->errors[] = "Max tickets must be provided.";
                } elseif ( !$description ) {
                    $this->errors[] = "The event must have a description.";
                } elseif ( !$venueId ) {
                    $this->errors[] = "The event must have a venue.";
                }
                
                if ( !$this->errors ) {
                    
                    if ( $objClassEvents->updateEvent( $data, $eventId ) ) {
                        $this->messages[] = "Event details updated.";
                    } else {
                        $this->errors[] = "Unable to update event details. Please try again.";
                    }
                
                }
                $event = $objClassEvents->getEventById( $eventId );
                $this->render( 'admin-editevent', array(
                    "event"  => $event,
                    'venues' => $venues,
                ) );
                break;
            case 'deleteevents':
                $eventIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                
                if ( $eventIds ) {
                    $deleted = 0;
                    $unableToDelete = 0;
                    foreach ( $eventIds as $eventId ) {
                        
                        if ( $objClassEvents->deleteEvent( $eventId ) ) {
                            $deleted++;
                        } else {
                            $unableToDelete++;
                        }
                    
                    }
                    if ( $deleted ) {
                        $this->messages[] = "Deleted {$deleted} event(s).";
                    }
                    if ( $unableToDelete ) {
                        $this->errors[] = "Unable to delete {$unableToDelete} event(s).";
                    }
                } else {
                    $this->errors[] = "IDs of events not provided";
                }
                
                $this->render( 'admin-events', array(
                    "events" => $objClassEvents->getUpcomingEvents(),
                ) );
                break;
            case 'delete':
                $eventId = intval( $this->_REQUEST['event_id'] );
                
                if ( !$eventId ) {
                    $this->errors[] = "Event id not provided";
                } else {
                    $event = $objClassEvents->getEventById( $eventId );
                    if ( !$event ) {
                        $this->errors[] = "Event with id '{$eventId}' does not exist";
                    }
                    if ( $event ) {
                        
                        if ( $objClassEvents->deleteEvent( $eventId ) ) {
                            $this->messages[] = "Event deleted.";
                        } else {
                            $this->errors[] = "Unable to delete event. Please try again.";
                        }
                    
                    }
                }
                
                $this->render( 'admin-events', array(
                    "events" => $objClassEvents->getUpcomingEvents(),
                ) );
                break;
            case 'list-purchased-tickets':
                $objDancepressClassEventTicketSales = new \DancePressTRWA\Models\DancepressClassEventTicketSales( $this->sessionCondition );
                $this->render( 'admin-purchased-tickets', array(
                    "purchases" => $objDancepressClassEventTicketSales->findEventTicketSales( $this->_REQUEST ),
                ) );
                break;
        }
    }
    
    private function reports()
    {
        $objReports = new \DancePressTRWA\Models\ClassReports( $this->sessionCondition );
        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
        $classList = $objClassManager->getAllClass( false, false, 'c.name' );
        switch ( self::$action ) {
            default:
                $allColumnMap = $objReports::getAllColumnMap();
                $optionStudentShowDeactivated = ( isset( $this->_REQUEST['option-students-showdeactivated'] ) ? ( $this->_REQUEST['option-students-showdeactivated'] == 'on' ? true : false ) : false );
                $selectedColumns = array();
                $selectedHideEmpty = array();
                $filters = array();
                foreach ( $allColumnMap as $cmk => $cm ) {
                    if ( isset( $this->_REQUEST[$cmk] ) ) {
                        
                        if ( $this->_REQUEST[$cmk] == 'on' ) {
                            $selectedColumns[] = $cmk;
                            if ( isset( $this->_REQUEST[$cmk . '-hideempty'] ) ) {
                                if ( $this->_REQUEST[$cmk . '-hideempty'] == 'on' ) {
                                    $selectedHideEmpty[] = $cmk;
                                }
                            }
                        }
                    
                    }
                    if ( isset( $this->_REQUEST['filter-' . $cmk] ) ) {
                        
                        if ( $this->_REQUEST['filter-' . $cmk] ) {
                            if ( !isset( $filters[$allColumnMap[$cmk]['table']] ) ) {
                                $filters[$allColumnMap[$cmk]['table']] = array();
                            }
                            
                            if ( isset( $allColumnMap[$cmk]['json-field'] ) && $allColumnMap[$cmk]['json-field'] ) {
                                $filters[$allColumnMap[$cmk]['table']]['meta-' . $allColumnMap[$cmk]['json-field']] = array(
                                    'value'   => $this->_REQUEST['filter-' . $cmk],
                                    'compare' => $allColumnMap[$cmk]['filter-compare'],
                                );
                                $filters[$allColumnMap[$cmk]['table']]['meta-' . $allColumnMap[$cmk]['json-field']]['json-field'] = $allColumnMap[$cmk]['json-field'];
                            } else {
                                $filters[$allColumnMap[$cmk]['table']][$allColumnMap[$cmk]['column']] = array(
                                    'value'   => $this->_REQUEST['filter-' . $cmk],
                                    'compare' => $allColumnMap[$cmk]['filter-compare'],
                                );
                            }
                        
                        }
                    
                    }
                }
                if ( !$optionStudentShowDeactivated ) {
                    $filters['ds_students'] = array(
                        'active' => array(
                        'value'   => 1,
                        'compare' => 'exact',
                    ),
                    );
                }
                $studentsParentsClasses = $objReports->getMergedReport( $selectedColumns, $filters );
                $studentsParentsClasses = $objReports->appendJSONFields( $studentsParentsClasses, $selectedColumns );
                if ( $studentsParentsClasses ) {
                    //filter out by hiding results where "hide result if empty" is checked
                    for ( $i = count( $studentsParentsClasses ) - 1 ;  $i >= 0 ;  $i-- ) {
                        foreach ( $selectedHideEmpty as $she ) {
                            $varName = ( isset( $allColumnMap[$she]['json-field'] ) ? $allColumnMap[$she]['table'] . '_' . $allColumnMap[$she]['json-field'] : $allColumnMap[$she]['table'] . '_' . $allColumnMap[$she]['column'] );
                            
                            if ( !isset( $studentsParentsClasses[$i]->{$varName} ) ) {
                                //this probably means an error...
                                continue;
                                //skip to avoid errors, although data output likely breaks
                            }
                            
                            if ( $studentsParentsClasses[$i]->{$varName} == '' ) {
                                unset( $studentsParentsClasses[$i] );
                            }
                        }
                    }
                }
                // get header titles and add filter input - hackity hack hack. //FIXME: skip if no mapping found (meta, I'm looking at you)
                $headers = array();
                $titles = array();
                if ( $studentsParentsClasses ) {
                    foreach ( $studentsParentsClasses as $spc ) {
                        $tables = array( '', 'ds_parents_meta', 'ds_students_meta' );
                        foreach ( $tables as $t ) {
                            foreach ( $spc as $column => $value ) {
                                foreach ( $allColumnMap as $acmk => $acm ) {
                                    //echo ($acm['table'] . '_' . $acm['column']) . ' - ' . $column .'<br>'; //ds_parents_meta - ds_class_students_week_day
                                    switch ( $t ) {
                                        case '':
                                            $compareVal = $column;
                                            break;
                                        default:
                                            $compareVal = $t;
                                            break;
                                    }
                                    
                                    if ( $acm['table'] . '_' . $acm['column'] == $compareVal ) {
                                        if ( $compareVal == 'ds_parents_meta' || $compareVal == 'ds_students_meta' ) {
                                            if ( !in_array( $acmk, $selectedColumns ) ) {
                                                continue;
                                            }
                                        }
                                        $header = '';
                                        $filterName = 'filter-' . $acmk;
                                        //$acm['column'];
                                        switch ( $acm['filter-type'] ) {
                                            case 'text':
                                                $header .= '<br><input type="text" name="' . $filterName . '" value="' . htmlspecialchars( @$this->_REQUEST['filter-' . $acmk] ) . '"/>';
                                                break;
                                            case 'select-class':
                                                $header .= '<br><select name="' . $filterName . '">';
                                                $header .= '<option value="">Select</option>';
                                                foreach ( $classList as $c ) {
                                                    $selected = ( htmlspecialchars( @$this->_REQUEST['filter-' . $acmk] ) == $c->id ? ' selected="selected" ' : '' );
                                                    $header .= '<option value="' . $c->id . '" ' . $selected . '>' . $c->name . ' (' . $c->weekday_name . ')</option>';
                                                }
                                                $header .= '</select>';
                                        }
                                        $titles[] = $acm['title'];
                                        $headers[] = $header;
                                        if ( !$t ) {
                                            break;
                                        }
                                    }
                                
                                }
                                if ( $t ) {
                                    break;
                                }
                            }
                        }
                        break;
                        //only need first row to get header
                    }
                }
                $this->render( $this->page, array(
                    'studentsParentsClasses'       => $studentsParentsClasses,
                    'studentsColumnMap'            => $objReports::$studentsColumnMap,
                    'parentsColumnMap'             => $objReports::$parentsColumnMap,
                    'classStudentsColumnMap'       => $objReports::$classStudentsColumnMap,
                    'allColumnMap'                 => $allColumnMap,
                    'selectedColumns'              => $selectedColumns,
                    'selectedHideEmpty'            => $selectedHideEmpty,
                    'headers'                      => $headers,
                    'filters'                      => $filters,
                    'classList'                    => $classList,
                    'titles'                       => $titles,
                    'optionStudentShowDeactivated' => $optionStudentShowDeactivated,
                ) );
                break;
        }
    }
    
    public function sendEmail()
    {
        $objMail = new \DancePressTRWA\Models\Mailing( $this->sessionCondition );
        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
        $includeDeactivated = ( isset( $this->_REQUEST['include_deactivated'] ) ? true : false );
        switch ( self::$action ) {
            case 'emailgroups':
                
                if ( $this->_REQUEST['group_id'] == '' ) {
                    $this->errors[] = "You did not select a group to send the message to. Please return to the previous page and add the missing information.";
                    $this->render( 'empty' );
                    return;
                }
                
                
                if ( $this->_REQUEST['special_functions'] == 'recommendation' ) {
                    $parents = $objMail->retrieveGroupMembers( $this->_REQUEST, $includeDeactivated );
                    $objRecs = new \DancePressTRWA\Models\CourseRecommendation( $this->sessionCondition );
                    $recs = $objRecs->getRecommendationsByIds( $parents );
                    $input = array();
                    
                    if ( empty($this->_REQUEST['subject']) ) {
                        $this->errors[] = "Subject not provided";
                    } else {
                        $input['subject'] = $this->_REQUEST['subject'];
                    }
                    
                    
                    if ( empty($this->_REQUEST['message']) ) {
                        $this->errors[] = "Message not provided";
                    } else {
                        $input['message'] = $this->_REQUEST['message'];
                    }
                    
                    if ( !$this->errors ) {
                        
                        if ( $objMail->sendRecsToGroup(
                            $parents,
                            $recs,
                            $input,
                            $includeDeactivated
                        ) ) {
                            $this->messages[] = "Mailing sent successfully.";
                        } else {
                            $this->errors[] = "Mailing error: message not sent.";
                        }
                    
                    }
                } elseif ( $objMail->sendToGroup( $this->_REQUEST, $includeDeactivated ) ) {
                    $this->messages[] = "Mailing sent successfully.";
                } else {
                    $this->errors[] = "Mailing error: message not sent.";
                }
                
                $this->render( $this->page, array(
                    'action' => "emailgroups",
                    'groups' => $objMail->getGroups(),
                ) );
                break;
            case 'sendtoids':
                $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $subject = $this->_REQUEST['subject'];
                $message = $this->_REQUEST['message'];
                $input = array();
                $input['ids'] = $parentIds;
                
                if ( empty($this->_REQUEST['subject']) ) {
                    $this->errors[] = "Subject not provided";
                } else {
                    $input['subject'] = $this->_REQUEST['subject'];
                }
                
                
                if ( empty($this->_REQUEST['message']) ) {
                    $this->errors[] = "Message not provided";
                } else {
                    $input['message'] = $this->_REQUEST['message'];
                }
                
                
                if ( $this->_REQUEST['special_functions'] == 'recommendation' ) {
                    $objParent = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
                    foreach ( $parentIds as $pid ) {
                        $parent = $objClassParents->getParentByIdCommunity( $pid );
                        if ( $includeDeactivated == false && !$parent[0]->active ) {
                            continue;
                        }
                        $objRecs = new \DancePressTRWA\Models\CourseRecommendation( $this->sessionCondition );
                        $recs = $objRecs->getRecommendationsByIds( $parent );
                        $objMail->sendRecsToGroup(
                            $parent,
                            $recs,
                            $input,
                            $includeDeactivated
                        );
                    }
                } elseif ( $objMail->sendToIds( array(
                    'id'      => $parentIds,
                    'subject' => $subject,
                    'message' => $message,
                ), $includeDeactivated ) ) {
                    $this->messages[] = "Mailing sent successfully.";
                } else {
                    $this->errors[] = "Mailing error: message not sent.";
                }
                
                $this->render( $this->page, array(
                    'action' => "emailgroups",
                    'groups' => $objMail->getGroups(),
                ) );
                break;
            case 'emailids':
                $this->render( $this->page, array(
                    'ids'    => $this->_REQUEST['ids'],
                    'action' => 'sendtoids',
                ) );
                break;
            case 'email-class':
                $classId = intval( $this->_REQUEST['class_id'] );
                
                if ( !$classId ) {
                    $this->errors[] = "Class id not provided";
                    $this->render( 'empty' );
                    return;
                }
                
                
                if ( !($parentIds = $objClassManager->getParentIdsByClassId( $classId )) ) {
                    $this->errors[] = "No clients found to email. Is this class empty?";
                    $this->render( 'empty' );
                    return;
                }
                
                $this->render( $this->page, array(
                    'ids'    => $parentIds,
                    'action' => 'sendtoids',
                ) );
                break;
            case 'emailselectedclasses':
                $classIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $bulkIds = array();
                foreach ( $classIds as $id ) {
                    
                    if ( !($parentIds = $objClassManager->getParentIdsByClassId( $id )) ) {
                        continue;
                    } else {
                        $bulkIds = array_merge( $bulkIds, $parentIds );
                    }
                
                }
                $this->render( $this->page, array(
                    'ids'    => $bulkIds,
                    'action' => 'sendtoids',
                ) );
                break;
            default:
                $this->render( $this->page, array(
                    'action' => "emailgroups",
                    'groups' => $objMail->getGroups(),
                ) );
                break;
        }
    }
    
    private function manageGroups()
    {
        $objMail = new \DancePressTRWA\Models\Mailing( $this->sessionCondition );
        $objParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        switch ( self::$action ) {
            case 'create_list':
                $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $parents = $objParents->getParentsByIds( $parentIds );
                $this->render( 'admin-create-group__premium_only', array(
                    'ids'     => $this->_REQUEST['ids'],
                    'action'  => 'create_mailing_group',
                    'parents' => $parents,
                ) );
                break;
            case 'modify_list':
                $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $parents = $objParents->getParentsByIds( $parentIds );
                $this->render( 'admin-modify-group__premium_only', array(
                    'ids'     => $this->_REQUEST['ids'],
                    'action'  => 'modify_mailing_group',
                    'groups'  => $objMail->getGroups(),
                    'parents' => $parents,
                ) );
                break;
            case 'save_new_group':
                $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $group_name = $this->_REQUEST['group_name'];
                if ( !$parentIds ) {
                    $this->errors[] = "Parent ids not provided";
                }
                if ( !$group_name ) {
                    $this->errors[] = "Group name not provided";
                }
                
                if ( !$this->errors ) {
                    $data = array(
                        'ids'        => $parentIds,
                        'group_name' => $group_name,
                    );
                    
                    if ( !$objMail->saveNewGroup( $data ) ) {
                        $this->errors[] = "Error encountered: Group with this name already exists.";
                        $this->render( 'empty' );
                        return;
                    }
                    
                    $this->messages[] = "New mailing group created";
                }
                
                $this->render( $this->page, array(
                    'action' => "emailgroups",
                    'groups' => $objMail->getGroups(),
                ) );
                break;
            case 'save_group_update':
                $groupId = intval( $this->_REQUEST['group_id'] );
                $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
                $update_type = $this->_REQUEST['update_type'];
                if ( !$groupId ) {
                    $this->errors[] = "Group id not provided";
                }
                if ( !$parentIds ) {
                    $this->errors[] = "Parent ids not provided";
                }
                if ( $update_type ) {
                    $this->errors[] = "Update type not provided";
                }
                
                if ( $this->errors ) {
                    $this->render( 'empty' );
                    break;
                }
                
                $data = array(
                    'group_id'    => $groupId,
                    'update_type' => $update_type,
                    'ids'         => $parentIds,
                );
                
                if ( !$objMail->updateGroup( $data ) ) {
                    $this->errors[] = "Error encountered";
                    $this->render( 'empty' );
                    return;
                }
                
                $this->messages[] = "Mailing group updated";
                $this->render( 'admin-view-group__premium_only', array(
                    'parents' => $objMail->getGroupParents( $groupId ),
                ) );
                break;
            case 'manage-groups':
                $this->render( $this->page, array(
                    'groups' => $objMail->getGroups(),
                ) );
                break;
            case 'delete-groups':
                $groupId = intval( $this->_REQUEST['id'] );
                
                if ( !$groupId ) {
                    $this->errors[] = "Group id not provided";
                    $this->render( 'empty' );
                    break;
                }
                
                
                if ( $objMail->deleteGroup( $groupId ) !== false ) {
                    $this->messages[] = 'Mailing Group deleted';
                } else {
                    $this->messages[] = "An error was encountered during deletion. Report to developer if problem continues.";
                }
                
                $this->render( $this->page, array(
                    'groups' => $objMail->getGroups(),
                ) );
                break;
            case 'delete-group-member':
                $parentId = intval( $this->_REQUEST['parent_id'] );
                $groupId = intval( $this->_REQUEST['group_id'] );
                
                if ( $objMail->deleteGroupMember( $parentId, $groupId ) !== false ) {
                    $this->messages[] = 'Client deleted from group';
                } else {
                    $this->messages[] = "An error was encountered during deletion. Report to developer if problem continues.";
                }
                
                $this->render( 'admin-view-group__premium_only', array(
                    'parents' => $objMail->getGroupParents( $groupId ),
                ) );
                break;
            case 'view-group':
                $groupId = intval( $this->_REQUEST['id'] );
                
                if ( !$groupId ) {
                    $this->errors[] = "Group id not provided";
                    $this->render( 'empty' );
                    break;
                }
                
                $this->render( 'admin-view-group__premium_only', array(
                    'parents' => $objMail->getGroupParents( $groupId ),
                ) );
                break;
            default:
                $this->render( $this->page, array(
                    'groups' => $objMail->getGroups(),
                ) );
                break;
        }
    }
    
    public function checkRegistrationDuplicate()
    {
        $field = $this->_REQUEST['field'];
        $value = $this->_REQUEST['value'];
        
        if ( !$field ) {
            echo  json_encode( array(
                'error' => array(
                'message' => 'Field not provided',
            ),
            ) ) ;
            die;
        } elseif ( !$value ) {
            echo  json_encode( array(
                'error' => array(
                'message' => 'Value not provided',
            ),
            ) ) ;
            die;
        }
        
        $objRegistration = new \DancePressTRWA\Models\Registration( $this->sessionCondition );
        $status = false;
        switch ( $field ) {
            case 'name':
                $status = $objRegistration->checkDuplicateName( $value );
                break;
            case 'email':
                $status = $objRegistration->checkDuplicateEmail( $value );
                break;
            case 'additionalemail':
                $status = $objRegistration->checkDuplicateEmail( $value );
                break;
            case 'address':
                $status = $objRegistration->checkDuplicateAddress( $value );
                break;
        }
        echo  json_encode( array(
            'hasduplicate' => (int) $status,
        ) ) ;
        die;
    }

}