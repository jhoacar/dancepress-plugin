<?php

namespace DancePressTRWA\Controllers;

use  Exception as PHPException ;
/**
 * Controls options page
 *
 * @since 1.1911
 */
final class OptionsController extends Controller
{
    public function __construct()
    {
        Parent::__construct();
        $this->setPossibleOptions( array(
            DANCEPRESSTRWA_SESSION_OPTION => 'Default Session',
        ) );
        //place in Session class
        $this->setPossibleUserOptions( array(
            DANCEPRESSTRWA_SESSION_OPTION => 'Current Session',
        ) );
        //place in Session class
        $this->page = ( isset( $this->_REQUEST['page'] ) ? $this->_REQUEST['page'] : false );
        $action = self::getAction();
        
        if ( method_exists( $this, filter_var( $action, FILTER_SANITIZE_STRING ) ) ) {
            $this->{$action}();
        } elseif ( method_exists( $this, filter_var( $action . '__premium_only', FILTER_SANITIZE_STRING ) ) ) {
            $premiumAction = $action . '__premium_only';
            $this->{$premiumAction}();
        } else {
            $this->optionsDefault();
        }
    
    }
    
    private function setPossibleOptions( $po )
    {
        $this->possibleOptions = $po;
    }
    
    private function getPossibleOptions()
    {
        return $this->possibleOptions;
    }
    
    private function setPossibleUserOptions( $po )
    {
        $this->possibleUserOptions = $po;
    }
    
    private function getPossibleUserOptions()
    {
        return $this->possibleUserOptions;
    }
    
    private function optionsDefault()
    {
        $option = new \DancePressTRWA\Models\Option();
        $objSessions = new \DancePressTRWA\Models\Sessions( $this->sessionCondition );
        $this->active_tab = ( isset( $this->_REQUEST['tab'] ) ? $this->_REQUEST['tab'] : false );
        $sessions = $objSessions->getSessions();
        $currentOptions = array();
        $possibleOptions = $this->getPossibleOptions();
        foreach ( $possibleOptions as $ok => $o ) {
            $currentOptions[$ok] = get_option( $ok );
        }
        $currentUserOptions = array();
        $possibleUserOptions = $this->getPossibleUserOptions();
        foreach ( $possibleUserOptions as $ok => $o ) {
            $currentUserOptions[$ok] = get_user_option( $ok );
        }
        $otheroptions = array(
            'ds_allow_public_ticket_sales' => get_option( 'ds_allow_public_ticket_sales' ),
            'ds_withdrawal_policy'         => get_option( 'ds_withdrawal_policy' ),
            'ds_rec_schedule'              => get_option( 'ds_rec_schedule' ),
            'dstrwa_country'               => get_option( 'dstrwa_country' ),
            'dstrwa_province'              => get_option( 'dstrwa_province' ),
            'dstrwa_city'                  => get_option( 'dstrwa_city' ),
            'dstrwa_class_limit'           => get_option( 'dstrwa_class_limit' ),
            'ds_installment_fees'          => get_option( 'ds_installment_fees' ),
            'dstrwa_multi'                 => get_option( 'dstrwa_multi' ),
            'ds_stripe_install_enabled'    => get_option( 'ds_stripe_install_enabled' ),
            'ds_stripe_advance_enabled'    => get_option( 'ds_stripe_advance_enabled' ),
            'ds_cheque_payment_enabled'    => get_option( 'ds_cheque_payment_enabled' ),
            'ds_nofees_enabled'            => get_option( 'ds_nofees_enabled' ),
        );
        $feeoptions = $option->getFeeOptions();
        if ( !empty($this->_REQUEST['ds_installment_fees']) ) {
            $feeoptions['ds_installment_fees'] = $this->_REQUEST['ds_installment_fees'];
        }
        $countries = new \DancePressTRWA\Models\Countries();
        $currencies = new \DancePressTRWA\Models\Currencies();
        $this->render( $this->page, array(
            'countries'      => $countries->getAll(),
            'currencies'     => $currencies->getAll(),
            'current'        => $currentOptions,
            'currentUser'    => $currentUserOptions,
            'possible'       => $possibleOptions,
            'possibleUser'   => $possibleUserOptions,
            'sessions'       => $sessions,
            'currentSession' => parent::$sessName,
            'contactoptions' => $option->getContactOptions(),
            'feeoptions'     => $feeoptions,
            'mailingoptions' => $option->getMailingOptions(),
            'stripeoptions'  => $option->getStripeOptions(),
            'otheroptions'   => $otheroptions,
            'active_tab'     => $this->active_tab,
        ) );
        return;
    }
    
    private function save()
    {
        $options = $this->_REQUEST;
        $possibleOptions = $this->getPossibleOptions();
        foreach ( $possibleOptions as $ok => $o ) {
            if ( array_key_exists( $ok, $possibleOptions ) ) {
                //only allow valid data
                update_option( $ok, $options[$ok] );
            }
        }
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function saveuser()
    {
        $options = $this->_REQUEST;
        $possibleOptions = $this->getPossibleOptions();
        foreach ( $options as $ok => $o ) {
            if ( array_key_exists( $ok, $possibleOptions ) ) {
                update_user_option( get_current_user_id(), $ok, $o );
            }
        }
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function savesession()
    {
        $objSessions = new \DancePressTRWA\Models\Sessions( $this->sessionCondition );
        $newSession = ( isset( $this->_REQUEST['newsession'] ) ? $this->_REQUEST['newsession'] : false );
        $sessionId = $objSessions->addSession( $newSession );
        
        if ( !$this->sessionId ) {
            update_option( 'db_session', $sessionId );
            update_user_option( get_current_user_id(), 'db_session', $sessionId );
        }
        
        //Note wp_redirect will cause problems on servers which don't use output buffering, as headers already sent.
        //wp_redirect(site_url(). '/wp-admin/admin.php?page=admin-options');
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function copyclasses()
    {
        $toSessId = intval( $this->_REQUEST['copytoid'] );
        $fromSessId = intval( $this->_REQUEST['copyfromid'] );
        
        if ( !$toSessId || !$fromSessId ) {
            $this->errors[] = "To and/or from session id not provided";
            $this->setAction( '' );
            $this->__construct();
            return;
        }
        
        $objSessions = new \DancePressTRWA\Models\Sessions( $this->sessionCondition );
        $objSessions->copySessionClasses( $toSessId, $fromSessId );
        $this->messages = "Sessions copied successfully";
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function copyparentsstudents()
    {
        $toSessId = intval( $this->_REQUEST['copytoid'] );
        $fromSessId = intval( $this->_REQUEST['copyfromid'] );
        
        if ( !$toSessId || !$fromSessId ) {
            $this->errors[] = "To and/or from session id not provided";
            $this->setAction( '' );
            $this->__construct();
            return;
        }
        
        $objSessions = new \DancePressTRWA\Models\Sessions( $this->sessionCondition );
        $objSessions->copySessionParentsStudents( $toSessId, $fromSessId );
        $this->messages = "Parents and students copied successfully";
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function updatecontactoptions()
    {
        foreach ( $this->_REQUEST as $key => $value ) {
            update_option( $key, $value );
        }
        $this->messages[] = "Contact Options successfully updated";
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function updatemailingoptions()
    {
        $validOptions = [ 'ds_mailing_signature_image', 'ds_mailing_description' ];
        foreach ( $this->_REQUEST as $key => $value ) {
            if ( in_array( $key, $validOptions ) ) {
                update_option( $key, $value );
            }
        }
        $this->messages[] = "Mailing Options successfully updated";
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function updatestripeoptions()
    {
        $validOptions = [
            'ds_stripe_percent',
            'ds_stripe_secret_key',
            'ds_stripe_publishable_key',
            'ds_stripe_currency'
        ];
        foreach ( $this->_REQUEST as $key => $value ) {
            if ( in_array( $key, $validOptions ) ) {
                update_option( $key, $value );
            }
        }
        $this->messages[] = "Stripe Options successfully updated";
        $this->setAction( '' );
        $this->__construct();
        return;
    }
    
    private function updateotheroptions()
    {
        $validOptions = [
            'dstrwa_province',
            'dstrwa_country',
            'dstrwa_city',
            'dstrwa_class_limit',
            'ds_allow_public_ticket_sales',
            'ds_withdrawal_policy',
            'ds_rec_schedule'
        ];
        foreach ( $this->_REQUEST as $key => $value ) {
            if ( in_array( $key, $validOptions ) ) {
                
                if ( get_option( $key ) !== false ) {
                    update_option( $key, "{$value}" );
                    //quotes preserves 0 as 0 rather than empty string.
                } else {
                    add_option(
                        $key,
                        $value,
                        '',
                        'no'
                    );
                }
            
            }
        }
        //die();
        $this->messages[] = "Other Options successfully updated";
        $this->setAction( '' );
        $this->__construct();
        return;
    }

}