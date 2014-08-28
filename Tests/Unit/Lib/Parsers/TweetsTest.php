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
namespace Tests\Unit\Lib\Parsers;

/**
 * Test the Link Resource
 *
 * @author Johnathan Pulos
 */
class TweetsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * A JSON Object that represents the response of Twitter's API
     *
     * @var Object
     * @access private
     **/
    private $searchTweetsFactory;
    /**
     * A JSON Object that represents the response of Twitter's API
     *
     * @var Object
     * @access private
     **/
    private $searchTweetsSingleTweetFactory;
    /**
     * Setup the testing
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setUp()
    {
        $DS = DIRECTORY_SEPARATOR;
        $jsonFile = __DIR__ . $DS . ".." . $DS . ".." . $DS . ".." . $DS . "Support" . $DS . "Factories" . $DS . "SearchTweets.json";
        $this->searchTweetsFactory = json_decode(file_get_contents($jsonFile));
        $jsonFile = __DIR__ . $DS . ".." . $DS . ".." . $DS . ".." . $DS . "Support" . $DS . "Factories" . $DS . "SearchTweetsSingleTweet.json";
        $this->searchTweetsSingleTweetFactory = json_decode(file_get_contents($jsonFile));
    }
    /**
     * __construct should throw an error if passed a non \Embedly\Embedly object for embedly
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructThrowsErrorIfGivenANonEmbedlyObject()
    {
        $this->setupTweetsParser('Fake Embedly Object', null);
    }
    /**
     * __construct should throw an error if passed a non \Cocur\Slugify\Slugify object for slugify
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructThrowsErrorIfGivenANonSlugifyObject()
    {
        $this->setupTweetsParser(null, 'Fake Slugify Object');
    }
    /**
     * parseLinksFromAPI() should return an array with link_url set for each link in the JSON object
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testParseLinksFromAPIShouldReturnAllLinksSeperated()
    {
        $expectedLinks = array(
            "http://weadapt.org/knowledge-base/improving-access-to-climate-adaptation-information/mwash",
            "http://lyricspro.net/",
            "http://yahoo.com/"
        );
        $expectedLength = count($expectedLinks);
        $tweetsParser = $this->setupTweetsParser();
        $linkData = $tweetsParser->parseLinksFromAPI($this->searchTweetsFactory);
        $links = array();
        $this->assertFalse(empty($linkData));
        foreach ($linkData as $link) {
            array_push($links, $link['link_url']);
        }
        $this->assertEquals($expectedLength, count($links));
        foreach ($expectedLinks as $expectedLink) {
            $this->assertTrue(in_array($expectedLink, $links));
        }
    }
    /**
     * parseLinksFromAPI() should return an array with social_media_id and social_media_account set for each link in 
     * the JSON object
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testParseLinksFromAPIShouldReturnAllLinksSeperatedWithSocialMediaInfo()
    {
        $expectedId = "505097029471961088";
        $expectedAccount = "Mobile_Advance";
        $tweetsParser = $this->setupTweetsParser();
        $linkData = $tweetsParser->parseLinksFromAPI($this->searchTweetsSingleTweetFactory);
        $this->assertFalse(empty($linkData));
        $this->assertEquals($expectedId, $linkData[0]['social_media_id']);
        $this->assertEquals($expectedAccount, $linkData[0]['social_media_account']);
    }
    /**
     * parseLinksFromAPI() should return all the links with the correct publish date
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testParseLinksFromAPIShouldReturnAllLinksSeperatedWithCorrectDates()
    {
        $expectedDate = "2014-08-28 20:58:10";
        $tweetsParser = $this->setupTweetsParser();
        $linkData = $tweetsParser->parseLinksFromAPI($this->searchTweetsSingleTweetFactory);
        $this->assertFalse(empty($linkData));
        $this->assertEquals($expectedDate, $linkData[0]['link_date']);
        $this->assertEquals($expectedDate, $linkData[0]['link_published_date']);
    }
    /**
     * parseLinksFromAPI() should set the defaults for the links
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testParseLinksFromAPIShouldReturnAllLinksSeperatedWithDefaultValues()
    {
        $expectedDefaults = array(
            'link_author'   =>  1,
            'link_status'   =>  'published',
            'link_randkey'  =>  0,
            'link_votes'    =>  1,
            'link_karma'    =>  1,
            'link_modified' =>  '',
            'link_category' =>  1
        );
        $tweetsParser = $this->setupTweetsParser();
        $tweetsParser->setDefaultLinkValues($expectedDefaults);
        $linkData = $tweetsParser->parseLinksFromAPI($this->searchTweetsSingleTweetFactory);
        $this->assertFalse(empty($linkData));
        foreach ($expectedDefaults as $key => $value) {
            $this->assertEquals($value, $linkData[0][$key]);
        }
    }
    /**
     * Sets up a Tweets object with the given objects
     *     
     * @param \Embedly\Embedly $embedlyObj The Embedly object for retrieving link information
     * @param \Cocur\Slugify\Slugify $slugifyObj The Slugify object for turning strings into slugs
     * @return \Parsers\Tweets
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function setupTweetsParser($embedlyObj = null, $slugifyObj = null)
    {
        if (is_null($embedlyObj)) {
            $embedlyObj = $this->getMock('\Embedly\Embedly', array('oembed'), array());
        }
        if (is_null($slugifyObj)) {
            $slugifyObj = $this->getMock('\Cocur\Slugify\Slugify', array('slugify'));
        }
        return new \Parsers\Tweets($embedlyObj, $slugifyObj);
    }
}
