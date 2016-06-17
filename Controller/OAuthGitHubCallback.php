<?php
namespace OAuthBundle\Controller;
use Phifty\Controller;
use OAuthBundle\OAuthBundle;
use OAuthProvider\OAuthProvider;
use OAuthBundle\Controller\OAuth2\AccessTokenController;
use OAuthBundle\UserInfoFetcher;
use OAuth2;

class OAuthGitHubCallback extends AccessTokenController
{

    public function getIdentity(array $userInfo, array $tokenInfo)
    {
        return $userInfo['login'];
    }

    public function fetchUserInfo($client, array $tokenInfo)
    {
        $ret = $client->fetch('https://api.github.com/user', array() , OAuth2\Client::HTTP_METHOD_GET, array( 'User-Agent' => kernel()->getApplicationName() ) );
        return $ret['result'];
    }

    public function getConfig() {
        return $this->bundle->config('Providers.GitHub');
    }

    public function createProvider() {
        $config = $this->getConfig();
        return OAuthProvider::create('github',array(
            'client_id'     => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
    }

    public function getCallbackUrl() {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/github/callback');
    }

}


/*
var_dump($response, $response['result']);

array
  'result' => 
    array
      'user' => 
        array
          'plan' => 
            array
              ...
          'gravatar_id' => string '7490b4e3e9cb85a1f7dc0c8ea01a86e5' (length=32)
          'company' => string '' (length=0)
          'name' => string 'Yo-An Lin' (length=9)
          'created_at' => string '2009/02/01 07:20:08 -0800' (length=25)
          'location' => string 'Taipei' (length=6)
          'disk_usage' => int 79792
          'collaborators' => int 0
          'public_repo_count' => int 167
          'public_gist_count' => int 269
          'blog' => string 'http://c9s.me' (length=13)
          'following_count' => int 581
          'id' => int 50894
          'owned_private_repo_count' => int 0
          'private_gist_count' => int 9
          'type' => string 'User' (length=4)
          'permission' => null
          'total_private_repo_count' => int 0
          'followers_count' => int 259
          'login' => string 'c9s' (length=3)
          'email' => string 'cornelius.howl@gmail.com' (length=24)
  'code' => int 200
  'content_type' => string 'application/json; charset=utf-8' (length=31)
array
  'user' => 
    array
      'plan' => 
        array
          'name' => string 'free' (length=4)
          'collaborators' => int 0
          'space' => int 307200
          'private_repos' => int 0
      'gravatar_id' => string '7490b4e3e9cb85a1f7dc0c8ea01a86e5' (length=32)
      'company' => string '' (length=0)
      'name' => string 'Yo-An Lin' (length=9)
      'created_at' => string '2009/02/01 07:20:08 -0800' (length=25)
      'location' => string 'Taipei' (length=6)
      'disk_usage' => int 79792
      'collaborators' => int 0
      'public_repo_count' => int 167
      'public_gist_count' => int 269
      'blog' => string 'http://c9s.me' (length=13)
      'following_count' => int 581
      'id' => int 50894
      'owned_private_repo_count' => int 0
      'private_gist_count' => int 9
      'type' => string 'User' (length=4)
      'permission' => null
      'total_private_repo_count' => int 0
      'followers_count' => int 259
      'login' => string 'c9s' (length=3)
      'email' => string 'cornelius.howl@gmail.com' (length=24)
*/

