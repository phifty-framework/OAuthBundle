<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth2\RequestTokenController;
use OAuth2;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

function parse_signed_request($signed_request, $secret) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }

    // check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }
    return $data;
}

class OAuthFacebook extends RequestTokenController
{

    public function indexAction()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Facebook');

        // TODO: use facebook SDK class
        // FacebookSession::setDefaultApplication('YOUR_APP_ID', 'YOUR_APP_SECRET');


        $provider = OAuthProvider::create('facebook',array(
            'client_id'     => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/facebook/callback');
        $scopes = $bundle->config('Providers.Facebook.Scopes')->config ?: array('email','basic_info');
        return $this->runRequestToken($provider, $callbackUrl, array('scope' => $scopes));
    }


    /**
     * here you'll get the user id who is removing or deauthorize your application
     *
     * https://developers.facebook.com/docs/facebook-login/using-login-with-games
     */
    public function deauthorizeCallbackAction() {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Facebook');

        $data       =   parse_signed_request($_REQUEST['signed_request'], $config->ClientSecret);
        $fbUserId   =   $data['user_id'];
        // $fbUserId this is the Facebook User UID who is removed your application. So you can use this id to update your database or do other tasks if required for your application
        // These methods are provided by facebook
        // http://developers.facebook.com/docs/authentication/canvas
    }

}

