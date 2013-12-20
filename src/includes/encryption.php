<?php
/*
function herisson_generate_keys_pair() {

 // Create the keypair
 $res=openssl_pkey_new();

 // Get private key
 openssl_pkey_export($res, $privkey);

 // Get public key
 $pubkey=openssl_pkey_get_details($res);
 $pubkey=$pubkey["key"];

 #print "public : $pubkey\n";
 #print "private : $privkey\n";

 return array($pubkey,$privkey);
}
*/

function herisson_encrypt($data,$friend_public_key) {
 $options = get_option('HerissonOptions');
	$my_public_key  = $options['publicKey'];
	$my_private_key = $options['privateKey'];

# $options = get_option('HerissonOptions');
# $sealed = null;

# $data = "Only I know the purple fox. Trala la !";
 $hash = HerissonEncryption::i()->hash($data);
	if (!openssl_private_encrypt($hash,$hash_crypted,$my_private_key)) {
	 HerissonNetwork::reply(417);
	 echo __('Error while encrypting hash with my private key',HERISSON_TD);
	}
# echo "$hash -> $hash_crypted<br>\n";
 $data_crypted = null;
# echo "data : $data<br><br>\n";
#	echo "friend public key : $friend_public_key<br><br>\n";
#	openssl_get_publickey($friend_public_key);
#	echo "friend public key : $friend_public_key<br><br>\n";
#	echo "friend public key : ".openssl_get_publickey($friend_public_key)."<br><br>\n";
 if (!openssl_seal($data,$data_crypted,$seal_key,array($friend_public_key))) {
	 HerissonNetwork::reply(417);
	 echo __('Error while encrypting data with friend public key<br>',HERISSON_TD);
	}
# echo "$seal_key[0] , $data -> $data_crypted<br><br>\n";

	return array(
			 'data' => base64_encode($data_crypted),
				'hash' => base64_encode($hash_crypted),
				'seal' => base64_encode($seal_key[0]),
			);
 
 /*
 $crypted0 = $data;
 echo openssl_private_encrypt($crypted0,$crypted1,$priv1)."<br>\n";
 print_r("crypted1 : $crypted1<br>\n");
 
 echo openssl_public_encrypt($crypted1,$crypted2,$pub2)."<br>\n";
 print_r("crypted2 : $crypted2<br>\n");
 
 echo openssl_private_decrypt($crypted2,$crypted3,$priv2)."<br>\n";
 print_r("crypted3 : $crypted3<br>\n");
 
 echo openssl_public_decrypt($crypted3,$crypted4,$pub1)."<br>\n";
 print_r("crypted4 : $crypted4<br>\n");
	*/

}


function herisson_decrypt($json_string,$friend_public_key) {
 $options = get_option('HerissonOptions');
	$my_public_key  = $options['publicKey'];
	$my_private_key = $options['privateKey'];

 $json_data = json_decode($json_string,1);

 if ($json_data === null) {
	 HerissonNetwork::reply(417);
	 echo __('Error while decoding json string<br>',HERISSON_TD);
 }

 $data_crypted = base64_decode($json_data['data']);
 $hash_crypted = base64_decode($json_data['hash']);
	$seal         = base64_decode($json_data['seal']);

# $hash = HerissonEncryption::i()->hash($data);
	if (!openssl_open($data_crypted,$data,$seal,$my_private_key)) {
	 HerissonNetwork::reply(417);
	 echo __('Error while decrypt with my private key<br>',HERISSON_TD);
	}
# echo "$data_crypted -> $data<br>\n";

 
 if (!openssl_public_decrypt($hash_crypted,$hash,$friend_public_key)) {
	 HerissonNetwork::reply(417);
	 echo __('Error while decrypt with friend public key<br>',HERISSON_TD);
	}
# echo "$hash_crypted -> $hash<br>\n";

 if (HerissonEncryption::i()->hash($data) != $hash) {
	 HerissonNetwork::reply(417);
  echo __('Error : mismatch between hash and data, maybe the publickey stored for this site is not correct, or maybe it is a man in the middle attack !<br>');
	}
	return $data;
}
/*
function herisson_hash($data) {
 return sha256($data);
}
*/

function herisson_encrypt_short($data) {
 $options = get_option('HerissonOptions');
	$my_public_key  = $options['publicKey'];
	$my_private_key = $options['privateKey'];

 $hash = HerissonEncryption::i()->hash($data);
	if (!openssl_private_encrypt($hash,$hash_crypted,$my_private_key)) {
	 echo __('Error while encrypting hash with my private key',HERISSON_TD);
	}
	return base64_encode($hash_crypted);
}


function herisson_decrypt_short($data,$friend_public_key) {
	$hash_crypted = base64_decode($data);
	if (!openssl_public_decrypt($hash_crypted,$hash,$friend_public_key)) {
	 echo __('Error while decrypting hash with friend public key',HERISSON_TD);
	}
 return $hash;
}


function herisson_check_short($data,$signature,$friend_public_key) {
 if (herisson_decrypt_short($signature,$friend_public_key) == HerissonEncryption::i()->hash($data)) {
	 return true;
	}
	return false;
}

/***** Backup functions *****/

function herisson_encrypt_backup() {
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
	if (!openssl_private_encrypt($hash,$hash_crypted,$my_public_key)) {
	 echo __('Error while encrypting bkacup hash with my public key',HERISSON_TD);
	}
 $data_crypted = null;

 if (!openssl_seal($data,$data_crypted,$seal_key,array($my_public_key))) {
	 echo __('Error while encrypting backup data with my public key<br>',HERISSON_TD);
	}

	return array(
			 'data' => base64_encode($data_crypted),
				'hash' => base64_encode($hash_crypted),
				'seal' => base64_encode($seal_key[0]),
			);
}



class HerissonEncryption {

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
     * @return HerissonEncryption instance
     */
    public static function i()
    {
        if(is_null(self::$i)) {
            self::$i = new HerissonEncryption();
        }
        return self::$i;
    }

    /**
     * Constructor
     * @return void
     */
    public function __construct()
    {
		$this->loadKeys();
    }

	/**
	 * Load keys from wordpress options
	 *
	 * @return void
	 */
	public function loadKeys()
	{
		$options		= get_option('HerissonOptions');
		$this->public	= $options['publicKey'];
		$this->private	= $options['privateKey'];
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
	 * @return hashed data in sha256
	 */
	public function hash($data)
	{
		return hash("sha256",$data);
	}

	/**
	 * Encrypt data with the private key, and convert it into base64
	 *
	 * @param $data string to be encrypted
	 * @return base64 encrypted data
	 */
	function encryptShort($data)
	{
		$hash = HerissonEncryption::i()->hash($data);
		if (!openssl_private_encrypt($hash,$hash_crypted,$this->private)) {
			echo __('Error while encrypting hash with my private key',HERISSON_TD);
		}
		return base64_encode($hash_crypted);
	}



}


class HerissonEncryptionException {


}



