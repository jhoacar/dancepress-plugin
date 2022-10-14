<?php

/*
 *	Plugin Name: DancePress (trwa)
 *	Plugin URI: http://dancepress.ca/
 *	Description: DancePress offers a complete solution for dance schools looking for an attractive WordPress website integrated with a powerful Dance School Management Application.
 *	Author: TannerRitchie Web Applications/DancePress
 *   Author URI: https://dancepress.ca
 *	Version: 2.4.1
 * 	Text domain: dancepress-trwa
 *	Copyright: Copyright TannerRitchie Publishing
 *	License: GPL 2 or later
 *
 */
/**
 *	@author TannerRitchie Web Applications/DancePress
 *	@version 2.2.0
 *	@copyright TannerRitchie Publishing
 */
ob_start();
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
//Start freemius wrapper. (Enables auto disabling of free version on install.)

if ( function_exists( 'dancepress_fs' ) ) {
    dancepress_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'dancepress_fs' ) ) {
        // Create a helper function for easy SDK access.
        function dancepress_fs()
        {
            global  $dancepress_fs ;
            
            if ( !isset( $dancepress_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $dancepress_fs = fs_dynamic_init( array(
                    'id'             => '2265',
                    'slug'           => 'dancepress-trwa',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_d5ee744a05999a9d4035b97631358',
                    'is_premium'     => false,
                    'premium_suffix' => '',
                    'has_addons'     => true,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 14,
                    'is_require_payment' => false,
                ),
                    'menu'           => array(
                    'slug'    => 'dancepress_menu',
                    'support' => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $dancepress_fs;
        }
        
        // Init Freemius.
        dancepress_fs();
        // Signal that SDK was initiated.
        do_action( 'dancepress_fs_loaded' );
    }
    
    $tax_percent = ( is_numeric( get_option( 'ds_tax_percent' ) ) ? get_option( 'ds_tax_percent' ) : 0 );
    $class_limit = (int) get_option( 'dstrwa_class_limit', 100 );
    define( 'DANCEPRESSTRWA_TAX_MULTIPLIER', $tax_percent / 100 );
    define( 'DANCEPRESSTRWA_TAX_DIVIDER', $tax_percent / 100 + 1 );
    define( 'DANCEPRESSTRWA_CLASS_LIMIT', $class_limit );
    define( 'DANCEPRESSTRWA_PORTAL_CATEGORY', 5 );
    define( 'DANCEPRESSTRWA_COMPANY_CATEGORY', 6 );
    define( 'DANCEPRESSTRWA_SESSION_OPTION', 'db_session' );
    define( 'PLUGIN_DOCUMENT_ROOT', dirname( __FILE__ ) );
    $timezone_identifier = get_option( 'timezone_string' );
    if ( !$timezone_identifier ) {
        $timezone_identifier = 'America/Toronto';
    }
    date_default_timezone_set( $timezone_identifier );
    //Include required files
    load_plugin_textdomain( 'dance', false, basename( dirname( __FILE__ ) ) . '/languages' );
    require 'app/controllers/Controller.class.php';
    require 'app/controllers/DanceSchool.class.php';
    require 'app/controllers/DanceSchoolAdmin.class.php';
    require 'app/controllers/OptionsController.class.php';
    require 'app/models/Model.class.php';
    require 'app/models/News.class.php';
    require 'app/models/Image.class.php';
    require 'app/models/Dance.class.php';
    require 'app/models/Countries.class.php';
    require 'app/models/Currencies.class.php';
    require 'app/models/ClassManager.class.php';
    require 'app/models/ClassCategories.class.php';
    require 'app/models/Parents.class.php';
    require 'app/models/ClassStudents.class.php';
    require 'app/models/ClassReports.class.php';
    require 'app/models/Sessions.class.php';
    require 'app/models/ListManager.class.php';
    require 'app/models/Teachers.class.php';
    require 'app/models/Option.class.php';
    require 'app/models/ClassVenues.class.php';
    require 'app/models/ClassEvents.class.php';
    require 'app/controllers/RegistrationController.class.php';
    require 'app/controllers/ClientsController.class.php';
    require 'app/models/Registration.class.php';
    require 'app/util/Mailman.class.php';
    require 'library/Logger.class.php';
    require 'library/functions.php';
    register_activation_hook( __FILE__, 'dance_activate' );
    add_action(
        'upgrader_process_complete',
        'trwadance_upgrade',
        10,
        2
    );
    //Enable ajax calls
    
    if ( !isset( $_REQUEST['ajax'] ) ) {
        define( 'IS_AJAX', false );
    } else {
        //Initialize json or ajax response
        $c = new \DancePressTRWA\Controllers\DanceSchool();
        add_action( 'init', array( $c, 'startJDB' ) );
        define( 'IS_AJAX', true );
    }
    
    require 'dance-admin.php';
}

//End of freemius wrapper