<?php

require_once __DIR__."/Env.php";

/**
 * Class: HerissonEncryptionTest
 * 
 * Test HerissonEncryption class
 *
 * @see PHPUnit_Framework_TestCase
 */
class HerissonEncryptionTest extends PHPUnit_Framework_TestCase
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

    protected function setUp()
    {
        $this->e = HerissonEncryption::i();
        $this->sample = "Hello World! This is a sample.";
        $this->sampleLong = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?";
    }

    public function testGenerateKeyPairs()
    {
        $this->assertGreaterThanOrEqual(20, strlen($this->e->public));
        $this->assertGreaterThanOrEqual(20, strlen($this->e->private));
    }

    public function testGenerateKeyPairsReload()
    {
        $public = $this->e->public;
        $private = $this->e->private;
        $this->e->generateKeyPairs();
        $this->assertNotEquals($public, $this->e->public);
        $this->assertNotEquals($private, $this->e->private);
    }

    public function testKeyAttributes()
    {
        $this->assertGreaterThanOrEqual(20, strlen($this->e->public));
        $this->assertGreaterThanOrEqual(20, strlen($this->e->private));
    }

    public function testHash()
    {
        $this->assertEquals($this->e->hash($this->sample), hash("sha256", $this->sample));
    }

    public function testHashDuplicate()
    {
        $this->assertEquals($this->e->hash($this->sample), $this->e->hash($this->sample));
    }

    /* short encryption tests with short data */
    public function testPublicEncrypt()
    {
        $crypted = $this->e->publicEncrypt($this->sample, $this->e->public);
        $uncrypted = $this->e->privateDecrypt($crypted, $this->e->private);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPublicEncryptWithDefault()
    {
        $crypted = $this->e->publicEncrypt($this->sample);
        $uncrypted = $this->e->privateDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPrivateEncrypt()
    {
        $crypted = $this->e->privateEncrypt($this->sample, $this->e->private);
        $uncrypted = $this->e->publicDecrypt($crypted, $this->e->public);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPrivateEncryptWithDefault()
    {
        $crypted = $this->e->privateEncrypt($this->sample);
        $uncrypted = $this->e->publicDecrypt($crypted);
        $this->assertEquals($this->sample, $uncrypted);
    }

    /* short encryption tests with long data */
    public function testRegularPublicEncryptWithLongDataFail()
    {
        $this->setExpectedException('HerissonEncryptionException');
        $crypted = $this->e->publicEncrypt($this->sampleLong, $this->e->public);
    }

    public function testRegularPublicEncryptWithLongDataWithDefaultFail()
    {
        $this->setExpectedException('HerissonEncryptionException');
        $crypted = $this->e->publicEncrypt($this->sampleLong);
    }

    public function testRegularPrivateEncryptWithLongDataFail()
    {
        $this->setExpectedException('HerissonEncryptionException');
        $crypted = $this->e->privateEncrypt($this->sampleLong, $this->e->private);
    }

    public function testRegularPrivateEncryptWithLongDataWithDefaultFail()
    {
        $this->setExpectedException('HerissonEncryptionException');
        $crypted = $this->e->privateEncrypt($this->sampleLong);
    }

    /* Long encryption tests with long data */
    public function testPrivateEncryptWithLongData()
    {
        $encryption_data = $this->e->privateEncryptLongData($this->sampleLong, $this->e->private);
        $uncrypted = $this->e->publicDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv'], $this->e->public);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }

    public function testPrivateEncryptWithLongDataWithDefault()
    {
        $encryption_data = $this->e->privateEncryptLongData($this->sampleLong);
        $uncrypted = $this->e->publicDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }

    public function testPublicEncryptWithLongData()
    {
        $encryption_data = $this->e->publicEncryptLongData($this->sampleLong, $this->e->public);
        $uncrypted = $this->e->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv'], $this->e->private);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }

    public function testPublicEncryptWithLongDataWithDefault()
    {
        $encryption_data = $this->e->publicEncryptLongData($this->sampleLong);
        $uncrypted = $this->e->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
        $this->assertEquals($this->sampleLong, $uncrypted);
    }

    /* Long encryption tests with short data */
    public function testPrivateEncryptWithShortData()
    {
        $encryption_data = $this->e->privateEncryptLongData($this->sample, $this->e->private);
        $uncrypted = $this->e->publicDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv'], $this->e->public);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPrivateEncryptWithShortDataWithDefault()
    {
        $encryption_data = $this->e->privateEncryptLongData($this->sample);
        $uncrypted = $this->e->publicDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPublicEncryptWithShortData()
    {
        $encryption_data = $this->e->publicEncryptLongData($this->sample, $this->e->public);
        $uncrypted = $this->e->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv'], $this->e->private);
        $this->assertEquals($this->sample, $uncrypted);
    }

    public function testPublicEncryptWithShortDataWithDefault()
    {
        $encryption_data = $this->e->publicEncryptLongData($this->sample);
        $uncrypted = $this->e->privateDecryptLongData($encryption_data['data'], $encryption_data['hash'], $encryption_data['iv']);
        $this->assertEquals($this->sample, $uncrypted);
    }

}

