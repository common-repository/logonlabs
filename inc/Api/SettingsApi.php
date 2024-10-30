<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Api;

use WP_Query;

class SettingsApi {

    public $admin_pages = array();

    public $settings = array();
    public $posts = array();
    public $sections = array();
    public $fields = array();

    public function register() {
        if (!empty($this->admin_pages)) {
            add_action('admin_menu', array($this, 'addAdminMenu'));
        }

        if (!empty($this->settings)) {
            add_action('admin_init', array($this, 'registerCustomFields'));
        }

        if (!empty($this->posts)) {
            $this->createPluginPosts();
        }
    }

    public function addPages(array $pages) {
        $this->admin_pages = $pages;
        return $this;
    }

    public function addPosts(array $posts) {
        $this->posts = $posts;
        return $this;
    }

    public function addAdminMenu() {
        foreach ($this->admin_pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'],
                $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
        }
    }

    public function setSettings($options) {
        $this->settings = $options;
        return $this;
    }

    public function setSections($options) {
        $this->sections = $options;
        return $this;
    }

    public function setFields($options) {
        $this->fields = $options;
        return $this;
    }

    public function registerCustomFields() {
        foreach ($this->settings as $setting) {
            $args = array();
            if (isset($setting['callback'])) {
                $args['sanity_callback'] = $setting['callback'];
            }
            if (isset($setting['default'])) {
                $args['default'] = $setting['default'];
            }
            register_setting($setting['group'], $setting['name'], $args);
        }

        foreach ($this->sections as $section) {
            add_settings_section($section['id'], $section['title'], (isset($section['callback']) ? $section['callback'] : ''), $section['page']);
        }

        foreach ($this->fields as $field) {
            add_settings_field($field['id'], $field['title'], (isset($field['callback']) ? $field['callback'] : ''), $field['page'], $field['section'], (isset($field['args']) ? $field['args'] : ''));
        }
    }

    public function createPluginPosts() {
        foreach ($this->posts as $slug => $post) {
            $query = new WP_Query('pagename=' . $slug);
            if (!$query->have_posts()) {
                update_option($post['option_id'],
                    wp_insert_post(
                        array(
                            'post_content' => $post['content'],
                            'post_name' => $slug,
                            'post_title' => $post['title'],
                            'post_status' => 'publish',
                            'post_type' => 'page',
                            'ping_status' => 'closed',
                            'comment_status' => 'closed',
                        )
                    )
                );
            }
        }
    }
}