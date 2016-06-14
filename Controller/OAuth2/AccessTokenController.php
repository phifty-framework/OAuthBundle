<?php
namespace OAuthPlugin\Controller\OAuth2;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;
use OAuthProvider\OAuthProvider;
use OAuth2;
use OAuthPlugin\Model\Credential;
use OAuthPlugin\Controller\BaseAccessTokenController;
use Exception;

class AuthenticationException extends Exception {

    public $response;
    public function __construct($msg, array $response) {
        parent::__construct($msg);
        $this->response = $response;
    }

}

abstract class AccessTokenController extends BaseAccessTokenController
{

    /**
     * @var string Used in OAuth2, this token is used to refresh the access token.
     */
    public $refreshToken;



    /**
     * @var OAuthProvider object
     */
    public function createOAuthClient($provider) {
        return new \OAuth2\Client($provider->getClientId() , $provider->getClientSecret());
    }

    /**
     * In this method, we firstly get the access token and register the response into session
     *
     *    $_SESSION[ provider id ] = array(
     *              'info'     => $userInfo,
     *              'token'    => $accessToken
     *              'credential_id' => $credential->id,
     *    );
     *
     * In the subclass controller, the getIdentity method must be implemented in order to 
     * load/create credential by "identity".
     *
     * and the access token is serialized into "Credential"."info" column.
     *
     * @param OAuth2\OAuth2Provider $provider Provider Object
     * @param string $callbackUrl
     */
    public function runAccessToken($provider, $callbackUrl)
    {


        // save provider
        $this->provider = $provider;
        $this->client = $this->createOAuthClient($provider);

        if ( ! isset( $_GET['code']) ) {
            throw new Exception("authorization code is not defined.");
        }

        $params = array('code' => $_GET['code'], 'redirect_uri' => $callbackUrl );

        // XXX: get refresh_token to prevent token expiring...

        $response = $this->client->getAccessToken( $provider->getAccessTokenUrl()  , 'authorization_code', $params);
        // $response = $this->client->getAccessToken(  .... , 'client_credentials', $params);

        // parse access token response
        if ( isset($response['result']['errors']) ) {
            throw new AuthenticationException($response['message'], $response);
        } elseif ( isset($response['result']['error']) ) {
            throw new AuthenticationException($response['result']['error']['message'], $response);
        }

        if ( is_array($response['result']) ) {
            $this->tokenResult = $response['result'];
        } elseif ( is_string($response['result']) ) {
            parse_str($response['result'], $this->tokenResult);
        } else {
            error_log("Unsupported OAuth token result type.");
        }

        /**
            array (size=3)
  'result' => 
    array (size=1)
      'error' => 
        array (size=3)
          'message' => string 'This IP can't make requests for that application.' (length=49)
          'type' => string 'OAuthException' (length=14)
          'code' => int 5
  'code' => int 400
  'content_type' => string 'application/json; charset=UTF-8' (length=31)
         */
        if ( isset($this->tokenResult['refresh_token']) ) {
            $this->refreshToken = $this->tokenResult['refresh_token'];
        }
        if ( isset($this->tokenResult['access_token']) ) {
            $this->accessToken = $this->tokenResult['access_token'];
        }
        if ( ! $this->accessToken ) {
            throw new Exception("No access token returned.");
        }

        // set the access token so we can ask more information through the API.
        $this->client->setAccessToken($this->accessToken);

        // ask the user info API provided by oauth provider, this method is implemented in sub-class.
        $this->userInfo = $this->fetchUserInfo($this->client,$this->tokenResult);

        // get the user identity from API so we can distinguish the credential record.
        $identity = $this->getIdentity($this->userInfo, $this->tokenResult);
        $this->credential = Credential::loadCredential(
            $provider->getName(),
            $provider->getClientId(),
            '2.0',
            $this->accessToken,
            $identity,
            $this->tokenResult
        );
        $this->registerSession();
    }

    public function registerSession()
    {
        kernel()->session[ $this->provider->getName() ] = array(
            'info'            => $this->userInfo,
            'access_token'    => $this->accessToken,
            'credential_id'   => $this->credential->id,
        );
    }
}

