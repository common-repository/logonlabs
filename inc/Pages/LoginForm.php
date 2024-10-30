<?php
/**
 * @package logon-sso-connect
 */

namespace Inc\Pages;

use \Inc\Base\BaseController;
use LogonLabs\IdPx\API\LogonClient as LogonClient;
use LogonLabs\IdentityProviders as IdentityProviders;

class LoginForm extends BaseController {

    public static $index = 0;

    public function register() {
        add_action('login_form', array($this, 'addLoginFormButtons'));
        add_action('register_form', array($this, 'addLoginFormButtons'));
        add_action('login_form', array($this, 'addLogonScripts'));
        add_action('register_form', array($this, 'addLogonScripts'));
    }

    public function addLogonScripts() {
        echo self::getLogonScripts(self::$index);
    }

    public function addLoginFormButtons() {
        echo self::getRenderedLoginButtons()['content'];
    }

    public static function getLogonScripts($id = false) {
        unset($_SESSION['token']);
        unset($_SESSION['signin_user']);

        if ($id === false) {
            $id = self::$index;
        }

        $ret = '';
        $app_id = get_option('logon_app_id');
        $api_path = get_option('logon_api_path');
        $theme_style = get_option('logon_theme_style');

        if (!empty($app_id) && !empty($api_path)) {
            $logonClient = new LogonClient(array(
                'api_path' => $api_path,
                'app_id' => $app_id
            ));

            $provider = false;
            $script = '';
            if (isset($_GET['provider'])) {
                $provider = true;
                $response = $logonClient->startLogin($_GET['provider']);
                if (!isset($response['redirect']) || strlen($response['redirect']) == 0) {
                    if (isset($response['body']['error']['message'])) {
                        $script .= '<div class="ll-error-message ll-error-message-' . $id . '">' . $response['body']['error']['message'] . '</div>';
                    }
                    $script .= '<script>console.warn(' . json_encode($response['body']['error']) . ')</script>';
                    $provider = false;
                } else {
                    $script = '<script>window.location.href ="' . $response['redirect'] . '";</script>';
                }
                $ret = $script;
            }

            if (!$provider) {
                $response = $logonClient->getProviders();

                $script .= <<<EOT
<script>
if (typeof window.queueLogon == 'undefined') {
    window.queueLogon = [];
}
if (typeof window.logonAsync == 'undefined') {
    window.logonAsync = function() {
        var call;
        while(queueLogon.length > 0) {
            call = queueLogon.shift();
            call();
        }
    }
}
window.queueLogon.push(function() {
    LogonClient.configure({
        app_id: '%%app_id%%'
    });

    var social_identity_providers = '%%social_identity_providers%%';
    var enterprise_identity_providers = '%%enterprise_identity_providers%%';
    LogonClient.ui.button('logonlabs-button-holder-%%index%%', {
        theme: '%%theme_style%%',
        pass: true,
        social_identity_providers: JSON.parse(social_identity_providers),
        enterprise_identity_providers: JSON.parse(enterprise_identity_providers),
    });
    jQuery(document).ready(function($){           
        $('#logonlabs-button-holder-%%index%% #logonlabs-ui').parents('.ll-login-fields').addClass('visible')
        $('#logonlabs-button-holder-%%index%% #logonlabs-ui>div').on('click', function(res){
            var name = $(res.currentTarget).attr('name');
            var identity_provider_id = $(res.currentTarget).attr("identity_provider_id");
            if (identity_provider_id) {
                name = identity_provider_id;
            }
            var search = document.location.search;
            if (search.indexOf('provider') > -1) {
                search = search.replace(/(provider=)[^\&]+/, '$1' + name);
            } else {
                if (search.length > 0) {
                    search += '&';
                } else {
                    search += '?';
                }
                search += 'provider=' + name;
            }
            document.location.search = search;
        });
    });
    
});
if (typeof LogonClient != 'undefined') {
    if (window.logonTimer) {
        clearTimeout(logonTimer);
    }
    window.logonTimer = setTimeout(function(){
        logonAsync(); 
    }, 10);
}
</script>
EOT;
                $app_id = get_option('logon_app_id');
                $script = str_replace('%%app_id%%', $app_id, $script);
                $script = str_replace('%%theme_style%%', $theme_style, $script);
                $script = str_replace('%%social_identity_providers%%', json_encode($response['body']['social_identity_providers']), $script);
                $script = str_replace('%%enterprise_identity_providers%%', json_encode($response['body']['enterprise_identity_providers']), $script);
                $script = str_replace('%%index%%', $id, $script);
                $ret = $script;

            }
        } else {
            $ret = '';
        }

        return $ret;

    }

    public static function getRenderedLoginButtons() {
        $ret = '';
        $id = ++self::$index;
        $app_id = get_option('logon_app_id');
        $app_secret = get_option('logon_app_secret');
        $api_path = get_option('logon_api_path');
        if (!empty($app_id) && !empty($app_secret) && !empty($api_path)) {
            $ret .= '<div class="ll-login-fields">';
            $ret .= '<div class="ll-login-button">';
            $ret .= 'Or continue with:';
            $ret .= '</div>';
            $ret .= '<div id="logonlabs-button-holder-'. $id . '"></div>';
            $ret .= '</div>';
        }

        return array(
            'content' => $ret,
            'index' => $id
        );
    }

}