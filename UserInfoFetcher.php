<?php
namespace OAuthBundle;

interface UserInfoFetcher {
    /**
     * Method to fetch user info
     *
     * @param $client maybe a OAuth\Client or OAuth2\Client object
     * @param array $tokenInfo
     */
    public function fetchUserInfo($client, array $tokenInfo);

    /**
     * This method should return a structure:
     *
     * @return array [ access_token => .... ]
     */
    public function getIdentity(array $userInfo, array $tokenInfo);
}

