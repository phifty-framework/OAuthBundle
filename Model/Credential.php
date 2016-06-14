<?php
namespace OAuthPlugin\Model;



class Credential 
extends \OAuthPlugin\Model\CredentialBase
{

    /**
     * If there is an existing credential, then we should update it.
     *
     * @param string $providerName   provider name
     * @param string $appId  application id from the provider, one provider can have more than one application id.
     * @param string $accessToken  OAuth1 oauth_token or OAuth2 token
     */
    static public function loadCredential($providerName, $appId, $version, $accessToken, $identity, $tokenInfo)
    {
        $args = array(
            'provider'           => $providerName,
            'version'            => $version,
            'app_id'             => $appId,
            'identity'           => $identity,

            // extra information
            'access_token'       => $accessToken, // the access token

            'data'               => json_encode($tokenInfo),
        );

        if (isset($tokenInfo['access_token'])) {
            $args['access_token'] = $tokenInfo['access_token'];
        }
        if (isset($tokenInfo['refresh_token'])) {
            $args['refresh_token'] = $tokenInfo['refresh_token'];
        }
        if (isset($tokenInfo['expires_at'])) {
            $args['expires_at'] = $tokenInfo['expires_at'];
        }
        $record = new static;
        $record->createOrUpdate($args, ['provider','app_id','identity']);
        return $record;
    }
    
}
