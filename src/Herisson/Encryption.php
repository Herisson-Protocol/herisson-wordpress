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
        if (!openssl_public_encrypt($data, $data_crypted, $key)) {
            echo __('Error while encrypting with public key', HERISSON_TD);
        }
        return $data_crypted;
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
        if (!openssl_public_decrypt($data_crypted, $data, $key)) {
            echo __('Error while decrypting with public key', HERISSON_TD);
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
            echo __('Error while encrypting with private key', HERISSON_TD);
        }
        return $data_crypted;
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
        if (!openssl_private_decrypt($data_crypted, $data, $key)) {
            echo __('Error while decrypting with private key', HERISSON_TD);
        }
        return $data;
    }

    /**
     * Encrypt data with the private key, and convert it into base64
     *
     * @param string $data the string to be encrypted
     *
     * @return base64 encrypted data
     */
    function encryptShort($data)
    {
        $hash = HerissonEncryption::i()->hash($data);
        return base64_encode($this->privateEncrypt($hash));
    }

    /**
     * Encrypt data with the private key, and convert it into base64
     *
     * @param string $data              the string to be encrypted
     * @param string $friend_public_key the friend public key needed to decrypt the data
     *
     * @return base64 encrypted data
     */
    function decryptShort($data, $friend_public_key)
    {
        $hash_crypted = base64_decode($data);
        return $this->publicDecrypt($hash_crypted);
    }

    /**
     * Encrypt data with the private key, and convert it into base64
     *
     * @param string $data              the string to be encrypted
     * @param string $friend_public_key the friend public key needed to encrypt the data
     *
     * @return base64 encrypted data
     */
    function encrypt($data, $friend_public_key)
    {
        $hash = $this->hash($data);
        if (!openssl_private_encrypt($hash, $hash_crypted, $this->private)) {
            HerissonNetwork::reply(417);
            echo __('Error while encrypting hash with my private key', HERISSON_TD);
        }
        $data_crypted = null;
        if (!openssl_seal($data, $data_crypted, $seal_key, array($friend_public_key))) {
            HerissonNetwork::reply(417);
            echo __('Error while encrypting data with friend public key<br>', HERISSON_TD);
        }

        return array(
            'data' => base64_encode($data_crypted),
            'hash' => base64_encode($hash_crypted),
            'seal' => base64_encode($seal_key[0]),
        );
    }

    /**
     * Check an encrypted data
     *
     * @param string $data              the encrypted data
     * @param string $signature         the signature used to encrypt
     * @param string $friend_public_key the friend public key needed to encrypt the data
     *
     * @return base64 encrypted data
     */
    function checkShort($data, $signature, $friend_public_key)
    {
        if ($this->decryptShort($signature, $friend_public_key) == $this->hash($data)) {
            return true;
        }
        return false;
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
         echo __('Error while encrypting bkacup hash with my public key', HERISSON_TD);
        }
     $data_crypted = null;

     if (!openssl_seal($data, $data_crypted, $seal_key, array($my_public_key))) {
         echo __('Error while encrypting backup data with my public key<br>', HERISSON_TD);
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
class HerissonEncryptionException
{


}


