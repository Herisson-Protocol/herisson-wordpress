<?

require_once __DIR__."/../src/includes/encryption.php"; 

/**
 * Hack function to replace get_option wordpress function in test methods
 *
 * @param $dummy Dummy plugin name
 * @return an array of options with publicKey and privateKey parameters
 */
$e = HerissonEncryption::i();
$e->generateKeyPairs();
$public = $e->public;
$private = $e->private;
function get_option($dummy) {
    global $public, $private;
	return array(
		'publicKey' => $public,
		'privateKey' => $private,
	);
}

/**
 * Class: EncryptionTest
 * 
 * Test HerissonEncryption class
 *
 * @see PHPUnit_Framework_TestCase
 */
class HerissonEncryptionTest extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
		require_once __DIR__."/../src/includes/encryption.php"; 
    }

	public function testGenerateKeyPairs() {
		$e = HerissonEncryption::i();
		$e->generateKeyPairs();
		$this->assertGreaterThanOrEqual(20,strlen($e->public));
		$this->assertGreaterThanOrEqual(20,strlen($e->private));
	}

	public function testKeyAttributes() {
		$e = HerissonEncryption::i();
		$this->assertGreaterThanOrEqual(20,strlen($e->public));
		$this->assertGreaterThanOrEqual(20,strlen($e->private));
	}

	public function testHash() {
		$e = HerissonEncryption::i();
		$str = "Hello world!";
		$this->assertEquals($e->hash($str), hash("sha256",$str));
	}

	public function testEncryptionShort() {
		$e = HerissonEncryption::i();
        $e->generateKeyPairs();
		$str = "Hello world!";
        $res = $e->encryptShort($str);
        $res2 = herisson_encrypt_short($str);
        $this->assertEquals($res,$res);
	}

}

