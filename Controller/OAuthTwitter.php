<?php
namespace OAuthBundle\Controller;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuth;
use OAuthException;
use OAuthBundle\OAuthBundle;
use OAuthBundle\Controller\OAuth1\RequestTokenController;

class OAuthTwitter extends RequestTokenController
{
    public function indexAction()
    {
        $bundle = OAuthBundle::getInstance();
        $config = $bundle->config('Providers.Twitter');
        $provider = OAuthProvider::create('twitter',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() .  ($config->CallbackUrl ?: '/oauth/twitter/callback');
        return $this->runRequestToken( $provider, $callbackUrl );
    }



}
