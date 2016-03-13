<?php
namespace OAuthPlugin\Controller\OAuth1;
use Phifty\Controller;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\OAuthPlugin;
use OAuth;
use OAuthException;

class RequestTokenController extends Controller
{

    public function runRequestToken($provider,$callbackUrl)
    {
        $bundle = OAuthPlugin::getInstance();
        $session = kernel()->session;

        try {
            $oauth = new OAuth( $provider->getConsumerKey() , $provider->getConsumerSecret() );
            // $oauth->enableDebug();
            $requestTokenInfo = $oauth->getRequestToken( $provider->getRequestTokenUrl() , $callbackUrl );

            if(!empty($requestTokenInfo)) {
                // var_dump( $requestTokenInfo ); 
                $session['secret'] = $requestTokenInfo['oauth_token_secret'];
                $session['state'] = 1;

                $url = $provider->getAuthorizeUrl() . '?oauth_token=' . $requestTokenInfo['oauth_token'];
                return $this->forward( $bundle->config('Redirect') , 'index' , array(
                    'provider' => $provider,
                    'url' => $url,
                    'seconds' => $bundle->config('RequestRedirectDelay') ?: 0
                ));

            } else {
                print "Failed fetching request token, response was: " . $oauth->getLastResponse();
            }
        } catch(OAuthException $e) {
            return $this->forward( 'OAuthPlugin\Controller\AuthenticationError','index',array(
                'message' => $e->lastResponse,
                'e' => $e,
            ));
        }


    }
}
