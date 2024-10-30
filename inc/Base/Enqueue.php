<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController {
    public function register() {
        add_action('wp_enqueue_scripts', array($this, 'wordpressEnqueue'));
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));
        add_action('login_enqueue_scripts', array($this, 'loginEnqueue'));
    }

    public function wordpressEnqueue() {
        wp_enqueue_script('jquery');
    }

    public function adminEnqueue() {
        wp_enqueue_style('admin_style', $this->plugin_url . '/assets/admin.css');
        wp_enqueue_script('admin_script', $this->plugin_url . '/assets/admin.js', array('jquery'));
    }

    public function loginEnqueue() {
        wp_enqueue_style('login_style', $this->plugin_url . '/assets/login.css');
        wp_enqueue_script('login_script', $this->plugin_url . '/assets/login.js', array('jquery'));


        $app_id = get_option('logon_app_id');
        $app_secret = get_option('logon_app_secret');
        $api_path = get_option('logon_api_path');
        if (!empty($app_id) && !empty($app_secret) && !empty($api_path)) {
            wp_enqueue_script('logon_script', $this->plugin_url . '/assets/logonlabs.min.js', array('jquery'));
            wp_localize_script( 'logon_script', 'app_id', $app_id );
        }
    }
}