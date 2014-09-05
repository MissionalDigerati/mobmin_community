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
 * Test the TweetFeed Resource
 *
 * @author Johnathan Pulos
 */
class TweetFeedTest extends \PHPUnit_Framework_TestCase
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
    private $tweetFeedFactory = array(
        'tweet_id'          =>  "455REWSS332",
        'tweeter_id'        =>  "12234432ssdw3444",
        'tweeter_name'      =>  "jpulos",
        'content'           =>  "Here is a tweet from me! #MyTweet",
        'published_date'    =>  null
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
        $this->tweetFeedFactory['published_date'] = $today;

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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tweet_feed");
    }
    /**
     * save() should save a tweet to tweet_feed
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldSaveATweetToTweetFeed()
    {
        $tweet = $this->tweetFeedFactory;
        $tweet['tweeter_name'] = "testSaveShouldSaveATweetToTweetFeed";
        $tweetFeedResource = new \Resources\TweetFeed($this->db);
        $tweetFeedResource->save($tweet);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tweet_feed WHERE tweeter_name = 'testSaveShouldSaveATweetToTweetFeed'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertFalse(empty($actual));
    }
    /**
     * save() should strip HTML from the content field
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldStripHTMLFromTheContent()
    {
        $expectedContent = "I love ice cream sold on Twitter. #IceCreamLove";
        $tweet = $this->tweetFeedFactory;
        $tweet['tweeter_name'] = "testSaveShouldStripHTMLFromTheContent";
        $tweet['content'] = "<strong>I love ice cream sold on Twitter.</strong> <p>#IceCreamLove</p>";
        $tweetFeedResource = new \Resources\TweetFeed($this->db);
        $tweetFeedResource->save($tweet);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tweet_feed WHERE tweeter_name = 'testSaveShouldStripHTMLFromTheContent'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertFalse(empty($actual));
        $this->assertEquals($expectedContent, $actual[0]['content']);
    }
    /**
     * exists() should return a tweet
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testExistsCanFindATweetFromTheTweetFeedTable()
    {
        $tweet = $this->tweetFeedFactory;
        $tweet['tweeter_name'] = "testExistsCanFindATweetFromTheTweetFeedTable";
        $tweetFeedResource = new \Resources\TweetFeed($this->db);
        $tweetFeedResource->save($tweet);
        $tweetExists = $tweetFeedResource->exists('testExistsCanFindATweetFromTheTweetFeedTable', 'tweeter_name');
        $this->assertTrue($tweetExists);
    }
}
