<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth2\RequestTokenController;
use OAuth2;
use OAuthPlugin\Model\Credential;

class OAuthRunKeeper extends RequestTokenController
{

    public function createProvider()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.RunKeeper');
        $provider = OAuthProvider::create('runkeeper',array(
            'client_id'     => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
        return $provider;
    }

    public function indexAction()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.RunKeeper');
        $provider = OAuthProvider::create('runkeeper',array(
            'client_id'     => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/runkeeper/callback');
        return $this->runRequestToken($provider, $callbackUrl);
    }


    public function deauthorizeAction() {
        $provider = $this->createProvider();
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.RunKeeper');
        if ( isset(kernel()->session[ $provider->getName() ]) ) {
            $info = kernel()->session[ $provider->getName() ];

            $credential = new Credential($info['credential_id']);

            $url = $provider->getDeauthorizeUrl();
            $client = new \OAuth2\Client( $provider->getClientId() , $provider->getClientSecret() );
            $client->setAccessToken($credential->token);
            $client->setAccessTokenType( \OAuth2\Client::ACCESS_TOKEN_URI );
            $response = $client->fetch($url, array(), \OAuth2\Client::HTTP_METHOD_POST);
            if ( $credential->id ) {
                $credential->delete();
            }
        }
        $callbackUrl = kernel()->getBaseUrl() . ($config->DeauthCallbackUrl ?: '/');
        return $this->redirect($callbackUrl);
    }

}

