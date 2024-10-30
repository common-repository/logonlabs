<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Base;

use \Inc\Base\BaseController;

class SettingsLinks extends BaseController {

    public function register() {
        add_filter("plugin_action_links_$this->plugin", array($this, 'addSettingsLinks'));
    }

    public function addSettingsLinks($links) {
        $settings_link = '<a href="admin.php?page=logon_sso_connect">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }
}