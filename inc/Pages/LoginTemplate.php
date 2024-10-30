<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Pages;

use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;
use \Inc\Pages\LoginForm;

class LoginTemplate extends BaseController {

    public $settings;
    public $callbacks;
    public $token;
    public $response = array();
    public $error_code;

    public static $FORM_START = array('woocommerce_login_form_start', 'woocommerce_register_form_start', 'login_form_top');
    public static $FORM_ICON_END = array('woocommerce_login_form', 'woocommerce_register_form');
    public static $FORM_BUTTON_END = array('woocommerce_login_form_end', 'woocommerce_register_form_end');
    public static $FORM_END_RETURN = array('login_form_bottom');

    public function register() {
        $this->settings = new SettingsApi();

        $this->registerStart();
        $this->registerEnd();
    }

    public function registerStart() {
        foreach(self::$FORM_START as $load) {
            add_action($load, array($this, 'addLogonScript'));
        }
    }

    public function registerEnd() {
        $theme_style = get_option('logon_theme_style');
        switch($theme_style) {
            case 'button':
                foreach(self::$FORM_BUTTON_END as $load) {
                    add_action($load, array($this, 'injectLogonClient'));
                }
                break;
            case 'icon':
                foreach(self::$FORM_ICON_END as $load) {
                    add_action($load, array($this, 'injectLogonClient'));
                }
                break;
        }
        foreach(self::$FORM_END_RETURN as $load) {
            add_action($load, array($this, 'addLogonClient'));
        }

    }

    public function injectLogonClient() {
        echo $this->addLogonClient();
    }

    public function addLogonScript() {
        wp_enqueue_style('login_style', $this->plugin_url . '/assets/form.css');
        $app_id = get_option('logon_app_id');
        $app_secret = get_option('logon_app_secret');
        $api_path = get_option('logon_api_path');
        if (!empty($app_id) && !empty($app_secret) && !empty($api_path)) {
            wp_enqueue_script('logon_script', $this->plugin_url . '/assets/logonlabs.min.js', array('jquery'));
            wp_localize_script( 'logon_script', 'app_id', $app_id );
        }
    }

    public function addLogonClient() {
        $ret = '';
        $button = LoginForm::getRenderedLoginButtons();
        $ret .= $button['content'];
        $ret .= LoginForm::getLogonScripts($button['index']);
        return $ret;
    }
}