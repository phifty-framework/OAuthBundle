<?php
namespace OAuthPlugin\Controller;
use OAuthProvider\OAuthProvider;
use OAuth;
use OAuthException;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth1\RequestTokenController;

class OAuthPlurk extends RequestTokenController
{
    public function indexAction()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Plurk');
        $provider = OAuthProvider::create('plurk',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/plurk/callback');
        return $this->runRequestToken( $provider, $callbackUrl );
    }
}

