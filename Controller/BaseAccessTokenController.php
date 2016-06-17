<?php
namespace OAuthBundle\Controller;
use Phifty\Controller;
use OAuthBundle\OAuthBundle;
use OAuthProvider\OAuthProvider;
use OAuth2;
use OAuthBundle\Model\Credential;
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
     * @var OAuthBundle\Model\Credential record object.
     */
    public $credential;


    /**
     * @var OAuth2\Client object or OAuth1 Client object
     */
    public $client;


    /**
     * @var OAuthBundle\OAuthBundle object instance
     */
    public $bundle;



    public function __construct()
    {
        $this->bundle = OAuthBundle::getInstance();
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
        $this->runAccessToken($this->provider, $this->getCallbackUrl());
        return $this->successAction();
        // XXX: 

        /* customizable exception handling
        try {
        } catch( Exception $e ) {
            return $this->exceptionAction($e);
        }
         */
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
        return $this->forward($controller, 'index',array(
            'exception' => $e,
        ));
    }


}

