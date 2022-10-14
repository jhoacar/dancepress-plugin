<?php
namespace DancePressTRWA\Controllers;

use \Exception as PHPException;

if (isset($wpdb)) {
    $wpdb->hide_errors();
}
/**
 * Controls public parent portal page
 *
 * @since 1.0
 */
final class ParentPortalController extends Controller
{
    public function startParentPortal()
    {
        $this->sessionCondition = dance_getDefaultSessionCondition();
        $this->sessionId = dance_getDefaultSessionId();

        if (is_user_logged_in()) {
            $objClassParents = new \DancePressTRWA\Models\Parents($this->sessionCondition);
            $this->isCompetitive = $objClassParents->isCompetitive();
            $this->isRec = $objClassParents->isRec();
        }

        load_plugin_textdomain('dancepress-trwa', false, dirname(plugin_basename(__FILE__)) . '/languages') ;

        $this->initializePublic();
        parent::$action = isset($this->_REQUEST['action']) ? $this->_REQUEST['action'] : false;
        $this->offset = isset($this->_REQUEST['offset']) ? (int) $this->_REQUEST['offset'] : 0;
        $this->limit = 30;

        if (method_exists($this, parent::$action)) {
            call_user_func_array(array($this, parent::$action), $this->_REQUEST);
        } else {
            $this->doParentPortalHome();
        }
    }

    public function doParentPortalHome()
    {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] != "") {
            $dance = new \DancePressTRWA\Controllers\DanceSchool();
            $dance->doDefault();
            return;
        } elseif (is_user_logged_in()) {
            global $current_user;
            if (!$current_user->caps || !$current_user->roles) { //Is the user at least a subscriber level user?
                $this->render('no-access__premium_only');
                return;
            }
            $user = wp_get_current_user();
            if (get_user_meta($user->ID, 'active', true) == 0) {
                //Double check for errors in activation ... check to see if marked active in parents table.
                $parent = new \DancePressTRWA\Models\Parents($this->sessionCondition);
                if (!$parent->isParentAlreadyActive($user->ID, 'user_id')) { //False == parent is active (!)
                    $parentId = $parent->getParentIdByWPUserId();
                    $parent->activateParent($parentId, false);
                    update_user_meta($user->ID, 'active', true);
                } else {
                    echo 'Your account is currently inactive. Please <a href="/registration/">Register</a> for the current session to activate your account. Your account will be activated once your registration fee has been received. <br/><br/>Please <a href="/contact-us">Contact us</a> if you have any questions.';
                    return;
                }
            };

            if (!$this->isCompetitive && $this->isRec) {
                $posts = get_posts(array('posts_per_page' => 10, 'category' => DANCEPRESSTRWA_PORTAL_CATEGORY));
            } elseif ($this->isCompetitive && !$this->isRec) {
                $posts = get_posts(array('posts_per_page' => 10,'category' => DANCEPRESSTRWA_COMPANY_CATEGORY));
            } elseif ($this->isCompetitive && $this->isRec) {
                $posts = get_posts(array('posts_per_page' => 10,'category' => DANCEPRESSTRWA_COMPANY_CATEGORY . ',' . DANCEPRESSTRWA_PORTAL_CATEGORY));
            } else {
                $posts = get_posts(array('posts_per_page' => 10,'category' => DANCEPRESSTRWA_PORTAL_CATEGORY));
            }

            $this->render('news', array('news' => $posts));
            return;
        }
        $this->render('news');
        return;
    }
}
