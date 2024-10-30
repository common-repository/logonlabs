<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Pages;

use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;
use \Inc\Api\Callbacks\AdminCallbacks;
use LogonLabs\IdPx\API\LogonClient as LogonClient;

class LogonAuthorize extends BaseController {

    public $settings;
    public $callbacks;
    public $token;
    public $response = array();
    public $shortcode_tag = 'll-authorize-callback';
    public $error_code;
    public $valid_session;

    public function register() {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
        $posts = array(
            'logonauthorize' => array(
                'title' => __('Authorizing', 'logonlabs'),
                'content' => "[$this->shortcode_tag]",
                'option_id' => 'll_authorize_page'
            )
        );

        $this->settings->addPosts($posts)->register();
        add_shortcode($this->shortcode_tag, array($this, 'handleShortCode'));
        add_action('wp', array($this, 'verifyCode'));
    }

    public function verifyCode() {
        if (!is_page('logonauthorize')) {
            return;
        }

        if (!isset($_GET['token'])) {
            return;
        }

        $token = $_GET['token'];
        $session_token = false;
        if (isset($_SESSION['token'])) {
            $session_token = $_SESSION['token'];
        }
        if ($token != $session_token) {

            $_SESSION['token'] = $token;
            $app_id = get_option('logon_app_id');
            $app_secret = get_option('logon_app_secret');
            $api_path = get_option('logon_api_path');
            if (empty($app_id) || empty($app_secret) || empty($api_path)) {
                $_SESSION['empty_config'] = true;
                return;
            }
            unset($_SESSION['empty_config']);
            $logonClient = new LogonClient(array(
                'api_path' => $api_path,
                'app_id' => $app_id,
                'app_secret' => $app_secret
            ));

            $response = $logonClient->validateLogin($token);
            $_SESSION['response'] = $response;
            $valid_session = false;
            if ($response['body'] && isset($response['body']['error'])) {
                $error_code = $response['body']['error']['message'];
            } else {
                $valid_session = $response['body'];
            }

            if ($valid_session !== false) {
                if ($valid_session['event_success']) {
                    $email = sanitize_email($valid_session['email_address']);
                    $user = get_user_by('email', $email);
                    if (!$user) {
                        $this->registerUser($valid_session);
                        $_SESSION['signin_user'] = false;
                    } else {
                        $this->loginUser($user->ID);
                        $_SESSION['signin_user'] = true;
                    }
                }
            }
        }
    }

    public function handleShortCode() {
//        $token = $_GET['token'];
        if (empty($_GET['token'])) {
            return __('No token provided, invalid access', 'logonlabs');
        }

        if ($_SESSION['empty_config']) {
            return __('LogonLabs settings not completed. Please contact administrator', 'logonlabs');
        }

        $response = $_SESSION['response'];
        $output = '<div style="text-align: center;">';

        $error_code = false;
        $valid_session = false;
        if ($response['body'] && isset($response['body']['error'])) {
            $error_code = $response['body']['error']['message'];
        } else {
            $valid_session = $response['body'];
        }

        if ($error_code) {
            $output .= '<code>' . $error_code . '</code>';
        }
        if ($valid_session) {
            if ($valid_session['event_success']) {
                $output .= '<p>Redirecting in 3 seconds</p>';
                $url = $this->redirectLocation();
                $script = <<<EOT
<script>
var redirect_url = '%%url%%';
var timer = setTimeout(function() {
    window.location = redirect_url;
}, 3000);
</script>
EOT;
                $script = str_replace('%%url%%', $url, $script);
                $output .= $script;

            } else {
                $output .= 'not valid login';
                $output .= json_encode($response['body']);
            }
        } else if (!$error_code) {
            $output .= 'Something went wrong!';
        }
        $output .= '</div>';
        return $output;
    }

    public function redirectLocation() {
        global $current_user;
        return ( is_array( $current_user->roles ) && in_array( 'administrator', $current_user->roles ) ) ? admin_url() : site_url();
    }


    public function registerUser($data) {
        $new_pass = wp_generate_password();
        $new_hash = wp_hash_password($new_pass);
        $name = $data['identity_provider_data']['first_name'] . ' ' . $data['identity_provider_data']['last_name'];

        $user = array();
        $user['user_email'] = sanitize_email($data['email_address']);
        $user['user_login'] = sanitize_user($data['email_address']);
        $user['display_name'] = sanitize_text_field($name);
        $user['user_nicename'] = sanitize_text_field($name);
        $user['user_registered'] = current_time('mysql', 1);
        $user['user_pass'] = $new_hash;
        $user_id = wp_insert_user($user);
        $this->loginUser($user_id);

    }

    public function loginUser($user_id) {
        $user = get_user_by('id', $user_id);

        //var_dump($user);
        if ($user) {
            $secure_cookie = is_ssl();
            $secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $user->user_pass);
            wp_clear_auth_cookie();
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id, true, $secure_cookie);

            do_action( 'wp_login', $user->user_login, $user, false);
        }

//        global $current_user;
//        var_dump($current_user);


    }
}