<?php
namespace OAuthPlugin\Controller;
use OAuthProvider\OAuthProvider;
use OAuthException;
use Phifty\Controller;
use OAuth;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth1\AccessTokenController;

class OAuthPlurkCallback extends AccessTokenController
{

    public function getIdentity($userinfo,$tokeninfo)
    {
        return $userinfo->user_info->id;

        // ->display_name
        //  ->nick_name
    }

    public function fetchUserInfo($oauth)
    {
        $oauth->fetch('http://www.plurk.com/APP/Profile/getOwnProfile');
        $response = $oauth->getLastResponse();
        return json_decode($response);
    }

    public function getConfig() {
        return $this->bundle->config('Providers.Plurk');
    }

    public function createProvider() {
        $config = $this->getConfig();
        return OAuthProvider::create('plurk',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
    }

    public function getCallbackUrl() {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/plurk/callback');
    }
}





