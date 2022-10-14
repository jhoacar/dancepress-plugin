<?php

/**
* Library of various helper functions
*/

function dancepresstrwa_register_session()
{
    ob_start(); //Solves the 'headers already sent' issue.
    if (!session_id() && !headers_sent()) {
        session_start();
    }
}

function widget_dance($args)
{
}

//Start registration via shortcode
function dancepress_registration()
{
    if (is_admin()) {
        return;
    }
    dancepresstrwa_register_session();
    $danceSchool = new \DancePressTRWA\Controllers\DanceSchool();
    $danceSchool->startDance();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
//Start partent portal via shortcode
function dancepress_portal()
{
    if (is_admin()) {
        return;
    }
    dancepresstrwa_register_session();
    $c = new \DancePressTRWA\Controllers\DanceSchool();
    $c->startParentPortal();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

// function dancepresstrwa_save_error(){
// 	file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());
// }

function dance_activate()
{
    $config_user = "dance_user";
    //These roles should always exists, but avoid fatal error if they don't.
    if ($role = get_role('editor')) {
        $role->add_cap($config_user);
    }
    if ($role = get_role('administrator')) {
        $role->add_cap($config_user);
    }

    //Run database modification SQL.
    //Modify this SQL with database structure when structure changed.
    //NOTE - dbDelta will fail if backticks used, Indexes absent, or fields not on separate lines.
    //NOTE - dbDelta will NOT run arbitrary SQL. It only compares structure in CREATE statements and updates structure to match.
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once(plugin_dir_path(__FILE__) . '../sql/install.sql.php');

    foreach ($sql as $q) {
        $reports[] = dbDelta($q);
    }

    require_once(plugin_dir_path(__FILE__) . '../sql/install-data.sql.php');
    foreach ($sql as $q) {
        $wpdb->query($q);
    }
    include_once(plugin_dir_path(__FILE__) . '../views/admin-newactivation.php');
}

function trwadance_upgrade($upgraderObj, $options)
{
    $our_plugin = plugin_basename(__FILE__);
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
        // Iterate through the plugins being updated and check if ours is there
        foreach ($options['plugins'] as $plugin) {
            if ($plugin == $our_plugin) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                require_once(plugin_dir_path(__FILE__) . '../sql/install.sql.php');
                foreach ($sql as $q) {
                    dbDelta($q);
                }
            }
        }
    }
}

function dance_getDefaultSessionId()
{
    return get_option(DANCEPRESSTRWA_SESSION_OPTION);
}

/**
* Get the DEFAULT session condition for all users. For use in SQL statements.
* This will probably NOT be what you want when working in admin area.
* @return string $defaultSessionCondition
*/
function dance_getDefaultSessionCondition()
{
    $defaultSessionId = dance_getDefaultSessionId();
    $defaultSessionCondition = $defaultSessionId ? '`sessions_id`=' . (int)$defaultSessionId : '`sessions_id` IS NULL';
    return $defaultSessionCondition;
}

function dance_getUserSessionId()
{
    return get_user_option(DANCEPRESSTRWA_SESSION_OPTION);
}

/**
* Get the CURRENT user session condition (ie, the WORKING session). For use in SQL statements.
* This is probably what you want when working in the admin area.
* @return string $userSessionCondition
*/
function dance_getUserSessionCondition()
{
    $userSessionId = dance_getUserSessionId();
    $defaultSessionId = dance_getDefaultSessionId();
    if (!$userSessionId) {
        $userSessionId = $defaultSessionId;
    }
    $userSessionCondition = $userSessionId ? '`sessions_id`=' . (int)$userSessionId : '`sessions_id` IS NULL';
    return $userSessionCondition;
}

//Expects str time in format hh:mm:ss
function dance_timeToDecimal($timeStr)
{
    $timeEls = explode(':', $timeStr);
    return $timeEls[0] + ($timeEls[1] / 60) + ($timeEls[2] / 3600);
}

/**
 * Show a simplified stack trace
 * Optionally set number of levels to display and whether to include function/method that was last called
 * Defaults to show all levels, and show callee
 * @param mixed $opt int levels | array[int levels, boolean includeCallee]
 * @return void
 */
function dstrwa_trace($opt = ['levels' => false, 'callee' => true, 0 => false, 1 => true])
{
    if (is_numeric($opt)) {
        $levels = $opt;
        $ignore = 1;
    } else {
        $ignore = isset($opt[0]) && $opt[0] ? 1 : 2;
        $levels = isset($opt[1]) ? $opt[1] : false;
        $ignore = isset($opt['callee']) && $opt['callee'] ? 1 : 2;
        $levels = isset($opt['levels']) ? $opt['levels'] : false;
    }
    $t = debug_backtrace();
    $i = 0;

    echo "<pre>-------start trace---------------------\n";
    foreach ($t as $k => $v) {
        if ($k < $ignore) {
            continue;
        }
        if ($levels && $i == $levels) {
            break;
        }
        echo $v['class'] . '::' .$v['function'] . '()' . "\n";
        $i++;
    }
    echo "-------end trace---------------------\n</pre>";
}
