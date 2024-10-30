<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Api\Callbacks;

use \Inc\Base\BaseController;

class AdminCallbacks extends BaseController {

    public function adminDashboard() {
        return require_once "$this->plugin_path/templates/admin.php";
    }

    public function logonOptionGroups($input) {
        return $input;
    }

    public function logonSections() {
        echo 'App Information';
    }

    public function logonAppId() {
        $value = esc_attr(get_option('logon_app_id'));
        echo '<input type="text" class="regular-text fixed-row" name="logon_app_id" value="' . $value . '" placeholder="APP ID"/>';
    }
    public function logonAppSecret() {
        $secret = esc_attr(get_option('logon_app_secret'));
        echo '<input type="text" class="regular-text fixed-row" name="logon_app_secret" value="' . $secret . '" placeholder="APP SECRET"/>';
    }
    public function logonApiPath() {
        $path = esc_attr(get_option('logon_api_path'));
        //'https://api.logonlabs.com'
        //'https://api.logon-dev-stable.com'
        //'https://api.logon-dev.com'
        //echo '<input type="text" class="regular-text" name="logon_api_path" value="' . $path . '" placeholder="API PATH"/>';

        if ($this->dev) {
            echo '<select class="select-row" name="logon_api_path">';
            echo '<option value="https://api.logonlabs.com" ' . ($path == 'https://api.logonlabs.com' ? 'selected' : '') . '>Production</option>';
            echo '</select>';
        } else {
            echo '<input type="hidden" name="logon_api_path" value="https://api.logonlabs.com"/>';

        }
    }
    public function logonThemeStyle() {
        $style = esc_attr(get_option('logon_theme_style'));

        echo '<div class="flex-row">';
        echo '<input type="radio" name="logon_theme_style" value="icon" ' . ($style == 'icon' ? 'checked' : '') . '/>' ;
        echo '<img src="' . $this->plugin_url . 'assets/ll_icons.png" border="0" />';
        echo '</div>';
        echo '<div class="flex-row">';
        echo '<input type="radio" name="logon_theme_style" value="button" ' . ($style == 'button' ? 'checked' : '') . '/>';
        echo '<img src="' . $this->plugin_url . 'assets/ll_buttons.png" border="0" />';
        echo '</div>';
    }
}