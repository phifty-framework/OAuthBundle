<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;
use OAuthProvider\OAuthProvider;
use OAuthPlugin\Controller\OAuth2\AccessTokenController;
use OAuth2;
use OAuthPlugin\MemberRegisterable;
use OAuthPlugin\UserInfoFetcher;
use MemberBundle\Model\Member;
use Exception;

class OAuthFacebookCallback extends AccessTokenController implements MemberRegisterable, UserInfoFetcher
{

    public function getIdentity(array $userInfo, array $tokenInfo)
    {
        // return $userInfo['username'] . '-' . $userInfo['id'];
        return $userInfo['id'];
    }

    public function fetchUserInfo($client, array $tokenInfo)
    {
        // Adding fields means if you only needs one or two more fields.
        // $ret = $client->fetch('https://graph.facebook.com/me', array('fields' => 'name,email') );
        $ret = $client->fetch('https://graph.facebook.com/me');
        return $ret['result'];
    }


    public function getConfig() {
        return $this->bundle->config('Providers.Facebook');
    }

    public function createProvider() {
        $config = $this->getConfig();
        return OAuthProvider::create('facebook',array(
            'client_id'     => $config->ClientId,
            'client_secret' => $config->ClientSecret,
        ));
    }

    public function getCallbackUrl() {
        $config = $this->getConfig();
        return kernel()->getBaseUrl() . ($config->CallbackUrl ?: '/oauth/facebook/callback');
    }


    /**
     * This method returns a Member object, but it does not update CurrentMember's current record.
     */
    public function registerMember(array $userInfo)
    {
        $member = new Member;

        // if the Credential record is already connected with a member record, 
        // we should simply load the CurrentMember

        if ($memberId = $this->credential->member_id) {
            // set current member login
            $ret = $member->load( intval($memberId) );
            if ( $ret->success ) {
                // if the member record is not found, we should re-register the member.
                $member->firstLogin = false;
                return $member;
            }
        }

        /**
         * The registration process:
         *
         * @var array $this->userInfo
            array(
                'id' => string '614411714' (length=9)
                'name' => string 'Yo-An Lin' (length=9)
                'first_name' => string 'Yo-An' (length=5)
                'last_name' => string 'Lin' (length=3)
                'link' => string 'http://www.facebook.com/yoan.lin' (length=32)
                'username' => string 'yoan.lin' (length=8)
                'gender' => string 'male' (length=4)
                'timezone' => int 8
                'locale' => string 'zh_TW' (length=5)
                'verified' => boolean true
                'updated_time' => string '2012-02-02T19:13:21+0000' (length=24)
            )
        */

        /* TODO: get picture from facebook */
        // https://graph.facebook.com/{user_id}/picture


        // Merge the registered member by e-mail
        //
        // 當會員已經手動註冊過，卻又點擊 FB OAuth 時，
        // 就會發生沒有 credential 卻已經有member的情況
        // 這時候不需要重新建立會員資料
        $member->load(array('email' => $this->userInfo['email']));
        if (!$member->id) {
            $ret = $member->create(array(
                'name'      => $this->userInfo['name'],
                'nickname'  => $this->userInfo['name'],
                'email'     => $this->userInfo['email'],
                'confirmed' => true,
                'password'  => sha1(microtime()), // generate a random password
                'auth_token' => sha1(microtime()),
            ));
            if ($ret->error) {
                throw new Exception($ret->message);
            }
        }
        if (!$member->id) {
            throw new Exception("Can not create member.");
        }
        // we use rawCreate method to create record, so we need to reload the full data.
        $member->reload();
        $member->firstLogin = true;

        // update the existing credential with the new member id
        $this->credential->update(array( 'member_id' => $member->id ));
        return $member;
    }


}


/*
array
  'result' =>
    array
      'id' => string '614411714' (length=9)
      'name' => string 'Yo-An Lin' (length=9)
      'first_name' => string 'Yo-An' (length=5)
      'last_name' => string 'Lin' (length=3)
      'link' => string 'http://www.facebook.com/yoan.lin' (length=32)
      'username' => string 'yoan.lin' (length=8)
      'gender' => string 'male' (length=4)
      'timezone' => int 8
      'locale' => string 'zh_TW' (length=5)
      'verified' => boolean true
      'updated_time' => string '2011-11-20T23:16:20+0000' (length=24)
  'code' => int 200
  'content_type' => string 'text/javascript; charset=UTF-8' (length=30)
array
  'id' => string '614411714' (length=9)
  'name' => string 'Yo-An Lin' (length=9)
  'first_name' => string 'Yo-An' (length=5)
  'last_name' => string 'Lin' (length=3)
  'link' => string 'http://www.facebook.com/yoan.lin' (length=32)
  'username' => string 'yoan.lin' (length=8)
  'gender' => string 'male' (length=4)
  'timezone' => int 8
  'locale' => string 'zh_TW' (length=5)
  'verified' => boolean true
  'updated_time' => string '2011-11-20T23:16:20+0000' (length=24)
*/
