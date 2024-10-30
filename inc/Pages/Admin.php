<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Pages;

use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;
use \Inc\Api\Callbacks\AdminCallbacks;

class Admin extends BaseController {

    public $settings;
    public $pages;
    public $callbacks;

    public function register() {
        $api_path = get_option('logon_api_path');
        if (empty($api_path)) {
            update_option('logon_api_path', 'https://api.logonlabs.com/');
        }
        $style = get_option('logon_theme_style');
        if (empty($style)) {
            update_option('logon_theme_style', 'button');
        }
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
        $this->pages = array(
            array(
                'page_title' => 'Logon SSO Connect',
                'menu_title' => 'Logon SSO',
                'capability' => 'manage_options',
                'menu_slug' => 'logon_sso_connect',
                'callback' => array($this->callbacks, 'adminDashboard'),
                'icon_url' => $this->plugin_url . '/assets/favicon.png',
                'position' => 110
            )
        );

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->addPages($this->pages)->register();
        add_action('admin_init', array($this, 'setSettings'));

    }

    public function setSettings() {
        $args = array(
            array(
                'group' => 'logon_option_groups',
                'name' => 'logon_app_id',
                'callback' => array($this->callbacks, 'logonOptionGroups')
            ),
            array(
                'group' => 'logon_option_groups',
                'name' => 'logon_app_secret',
                'callback' => array($this->callbacks, 'logonOptionGroups')
            ),
            array(
                'group' => 'logon_option_groups',
                'name' => 'logon_api_path',
                'default' => 'https://api.logon-dev.com',
                'callback' => array($this->callbacks, 'logonOptionGroups')
            ),
            array(
                'group' => 'logon_option_groups',
                'name' => 'logon_theme_style',
                'default' => 'button',
                'callback' => array($this->callbacks, 'logonOptionGroups')
            )
        );
        $this->settings->setSettings($args);
    }

    public function setSections() {
        $args = array(
            array(
                'id' => 'logon_admin_index',
                'title' => 'App Settings',
                'callback' => array($this->callbacks, 'logonSections'),
                'page' => 'logon_sso_connect'
            )
        );
        $this->settings->setSections($args);
    }
    public function setFields() {
        $args = array(
            array(
                'id' => 'logon_app_id',
                'title' => 'APP ID',
                'callback' => array($this->callbacks, 'logonAppId'),
                'page' => 'logon_sso_connect',
                'section' => 'logon_admin_index',
                'args' => array(
                    'label_for' => 'logon_app_id'
                )
            ),
            array(
                'id' => 'logon_app_secret',
                'title' => 'APP SECRET',
                'callback' => array($this->callbacks, 'logonAppSecret'),
                'page' => 'logon_sso_connect',
                'section' => 'logon_admin_index',
                'args' => array(
                    'label_for' => 'logon_app_secret'
                )
            ),
            array(
                'id' => 'logon_api_path',
                'title' => $this->dev ? 'API PATH' : '',
                'callback' => array($this->callbacks, 'logonApiPath'),
                'page' => 'logon_sso_connect',
                'section' => 'logon_admin_index',
                'args' => array(
                    'label_for' => 'logon_api_path'
                )
            ),
            array(
                'id' => 'logon_theme_style',
                'title' => 'THEME',
                'callback' => array($this->callbacks, 'logonThemeStyle'),
                'page' => 'logon_sso_connect',
                'section' => 'logon_admin_index',
                'args' => array(
                    'label_for' => 'logon_theme_style'
                )
            )
        );
        $this->settings->setFields($args);
    }
}