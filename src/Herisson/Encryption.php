<?php
/**
 * HerissonEncryption
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * HerissonEncryption
 * 
 * Handles public/private key asymetric encryption
 * 
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonEncryption
{

    /**
     * singleton
     * @var HerissonEncryption
     */
    public static $i;

    /**
     * Public key
     */
    public static $public;

    /**
     * Private key
     */
    public static $private;

    /**
     * Encryption method for long data
     */
    public static $method = "aes256";

    /**
     * Creating singleton
     *
     * @return HerissonEncryption instance
     */
    public static function i()
    {
        if (is_null(self::$i)) {
            self::$i = new HerissonEncryption();
        }
        return self::$i;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->loadKeys();
    }

    /**
     * Load keys from wordpress options
     * 
     * If not in Wordpress environment, generates a new public/private key pair.
     *
     * @return void
     */
    public function loadKeys()
    {
        global $wp_version;
        if (isset($wp_version)) {
            $options        = get_option('HerissonOptions');
            $this->public   = $options['publicKey'];
            $this->private  = $options['privateKey'];
        } else {
            $this->generateKeyPairs();
        }
    }

    /**
     * Generate a public/private keys pair, and set them in $public and $private attributes
     *
     * @return void
     */
    public function generateKeyPairs()
    {
        /*
        error_log("===================================");
        error_log("Generating new key pairs ");
        error_log("===================================");
        */
        // Create the keypair
        $res = openssl_pkey_new();

        // Get private key
        openssl_pkey_export($res, $this->private);

        // Get public key
        $pubkey = openssl_pkey_get_details($res);
        $this->public = $pubkey["key"];
    }

    /**
     * Hash a variable in sha256
     *
     * @param string $data the data to hash
     *
     * @return hashed data in sha256
     */
    public function hash($data)
    {
        return hash("sha256", $data);
    }

    /**
     * Create a random IV string
     *
     * @param integer $length the length of the expected string (default=16)
     *
     * @return a random IV string of given length
     */
    public function createIV($length=16)
    {
        if (function_exists('mcrypt_create_iv')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * Encrypt data using a public key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key, if none given, the $this->public key is used
     *
     * @return the encrypted data
     */
    function publicEncrypt($data, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }

        # error_log("public encryption of $data with key : $key");
        if (!openssl_public_encrypt($data, $data_crypted, $key)) {
            throw new HerissonEncryptionException(__('Error while encrypting with public key', HERISSON_TD));
        }
        return base64_encode($data_crypted);
    }

    /**
     * Decrypt encrypted data using a public key
     *
     * @param mixed $data_crypted the encrypted data
     * @param mixed $key          optional public key, if none given, the $this->public key is used
     *
     * @return the clear data
     */
    function publicDecrypt($data_crypted, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }
        if (!openssl_public_decrypt(base64_decode($data_crypted), $data, $key)) {
            throw new HerissonEncryptionException(__('Error while decrypting with public key', HERISSON_TD));
        }
        return $data;
    }

    /**
     * Encrypt data using a private key
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return the encrypted data
     */
    function privateEncrypt($data, $key=null)
    {
        if (is_null($key)) {
            $key = $this->private;
        }

        if (!openssl_private_encrypt($data, $data_crypted, $key)) {
            throw new HerissonEncryptionException(__('Error while encrypting with private key', HERISSON_TD));
        }
        return base64_encode($data_crypted);
    }

    /**
     * Decrypt encrypted data using a private key
     *
     * @param mixed $data_crypted the encrypted data
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return the clear data
     */
    function privateDecrypt($data_crypted, $key=null)
    {
        if (is_null($key)) {
            $key = $this->private;
        }
        if (!openssl_private_decrypt(base64_decode($data_crypted), $data, $key)) {
            throw new HerissonEncryptionException(__('Error while decrypting with private key', HERISSON_TD));
        }
        return $data;
    }

    /**
     * Encrypt long data using a public key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the public key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional public key, if none given, the $this->public key is used
     *
     * @return an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    function publicEncryptLongData($data, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_public_encrypt($hash, $hash_crypted, $key)) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while encrypting hash with public key', HERISSON_TD));
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while encrypting long data with encryption method', HERISSON_TD));
        }

        return array(
            'data'  => base64_encode($data_crypted),
            'hash'  => base64_encode($hash_crypted),
            'iv'    => base64_encode($iv),
        );
    }


    /**
     * Decrypt long data using a public key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the public key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the crypted data (crypted with the hash)
     * @param mixed $hash_crypted the crypted hash (crypted with the private key)
     * @param mixed $key          optional public key, if none given, the $this->public key is used
     *
     * @return the decrypted data
     */
    function publicDecryptLongData($data_crypted, $hash_crypted, $iv, $key=null)
    {
        if (is_null($key)) {
            $key = $this->public;
        }

        if (!openssl_public_decrypt(base64_decode($hash_crypted), $hash, $key)) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while decrypting hash with public key', HERISSON_TD));
        }

        if (!($data = openssl_decrypt(base64_decode($data_crypted), self::$method, $hash, 0, base64_decode($iv)))) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while encrypting long data with encryption method', HERISSON_TD));
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while comparing checksum of decrypted data', HERISSON_TD));
        }
       
        return $data;
    }

    /**
     * Encrypt long data using a private key
     *
     * Since we want to encrypt long data, we encrypt the hash of the data with 
     * the private key, and we encrypt the data with self::method (eg. aes256) 
     * and the password is the encrypted hash
     *
     * @param mixed $data the data to encrypt
     * @param mixed $key  optional private key, if none given, the $this->private key is used
     *
     * @return an array with 'data' => the encrypted data, 'hash' => the encrypted hash
     */
    function privateEncryptLongData($data, $key=null)
    {
        if (is_null($key)) {
            $key = $this->private;
        }
        $hash = $this->hash($data);

        $iv = $this->createIV();

        if (!openssl_private_encrypt($hash, $hash_crypted, $key)) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while encrypting hash with private key', HERISSON_TD));
        }

        $data_crypted = null;
        if (!($data_crypted = openssl_encrypt($data, self::$method, $hash, 0, $iv))) {
#            HerissonNetwork::reply(417);
            throw new HerissonEncryptionException(__('Error while encrypting long data with encryption method', HERISSON_TD));
        }

        return array(
            'data'  => base64_encode($data_crypted),
            'hash'  => base64_encode($hash_crypted),
            'iv'    => base64_encode($iv),
        );
    }


    /**
     * Decrypt long data using a private key
     *
     * Since we want to decrypt a long encrypted data, we decrypt the crypted hash with the private key,
     * and we decrypt the long data with the decrypted hash using encryption 
     * method
     *
     * @param mixed $data_crypted the crypted data (crypted with the hash)
     * @param mixed $hash_crypted the crypted hash (crypted with the public key)
     * @param mixed $key          optional private key, if none given, the $this->private key is used
     *
     * @return the decrypted data
     */
    function privateDecryptLongData($data_crypted, $hash_crypted, $iv, $key=null)
    {
        if (is_null($key)) {
            $key = $this->private;
        }

        if (!openssl_private_decrypt(base64_decode($hash_crypted), $hash, $key)) {
            throw new HerissonEncryptionException(__('Error while decrypting hash with private key', HERISSON_TD));
        }

        if (!($data = openssl_decrypt(base64_decode($data_crypted), self::$method, $hash, 0, base64_decode($iv)))) {
            throw new HerissonEncryptionException(__('Error while encrypting long data with encryption method', HERISSON_TD));
        }

        // Check the hash
        if ($hash != $this->hash($data)) {
            throw new HerissonEncryptionException(__('Error while comparing checksum of decrypted data', HERISSON_TD));
        }
       
        return $data;
    }

    /*
    function herisson_encrypt_backup()
    {
     $options = get_option('HerissonOptions');

     $_bookmarks = Doctrine_Query::create()
      ->from('WpHerissonBookmarks')
      ->where("id=$id")
      ->execute();
     $bookmarks = array();
     foreach ($_bookmarks as $bookmark) {
      $bookmarks[] = $bookmark->toArray();
     }
     $data = json_encode($bookmarks);

        $my_public_key  = $options['publicKey'];
        $my_private_key = $options['privateKey'];

     $hash = HerissonEncryption::i()->hash($data);
        if (!openssl_private_encrypt($hash, $hash_crypted, $my_public_key)) {
         throw new HerissonEncryptionException(__('Error while encrypting bkacup hash with my public key', HERISSON_TD));
        }
     $data_crypted = null;

     if (!openssl_seal($data, $data_crypted, $seal_key, array($my_public_key))) {
         throw new HerissonEncryptionException(__('Error while encrypting backup data with my public key<br>', HERISSON_TD));
        }

        return array(
                 'data' => base64_encode($data_crypted),
                    'hash' => base64_encode($hash_crypted),
                    'seal' => base64_encode($seal_key[0]),
                );
    }
    */

}


/**
 * HerissonEncryptionException
 * 
 * Handles encryption errors
 * 
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonEncryptionException extends Exception
{


}



