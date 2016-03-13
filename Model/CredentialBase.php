<?php
namespace OAuthPlugin\Model;

class CredentialBase  extends \Phifty\Model {
const schema_proxy_class = 'OAuthPlugin\\Model\\CredentialSchemaProxy';
const collection_class = 'OAuthPlugin\\Model\\CredentialCollection';
const model_class = 'OAuthPlugin\\Model\\Credential';
const table = 'credentials';

public static $column_names = array (
  0 => 'provider',
  1 => 'version',
  2 => 'app_id',
  3 => 'identity',
  4 => 'data',
  5 => 'member_id',
  6 => 'expires_in',
  7 => 'access_token',
  8 => 'refresh_token',
  9 => 'id',
);
public static $column_hash = array (
  'provider' => 1,
  'version' => 1,
  'app_id' => 1,
  'identity' => 1,
  'data' => 1,
  'member_id' => 1,
  'expires_in' => 1,
  'access_token' => 1,
  'refresh_token' => 1,
  'id' => 1,
);
public static $mixin_classes = array (
);



    /**
     * Code block for message id parser.
     */
    private function __() {
            }
}
