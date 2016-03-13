<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;
use OAuthProvider\OAuthProvider;
use OAuth2;
use OAuthPlugin\Model\Credential;
use Exception;

abstract class BaseAccessTokenController extends Controller
{

    /**
     * @var OAuthProvider OAuth provider object.
     */
    public $provider;


    /**
     * @var array
     */
    public $userInfo;

    /**
     * @var array { code: ... , access_token: ... }
     */
    public $tokenResult;

    /**
     * @var string access token
     */
    public $accessToken;

    /**
     * @var OAuthPlugin\Model\Credential record object.
     */
    public $credential;


    /**
     * @var OAuth2\Client object or OAuth1 Client object
     */
    public $client;


    /**
     * @var OAuthPlugin\OAuthPlugin object instance
     */
    public $bundle;

    /**
     * This method should return a structure:
     *
     * @return array [ access_token => .... ]
     */
    abstract public function fetchUserInfo($client,$tokenInfo);

    abstract public function getIdentity($userInfo,$tokenInfo);


    public function __construct()
    {
        $this->bundle = OAuthPlugin::getInstance();
        $this->provider = $this->createProvider();
    }


    abstract public function runAccessToken($provider, $callbackUrl);

    abstract public function createProvider();

    public function getCallbackUrl()
    {
        return kernel()->getBaseUrl() . '/oauth/' . strtolower($this->provider->getName());
    }

    public function indexAction()
    {
        try {
            $this->runAccessToken( $this->provider, $this->getCallbackUrl() );
            return $this->successAction();
        } catch( Exception $e ) {
            return $this->exceptionAction($e);
        }
    }

    public function registerSession()
    {
        kernel()->session[ $this->provider->getName() ] = array(
            'info'            => $this->userInfo,
            'access_token'    => $this->accessToken,
            'credential_id'   => $this->credential->id,
        );
    }

    /**
     * In the subclass, users can override this action method, by default we 
     * forward parameters to the AuthenticationSuccessController
     */
    public function successAction()
    {
        $controllerClass = $this->bundle->config('Success');
        if ( ! $controllerClass ) {
            throw new Exception("Success Authentication Controller is not defined.");
        }
        $controller = new $controllerClass;
        $controller->client = $this->client;
        $controller->provider = $this->provider;
        $controller->userInfo = $this->userInfo;
        $controller->tokenResult = $this->tokenResult;
        $controller->accessToken = $this->accessToken;
        $controller->credential = $this->credential;
        $controller->parent = $this;
        return $this->forward( $controller ,'index',array(
            'provider'    => $this->provider,
            'accessToken' => $this->tokenResult,
            'userInfo'    => $this->userInfo,
            'credential'  => $this->credential,
        ));
    }


    /**
     * Controller action method that handles the exception object.
     *
     * @param Exception
     */
    public function exceptionAction($e)
    {
        $controllerClass = $this->bundle->config('Error');
        $controller = new $controllerClass;
        $controller->provider = $this->provider;
        $controller->parent = $this;
        return $this->forward( $controller ,'index',array(
            'exception' => $e,
        ));
    }


}

