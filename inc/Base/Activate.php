<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Base;

class Activate {
    public static function activate() {
        flush_rewrite_rules();
    }
}