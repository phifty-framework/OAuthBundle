<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth2\RequestTokenController;
use OAuthProvider\OAuthProvider;
use OAuth2;

class OAuthGitHub extends RequestTokenController
{
    function indexAction() 
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.GitHub');
        $provider = OAuthProvider::create('github',array(
            'client_id' => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() . ( $config->CallbackUrl ?: '/oauth/github/callback' );
        return $this->runRequestToken($provider, $callbackUrl);
    }
}

