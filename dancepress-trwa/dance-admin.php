<?php

add_action( 'admin_menu', 'danceMenu' );
add_action( 'admin_bar_menu', 'danceAdminBarItem', 150 );

if ( is_admin() ) {
    add_action( 'admin_enqueue_scripts', 'danceAdminEnqueue' );
    //add_action('init', 'checkRegistrationDuplicate');
}

function checkRegistrationDuplicate()
{
    
    if ( IS_AJAX ) {
        $da = new DancePressTRWA\Controllers\DanceAdmin();
        add_action( 'wp_ajax_checkregistrationduplicate', array( $da, 'checkRegistrationDuplicate' ) );
    }

}

function danceMenu()
{
    $da_class = new DancePressTRWA\Controllers\DanceAdmin( false );
    add_menu_page(
        'Class Options',
        'DancePress',
        'dance_user',
        'dancepress_menu',
        array( $da_class, 'startAdmin' ),
        plugin_dir_url( __FILE__ ) . '/img/star.png',
        78
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Course Management' ),
        __( 'Course Management' ),
        'dance_user',
        'admin-listclass',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Student Management' ),
        __( 'Student Management' ),
        'dance_user',
        'admin-managestudents',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Client Management' ),
        __( 'Client Management' ),
        'dance_user',
        'admin-manageparents',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Venues' ),
        __( 'Venues' ),
        'dance_user',
        'admin-venues',
        array( $da_class, 'startAdmin' )
    );
    ////
    add_submenu_page(
        'dancepress_menu',
        __( 'Events' ),
        __( 'Events' ),
        'dance_user',
        'admin-events',
        array( $da_class, 'startAdmin' )
    );
    ////
    add_submenu_page(
        'dancepress_menu',
        __( 'Reports' ),
        __( 'Reports' ),
        'dance_user',
        'admin-reports',
        array( $da_class, 'startAdmin' )
    );
    ////
    add_submenu_page(
        'dancepress_menu',
        __( 'Options' ),
        __( 'Options' ),
        'dance_user',
        'admin-options',
        array( $da_class, 'startAdmin' )
    );
    ////
    //Create 'hidden' submenu items to keep dancepress menu open.
    add_submenu_page(
        'dancepress_menu',
        __( 'Add Clients' ),
        __( 'Add Clients' ),
        'dance_user',
        'admin-addclients',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Add Courses' ),
        __( 'Add Courses' ),
        'dance_user',
        'admin-addclass',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Course Categories' ),
        __( 'Course Categories' ),
        'dance_user',
        'admin-listclasscategories',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Add Class Category' ),
        __( 'Add Class Category' ),
        'dance_user',
        'admin-addclasscategory',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Edit Class Category' ),
        __( 'Edit Class Category' ),
        'dance_user',
        'admin-editclasscategory',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Edit Class' ),
        __( 'Edit Class' ),
        'dance_user',
        'admin-editclass',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Edit Child Class' ),
        __( 'Edit Child Class' ),
        'dance_user',
        'admin-editchildclass',
        array( $da_class, 'startAdmin' )
    );
    add_submenu_page(
        'dancepress_menu',
        __( 'Edit All Classes' ),
        __( 'Edit All Classes' ),
        'dance_user',
        'admin-editclassall',
        array( $da_class, 'startAdmin' )
    );
    add_action( 'admin_menu', 'dancepressHideMenuItems' );
    add_filter( 'submenu_file', 'dancepressHideMenuItems' );
}

/* From https://stackoverflow.com/questions/3902760/how-do-you-add-a-wordpress-admin-page-without-adding-it-to-the-menu/47577455#47577455*/
function dancepressHideMenuItems( $submenu_file )
{
    global  $plugin_page ;
    $hidden_submenus = array(
        'admin-addclients'          => true,
        'admin-addclass'            => true,
        'admin-listclasscategories' => true,
        'admin-addclasscategory'    => true,
        'admin-editclasscategory'   => true,
        'admin-editclass'           => true,
        'admin-editchildclass'      => true,
        'admin-editclassall'        => true,
    );
    // Hide the submenu.
    foreach ( $hidden_submenus as $submenu => $unused ) {
        remove_submenu_page( 'dancepress_menu', $submenu );
    }
    return $submenu_file;
}

function danceAdminEnqueue()
{
    $url = plugin_dir_url( __FILE__ );
    //Register Styles
    wp_register_style( 'jquery-ui-dl', plugins_url( '/css/jquery-ui-dl.css', __FILE__ ) );
    wp_register_style( 'datetimepicker.css', $url . '/css/jquery.datetimepicker.css' );
    wp_register_style( 'dance.css', $url . '/css/dance.css' );
    wp_register_style( 'print-style', $url . '/css/print-style.css' );
    //Register Scripts
    wp_register_script( "jquery-validate", $url . '/js/jquery.validate.js', 'jquery' );
    wp_register_script( "jquery-datetimepicker", $url . '/js/jquery.datetimepicker.js', 'jquery' );
    wp_register_script( "jquery-tablesorter", $url . '/js/jquery.tablesorter.min.js', 'jquery' );
    wp_register_script( "danceAdmin.js", $url . '/js/danceAdmin.js', array( 'jquery', 'jquery-tablesorter', 'jquery-datetimepicker' ) );
    //Enqueue Styles
    wp_enqueue_style(
        'thickbox.css',
        '/' . WPINC . '/js/thickbox/thickbox.css',
        null,
        '1.0'
    );
    wp_enqueue_style( 'datetimepicker.css' );
    wp_enqueue_style( 'jquery-ui-dl' );
    wp_enqueue_style( 'dance.css' );
    //Enqueue Scripts
    wp_enqueue_script( "jquery" );
    wp_enqueue_script( "jquery-ui-core", 'jquery' );
    wp_enqueue_script( "jquery-ui-accordion", 'jquery' );
    wp_enqueue_script( "jquery-ui-button", 'jquery' );
    wp_enqueue_script( "jquery-datetimepicker", 'jquery' );
    wp_enqueue_script( "jquery-validate", 'jquery' );
    wp_enqueue_script( 'danceAdmin.js' );
    wp_enqueue_script( 'media-upload' );
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_script( "jquery-tablesorter", 'jquery' );
}

function danceAdminBarItem()
{
    global  $wp_admin_bar ;
    if ( !is_admin() ) {
        $wp_admin_bar->add_node( array(
            'id'     => 'danceAdmin',
            'title'  => 'DancePress',
            'href'   => '/wp-admin/admin.php?page=dancepress_menu',
            'parent' => 'site-name',
        ) );
    }
}
