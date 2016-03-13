<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth2\RequestTokenController;
use OAuth2;
use OAuthPlugin\Model\Credential;

class OAuthStrava extends RequestTokenController
{

    public function createProvider() {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Strava');
        $provider = OAuthProvider::create('strava',array(
            'client_id' => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
        return $provider;
    }

    public function indexAction()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Strava');
        $provider = $this->createProvider();
        $callbackUrl = kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/strava/callback');
        return $this->runRequestToken($provider, $callbackUrl, array(
            // 'scope' => ($config->Scopes ?: array('public')),
        ));
    }

    public function deauthorizeAction() {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Strava');
        $provider = $this->createProvider();
        if ( isset(kernel()->session[ $provider->getName() ]) ) {
            $info = kernel()->session[ $provider->getName() ];

            $credential = new Credential($info['credential_id']);

            $url = $provider->getDeauthorizeUrl();
            $client = new \OAuth2\Client( $provider->getClientId() , $provider->getClientSecret() );
            $client->setAccessToken($credential->token);
            $client->setAccessTokenType( \OAuth2\Client::ACCESS_TOKEN_BEARER );
            $response = $client->fetch($url, array(), \OAuth2\Client::HTTP_METHOD_POST, array(), \OAuth2\Client::HTTP_FORM_CONTENT_TYPE_MULTIPART );
            if ( isset($response['result']['access_token']) ) {
                $token = $response['result']['access_token'];
                if ( $credential->id ) {
                    $credential->delete();
                }
            }
        }

        $callbackUrl = kernel()->getBaseUrl() . ($config->DeauthCallbackUrl ?: '/');
        return $this->redirect($callbackUrl);
    }
}

