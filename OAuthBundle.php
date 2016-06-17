<?php

/**
 * OAuth Plugin
 **/
namespace OAuthBundle;
use Phifty\Bundle;
use Phifty\ComposerConfigBridge;
use Facebook;


/*
  OAuthBundle:
    Redirect: VStock\Controller\OAuthRedirectController
    Success: VStock\Controller\OAuthSuccessController
    Error: VStock\Controller\OAuthErrorController
    Providers:
      Plurk:
        ConsumerKey:
        ConsumerSecret:
      Twitter:
        ConsumerKey:
        ConsumerSecret:
      Facebook:
        ClientId:
        ClientSecret:
        CallbackUrl: '/oauth/facebook/callback'
      GitHub:
        ClientId:
        ClientSecret:
        CallbackUrl: '/oauth/github/callback'
 */
class OAuthBundle extends Bundle
    implements ComposerConfigBridge
{

    public function createFacebookApp() {
        $config = $this->config('Providers.Facebook')->config;
        return new Facebook(array(
            'appId' => $config['ClientId'],
            'secret' => $config['ClientSecret'],
            'cookie' => true,
        ));
    }

    public function defaultConfig()
    {
        return array(
            'Redirect' => '\OAuthBundle\Controller\RedirectController',
            'Success'  => '\OAuthBundle\Controller\AuthenticationSuccessController',
            'Error'    => '\OAuthBundle\Controller\AuthenticationErrorController',
            'RequestRedirectDelay' => 0,
        );
    }

    public function init()
    {
        $this->addRecordAction('Credential');

        if ( $this->config('Providers.Twitter') ) {
            $this->route('/oauth/twitter', 'OAuthTwitter');
            $this->route('/oauth/twitter/callback', 'OAuthTwitterCallback' );
        }

        if ( $this->config('Providers.Plurk') ) {
            $this->route('/oauth/plurk', 'OAuthPlurk' );
            $this->route('/oauth/plurk/callback', 'OAuthPlurkCallback' );
        }

        if ( $this->config('Providers.Facebook') ) {
            $this->route('/oauth/facebook', 'OAuthFacebook' );
            $this->route('/oauth/facebook/callback', 'OAuthFacebookCallback' );
        }

        if ( $this->config('Providers.NikePlus') ) {
            $this->route('/oauth/nikeplus', 'OAuthNikePlus' );
            $this->route('/oauth/nikeplus/callback', 'OAuthNikePlusCallback' );
        }

        if ( $this->config('Providers.Strava') ) {
            $this->route('/oauth/strava', 'OAuthStrava' );
            $this->route('/oauth/strava/callback', 'OAuthStravaCallback' );
            $this->route('/oauth/strava/deauthorize', 'OAuthStrava:deauthorize' );
        }

        if ( $this->config('Providers.RunKeeper') ) {
            $this->route('/oauth/runkeeper', 'OAuthRunKeeper' );
            $this->route('/oauth/runkeeper/callback', 'OAuthRunKeeperCallback' );
            $this->route('/oauth/runkeeper/deauthorize', 'OAuthRunKeeper:deauthorize' );
        }

        if ( $this->config('Providers.GitHub') ) {
            $this->route('/oauth/github', 'OAuthGitHub' );
            $this->route('/oauth/github/callback', 'OAuthGitHubCallback' );
        }
    }

    public function getComposerDependency()
    {
        return [
            "corneltek/oauth-provider" => "~1",
            "corneltek/oauth2"         => "~1",
            "facebook/php-sdk-v4"      => "~5.0",
        ];
    }
}

