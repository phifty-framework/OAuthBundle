<?php
namespace OAuthPlugin\Controller\OAuth2;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;
use OAuthProvider\OAuthProvider;
use OAuth2;

class RequestTokenController extends Controller 
{


    public function runRequestToken($provider,$callbackUrl, $extraParameters = array() )
    {
        $bundle = OAuthPlugin::getInstance();
        $client = new OAuth2\Client( $provider->getClientId() , $provider->getClientSecret() );
        $url = $client->getAuthenticationUrl( $provider->getAuthorizeUrl()  , $callbackUrl, $extraParameters );
        return $this->forward( $bundle->config('Redirect') , 'index' , array(
            'provider' => $provider,
            'url' => $url,
            'seconds' => $bundle->config('RequestRedirectDelay') ?: 0
        ));
    }
}
