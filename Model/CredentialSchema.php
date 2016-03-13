<?php
namespace OAuthPlugin\Model;
use LazyRecord\Schema\SchemaDeclare;

class CredentialSchema extends SchemaDeclare
{

    public function schema()
    {
        $this->column('provider')
            ->required()
            ->varchar(24)
            ->label('OAuth Provider Name')
            ;

        // OAuth version
        $this->column('version')
            ->required()
            ->varchar(3)
            ->label('OAuth Version')
            ;

        $this->column('app_id')
            ->required()
            ->varchar(128)
            ->label('App Primary Key')
            ;

        $this->column('identity')
            ->required()
            ->varchar(128)
            ->label('Identity')
            ;  // user_id from remote service


        /* access token response */
        $this->column('data')
            ->text()
            ->label('Response Data')
            ; // access token info (json encode)

        $this->column('member_id')
            ->integer()
            ->label('Member Id')
            ;  // local user id

        // in seconds
        $this->column('expires_in')
            ->integer()
            ->label('Expires In')
            ;

        $this->column('access_token')
            ->required()
            ->varchar(256)
            ->label('Access Token')
            ;


        // if we have one.
        $this->column('refresh_token')
            ->varchar(128)
            ->label('Refresh Token')
            ;

        $this->belongsTo('member', 'MemberBundle\\Model\\MemberSchema', 'id', 'member_id' );
    }

}
