<?

require_once __DIR__."/../src/includes/encryption.php"; 

/**
 * Hack function to replace get_option wordpress function in test methods
 *
 * @param $dummy Dummy plugin name
 * @return an array of options with publicKey and privateKey parameters
 */
function get_option($dummy) {
    $e = HerissonEncryption::i();
	return array(
		'publicKey' => $e->public,
		'privateKey' => $e->private,
	);
}

function __($text) {
    return $text;
}

/**
 * Class: EncryptionTest
 * 
 * Test HerissonEncryption class
 *
 * @see PHPUnit_Framework_TestCase
 */
class HerissonEncryptionTest extends PHPUnit_Framework_TestCase {

    public $e;
    public $sample;
    protected function setUp()
    {
		require_once __DIR__."/../src/includes/encryption.php"; 
        $this->e = HerissonEncryption::i();
        $this->sample = "Hello World! This is a sample.";
    }

	public function testGenerateKeyPairs() {
		$this->assertGreaterThanOrEqual(20, strlen($this->e->public));
		$this->assertGreaterThanOrEqual(20, strlen($this->e->private));
	}

	public function testGenerateKeyPairsReload() {
        $public = $this->e->public;
        $private = $this->e->private;
        $this->e->generateKeyPairs();
        $this->assertNotEquals($public, $this->e->public);
        $this->assertNotEquals($private, $this->e->private);
	}

	public function testKeyAttributes() {
		$this->assertGreaterThanOrEqual(20, strlen($this->e->public));
		$this->assertGreaterThanOrEqual(20, strlen($this->e->private));
	}

	public function testHash() {
		$this->assertEquals($this->e->hash($this->sample), hash("sha256", $this->sample));
	}

	public function testHashDuplicate() {
		$this->assertEquals($this->e->hash($this->sample), $this->e->hash($this->sample));
	}

    # TODO Doesn't test anything
	public function testDecryptShort() {
        $keys = get_option("toto");
        $res = $this->e->encryptShort($this->sample);
        $res1 = $this->e->decryptShort($res, $this->e->public);
        $this->assertEquals(HerissonEncryption::i()->hash($this->sample), $res1);
	}

    # TODO Doesn't test anything
	public function testEncryptShort() {
        $keys = get_option("toto");
        $res = $this->e->encryptShort($this->sample);
#        $res2 = $this->e->encryptShort2($this->sample);
#        $res2 = herisson_encrypt_short($str);
#        $this->assertEquals($res, $res2);
	}

    # TODO Doesn't test anything
	public function testEncryption() {
        $_keys = openssl_pkey_new();
        $pubkey = openssl_pkey_get_details($_keys);
        $friend = $pubkey["key"];
        $res = $this->e->encrypt($this->sample, $friend);
	}

	public function testPublicEncrypt() {
        $crypted = $this->e->publicEncrypt($this->sample, $this->e->public);
        $uncrypted = $this->e->privateDecrypt($crypted, $this->e->private);
        $this->assertEquals($this->sample, $uncrypted);
    }

	public function testPublicEncryptWithDefault() {
        $crypted = $this->e->publicEncrypt($this->sample);
        $uncrypted = $this->e->privateDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
	}

	public function testPrivateEncrypt() {
        $crypted = $this->e->privateEncrypt($this->sample, $this->e->private);
        $uncrypted = $this->e->publicDecrypt($crypted, $this->e->public);
        $this->assertEquals($this->sample, $uncrypted);
    }

	public function testPrivateEncryptWithDefault() {
        $crypted = $this->e->privateEncrypt($this->sample);
        $uncrypted = $this->e->publicDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
	}

	public function testCheckShort() {
        $hash = $this->e->encryptShort($this->sample);

        $this->assertTrue($this->e->checkShort($this->sample, $hash, $this->e->public));
        $this->assertFalse($this->e->checkShort("Fake sample", $hash, $this->e->public));
	}

}

