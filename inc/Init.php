<?php
/**
 * @package logon-sso-connect
 */

namespace Inc;

final class Init {

    public static function get_services() {
        return [
            Base\Session::class,
            Pages\Admin::class,
            Pages\LoginForm::class,
            Pages\LoginTemplate::class,
            Pages\LogonAuthorize::class,
            Base\Enqueue::class,
            Base\SettingsLinks::class
        ];

    }

    public static function register_services() {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class) {
        $service = new $class();
        return $service;
    }
}

//
//use Inc\Activate;
//use Inc\Deactivate;
//
//if (!class_exists('LogonSSOConnect')) {
//
//    class LogonSSOConnect {
//
//        public $path;
//        public $plugin;
//
//        public function __construct() {
//            $this->path = plugin_dir_path(__FILE__);
//            $this->plugin = plugin_basename(__FILE__);
//        }
//
//        public function register() {
//            add_action('admin_menu', array($this, 'add_admin_pages'));
//            add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
//        }
//
//        public function add_admin_pages() {
//            add_menu_page('Logon SSO Connect', 'Logon SSO', 'manage_options', 'logon_sso_connect', array($this, 'admin_index'), 'dashicons-admin-network', 110);
//        }
//
//        public function admin_index() {
//            //require template
//            require_once $this->path . 'templates/admin.php';
//        }
//
//        public function settings_link($links) {
//            $settings_link = '<a href="admin.php?page=logon_sso_connect">Settings</a>';
//            array_push($links, $settings_link);
//            return $links;
//        }
//    }
//
//    $logonSSOConnect = new LogonSSOConnect();
//    $logonSSOConnect->register();
//
//
//    // activation
//    register_activation_hook(__FILE__, array('Activate', 'activate'));
//
//    // deactivations
//    register_deactivation_hook(__FILE__, array('Deactivate', 'deactivate'));
//
//}
//
//// require_once plugin_dir_path(__FILE__) . 'inc/files';
//
