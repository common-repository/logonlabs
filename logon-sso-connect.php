<?php
/**
 * @package logon-sso-connect
 */
/*
Plugin Name: LogonLabs
Plugin URI: https://logonlabs.com
Description: This is the LogonLabs SSO connector for the WordPress
Version: 1.5.4
Author: LogonLabs
License: GPLv2 or later
Text Domain: logon
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

defined('ABSPATH') or die('Warning, restricted area.');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once(dirname(__FILE__) . '/vendor/autoload.php');
}

register_activation_hook(__FILE__, array('Inc\\Base\\Activate', 'activate'));
register_activation_hook(__FILE__, array('Inc\\Base\\Deactivate', 'deactivate'));


//function activate_logon_sso_connect() {
//    Activate::activate();
//}
//
//function deactivate_logon_sso_connect() {
//    Deactivate::deactivate();
//}
//// activation
//register_activation_hook(__FILE__, 'activate_logon_sso_connect');
//register_deactivation_hook(__FILE__, 'deactivate_logon_sso_connect');

if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}