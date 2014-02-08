<?php
/**
 * HerissonFriendsTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Model;

use Herisson\Network;
use Herisson\Encryption;
use Herisson\Message;

require_once __DIR__."/../../Env.php";

/**
 * Class: HerissonFriendsTest
 * 
 * Test WpHerissonFriends class and ORM
 * Test friends requests and validation
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class HerissonFriendsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Configuration
     *
     * Create sample data, and herisson demo website
     * Clean up sample url friends
     *
     * @return void
     */
    protected function setUp()
    {
        $this->sampleName      = "Webpage sample name";
        $this->sampleUrl       = "http://www.example.org";
        $this->herissonName    = "Herisson Demo";
        $this->herissonUrl     = "http://herisson.wilkins.fr/bookmarks";
        $this->herisson        = new WpHerissonFriends();
        $this->herisson->alias = $this->herissonName;

        $this->herisson->setUrl($this->herissonUrl);

        $this->herissonPrivate = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDKasMkbTIn0dtEW3J4RSLmxAjbk7hDadvBhhQCPB6lFXQpKFp7
KPFo3+ucEGp2eZP1XHni6cXcIWuf8fgQdfg/66r0Ot5nbKZwcCxFI9OW87HQZLVO
faIvHZEseF6l10v9RDLUUyjbqySUtXr6FAsncK+Vv891sKO0GMm2BI0O8wIDAQAB
AoGAI7HiCblr39PFC+/oZsciWXl2ampJUzIGu8DOJHq/mLMI0f04v4E/2ROrs9C9
RXk5G0qcf+RjrOcD2KfN6/ExvptWiGmfBuhvS1wrl05Nz7WF21ePV5WcSOff97ky
Tl8E+3qyXtIBun3FHGaP2rX0cxkH7Gy4GI6b1GlYCcLgg/ECQQD4C+nNvjuLliCi
+sqhtvKZqb4rDGL+3OTZ1giTLnir41joGw4UtDUPE5+zlvHQBn1WnJGHPJLUDLnx
lpX/7Y25AkEA0OhMw+0OoSdDyQ1W75ogp/f4aPWajmPbtHuZAR67IOxoYbj6fXDw
yFU5EA/RpTauMGybhVDTVx72UTAtONO4CwJAEPmXvYnIP2w9vYmWNmzzu0pfhkip
ubFaRAIewhvLDFBZtECtvQL8IpUAN+UblVXsW/IJD404qyRX0U2x5DrYmQJAd0fs
bQJyE/oDbky7ktuCQeYIZIW31g2WaRsZZdZSKp5Ri1q/S9is4vYmOtGNdrQeCXA5
7IkV4uy+3+SOLaBVuQJBAMjNHTLZ1PVPSOUjdwxcuvLajeNzDLrvGsucCM2iW7kc
2ltfVqcKNwvHUd5MLgVLqQlRYQ5RcNXuv8crL7XuELA=
-----END RSA PRIVATE KEY-----";

        // Clean up sample url friends
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->sampleUrl));
        foreach ($friends as $f) {
            $f->delete();
        }

        // Clean up sample url friends
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->herissonUrl));
        foreach ($friends as $f) {
            $f->delete();
        }

        global $wpdb;
        $wpdb->db_connect();

        $this->options       = get_option('HerissonOptions');
        $this->backupOptions = get_option('HerissonOptions');
    }

    /**
     * Put back initial configuration
     *
     * Set options as they were 
     *
     * @return void
     */
    public function tearDown()
    {
        update_option('HerissonOptions', $this->backupOptions);
    }


    /**
     * Test adding a new friend and delete it
     *
     * @return void
     */
    public function testAddFriendAndDelete()
    {
        // Create a sample friend
        $f        = new WpHerissonFriends();
        $f->alias = $this->sampleName;
        $f->setUrl($this->sampleUrl);
        $f->save();

        // Check it's saved in the DB
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(1, sizeof($friends));

        // Delete it and verify it's not there anymore
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(0, sizeof($friends));
    }

    /**
     * Test setting and retrieving data
     *
     * @return void
     */
    public function testCreateSaveAndRetrieve()
    {
        // Create a sample friend
        $f             = new WpHerissonFriends();
        $datas = array(
            'alias'         => $this->sampleName,
            'url'           => $this->sampleUrl,
            'name'          => 'name',
            'email'         => 'email',
            'public_key'    => 'public_key',
            'is_active'     => 12,
            'b_wantsyou'    => 34,
            'b_youwant'     => 56,
        );
        $sql = array();
        foreach ($datas as $key => $value) {
            $f->$key = $value;
            $sql[] = "$key=?";
        }
        $f->save();
        $id = $f->id;

        // Check it's saved in the DB, with all parameters
        $friends = WpHerissonFriendsTable::getWhere(implode(' AND ', $sql),
            array_values($datas));
        $this->assertEquals(1, sizeof($friends));

        // Retrieve the id
        $g = WpHerissonFriendsTable::get($id);
        foreach ($datas as $key => $value) {
            $this->assertEquals($value, $g->$key);
        }

        // Cleanup
        $g->delete();

    }


    /**
     * Test retrieving /info of a Herisson site
     *
     * Request the /info and check public key, admin email and site name
     *
     * @return void
     */
    public function testFriendInfo()
    {
        $this->herisson->getInfo();
        $this->assertEquals(1, preg_match("/PUBLIC/", $this->herisson->public_key));
        $this->assertEquals("herisson@wilkins.fr", $this->herisson->email);
        $this->assertEquals("Herisson Demo Instance", $this->herisson->name);
    }


    /**
     * Test asking a new friend that is a Herisson site
     *
     * @return void
     */
    public function testAskFriend()
    {
        // Ask for a friend
        $this->herisson->askForFriend();

        // Verify we have it pending in DB
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(1, sizeof($friends));

        // Delete it and verify it's not there anymore
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(0, sizeof($friends));
    }


    /**
     * Test asking a new friend that is not a Herisson site, and verify it's not saved in the DB
     *
     * @return void
     */
    public function testAskFriendNotHerisson()
    {
        $this->herisson->setUrl($this->sampleUrl);
        $this->herisson->askForFriend();
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(0, sizeof($friends));
    }

    
    /**
     * Test insertion when a friend is asking for friendship, but friendship needs validation
     *
     * @return void
     */
    public function testBeingAskedByFriendPending()
    {

        // Set friendship needs validation
        $this->options['acceptFriends'] = 1;
        update_option('HerissonOptions', $this->options);

        $network   = new Network();
        $signature = Encryption::i()->privateEncrypt($this->herissonUrl, $this->herissonPrivate);
        $postData  = array(                                                       
            'url'       => $this->herissonUrl,
            'signature' => $signature,
        );

        // ask our installation to add this site
        $content = $network->download(HERISSON_LOCAL_URL."/ask", $postData);
        $this->assertEquals(200, $content['code']);

        // check it's pending 
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_wantsyou=? and is_active=?',
            array($this->herissonUrl, 1, 0));
        $this->assertEquals(1, sizeof($friends));

        // Clean up
        foreach ($friends as $f) {
            $f->delete();
        }

        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?',
            array($this->herisson->url, 1, 0));
        $this->assertEquals(0, sizeof($friends));

    }


    /**
     * Test insertion when a friend is asking for friendship, but friendship is refused
     *
     * @return void
     */
    public function testBeingAskedByFriendRefused()
    {

        // Set friendship needs validation
        $this->options['acceptFriends'] = 0;
        update_option('HerissonOptions', $this->options);

        $network   = new Network();
        $signature = Encryption::i()->privateEncrypt($this->herissonUrl, $this->herissonPrivate);
        $postData  = array(                                                       
            'url'       => $this->herissonUrl,
            'signature' => $signature,
        );

        // ask our installation to add this site
        try {
            $content = $network->download(HERISSON_LOCAL_URL."/ask", $postData);
        } catch (Network\Exception $e) {
            $this->assertEquals(403, $e->getCode());
        }

        // check it's pending 
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_wantsyou=? and is_active=?',
            array($this->herissonUrl, 1, 0));
        $this->assertEquals(0, sizeof($friends));

    }


    /**
     * Test insertion when a friend is asking for friendship, but friendship is accepted
     *
     * @return void
     */
    public function testBeingAskedByFriendAccepted()
    {

        // Set friendship needs validation
        $this->options['acceptFriends'] = 2;
        update_option('HerissonOptions', $this->options);

        $network   = new Network();
        $signature = Encryption::i()->privateEncrypt($this->herissonUrl, $this->herissonPrivate);
        $postData  = array(                                                       
            'url'       => $this->herissonUrl,
            'signature' => $signature,
        );

        // ask our installation to add this site
        try {
            $content = $network->download(HERISSON_LOCAL_URL."/ask", $postData);
        } catch (Network\Exception $e) {
            $this->assertEquals(202, $e->getCode());
        }

        // check it's pending 
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_wantsyou=? and is_active=?',
            array($this->herissonUrl, 0, 1));
        $this->assertEquals(1, sizeof($friends));

        // Clean up
        foreach ($friends as $f) {
            $f->delete();
        }

        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?',
            array($this->herisson->url, 0, 1));
        $this->assertEquals(0, sizeof($friends));

    }


    /**
     * Test inserting a new friend that need validation, and validates it.
     *
     * @return void
     */
    public function testValidateFriendFront()
    {
        // create a fake request from sample site
        $f = new WpHerissonFriends();
        $e = Encryption::i();
        $e->generateKeyPairs();
        $f->public_key = $e->public;
        $f->url        = $this->sampleUrl;
        $f->b_youwant  = 1;
        $f->save();

        // Check the request is pending
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?', array($f->url, 1, 0));
        $this->assertEquals(1, sizeof($friends));

        // encrypt sample url, with sample private key
        $network   = new Network();
        $signature = Encryption::i()->privateEncrypt($f->url, $e->private);
        $postData  = array(                                                       
            'url'       => $f->url,
            'signature' => $signature,
        );

        // request our installation to validate sample site
        $content = $network->download(HERISSON_LOCAL_URL."/validate", $postData);
        $this->assertEquals(200, $content['code']);

        // check it's not pending anymore
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?', array($f->url, 0, 1));
        $this->assertEquals(1, sizeof($friends));

        // Delete it and check it's not here anymore
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($f->url, 0));
        $this->assertEquals(0, sizeof($friends));
    }

    /**
     * Test inserting a new friend that need validation, and try to validate it with the wrong private key for cipher
     *
     * @return void
     */
    public function testValidateFriendFrontErrorEncoding()
    {
        // create a fake request from sample site
        $f = new WpHerissonFriends();
        $e = Encryption::i();
        $e->generateKeyPairs();
        $f->public_key = $e->public;
        $f->url        = $this->sampleUrl;
        $f->b_youwant  = 1;
        $f->save();

        // Change the key pairs, to create a cipher error
        $e->generateKeyPairs();

        // Check the request is pending
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?', array($f->url, 1, 0));
        $this->assertEquals(1, sizeof($friends));

        // encrypt sample url, with sample private key
        $network   = new Network();
        $signature = Encryption::i()->privateEncrypt($f->url, $e->private);
        $postData  = array(                                                       
            'url'       => $f->url,
            'signature' => $signature,
        );

        // request our installation to validate sample site
        try {
            $content = $network->download(HERISSON_LOCAL_URL."/validate", $postData);
        } catch (Network\Exception $e) {
            $this->assertEquals(417, $e->getCode());
        }

        // check it's not pending anymore
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=? and is_active=?', array($f->url, 1, 0));
        $this->assertEquals(1, sizeof($friends));

        // Delete it and check it's not here anymore
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($f->url, 1));
        $this->assertEquals(0, sizeof($friends));
    }

    /**
     * Test validating a friend with the wrong key
     *
     * @return void
     */
    public function testValidateFriendWaitingError()
    {
        // create a fake request from sample site
        $f = new WpHerissonFriends();
        $e = Encryption::i();
        $e->generateKeyPairs();
        $f->public_key = $e->public;
        $f->url        = $this->herissonUrl;
        //$f->setUrl($this->herissonUrl);
        $f->b_wantsyou = 1;
        $f->save();

        // Check the request is pending
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_wantsyou=? and is_active=?', array($f->url, 1, 0));
        $this->assertEquals(1, sizeof($friends));

        $friend = $friends[0];
        $friend->validateFriend();
        $msgs = Message::i()->getErrors();
        $msgs = array_reverse($msgs);
        $this->assertEquals(1, preg_match("/417/", $msgs[0]));

    }

}

