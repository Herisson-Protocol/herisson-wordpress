<?php

require_once __DIR__."/Env.php";

/**
 * Class: HerissonORMFriendsTest
 * 
 * Test WpHerissonFriends class and ORM
 * Test friends requests and validation
 *
 * @see PHPUnit_Framework_TestCase
 */
class HerissonORMFriendsTest extends PHPUnit_Framework_TestCase
{

    public $me;

    public $friend;

    protected function setUp()
    {
        $this->sampleName   = "Webpage sample name";
        $this->sampleUrl    = "http://www.example.org";

        $this->herissonName = "Herisson Demo";
        $this->herissonUrl  = "http://herisson.wilkins.fr/bookmarks";

        $this->herisson = new WpHerissonFriends();
        $this->herisson->alias = $this->herissonName;
        $this->herisson->setUrl($this->herissonUrl);
    }

    public function testAddFriendAndDelete()
    {
        $f = new WpHerissonFriends();
        $f->alias = $this->sampleName;
        $f->setUrl($this->sampleUrl);
        $f->save();
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(1, sizeof($friends));
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(0, sizeof($friends));
    }


    public function testFriendInfo()
    {
        $this->herisson->getInfo();
        $this->assertEquals(1, preg_match("/PUBLIC/", $this->herisson->public_key));
        $this->assertEquals("herisson@wilkins.fr", $this->herisson->email);
        $this->assertEquals("Herisson Demo Instance", $this->herisson->name);
    }

    public function testAskFriend()
    {
        $this->herisson->askForFriend();
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(1, sizeof($friends));
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(0, sizeof($friends));
    }

    public function testAskFriendNotHerisson()
    {
        $this->herisson->setUrl($this->sampleUrl);
        $this->herisson->askForFriend();
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($this->herisson->url, 1));
        $this->assertEquals(0, sizeof($friends));
    }

    public function testValidateFriendFront()
    {
        $f = new WpHerissonFriends();
        $e = HerissonEncryption::i();
        $e->generateKeyPairs();
        $f->public_key = $e->public;
        $f->url = $this->sampleUrl;
        $f->b_youwant = 1;
        $f->save();

        $network = new HerissonNetwork();
        $signature = HerissonEncryption::i()->privateEncrypt($f->url, $e->private);
        $postData = array(                                                       
            'url'=> $f->url,
            'signature' => $signature
        );

        $content = $network->download(HERISSON_LOCAL_URL."/validate", $postData);

        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($f->url, 0));
        $this->assertEquals(1, sizeof($friends));
        foreach ($friends as $f) {
            $f->delete();
        }
        $friends = WpHerissonFriendsTable::getWhere('url=? and b_youwant=?', array($f->url, 0));
        $this->assertEquals(0, sizeof($friends));
    }

}

