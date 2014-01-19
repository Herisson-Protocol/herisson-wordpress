<?php
/**
 * Herisson\EncryptionTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson;

require_once __DIR__."/../Env.php";


/**
 * Class: HerissonEncryptionTest
 * 
 * Test HerissonEncryption class
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class EncryptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The HerissonEncryption object
     */
    public $e;

    /**
     * Short text sample
     */
    public $sample;

    /**
     * Long text sample
     */
    public $sampleLong;

    /**
     * Configuration
     *
     * Create sample data, and Encryption object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->e          = Encryption::i();
        $this->sample     = "Hello World! This is a sample.";
        $this->sampleLong = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?";
    }

    /**
     * Test generating new keys, and test that they are differents than the others
     * 
     * @return void
     */
    public function testGenerateKeyPairsReload()
    {
        $public  = $this->e->public;
        $private = $this->e->private;
        $this->e->generateKeyPairs();
        $this->assertNotEquals($public, $this->e->public);
        $this->assertNotEquals($private, $this->e->private);
    }


    /**
     * Test the length of the keys
     * 
     * @return void
     */
    public function testKeyAttributes()
    {
        $this->assertGreaterThanOrEqual(20, strlen($this->e->public));
        $this->assertGreaterThanOrEqual(20, strlen($this->e->private));
    }


    /**
     * Test the hash method
     * 
     * @return void
     */
    public function testHash()
    {
        $this->assertEquals($this->e->hash($this->sample), hash("sha256", $this->sample));
    }


    /**
     * Test that the hash is consistent
     * 
     * @return void
     */
    public function testHashDuplicate()
    {
        $this->assertEquals($this->e->hash($this->sample), $this->e->hash($this->sample));
    }


    /* short encryption tests with short data */
    /**
     * Test short encryption with a given public key
     * 
     * @return void
     */
    public function testPublicEncrypt()
    {
        $crypted   = $this->e->publicEncrypt($this->sample, $this->e->public);
        $uncrypted = $this->e->privateDecrypt($crypted, $this->e->private);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /**
     * Test short encryption with default public key
     * 
     * @return void
     */
    public function testPublicEncryptWithDefault()
    {
        $crypted   = $this->e->publicEncrypt($this->sample);
        $uncrypted = $this->e->privateDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /**
     * Test short encryption with a given private key
     * 
     * @return void
     */
    public function testPrivateEncrypt()
    {
        $crypted   = $this->e->privateEncrypt($this->sample, $this->e->private);
        $uncrypted = $this->e->publicDecrypt($crypted, $this->e->public);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /**
     * Test short encryption with default private key
     * 
     * @return void
     */
    public function testPrivateEncryptWithDefault()
    {
        $crypted   = $this->e->privateEncrypt($this->sample);
        $uncrypted = $this->e->publicDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /* short encryption tests with long data */
    /**
     * Test that public key encryption will fail with long data, given a public key
     * 
     * @return void
     */
    public function testRegularPublicEncryptWithLongDataFail()
    {
        $this->setExpectedException('Herisson\Encryption\Exception');
        $crypted = $this->e->publicEncrypt($this->sampleLong, $this->e->public);
    }


    /**
     * Test that public key encryption will fail with long data, with default public key
     * 
     * @return void
     */
    public function testRegularPublicEncryptWithLongDataWithDefaultFail()
    {
        $this->setExpectedException('Herisson\Encryption\Exception');
        $crypted = $this->e->publicEncrypt($this->sampleLong);
    }


    /**
     * Test that private key encryption will fail with long data, given a private key
     * 
     * @return void
     */
    public function testRegularPrivateEncryptWithLongDataFail()
    {
        $this->setExpectedException('Herisson\Encryption\Exception');
        $crypted = $this->e->privateEncrypt($this->sampleLong, $this->e->private);
    }


    /**
     * Test that private key encryption will fail with long data, with default private key
     * 
     * @return void
     */
    public function testRegularPrivateEncryptWithLongDataWithDefaultFail()
    {
        $this->setExpectedException('Herisson\Encryption\Exception');
        $crypted = $this->e->privateEncrypt($this->sampleLong);
    }


    /* Long encryption tests with long data */
    /**
     * Test private key encryption works with long data, given a private key
     * 
     * @return void
     */
    public function testPrivateEncryptWithLongData()
    {
        $encryptionData = $this->e->privateEncryptLongData($this->sampleLong, $this->e->private);
        $uncrypted      = $this->e->publicDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv'], $this->e->public);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }


    /**
     * Test private key encryption works with long data, with default private key
     * 
     * @return void
     */
    public function testPrivateEncryptWithLongDataWithDefault()
    {
        $encryptionData = $this->e->privateEncryptLongData($this->sampleLong);
        $uncrypted      = $this->e->publicDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }


    /**
     * Test public key encryption works with long data, given a public key
     * 
     * @return void
     */
    public function testPublicEncryptWithLongData()
    {
        $encryptionData = $this->e->publicEncryptLongData($this->sampleLong, $this->e->public);
        $uncrypted      = $this->e->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv'], $this->e->private);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }


    /**
     * Test public key encryption works with long data, with default public key
     * 
     * @return void
     */
    public function testPublicEncryptWithLongDataWithDefault()
    {
        $encryptionData = $this->e->publicEncryptLongData($this->sampleLong);
        $uncrypted      = $this->e->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }


    /* Long encryption tests with short data */
    /**
     * Test long private key encryption works with short data, with a given private key
     * 
     * @return void
     */
    public function testPrivateEncryptWithShortData()
    {
        $encryptionData = $this->e->privateEncryptLongData($this->sample, $this->e->private);
        $uncrypted      = $this->e->publicDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv'], $this->e->public);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /**
     * Test long private key encryption works with short data, with default private key
     * 
     * @return void
     */
    public function testPrivateEncryptWithShortDataWithDefault()
    {
        $encryptionData = $this->e->privateEncryptLongData($this->sample);
        $uncrypted      = $this->e->publicDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
        $this->assertEquals($this->sample, $uncrypted);
    }


    /**
     * Test long public key encryption works with short data, with a given public key
     * 
     * @return void
     */
    public function testPublicEncryptWithShortData()
    {
        $encryptionData = $this->e->publicEncryptLongData($this->sample, $this->e->public);
        $uncrypted      = $this->e->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv'], $this->e->private);
        $this->assertEquals($this->sample, $uncrypted);
    }

    
    /**
     * Test long public key encryption works with short data, with default public key
     * 
     * @return void
     */
    public function testPublicEncryptWithShortDataWithDefault()
    {
        $encryptionData = $this->e->publicEncryptLongData($this->sample);
        $uncrypted      = $this->e->privateDecryptLongData($encryptionData['data'], $encryptionData['hash'], $encryptionData['iv']);
        $this->assertEquals($this->sample, $uncrypted);
    }

}

