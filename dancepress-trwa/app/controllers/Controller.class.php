<?php

namespace DancePressTRWA\Controllers;

//Stripe
use  Stripe ;
//DancePressTRWA
use  DancePressTRWA\Models\Option ;
//PHP
use  Exception as PHPException ;
/**
 * App controller
 * Other Controllers all extend this controller. Should be 1 controller per page.
 * @package DancePressTRWA\Controllers
 * @since 1.0
 */
class Controller
{
    protected  $db ;
    protected  $errors ;
    protected  $messages ;
    protected  $plugin_url ;
    protected  $json ;
    protected  $_REQUEST ;
    private  $helpers ;
    protected static  $action ;
    protected static  $sessName ;
    protected  $output ;
    private static  $userSessionId ;
    private static  $userSessionCondition ;
    public  $sessionId ;
    public  $sessionCondition ;
    /**
     *
     *	@param boolean $output supress or allow output.
     *
     *	@return void
     */
    public function __construct( $output = true )
    {
        $this->output = $output;
        $this->_REQUEST = stripslashes_deep( $_REQUEST );
        $this->_REQUEST = $this->basicArraySanitize( $this->_REQUEST );
        $this->plugin_url = str_replace( '/app/controllers/', '', plugin_dir_url( __FILE__ ) );
        $this->sessionId = dance_getDefaultSessionId();
        $this->sessionCondition = dance_getDefaultSessionCondition();
        
        if ( is_admin() ) {
            $option = new Option();
            $this->setAdminSessionVars();
        }
    
    }
    
    private function setAdminSessionVars()
    {
        self::$userSessionId = ( isset( self::$userSessionId ) ? self::$userSessionId : dance_getUserSessionId() );
        self::$userSessionCondition = ( isset( self::$userSessionCondition ) ? self::$userSessionCondition : dance_getUserSessionCondition() );
        $this->sessionCondition = ( self::$userSessionCondition ? self::$userSessionCondition : $this->sessionCondition );
        $this->sessionId = ( self::$userSessionId ? self::$userSessionId : $this->sessionId );
        $objSession = new \DancePressTRWA\Models\Sessions();
        self::$sessName = ( isset( self::$sessName ) ? self::$sessName : $objSession->getSessionNameById( $this->sessionId ) );
        
        if ( self::$sessName == null && self::$action != 'savesession' && self::$action != 'save' && self::$action != 'saveuser' ) {
            $this->page = "admin-options";
            self::$action = false;
        }
    
    }
    
    protected function initializePublic()
    {
        wp_register_script( "jquery-validate", $this->plugin_url . '/js/jquery.validate.js', 'jquery' );
        wp_register_script( "jquery-datetimepicker", $this->plugin_url . '/js/jquery.datetimepicker.js', 'jquery' );
        wp_register_style( 'dance.css', $this->plugin_url . '/css/dance.css' );
        wp_register_style( 'datetimepicker.css', $this->plugin_url . '/css/jquery.datetimepicker.css' );
        //DanceSchool Public Scripts
        wp_register_script(
            'dance.js',
            $this->plugin_url . '/js/dance.js',
            'jquery',
            false,
            true
        );
        wp_register_script( "stripe", "https://checkout.stripe.com/checkout.js" );
        wp_enqueue_style( 'dance.css' );
        wp_enqueue_style( 'datetimepicker.css' );
        wp_enqueue_script( "jquery" );
        wp_enqueue_script( "jquery-ui-core", 'jquery' );
        wp_enqueue_script( "jquery-ui-accordion", 'jquery' );
        wp_enqueue_script( "jquery-ui-button", 'jquery' );
        wp_enqueue_script( "jquery-effects-blind", 'jquery-effects-core' );
        wp_enqueue_script( "jquery-validate", 'jquery' );
        wp_enqueue_script( "jquery-datetimepicker", 'jquery' );
        wp_enqueue_script( 'dance.js', array(
            'jquery',
            'jquery-ui-core',
            'jquery-datetimepicker',
            'jquery-validate'
        ) );
        wp_enqueue_script( 'stripe' );
    }
    
    protected function render( $view, $data = array() )
    {
        if ( !$this->output ) {
            return;
        }
        
        if ( is_admin() ) {
            $before = '<div class="wrap">';
            $op = ( self::$sessName ? self::$sessName : '<strong>No Working Session set. Please set a Working Session in <a href="' . site_url() . '/wp-admin/admin.php?page=admin-options">Options</a>.</strong>' );
            $before .= "<p>Current Working Session: " . $op . "</p>";
            $after = '</div>';
        } else {
            $before = '';
            $after = '';
        }
        
        echo  $before ;
        if ( isset( $this->errors ) ) {
            $this->errors();
        }
        if ( isset( $this->messages ) ) {
            $this->messages();
        }
        $this->doRender( $view, $data );
        echo  $after ;
    }
    
    protected static function setAction( $action )
    {
        self::$action = $action;
    }
    
    protected static function getAction()
    {
        return self::$action;
    }
    
    private function errors()
    {
        if ( is_array( $this->errors ) ) {
            $this->doRender( 'error', array(
                'errors' => $this->errors,
            ) );
        }
    }
    
    private function messages()
    {
        if ( is_array( $this->messages ) ) {
            $this->doRender( 'message', array(
                'message' => $this->messages,
            ) );
        }
    }
    
    private function doRender( $view, $data = array() )
    {
        if ( is_array( $data ) ) {
            extract( $data );
        }
        $pathinfo = pathinfo( __FILE__ );
        $viewFile = $pathinfo['dirname'] . '/../../views/' . $view . '.php';
        $viewFilePremium = $pathinfo['dirname'] . '/../../views/' . $view . '__premium_only' . '.php';
        
        if ( $this->json ) {
            echo  json_encode( $data ) ;
            die;
        } elseif ( file_exists( $viewFile ) ) {
            include $viewFile;
        } elseif ( file_exists( $viewFilePremium ) && dancepress_fs()->is_plan__premium_only( 'pro' ) ) {
            include $viewFilePremium;
        } else {
            die( 'Page not found.' . $viewFile . ' ' . $viewFilePremium );
        }
    
    }
    
    /**
     *	doHandleaction - direct traffic in most cludgy and procedural way possible.
     *
     * 	@param array $page to be delivered
     * 	@param string $action to be done
     *	@param array $input received from user
     *	FIXME to be deprecated. Should soon be replaced by appropriate structure/calls to page controllers.
     */
    protected function doHandleaction( $page, $action, $input )
    {
        //action for new class
        //FIXME This makes no sense. Should have simple code to call correct class and method.
        
        if ( $action == 'savenew' ) {
            switch ( $page ) {
                case 'admin-addclass':
                    $installments = ( isset( $input['ds_installment_fees'] ) ? $this->formatInstallmentsFees( $input['ds_installment_fees'] ) : false );
                    $input['ds_installment_fees'] = $installments;
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $objClassManager->addNew( $input );
                    $this->page = 'admin-listclass';
                    $this->startAdmin( $this->page, true );
                    exit;
                    break;
                case 'admin-assignclass':
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $objClassManager->AssignClassToStudents( $input );
                    $this->startAdmin( 'admin-listclassstudents', true );
                    exit;
                    break;
                case 'admin-addclasscategory':
                    $objClassManager = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
                    $objClassManager->addNewClassCategory( $input );
                    $this->page = 'admin-listclasscategories';
                    $this->startAdmin( 'admin-listclasscategories', true );
                    //Preferred way to cause 'redirect'. Redirects break if output buffering is off.
                    exit;
                    break;
                default:
                    break;
            }
        } elseif ( $action == 'save' ) {
            switch ( $page ) {
                case 'admin-editclass':
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $objClassManager->adminUpdateClass( $input );
                    $this->messages[] = "Changes saved successfully.";
                    self::$action = false;
                    $this->startAdmin( 'admin-listclass', true );
                    exit;
                    break;
                case 'admin-editchildclass':
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $objClassManager->adminUpdateClass( $input );
                    $this->messages[] = "Changes saved successfully.";
                    self::$action = false;
                    $this->startAdmin( 'admin-listclass', true );
                    exit;
                    break;
                case 'admin-editclassall':
                    
                    if ( !$input ) {
                        self::$action = false;
                        $this->startAdmin( 'admin-listclass', true );
                        exit;
                        break;
                    }
                    
                    $objClassManager = new \DancePressTRWA\Models\ClassManager( $this->sessionCondition );
                    $objClassManager->adminUpdateAllClasses( $input );
                    $this->messages[] = "Changes saved successfully.";
                    self::$action = false;
                    $this->startAdmin( 'admin-listclass', true );
                    exit;
                    break;
                case 'admin-editclasscategory':
                    $objClassManager = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
                    $objClassManager->adminUpdateClassCategory( $input );
                    $this->messages[] = "Changes saved successfully.";
                    $this->page = 'admin-listclasscategories';
                    self::$action = false;
                    $this->startAdmin( true, true );
                    exit;
                    break;
                default:
                    break;
            }
        } elseif ( $action == 'delete' ) {
            switch ( $page ) {
                case 'admin-listclasscategories':
                    $classCategoryIds = array_filter( $this->_REQUEST['deleteclasscategory'], 'ctype_digit' );
                    
                    if ( !empty($classCategoryIds) ) {
                        $objClassManager = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
                        $objClassManager->deleteClassCategory( $classCategoryIds );
                        $this->messages[] = "Changes saved successfully.";
                    }
                    
                    break;
                default:
                    break;
            }
        }
    
    }
    
    /**
     *
     * @return string $sessionName
     */
    protected function getSessionName()
    {
        $session = new \DancePressTRWA\Models\Sessions( $this->sessionId );
        return $session->getSessionNameById( $this->sessionId );
    }
    
    /**
     * Check site is connected via https (and not a localhost instance). Redirects if not.
     * @return boolean
     */
    protected function checkHTTPS()
    {
        if ( !$this->isLocalHost() ) {
            
            if ( !isset( $_SERVER['HTTPS'] ) ) {
                $redirect = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                header( 'Location: ' . $redirect );
            }
        
        }
    }
    
    /**
     *
     * @return boolean
     */
    protected function isLocalHost()
    {
        if ( preg_match( "/^(127)\\./", $_SERVER['SERVER_ADDR'] ) ) {
            return true;
        }
        return false;
    }
    
    /**
     * Format installment fees
     * @param array $ds_installment_fees
     * @return boolean|array
     */
    private function formatInstallmentsFees( $ds_installment_fees )
    {
        try {
            return \DancePressTRWA\Models\Option::formatSubmittedInstallmentFees( $ds_installment_fees );
        } catch ( PHPException $e ) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Basic array sanitization.
     *
     * Some basic initial input sanitization. Further sanitization occurs in methods appropriate to context.
     * aka there's no such thing as universally appropriate sanitization.
     * @param array $unsafeArray
     * @return $saferArray
     *
     */
    private function basicArraySanitize( $array )
    {
        foreach ( $array as $key => $value ) {
            
            if ( !is_array( $value ) ) {
                $array[$key] = sanitize_text_field( $value );
            } else {
                $array[$key] = $this->basicArraySanitize( $value );
            }
        
        }
        return $array;
    }
    
    protected function getParameter( $name, $default = null )
    {
        if ( !array_key_exists( $name, $this->_REQUEST ) ) {
            return $default;
        }
        return $this->_REQUEST[$name];
    }
    
    /**
     *
     * @param string $error
     */
    protected function addError( $error )
    {
        $this->errors[] = $error;
    }

}