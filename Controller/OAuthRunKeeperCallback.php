<?php
namespace OAuthBundle\Controller;
use Phifty\Controller;
use OAuthBundle\OAuthBundle;
use OAuthProvider\OAuthProvider;
use OAuthBundle\Controller\OAuth2\AccessTokenController;
use OAuth2;
use OAuthBundle\UserInfoFetcher;

class OAuthRunKeeperCallback extends AccessTokenController implements UserInfoFetcher
{
    public function getIdentity(array $userInfo, array $tokenInfo)
    {
        preg_match('#runkeeper\.com/user/(\d+)#', $userInfo['profile'], $regs);
        $userId = $regs[1];
        return $userId;
    }

    /*
    curl -H "Authorization: Bearer 368a162d2c5e4fbdb070121deaf80f3b" -H "Accept:application/vnd.com.runkeeper.Profile+json" https://api.runkeeper.com/profile

    {
        "profile" : "http://runkeeper.com/user/1122458230",
        "athlete_type" : "Runner",
        "birthday" : "Tue, 3 Sep 1985 00:00:00",
        "normal_picture" : "http://graph.facebook.com/614411714/picture?type=large",
        "elite" : "false",
        "medium_picture" : "http://graph.facebook.com/614411714/picture?type=small",
        "gender" : "M",
        "name" : "Yo-An Lin"
    }
    */
    public function fetchUserInfo($client, array $tokenInfo) 
    {
        $ret = $client->fetch('https://api.runkeeper.com/profile',array(), \OAuth2\Client::HTTP_METHOD_GET, array(
            "Accept" => "application/vnd.com.runkeeper.Profile+json",
        ));
        return $ret['result'];
    }


    public function getConfig()
    {
        return $this->bundle->config('Providers.RunKeeper');
    }

    public function createProvider()
    {
        $config = $this->getConfig();
        return OAuthProvider::create('runkeeper',array(
            'client_id' => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
    }

    public function getCallbackUrl()
    {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/runkeeper/callback');
    }

}

