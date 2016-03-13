<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthProvider\OAuth2\OAuthFacebook as OAuthFacebookProvider;
use OAuthPlugin\OAuthPlugin;
use MemberBundle\CurrentMember;
use MemberBundle\Model\Member;
use Exception;
use Facebook;


/**
 * The default success handler:
 *
 *   dump related data structure
 */
class AuthenticationSuccessController extends Controller
{
    /**
     * @var Controller parent controller (callback controller object)
     */
    public $parent;

    public $client;

    public $provider;

    public $userInfo;

    /**
     * @var array { code: ... , access_token: ... }
     */
    public $tokenResult;

    /**
     * @var string access token string
     */
    public $accessToken;


    /**
     * @var OAuthPlugin\Model\Credential record object.
     */
    public $credential;


    /**
     * @var OAuthPluin\OAuthPlugin object instance.
     */
    public $bundle;

    /**
     * @var MemberBundle\Model\Member a Member object, which will be created if 
     * the 'AutoCreateMember' option is enabled.
     */
    public $member;

    public function __constructor() 
    {
        $this->bundle = OAuthPlugin::getInstance();
    }

    public function defaultHandler() {
        if ( $this->bundle->config('Debug') ) {
            $this->dump();
        }
        if ( $this->bundle->config('AutoCreateMember') ) {
            if ( ! $this->credential ) {
                throw new Exception('Empty credential object.');
            }
            if ( method_exists($this->parent, 'registerMember') ) {

                if ( $memberId = $this->credential->member_id ) {
                    // set current member login
                    $ret = $member->load( intval($memberId) );
                    if ( $ret->success ) {
                        // if the member record is not found, we should re-register the member.
                        $member->firstLogin = false;
                        $this->member = $member;
                    }
                }

                if ( ! $this->member ) {
                    $this->member = $this->parent->registerMember();
                }

                // the member record is created, we register the member record to the member's session.
                if ( $this->member->id ) {
                    $cMember = new CurrentMember;
                    $cMember->setRecord($this->member);
                } else {
                    throw new Exception('Can not member record not found.');
                }
            }
        }
    }

    /**
     * The property setting should be in __constructor (do this later)
     */
    public function indexAction()
    {
        $this->defaultHandler();
        return $this->render('@OAuthPlugin/success.html', [ '_controller' => $this ]);
    }


    public function dump() {
        echo "Provider:\n";
        var_dump( $this->provider );

        echo "UserInfo:\n";
        var_dump( $this->userInfo );

        echo "AccessToken:\n";
        var_dump( $this->accessToken );

        echo "TokenResult:\n";
        var_dump( $this->tokenResult );

        echo "Credential:\n";
        var_dump( $this->credential );
    }




}





