<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuth;
use OAuthException;
use OAuthPlugin\OAuthPlugin;
use OAuthPlugin\Controller\OAuth1\RequestTokenController;

class OAuthTwitter extends RequestTokenController
{
    public function indexAction()
    {
        $bundle = OAuthPlugin::getInstance();
        $config = $bundle->config('Providers.Twitter');
        $provider = OAuthProvider::create('twitter',array(
            'consumer_key' => $config->ConsumerKey,
            'consumer_secret' => $config->ConsumerSecret,
        ));
        $callbackUrl = kernel()->getBaseUrl() .  ($config->CallbackUrl ?: '/oauth/twitter/callback');
        return $this->runRequestToken( $provider, $callbackUrl );
    }



}
