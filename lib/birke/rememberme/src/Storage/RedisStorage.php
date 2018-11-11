<?php

namespace Birke\Rememberme\Storage;

use Redis;

/**
 * Redis-Based Storage
 */
class RedisStorage implements StorageInterface
{
    /** @var Redis  */
    private $redis;

    /** @var string  */
    private $key_prefix;

    /**
     * RedisStorage constructor.
     * @param Redis $redis
     * @param string $key_prefix - a string to prepend before all keys
     */
    public function __construct(Redis $redis, string $key_prefix)
    {
        $this->redis = $redis;
        $this->key_prefix = $key_prefix;
    }

    /**
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @return [int, array]
     */
    public function findTriplet($credential, $token, $persistentToken)
    {
        $originalPersistentToken = $persistentToken;

        // Hash the tokens, because they can contain a salt and can be accessed in the file system
        $persistentToken = sha1($persistentToken);
        $token = sha1($token);
        $keyName = $this->getKeyName($credential, $persistentToken);

        $storedValue = $this->redis->get($keyName);

        if ($storedValue === false) {
            return [self::TRIPLET_NOT_FOUND, null];
        }

        $data = json_decode_safe($storedValue);

        $fileToken = $data['token'];
        $encrypted_user_data = $data['encrypted_user_data'];

        $jsonUserData = openssl_decrypt(
            $encrypted_user_data,
            'aes-256-ctr',
            $originalPersistentToken,
            0,
            str_pad(substr($credential, 0, 16), 16)
        );

        $user_data = json_decode_safe($jsonUserData);
        if ($fileToken == $token) {
            return [self::TRIPLET_FOUND, $user_data];
        }

        return [self::TRIPLET_INVALID, null];
    }

    /**
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     * @param array  $user_data
     * @return $this
     */
    public function storeTriplet($credential, $token, $persistentToken, $expire, array $user_data = [])
    {
        $originalPersistentToken = $persistentToken;

        // Hash the tokens, because they can contain a salt and can be accessed in the file system
        $persistentToken = sha1($persistentToken);
        $token = sha1($token);

        $keyName = $this->getKeyName($credential, $persistentToken);

        $data = [];
        $data['token'] = $token;

        $jsonUserData = json_encode($user_data);

        $encryptedJsonUserData = openssl_encrypt(
            $jsonUserData,
            'aes-256-ctr',
            $originalPersistentToken,
            0, // $options
            str_pad(substr($credential, 0, 16), 16)  // $iv
        );

        $data['encrypted_user_data'] = $encryptedJsonUserData;
        $string = json_encode($data);

        $ttl = $expire - time();
        $this->redis->setex($keyName, $ttl, $string);

        return $this;
    }

    /**
     * @param mixed  $credential
     * @param string $persistentToken
     */
    public function cleanTriplet($credential, $persistentToken)
    {
        $persistentToken = sha1($persistentToken);
        $keyName = $this->getKeyName($credential, $persistentToken);

        $this->redis->del($keyName);
    }

    /**
     * Replace current token after successful authentication
     * @param mixed  $credential
     * @param string $token
     * @param string $persistentToken
     * @param int    $expire
     */
    public function replaceTriplet($credential, $token, $persistentToken, $expire)
    {
        $this->storeTriplet($credential, $token, $persistentToken, $expire);
    }

    /**
     * @param mixed $credential
     * @TODO - this needs testing.
     */
    public function cleanAllTriplets($credential)
    {
        $delete_lua = <<< LUA
local keys = redis.call('keys', ARGV[1]) 
for i=1,#keys,5000 do 
  redis.call('del', unpack(keys, i, math.min(i+4999, #keys)))
end 
return keys" 0 prefix:*
LUA;

        $user_key_pattern = $this->key_prefix . '_' . $credential.'*';

        // The first argument of EVAL is a Lua 5.1
        // The second argument of EVAL is the number of arguments that follows the script that represents Redis key names.
        $this->redis->eval($delete_lua, [$user_key_pattern], 0);
    }

    /**
     * Remove all expired triplets of all users.
     *
     * @param int $expiryTime Timestamp, all tokens before this time will be deleted
     * @return void
     */
    public function cleanExpiredTokens($expiryTime)
    {
        //Nothing to do - Redis handles expiring tokens internally.
    }

    /**
     * @param $credential
     * @param $persistentToken
     * @return string
     */
    protected function getKeyName($credential, $persistentToken)
    {
        return $this->key_prefix . '_' . $credential . '_' . $persistentToken;
        //return $this->path.DIRECTORY_SEPARATOR.$credential.".".$persistentToken.$this->suffix;
    }
}
