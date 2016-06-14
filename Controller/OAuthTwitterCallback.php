<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuth;
use OAuthException;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth1\AccessTokenController;
use OAuthPlugin\UserInfoFetcher;

class OAuthTwitterCallback extends AccessTokenController implements UserInfoFetcher
{
    public function getIdentity(array $userinfo, array $tokenInfo)
    {
        // if we only use tokenInfo, we don't need to call an extra API.
        // return $tokenInfo['screen_name'] . '-' . $tokenInfo['user_id'];
        return $tokenInfo['user_id'];
    }

    /**
     * User info API
     * https://dev.twitter.com/docs/api/1.1/get/users/show
     */
    public function fetchUserInfo($client, array $accessTokenInfo)
    {
        $url = 'https://api.twitter.com/1.1/users/show.json?' . http_build_query(array(
            // either an user_id or a screen_name is required for this method call.
            'user_id' => $accessTokenInfo['user_id'],
            // 'screen_name' => $accessTokenInfo['screen_name'],
        ));
        $client->fetch($url);
        return json_decode($client->getLastResponse());
    }

    public function getConfig() {
        return $this->bundle->config('Providers.Twitter');
    }

    public function createProvider() {
        $config = $this->getConfig();
        return OAuthProvider::create('twitter',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
    }

    public function getCallbackUrl() {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/twitter/callback');
    }
}


/*
access token => array
  'oauth_token' => string '' (length=50)
  'oauth_token_secret' => string '' (length=42)
  'user_id' => string '783117' (length=6)
  'screen_name' => string 'c9s' (length=3)
*/



