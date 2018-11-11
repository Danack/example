<?php

namespace Birke\Rememberme\Storage;

/**
 * File-Based Storage
 */
class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected $path = "";

    /**
     * @var string
     */
    protected $suffix = ".txt";

    /**
     * @param string $path
     * @param string $suffix
     */
    public function __construct($path = "", $suffix = ".txt")
    {
        $this->path = $path;
        $this->suffix = $suffix;
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
        $fn = $this->getFilename($credential, $persistentToken);

        if (!file_exists($fn)) {
            return [self::TRIPLET_NOT_FOUND, null];
        }

        $string = trim(file_get_contents($fn));
        $data = json_decode_safe($string);

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
     * @return $this
     */
    public function storeTriplet($credential, $token, $persistentToken, $expire, array $user_data = [])
    {
        $originalPersistentToken = $persistentToken;

        // Hash the tokens, because they can contain a salt and can be accessed in the file system
        $persistentToken = sha1($persistentToken);
        $token = sha1($token);
        $fn = $this->getFilename($credential, $persistentToken);

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

        file_put_contents($fn, $string);

        return $this;
    }

    /**
     * @param mixed  $credential
     * @param string $persistentToken
     */
    public function cleanTriplet($credential, $persistentToken)
    {
        $persistentToken = sha1($persistentToken);
        $fn = $this->getFilename($credential, $persistentToken);

        if (file_exists($fn)) {
            unlink($fn);
        }
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
        $this->cleanTriplet($credential, $persistentToken);
        $this->storeTriplet($credential, $token, $persistentToken, $expire);
    }

    /**
     * @param mixed $credential
     */
    public function cleanAllTriplets($credential)
    {
        foreach (glob($this->path.DIRECTORY_SEPARATOR.$credential.".*".$this->suffix) as $file) {
            unlink($file);
        }
    }

    /**
     * Remove all expired triplets of all users.
     *
     * @param int $expiryTime Timestamp, all tokens before this time will be deleted
     * @return void
     */
    public function cleanExpiredTokens($expiryTime)
    {
        foreach (glob($this->path.DIRECTORY_SEPARATOR."*".$this->suffix) as $file) {
            if (filemtime($file) < $expiryTime) {
                unlink($file);
            }
        }
    }

    /**
     * @param $credential
     * @param $persistentToken
     * @return string
     */
    protected function getFilename($credential, $persistentToken)
    {
        return $this->path.DIRECTORY_SEPARATOR.$credential.".".$persistentToken.$this->suffix;
    }
}
