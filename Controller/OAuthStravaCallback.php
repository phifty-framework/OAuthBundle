<?php
namespace OAuthBundle\Controller;
use Phifty\Controller;
use OAuthBundle\OAuthBundle;
use OAuthProvider\OAuthProvider;
use OAuthBundle\Controller\OAuth2\AccessTokenController;
use OAuth2;

class OAuthStravaCallback extends AccessTokenController
{

    /**
     *
     *  {
     *      "access_token": "83ebeabdec09f6670863766f792ead24d61fe3f9",
     *      "athlete": {
     *          "id": 227615,
     *          "resource_state": 3,
     *          "firstname": "John",
     *          "lastname": "Applestrava",
     *          "profile_medium": "http://pics.com/227615/medium.jpg",
     *          "profile": "http://pics.com/227615/large.jpg",
     *          "city": "San Francisco",
     *          "state": "California",
     *          "country": "United States",
     *          "sex": "M",
     *          "friend": null,
     *          "follower": null,
     *          "premium": true,
     *          "created_at": "2008-01-01T17:44:00Z",
     *          "updated_at": "2013-09-04T20:00:50Z",
     *          "follower_count": 273,
     *          "friend_count": 19,
     *          "mutual_friend_count": 0,
     *          "date_preference": "%m/%d/%Y",
     *          "measurement_preference": "feet",
     *          "email": "john@applestrava.com",
     *          "clubs": [ ],
     *          "bikes": [ ],
     *          "shoes": [ ]
     *      }
     *  }
     *
     */
    public function getIdentity(array $userInfo, array $tokenInfo)
    {
        return $userInfo['id'];
    }

    public function fetchUserInfo($client, array $tokenInfo)
    {
        return $tokenInfo['athlete'];
    }

    public function getConfig()
    {
        return $this->bundle->config('Providers.Strava');
    }

    public function createProvider()
    {
        $config = $this->getConfig();
        return OAuthProvider::create('strava',array(
            'client_id' => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
    }

    public function getCallbackUrl()
    {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/strava/callback');
    }
}

