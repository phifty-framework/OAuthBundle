<?php
namespace OAuthBundle\Controller;
use OAuthProvider\OAuthProvider;
use OAuthException;
use Phifty\Controller;
use OAuth;
use OAuthBundle\OAuthBundle;
use OAuthBundle\Controller\OAuth1\AccessTokenController;
use OAuthBundle\UserInfoFetcher;

class OAuthPlurkCallback extends AccessTokenController implements UserInfoFetcher
{
    public function getIdentity(array $userinfo, array $tokeninfo)
    {
        return $userinfo->user_info->id;
        // ->display_name
        //  ->nick_name
    }

    public function fetchUserInfo($client, array $tokeninfo)
    {
        $client->fetch('http://www.plurk.com/APP/Profile/getOwnProfile');
        $response = $client->getLastResponse();
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





