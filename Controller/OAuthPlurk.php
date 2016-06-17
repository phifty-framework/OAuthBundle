<?php
namespace OAuthBundle\Controller;
use OAuthProvider\OAuthProvider;
use OAuth;
use OAuthException;
use OAuthBundle\OAuthBundle;
use OAuthBundle\Controller\OAuth1\RequestTokenController;

class OAuthPlurk extends RequestTokenController
{
    public function indexAction()
    {
        $bundle = OAuthBundle::getInstance();
        $config = $bundle->config('Providers.Plurk');
        $provider = OAuthProvider::create('plurk',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/plurk/callback');
        return $this->runRequestToken( $provider, $callbackUrl );
    }
}

