<?php
/**
 * This file is part of #MobMin Community.
 * 
 * #MobMin Community is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Joshua Project API is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see 
 * <http://www.gnu.org/licenses/>.
 *
 * @author Johnathan Pulos <johnathan@missionaldigerati.org>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
namespace tests\unit\lib\Resources;

/**
 * Test the TweetFeedAvatar Resource
 *
 * @author Johnathan Pulos
 */
class TweetFeedAvatarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The PDO database connection object
     *
     * @var \PHPToolbox\PDODatabase\PDODatabaseConnect
     * @access private
     */
    private $db;
    /**
     * The database table prefix
     *
     * @var string
     * @access private
     **/
    private $dbTablePrefix;
    /**
     * A factory for the tweet feed
     *
     * @var array
     * @access private
     **/
    private $tweetFeedAvatarFactory = array(
        'tweeter_id'            =>  "12234432ssdw3444",
        'tweeter_name'          =>  "jpulos",
        'tweeter_avatar_url'    =>  "http://www.test.com/avatar.jpg",
        'last_updated'          =>  null
    );
    /**
     * Setup the test
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setUp()
    {
        $today = date('Y-m-d H:i:s',time());
        $this->tweetFeedFactory['last_updated'] = $today;

        $pdoDb = \PHPToolbox\PDODatabase\PDODatabaseConnect::getInstance();
        $dbSettings = new \Support\DatabaseSettings();
        $this->dbTablePrefix = $dbSettings->default['table_prefix'];
        $pdoDb->setDatabaseSettings($dbSettings);
        $this->db = $pdoDb->getDatabaseInstance();
    }
    /**
     * tearDown for each test
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function tearDown()
    {
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tweet_feed_avatars");
    }
    /**
     * save() should save an avatar
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldSaveAnAvatar()
    {
        $avatar = $this->tweetFeedAvatarFactory;
        $avatar['tweeter_name'] = "testSaveShouldSaveAnAvatar";
        $tweetFeedAvatarResource = new \Resources\TweetFeedAvatar($this->db);
        $tweetFeedAvatarResource->save($avatar);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tweet_feed_avatars WHERE tweeter_name = 'testSaveShouldSaveAnAvatar'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertFalse(empty($actual));
    }
    /**
     * exists() should return an avatar
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testExistsCanFindAnAvatarFromTheTweetFeedAvatarsTable()
    {
        $avatar = $this->tweetFeedAvatarFactory;
        $avatar['tweeter_name'] = "testExistsCanFindAnAvatarFromTheTweetFeedAvatarsTable";
        $tweetFeedAvatarResource = new \Resources\TweetFeedAvatar($this->db);
        $tweetFeedAvatarResource->save($avatar);
        $avatarExists = $tweetFeedAvatarResource->exists('testExistsCanFindAnAvatarFromTheTweetFeedAvatarsTable', 'tweeter_name');
        $this->assertTrue($avatarExists);
    }
    /**
     * updateRecord() should update an existing record
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveSouldUpdateAnExistingTweetAvatar()
    {
        $expectedTweeterName = "Bilbo Baggins";
        $expectedURL = "http://www.BilboBaggins.com/img.jpg";
        $data = array(
            'tweeter_name'          =>  $expectedTweeterName,
            'tweeter_avatar_url'    =>  $expectedURL
        );
        $query = "INSERT INTO tweet_feed_avatars (tweeter_id, tweeter_name, tweeter_avatar_url, last_updated) " .
            "VALUES('172157274', 'ARJWright', 'http://test.com/test.jpeg', '2014-09-03 03:01:53')";
        $this->db->query($query);
        $lastID = $this->db->lastInsertId();
        $tweetFeedAvatarResource = new \Resources\TweetFeedAvatar($this->db);
        $tweetFeedAvatarResource->updateRecord($data, $lastID);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tweet_feed_avatars WHERE tweet_feed_avatar_id = " . $lastID);
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertFalse(empty($actual));
        $this->assertEquals($expectedTweeterName, $actual[0]['tweeter_name']);
        $this->assertEquals($expectedURL, $actual[0]['tweeter_avatar_url']);
    }
    /**
     * updateRecord() should throw an error if the record does not exist
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testUpdateRecordShouldThrowErrorIfRecordDoesNotExist()
    {
        $data = array(
            'tweeter_name'          =>  'Goober',
            'tweeter_avatar_url'    =>  'http://www.goober.com'
        );
        $tweetFeedAvatarResource = new \Resources\TweetFeedAvatar($this->db);
        $tweetFeedAvatarResource->updateRecord($data, rand(10000, 10000000));
    }
}
