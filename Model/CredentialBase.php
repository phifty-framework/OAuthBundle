<?php
namespace OAuthPlugin\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class CredentialBase
    extends BaseModel
{
    const SCHEMA_CLASS = 'OAuthPlugin\\Model\\CredentialSchema';
    const SCHEMA_PROXY_CLASS = 'OAuthPlugin\\Model\\CredentialSchemaProxy';
    const COLLECTION_CLASS = 'OAuthPlugin\\Model\\CredentialCollection';
    const MODEL_CLASS = 'OAuthPlugin\\Model\\Credential';
    const TABLE = 'credentials';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM credentials WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'provider',
      2 => 'version',
      3 => 'app_id',
      4 => 'identity',
      5 => 'data',
      6 => 'member_id',
      7 => 'expires_in',
      8 => 'access_token',
      9 => 'refresh_token',
    );
    public static $column_hash = array (
      'id' => 1,
      'provider' => 1,
      'version' => 1,
      'app_id' => 1,
      'identity' => 1,
      'data' => 1,
      'member_id' => 1,
      'expires_in' => 1,
      'access_token' => 1,
      'refresh_token' => 1,
    );
    public static $mixin_classes = array (
    );
    protected $table = 'credentials';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('OAuthPlugin\\Model\\CredentialSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getProvider()
    {
            return $this->get('provider');
    }
    public function getVersion()
    {
            return $this->get('version');
    }
    public function getAppId()
    {
            return $this->get('app_id');
    }
    public function getIdentity()
    {
            return $this->get('identity');
    }
    public function getData()
    {
            return $this->get('data');
    }
    public function getMemberId()
    {
            return $this->get('member_id');
    }
    public function getExpiresIn()
    {
            return $this->get('expires_in');
    }
    public function getAccessToken()
    {
            return $this->get('access_token');
    }
    public function getRefreshToken()
    {
            return $this->get('refresh_token');
    }
}
