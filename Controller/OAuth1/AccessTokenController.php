<?php
namespace OAuthBundle\Controller\OAuth1;
use OAuthProvider\OAuthProvider;
use OAuthException;
use Phifty\Controller;
use OAuthBundle\Model\Credential;
use OAuth;
use OAuthBundle\Controller\BaseAccessTokenController;

abstract class AccessTokenController extends BaseAccessTokenController
{
    public function runAccessToken($provider, $callbackUrl)
    {
        $this->provider = $provider;
        $session = kernel()->session;

        $this->client = new OAuth( $provider->getConsumerKey() , $provider->getConsumerSecret() );

        if ( $this->bundle->config('Debug') ) {
            $this->client->enableDebug();
        }

        $this->client->setToken($_GET['oauth_token'],$session['secret']);

        $this->tokenResult = $this->client->getAccessToken( $provider->getAccessTokenUrl() );

        $this->client->setToken( $this->tokenResult['oauth_token'], $this->tokenResult['oauth_token_secret']);

        $this->accessToken = $this->tokenResult['oauth_token'];

        $this->userInfo = $this->fetchUserInfo($this->client,$this->tokenResult);

        $identity = $this->getIdentity($this->userInfo,$this->tokenResult);
        $this->credential = Credential::loadCredential(
            $provider->getName(),
            $provider->getConsumerKey(),
            '1.0',
            $this->accessToken,
            $identity,
            $this->tokenResult );

        kernel()->session[ $provider->getName() ] = array(
            'info'     => $this->userInfo,
            'identity' => $identity,
            'token'    => $this->accessToken,
            'credential_id' => $this->credential->id,
        );
    }

}
