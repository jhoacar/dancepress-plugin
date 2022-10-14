<?php

namespace DancePressTRWA\Controllers;

//PHP
use  Exception as PHPException ;
//Stripe
use  Stripe_Charge ;
use  Stripe_Error ;
use  Stripe_CardError ;
use  Stripe_InvalidRequestError ;
use  Stripe_AuthenticationError ;
use  Stripe_ApiConnectionError ;
//DancePressTRWA
use  DancePressTRWA\Models\Billing ;
use  DancePressTRWA\Models\BillingInstallments ;
use  DancePressTRWA\Models\BillingCustomPayments ;
use  DancePressTRWA\Models\Parents ;
use  DancePressTRWA\Models\Option ;
use  DancePressTRWA\Models\Registration ;
use  DancePressTRWA\Util\Mailman ;
/**
 * Controls options page
 *
 * @since 1.1911
 */
final class ClientsController extends Controller
{
    private  $search ;
    public function __construct()
    {
        Parent::__construct();
        $this->page = ( isset( $this->_REQUEST['page'] ) ? $this->_REQUEST['page'] : false );
        $this->setSearch();
        $action = self::getAction();
        $action = str_replace( "-", "", $action );
        $premiumAction = $action . '__premium_only';
        
        if ( method_exists( $this, filter_var( $action, FILTER_SANITIZE_STRING ) ) ) {
            $this->{$action}();
        } elseif ( method_exists( $this, filter_var( $premiumAction, FILTER_SANITIZE_STRING ) ) ) {
            $this->{$premiumAction}();
        } else {
            
            if ( preg_match( "/^(admin-changedpaidstatus-installment)([\\d]{1,})\$/", $action ) ) {
                $this->changedpaidstatusinstallment();
            } else {
                $this->default();
            }
        
        }
    
    }
    
    public function default()
    {
        $studentId = ( !empty($this->_REQUEST['student_id']) ? intval( $this->_REQUEST['student_id'] ) : 0 );
        
        if ( $studentId ) {
            $parents = $objClassParents->findParentsByStudentId( $studentId );
            $this->render( $this->page, $parents );
        } else {
            $this->render( $this->page, '' );
        }
        
        return;
    }
    
    public function search()
    {
        $offset = 0;
        $limit = 100;
        $search = '';
        if ( isset( $this->_REQUEST['offset'] ) ) {
            $offset = $this->_REQUEST['offset'];
        }
        if ( isset( $this->_REQUEST['limit'] ) ) {
            $limit = $this->_REQUEST['limit'];
        }
        if ( isset( $this->_REQUEST['search'] ) ) {
            $search = $this->_REQUEST['search'];
        }
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objBillingInstallments = new \DancePressTRWA\Models\BillingInstallments( $this->sessionCondition );
        $inactiveOnly = false;
        $activeOnly = true;
        $maxParentInstallments = 0;
        $parents = [];
        
        if ( isset( $this->_REQUEST['searchdeactivated'] ) ) {
            $inactiveOnly = true;
            $activeOnly = false;
        }
        
        $parents = $objClassParents->findParentsCommunity(
            $search,
            $activeOnly,
            $inactiveOnly,
            $offset,
            $limit
        );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
        return;
    }
    
    public function create()
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
    
    public function edit()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $parentId = intval( $this->_REQUEST['parent_id'] );
        $isConfirmed = ( isset( $this->_REQUEST['show_unconfirmed'] ) ? 0 : 1 );
        $billing = null;
        
        if ( $isConfirmed ) {
            $parents = $objClassParents->getParentByIdCommunity( $parentId );
        } else {
            $parents = $objClassParents->getUnconfirmedParentByIdCommunity( $parentId );
        }
        
        foreach ( $parents as &$parent ) {
            $addressData = ( $parent->address_data ? (array) json_decode( $parent->address_data ) : array() );
            //more hack to extract address
            $metaData = ( $parent->meta ? (array) json_decode( $parent->meta ) : array() );
            $parent = (object) array_merge( (array) $parent, $addressData );
            $parent->meta = $metaData;
        }
        $children = $objClassParents->getChildrenByParentId( $parentId, $isConfirmed );
        $this->render( 'admin-editparent', array(
            'parents'  => $parents,
            'children' => $children,
            'billing'  => $billing,
        ) );
    }
    
    public function updateParent()
    {
        $objParents = new Parents();
        $objBilling = new Billing();
        $parentId = intval( $this->_REQUEST['parent_id'] );
        $email = $this->_REQUEST['email'];
        
        if ( !is_email( $email ) ) {
            $this->errors[] = "New email/username is not a valid email address. Please correct.";
        } else {
            if ( $email != $this->_REQUEST['emailold'] ) {
                
                if ( !$objParents->updateWordpressUsername( $email, $this->_REQUEST['emailold'] ) ) {
                    $this->errors[] = $objParents->getError();
                } else {
                    $this->messages[] = "Wordpress Username updated;";
                }
            
            }
            $firstname = $this->_REQUEST['firstname'];
            $lastname = $this->_REQUEST['lastname'];
            $address1 = $this->_REQUEST['address1'];
            $address2 = $this->_REQUEST['address2'];
            $city = $this->_REQUEST['city'];
            $postal_code = $this->_REQUEST['postal_code'];
            $phone_primary = $this->_REQUEST['phone_primary'];
            $phone_secondary = $this->_REQUEST['phone_secondary'];
            $stripeId = ( isset( $this->_REQUEST['stripe_id'] ) ? $this->_REQUEST['stripe_id'] : false );
            $registrationFee = ( isset( $this->_REQUEST['registration_fee'] ) ? $this->_REQUEST['registration_fee'] : false );
            $billingInstallments = ( isset( $this->_REQUEST['billing_installment'] ) ? $this->_REQUEST['billing_installment'] : false );
            $billingCustomPaymentAmounts = $this->getParameter( 'billing_custom_payment' );
            $paymentMethod = ( isset( $this->_REQUEST['payment_method'] ) ? $this->_REQUEST['payment_method'] : false );
            //
            $meta = ( !empty($this->_REQUEST['meta']) ? $this->_REQUEST['meta'] : array() );
            foreach ( $meta as $k => $v ) {
                if ( !$v ) {
                    unset( $meta[$k] );
                }
            }
            $custom_meta_key = $this->_REQUEST['custom_meta_key'];
            $custom_meta_value = $this->_REQUEST['custom_meta_value'];
            if ( !$firstname ) {
                $this->errors[] = "First name not provided";
            }
            if ( !$lastname ) {
                $this->errors[] = "Last name not provided";
            }
            if ( !$firstname ) {
                $this->errors[] = "First name not provided";
            }
            if ( !$address1 ) {
                $this->errors[] = "Address line 1 not provided";
            }
            if ( !$city ) {
                $this->errors[] = "City not provided";
            }
            if ( !$phone_primary ) {
                $this->errors[] = "Primary phone not provided";
            }
            
            if ( $this->errors ) {
                $this->render( 'empty' );
                return;
            }
            
            $data = array(
                'firstname'           => $firstname,
                'lastname'            => $lastname,
                'email'               => $email,
                'parent_id'           => $parentId,
                'address1'            => $address1,
                'address2'            => $address2,
                'city'                => $city,
                'postal_code'         => $postal_code,
                'phone_primary'       => $phone_primary,
                'phone_secondary'     => $phone_secondary,
                'meta'                => $meta,
                'custom_meta_key'     => $custom_meta_key,
                'custom_meta_value'   => $custom_meta_value,
                'stripe_id'           => $stripeId,
                'registration_fee'    => $registrationFee,
                'billing_installment' => $billingInstallments,
                'payment_method'      => $paymentMethod,
            );
            if ( $objParents->updateParentNew( $data ) ) {
                $this->messages[] = "Parent details updated";
            }
            if ( isset( $this->_REQUEST['deleted_billing_custom_payments'] ) ) {
                foreach ( $this->_REQUEST['deleted_billing_custom_payments'] as $billingCustomPaymentId ) {
                    $objBillingCustomPayments->delete( $billingCustomPaymentId );
                }
            }
            if ( $billingCustomPaymentAmounts ) {
                foreach ( $billingCustomPaymentAmounts as $billingCustomPaymentId => $amount ) {
                    if ( !$amount || $amount == 0.0 ) {
                        continue;
                    }
                    $billingCustomPayment = $objBillingCustomPayments->findById( $billingCustomPaymentId );
                    if ( $billingCustomPayment ) {
                        $objBillingCustomPayments->update( $billingCustomPayment->id, array(
                            "amount" => $amount,
                        ) );
                    }
                }
            }
            if ( !empty($this->_REQUEST['auto_update']) ) {
                $objBilling->updateBillingAgreementByParent( $parentId );
            }
        }
        
        $parents = $objParents->getParentByIdCommunity( $parentId );
        $billing = $objBilling->findById( $parents[0]->billing_id );
        foreach ( $parents as &$parent ) {
            $addressData = ( $parent->address_data ? (array) json_decode( $parent->address_data ) : array() );
            //more hack to extract address
            $metaData = ( $parent->meta ? (array) json_decode( $parent->meta ) : array() );
            $parent = (object) array_merge( (array) $parent, $addressData );
            $parent->meta = $metaData;
        }
        $children = $objParents->getChildrenByParentId( $parentId );
        $this->render( 'admin-editparent', array(
            'billing'  => $billing,
            'parents'  => $parents,
            'children' => $children,
        ) );
    }
    
    public function validate()
    {
        $objParents = new Parents();
        $objBilling = new Billing();
        $objBilling = new Billing();
        $objBillingInstallments = new BillingInstallments();
        $objRegistration = new Registration();
        $parentId = ( isset( $this->_REQUEST['id'] ) ? intval( $this->_REQUEST['id'] ) : false );
        $parentIds = ( isset( $this->_REQUEST['ids'] ) ? array_filter( $this->_REQUEST['ids'], 'ctype_digit' ) : false );
        
        if ( isset( $parentId ) && $parentId ) {
            
            if ( $objBilling->validateParentAccount( $parentId ) ) {
                $objParent = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
                $this->parent = $objParent->getParentByIdCommunity( $parentId );
                $parents = $objParent->findParents( $this->search, true );
                $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $parents );
                
                if ( isset( $this->_REQUEST['redirect'] ) ) {
                    wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-manageparents&action=paymentspending' );
                    return;
                }
                
                $this->render( $this->page, array(
                    'parents'               => $parents,
                    'search'                => $this->search,
                    'maxParentInstallments' => $maxParentInstallments,
                ) );
            } else {
                $this->errors[] = "Error encountered. Account not modified.";
            }
        
        } elseif ( isset( $parentIds ) ) {
            $this->render( 'admin-validation-done', count( $this->_REQUEST['ids'] ) );
        }
        
        return;
    }
    
    public function validateandclasslist()
    {
        $objParents = new Parents();
        $objBilling = new Billing();
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        foreach ( $parentIds as $id ) {
            
            if ( $objBilling->validateParentAccount( $id ) ) {
                $this->parent = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
                $this->parent = $this->parent->getParentByIdCommunity( $id );
                $objRegistration->makeParentUser( $this->parent[0] );
                $this->adminSendBillingUpdate__premium_only( $id );
            } else {
                $this->errors[] = "Error encountered. Account not modified.";
            }
        
        }
        $this->render( 'admin-validation-done', count( $parentIds ) );
    }
    
    public function classlistonly()
    {
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        foreach ( $parentIds as $id ) {
            $this->adminSendBillingUpdate__premium_only( $id, true );
        }
        $this->render( 'admin-validation-done', count( $parentIds ) );
    }
    
    public function classlistonlycomp()
    {
        $parentIds = array();
        if ( isset( $this->_REQUEST['ids'] ) ) {
            $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        }
        foreach ( $parentIds as $id ) {
            $this->adminSendBillingUpdate__premium_only( $id, true, true );
        }
        $this->render( 'admin-validation-done', count( $parentIds ) );
    }
    
    public function billingandclasslist()
    {
        if ( !isset( $this->_REQUEST['ids'] ) ) {
            throw new PHPException( "Parent ids not provided" );
        }
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        foreach ( $parentIds as $id ) {
            $this->adminSendBillingUpdate__premium_only( $id );
        }
        $this->render( 'admin-validation-done', count( $parentIds ) );
    }
    
    public function custom_email()
    {
        $ids = array(
            'ids' => $this->_REQUEST['ids'],
        );
        header( "Location: /wp-admin/admin.php?page=admin-sendemail&action=emailids&" . http_build_query( $ids ) );
    }
    
    public function create_list()
    {
        $ids = array(
            'ids' => $this->_REQUEST['ids'],
        );
        header( "Location: /wp-admin/admin.php?page=admin-managegroups&action=create_list&" . http_build_query( $ids ) );
    }
    
    public function modify_list()
    {
        $ids = array(
            'ids' => $this->_REQUEST['ids'],
        );
        header( "Location: /wp-admin/admin.php?page=admin-managegroups&action=modify_list&" . http_build_query( $ids ) );
    }
    
    public function process_payments_due()
    {
        $objBilling = new Billing();
        if ( !isset( $this->_REQUEST['ids'] ) ) {
            throw new PHPException( "Parent ids not provided" );
        }
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        $ids = array(
            'ids' => $parentIds,
        );
        $scheduledPayments = $objBilling->getNextScheduledPayments( $parentIds );
        $this->render( 'admin-scheduled-getconfirm__premium_only', array(
            'scheduled' => $scheduledPayments,
            'search'    => $this->_REQUEST['search'],
        ) );
    }
    
    public function do_scheduled_payments()
    {
        $objBilling = new Billing();
        $parentIds = array();
        
        if ( isset( $this->_REQUEST['ids'] ) ) {
            $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
            $result = $objBilling->doScheduledStripePayments( $parentIds );
            if ( isset( $objBilling->errors ) ) {
                $this->render( 'admin-scheduled-errors__premium_only', array(
                    'errors' => $objBilling->errors,
                ) );
            }
            foreach ( $result as $p ) {
                if ( isset( $p->payment_declined ) && $p->payment_declined ) {
                    continue;
                }
                if ( $p->nextPaymentAmount == 0 ) {
                    continue;
                }
                ob_start();
                $this->render( 'admin-charge-receipt__premium_only', array(
                    'p' => $p,
                ) );
                $message = ob_get_contents();
                ob_clean();
                //We don't want this output - just emailed
                $option = new \DancePressTRWA\Models\Option();
                $contactEmail = $option->getContactEmail();
                $blogName = get_bloginfo( 'name' );
                $toEmail = $p->email;
                $headers = [];
                $headers[] = "Content-type: text/html";
                $headers[] = "From: {$blogName} <{$contactEmail}>";
                if ( !wp_mail(
                    $toEmail,
                    "Notice of {$blogName} Fee Payment",
                    $message,
                    $headers
                ) ) {
                    echo  "Possible Error reported sending mail to {$toEmail}. Please report to developer.<br/>" ;
                }
            }
        }
        
        $this->render( 'admin-scheduled-done__premium_only' );
    }
    
    //Seems possible misplaced.
    public function addchild()
    {
        $objClassParents = new Parents();
        $objBilling = new Billing();
        $parentId = intval( $this->_REQUEST['parent_id'] );
        $parents = $objClassParents->getParentByIdCommunity( $parentId );
        $parent = $parents[0];
        
        if ( isset( $this->_REQUEST['firstname'] ) ) {
            $firstname = $this->_REQUEST['firstname'];
            $lastname = $this->_REQUEST['lastname'];
            $birthdate = $this->_REQUEST['birthdate'];
            $gender = $this->_REQUEST['gender'];
            //
            $meta = ( !empty($this->_REQUEST['meta']) ? $this->_REQUEST['meta'] : array() );
            $custom_meta_key = $this->_REQUEST['custom_meta_key'];
            $custom_meta_value = $this->_REQUEST['custom_meta_value'];
            if ( !$lastname ) {
                $this->errors[] = "Last name not provided";
            }
            if ( !$birthdate ) {
                $this->errors[] = "Birthdate not provided";
            }
            if ( !$gender ) {
                $this->errors[] = "Gender not provided";
            }
            
            if ( $this->errors ) {
                $this->render( 'empty' );
                return;
            }
            
            $data = array(
                'firstname'         => $firstname,
                'lastname'          => $lastname,
                'birthdate'         => $birthdate,
                'parent_id'         => $parentId,
                'gender'            => $gender,
                'meta'              => $meta,
                'custom_meta_key'   => $custom_meta_key,
                'custom_meta_value' => $custom_meta_value,
            );
            if ( !$objClassParents->addChild( $data, 1, 1 ) ) {
            }
            //Redirect to edit parent page
            $this->_REQUEST['page'] = 'admin-manageparents';
            $this->_REQUEST['parent_id'] = $parentId;
            $this->_REQUEST['action'] = 'edit';
            $this->startAdmin();
        } else {
            $this->render( 'admin-addchild', array(
                'parent' => $parent,
            ) );
        }
    
    }
    
    public function delete()
    {
        $objClassParents = new Parents();
        $objBillingInstallments = new BillingInstallments();
        $parentId = intval( $this->_REQUEST['id'] );
        $this->messages[] = $objClassParents->deleteParentFull( $parentId );
        $parents = $objClassParents->findParents( $this->search, true );
        $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $parents );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $this->search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    public function deactivate()
    {
        $objClassParents = new Parents();
        $objBillingInstallments = new BillingInstallments();
        $parentId = intval( $this->_REQUEST['id'] );
        $maxParentInstallments = false;
        $this->messages[] = $objClassParents->deactivateParent( $parentId );
        $parents = $objClassParents->findParents( $this->search, true );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $this->search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    public function activate()
    {
        $objClassParents = new Parents();
        $objBillingInstallments = new BillingInstallments();
        $parentId = intval( $this->_REQUEST['id'] );
        $maxParentInstallments = false;
        $this->messages[] = $objClassParents->activateParent( $parentId );
        $parents = $objClassParents->findParents( $this->search, true );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $this->search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    public function deleteParent()
    {
        $objClassParents = new Parents();
        //delete parent from incomplete registrations list
        $parentId = intval( $this->_REQUEST['parent_id'] );
        
        if ( !$parentId ) {
            $this->errors[] = "Parent id not provided";
        } else {
            $objClassParents->deleteParent( $parentId );
            wp_redirect( site_url() . '/wp-admin/admin.php?page=admin-manageparents&action=incompleteregistrations' );
        }
    
    }
    
    public function paymentspending()
    {
        $objBilling = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $pending = $objBilling->getPendingPayments();
        $this->render( 'admin-pendingpayments__premium_only', $pending );
    }
    
    public function incompleteregistrations()
    {
        $incompleteRegistrations = $objClassParents->getIncompleteRegistrations();
        $this->render( 'admin-incompleteregistrations', $incompleteRegistrations );
    }
    
    public function remind()
    {
        $parentId = intval( $this->_REQUEST['parent_id'] );
        $billingId = intval( $this->_REQUEST['billing_id'] );
        if ( !$parentId ) {
            $this->errors[] = "Parent id not provided";
        }
        if ( !$billingId ) {
            $this->errors[] = "Billing id not provided";
        }
        
        if ( $this->errors ) {
            $this->render( 'empty' );
            return;
        }
        
        $objBilling = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $parent = $objClassParents->getParentByIdCommunity( $parentId );
        $this->messages[] = $objBilling->sendReminder( $parent[0], $billingId );
        $pending = $objBilling->getPendingPayments();
        $this->render( 'admin-pendingpayments__premium_only', $pending );
    }
    
    public function deleteBilling()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objBilling = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $parentId = intval( $this->_REQUEST['parent_id'] );
        
        if ( !$parentId ) {
            $this->errors[] = "Parent id not provided";
            $this->render( 'empty' );
            return;
        }
        
        $parents = $objClassParents->getParentByIdCommunity( $parentId );
        foreach ( $parents as &$parent ) {
            $addressData = ( $parent->address_data ? (array) json_decode( $parent->address_data ) : array() );
            $parent = (object) array_merge( (array) $parent, $addressData );
        }
        $children = $objClassParents->getChildrenByParentId( $parentId );
        $this->render( 'admin-editparent', array(
            'parents'  => $parents,
            'children' => $children,
        ) );
    }
    
    public function deactivatedusers()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objBilling = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $maxParentInstallments = false;
        $parents = $objClassParents->findParents( $this->search, false, true );
        if ( $parents ) {
            $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $parents );
        }
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $this->search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    public function registrationbydate()
    {
        $objClassStudents = new \DancePressTRWA\Models\ClassStudents( $this->sessionCondition );
        $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
        $objRegistration = new \DancePressTRWA\Models\Registration( $this->sessionCondition );
        $currentSelectedStartYear = ( isset( $this->_REQUEST['startyear'] ) ? $this->_REQUEST['startyear'] : date( 'Y', time() ) - 1 );
        $currentSelectedStartMonth = ( isset( $this->_REQUEST['startmonth'] ) ? $this->_REQUEST['startmonth'] : date( 'm', time() ) );
        $currentSelectedStartDay = ( isset( $this->_REQUEST['startday'] ) ? $this->_REQUEST['startday'] : date( 'd', time() ) );
        $currentSelectedEndYear = ( isset( $this->_REQUEST['endyear'] ) ? $this->_REQUEST['endyear'] : date( 'Y', time() ) );
        $currentSelectedEndMonth = ( isset( $this->_REQUEST['endmonth'] ) ? $this->_REQUEST['endmonth'] : date( 'm', time() ) );
        $currentSelectedEndDay = ( isset( $this->_REQUEST['endday'] ) ? $this->_REQUEST['endday'] : date( 'd', time() ) );
        $rangeStart = mktime(
            0,
            0,
            0,
            $currentSelectedStartMonth,
            $currentSelectedStartDay,
            $currentSelectedStartYear
        );
        $rangeEnd = mktime(
            23,
            59,
            59,
            $currentSelectedEndMonth,
            $currentSelectedEndDay,
            $currentSelectedEndYear
        );
        $students = array();
        $students['all'] = $objRegistration->getRegistrationsByDateRange( $rangeStart, $rangeEnd );
        $students['competitive'] = $objRegistration->getCompetitiveRegistrationsByDateRange( $rangeStart, $rangeEnd );
        $students['recreational'] = $objRegistration->getRecRegistrationsByDateRange( $rangeStart, $rangeEnd );
        $payments = $objRegistration->getPaymentTypesByDateRange( $rangeStart, $rangeEnd );
        $drops = $objClassStudents->getStudentHistory( 'DROP', $rangeStart, $rangeEnd );
        $dropClassCounts = $objClassStudents->getHistoryClassCounts( $drops );
        $droppedClassDetails = $objClassManager->getClassStudentDataByIds( $drops );
        foreach ( $droppedClassDetails as &$c ) {
            $c->droppedCount = $dropClassCounts[$c->id];
        }
        //print_r($droppedClassDetails);
        $this->render( 'admin-registrationsbydate', array(
            'currentSelectedStartYear'  => $currentSelectedStartYear,
            'currentSelectedStartMonth' => $currentSelectedStartMonth,
            'currentSelectedStartDay'   => $currentSelectedStartDay,
            'currentSelectedEndYear'    => $currentSelectedEndYear,
            'currentSelectedEndMonth'   => $currentSelectedEndMonth,
            'currentSelectedEndDay'     => $currentSelectedEndDay,
            'students'                  => $students,
            'payments'                  => $payments,
            'droppedClassDetails'       => $droppedClassDetails,
        ) );
    }
    
    public function changepaidstatus()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $billing = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $billingId = ( !empty($this->_REQUEST['billingid']) ? (int) $this->_REQUEST['billingid'] : 0 );
        $registrationFee = ( !empty($this->_REQUEST['registrationfee']) ? $this->_REQUEST['registrationfee'] : 0 );
        $billingInstallmentId = ( !empty($this->_REQUEST['billinginstallmentid']) ? $this->_REQUEST['billinginstallmentid'] : 0 );
        $status = ( !empty($this->_REQUEST['status']) ? $this->_REQUEST['status'] : 0 );
        if ( !$billingId ) {
            $this->errors[] = "Billing id not provided";
        }
        if ( !isset( $registrationFee ) ) {
            $this->errors[] = "Registration fee not provided";
        }
        if ( !$billingInstallmentId ) {
            $this->errors[] = "Billing installment id not provided";
        }
        if ( !isset( $status ) ) {
            $this->errors[] = "Status not provided";
        }
        
        if ( $this->errors ) {
            $this->render( 'empty' );
            return;
        }
        
        
        if ( $registrationFee ) {
            $billing->changeRegistrationFeePaidStatus( $billingId, $status );
        } elseif ( $billingInstallmentId ) {
            $billing->changeInstallmentPaidStatus( $billingInstallmentId, $status );
        }
        
        if ( isset( $this->_REQUEST['redirect'] ) ) {
            header( 'Location: /wp-admin/admin.php?page=admin-manageparents&action=paymentspending' );
        }
        $parents = $objClassParents->findParents( $this->search, false );
        $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $parents );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    public function changedpaidstatusinstallment()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $billing = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $parentIds = array();
        if ( !isset( $this->_REQUEST['ids'] ) ) {
            throw new PHPException( "Parent ids not provided" );
        }
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        $inputdata = array();
        $inputdata['parentIds'] = $parentIds;
        $inputdata['originalAction'] = substr( $this->_REQUEST['action'], 0, -5 );
        //remove "-menu" suffix
        $this->render( 'admin-changedpaidstatus-selectbulkstatus__premium_only', $inputdata );
    }
    
    public function adminchangedpaidstatusinstallment()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $billing = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $parentIds = array();
        if ( !isset( $this->_REQUEST['ids'] ) ) {
            throw new PHPException( "Parent ids not provided" );
        }
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        $inputdata = array();
        $inputdata['parentIds'] = $parentIds;
        $inputdata['originalAction'] = substr( $this->_REQUEST['action'], 0, -5 );
        //remove "-menu" suffix
        $this->render( 'admin-changedpaidstatus-selectbulkstatus__premium_only', $inputdata );
    }
    
    public function adminbillingreceiptselectpayment()
    {
        $objBilling = new \DancePressTRWA\Models\Billing();
        $objBillingInstallments = new \DancePressTRWA\Models\BillingInstallments();
        $objClassParents = new \DancePressTRWA\Models\Parents();
        $parentIds = array();
        if ( !isset( $this->_REQUEST['ids'] ) ) {
            throw new PHPException( "Parent ids not provided" );
        }
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $objClassParents->getParents( $parentIds ) );
        $installmentOptions = array();
        $registrationFeeEnabled = false;
        $parents = array();
        for ( $i = 0 ;  $i < $maxParentInstallments ;  $i++ ) {
            $installmentOptions[$i] = false;
        }
        //If at least one parent has paid this installent then show it, else continue
        foreach ( $parentIds as $parentId ) {
            $parent = $objClassParents->findById( $parentId );
            $billing = $objBilling->getBillingHistoryByParentId( $parent->id );
            $parent->billing = $billing;
            $parents[] = $parent;
            //At least one client has paid the registration fee
            if ( $billing->registration_fee_paid ) {
                $registrationFeeEnabled = true;
            }
            if ( $billing->installments ) {
                foreach ( $billing->installments as $index => $installment ) {
                    
                    if ( $installment->paid ) {
                        $maxParentInstallments[$index] = true;
                        break;
                    }
                
                }
            }
        }
        $this->render( 'admin-billingreceipt-selectpayment__premium_only', [
            'parentIds'              => $parentIds,
            'parents'                => $parents,
            'maxParentInstallments'  => $maxParentInstallments,
            'installmentOptions'     => $installmentOptions,
            'registrationFeeEnabled' => $registrationFeeEnabled,
        ] );
    }
    
    public function adminchangedpaidstatusregistrationfee()
    {
        $changeRegistrationFeedPaidStatus = false;
        
        if ( $this->_REQUEST['action'] == 'admin-changedpaidstatus-registrationfee' ) {
            $changeRegistrationFeedPaidStatus = true;
        } else {
            $installmentNumber = str_replace( "admin-changedpaidstatus-installment", '', $this->_REQUEST['action'] );
            $installmentNumber = (int) $installmentNumber;
            $installmentNumber = $installmentNumber - 1;
            //Minus 1 to get index in set of installments
            if ( $installmentNumber < 0 ) {
                throw new PHPException( "Invalid installment number '{$installmentNumber}'" );
            }
        }
        
        $parentIds = array_filter( $this->_REQUEST['parentids'], 'ctype_digit' );
        $status = $this->_REQUEST['paymentstatus'][0];
        if ( !$parentIds ) {
            throw new PHPException( "Parent ids not provided" );
        }
        if ( !isset( $status ) ) {
            throw new PHPException( "Payment status not provided" );
        }
        $parents = $objClassParents->findParents( $this->search, false );
        foreach ( $parents as $parent ) {
            if ( in_array( $parent->id, $parentIds ) ) {
                
                if ( $changeRegistrationFeedPaidStatus ) {
                    $objBilling->changeRegistrationFeePaidStatus( $parent->billing_id, $status );
                } else {
                    //Find installment by index
                    $installments = $objBilling->getInstallments( $parent->billing_id );
                    
                    if ( array_key_exists( $installmentNumber, $installments ) ) {
                        $installment = $installments[$installmentNumber];
                        $objBilling->changeInstallmentPaidStatus( $installment->id, $status );
                    }
                
                }
            
            }
        }
        //use redirect instead to provide for similar data ordering from when the user first init bulk action
        wp_redirect( 'admin.php?page=admin-manageparents&action=search', 301 );
        exit;
    }
    
    public function adminbillingreceipt()
    {
        $objClassStudents = new \DancePressTRWA\Models\ClassStudents( $this->sessionCondition );
        wp_enqueue_style( 'print-style' );
        $objBilling = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $objBillingInstallments = new \DancePressTRWA\Models\BillingInstallments();
        $i = 0;
        //# receipts displayed
        $paymentNumbers = $this->getParameter( 'payment_number' );
        $selectedCustomPayments = $this->getParameter( 'custom_payments' );
        if ( !isset( $paymentNumbers ) && !isset( $selectedCustomPayments ) ) {
            throw new PHPException( "Payment numbers nor custom payments not provided" );
        }
        
        if ( isset( $this->_REQUEST['parentids'] ) ) {
            $parentIds = array_filter( $this->_REQUEST['parentids'], 'ctype_digit' );
            foreach ( $parentIds as $parentId ) {
                //get billingId from parentId
                $billingHistory = $objBilling->getBillingHistoryByParentId( $parentId );
                
                if ( $billingHistory ) {
                    $billingId = (int) $billingHistory->billing_id;
                    $billingItem = $objBilling->getTransactionWithParentById( $billingId, 1 );
                    
                    if ( $billingItem ) {
                        $billingItem->arrAddressData = json_decode( $billingItem->address_data );
                        $billingItem->arrBillingDetails = json_decode( $billingItem->billing_details );
                        $billingItem->students = $objClassStudents->getStudentsByParentId( $parentId );
                        if ( is_array( $paymentNumbers ) ) {
                            foreach ( $paymentNumbers as $paymentNumber ) {
                                $billingItem->currentPaymentTotal = null;
                                $billingItem->paymentName = null;
                                $billingItem->receiptDate = null;
                                
                                if ( $paymentNumber == 'registration' ) {
                                    $billingItem->currentPaymentTotal = $billingItem->registration_fee;
                                    $billingItem->paymentName = "Registration Fee";
                                    $billingItem->receiptDate = $billingItem->registration_fee_date;
                                } else {
                                    //Find installment by ordinal number
                                    $installments = $objBillingInstallments->getInstallments( $billingId );
                                    $installment = null;
                                    if ( array_key_exists( $paymentNumber, $installments ) ) {
                                        $installment = $installments[$paymentNumber];
                                    }
                                    
                                    if ( $installment && $installment->datetime_paid ) {
                                        $billingItem->currentPaymentTotal = $installment->amount;
                                        $billingItem->paymentName = $installment->payment_name;
                                        $billingItem->receiptDate = $installment->datetime_paid;
                                    }
                                
                                }
                                
                                
                                if ( isset( $billingItem->currentPaymentTotal ) ) {
                                    //Current parent being iterated over has the installment or registration payment
                                    $billingItem->currentPaymentTotal = number_format( $billingItem->currentPaymentTotal, 2 );
                                    if ( $i++ > 0 ) {
                                        $billingItem->hackNoPrint = true;
                                    }
                                    $this->render( 'admin-billingreceipt__premium_only', $billingItem );
                                }
                            
                            }
                        }
                        //End if
                        if ( $selectedCustomPayments && isset( $selectedCustomPayments[$parentId] ) ) {
                            foreach ( $selectedCustomPayments[$parentId] as $selectedParentCustomPaymentId ) {
                                $billingCustomPayment = $objBillingCustomPayments->findById( $selectedParentCustomPaymentId );
                                if ( !$billingCustomPayment->datetime_paid ) {
                                    continue;
                                }
                                $billingHistory = $objBilling->getBillingHistoryByParentId( $parentId );
                                $parent = $objParents->findById( $parentId );
                                $i++;
                                $this->render( 'admin-billing-custom-payment-receipt__premium_only', array(
                                    'parent'               => $parent,
                                    'billingCustomPayment' => $billingCustomPayment,
                                    'billingAddress'       => $billingItem->arrAddressData,
                                    'printButton'          => ( $i > 0 ? false : true ),
                                ) );
                            }
                        }
                    }
                
                }
            
            }
        } else {
            $billingId = intval( $this->_REQUEST['bid'] );
            $installmentId = ( isset( $this->_REQUEST['billinginstallmentid'] ) ? $this->_REQUEST['billinginstallmentid'] : null );
            $registrationPayment = ( isset( $this->_REQUEST['registration'] ) ? true : false );
            
            if ( !$billingId ) {
                throw new PHPException( "Billing id not provided" );
            } elseif ( !$installmentId && !$registrationPayment ) {
                throw new PHPException( "Installment id must be provided if not veiwing registration payment receipt" );
            }
            
            $billingItem = $objBilling->getTransactionWithParentById( $billingId, 1 );
            $billingItem->arrAddressData = json_decode( $billingItem->address_data );
            $billingItem->arrBillingDetails = json_decode( $billingItem->billing_details );
            $billingItem->students = $objClassStudents->getStudentsByParentId( $billingItem->parent_id );
            $billingItem->currentPaymentTotal = false;
            
            if ( $registrationPayment ) {
                $billingItem->currentPaymentTotal = $billingItem->registration_fee;
                $billingItem->paymentName = "Registration Fee";
                $billingItem->receiptDate = $billingItem->registration_fee_date;
            } else {
                //Get billing installment by id
                $billingInstallments = new \DancePressTRWA\Models\BillingInstallments();
                $installment = $billingInstallments->findById( $installmentId );
                if ( !$installment ) {
                    throw new PHPException( "Could not find installment with id '{$installmentId}'" );
                }
                $billingItem->currentPaymentTotal = $installment->amount;
                $billingItem->paymentName = $installment->payment_name;
                $billingItem->receiptDate = $installment->datetime_paid;
            }
            
            $billingItem->currentPaymentTotal = number_format( $billingItem->currentPaymentTotal, 2 );
            $this->render( 'admin-billingreceipt', $billingItem );
        }
    
    }
    
    public function change_schedule_availability_menu()
    {
        $this->render( 'admin-change-schedule-availability-selectoption', array(
            'ids' => $this->_REQUEST['ids'],
        ) );
    }
    
    public function change_schedule_availability()
    {
        $objectClassStudents = new \DancePressTRWA\Models\ClassStudents( $this->sessionCondition );
        $parentIds = array_filter( $this->_REQUEST['parentids'], 'ctype_digit' );
        $scheduleAvailable = intval( $this->_REQUEST['schedule_available'] );
        $parents = $objClassParents->getParents( $parentIds );
        foreach ( $parents as $parent ) {
            $students = $objectClassStudents->getStudentsByParentId( $parent->id );
            foreach ( $students as $student ) {
                if ( $student->is_company_student ) {
                    $objectClassStudents->updateScheduleAvailability( $student->id, $scheduleAvailable );
                }
            }
        }
        $this->messages[] = "Schedule availablity updated";
        $this->render( 'admin-manageparents', array() );
    }
    
    public function adminsyncwpusers()
    {
        
        if ( !isset( $this->_REQUEST['ids'] ) || empty($this->_REQUEST['ids']) ) {
            $this->errors[] = "You did not select any clients. Please try again.";
            $this->render( $this->page );
            return;
        }
        
        $objectParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $objectRegistration = new \DancePressTRWA\Models\Registration( $this->sessionCondition );
        $parents = $objectParents->getParents( $this->_REQUEST['ids'] );
        foreach ( $parents as $pk => $pv ) {
            $objectRegistration->makeParentUser( $pv, false );
        }
        $this->messages[] = count( $parents ) . " selected DancePress Clients synced to Wordpress Users";
        $this->render( $this->page );
        return;
    }
    
    public function getSearch()
    {
        return $this->search;
    }
    
    private function deactivateaccount()
    {
        $objClassParents = new \DancePressTRWA\Models\Parents( $this->sessionCondition );
        $billing = new \DancePressTRWA\Models\Billing( $this->sessionCondition );
        $parentIds = array_filter( $this->_REQUEST['ids'], 'ctype_digit' );
        $this->messages[] = $objClassParents->deactivateParents( $parentIds );
        $parents = $objClassParents->findParents( $this->search, true );
        $maxParentInstallments = $objBillingInstallments->getMaxParentBillingInstallments( $parents );
        $this->render( $this->page, array(
            'parents'               => $parents,
            'search'                => $this->search,
            'maxParentInstallments' => $maxParentInstallments,
        ) );
    }
    
    private function setSearch()
    {
        $this->search = ( isset( $this->_REQUEST['search'] ) ? $this->_REQUEST['search'] : false );
    }

}