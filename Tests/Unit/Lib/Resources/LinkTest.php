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
 * Test the Link Resource
 *
 * @author Johnathan Pulos
 */
class LinkTest extends \PHPUnit_Framework_TestCase
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
     * A factory for creating a link
     *
     * @var array
     * @access private
     **/
    private $linkFactory = array(
        'link_author'               =>  1,
        'link_status'               =>  'published',
        'link_randkey'              =>  0,
        'link_votes'                =>  1,
        'link_karma'                =>  1,
        'link_modified'             =>  '',
        'link_date'                 =>  '',
        'link_published_date'       =>  '',
        'link_category'             =>  0,
        'link_url'                  =>  'http://www.google.com',
        'link_url_title'            =>  'Google.com',
        'link_title'                =>  'Google',
        'link_title_url'            =>  'www-google-com',
        'link_content'              =>  'A great place to search for the best.',
        'link_summary'              =>  '',
        'link_tags'                 =>  '',
        'social_media_id'           =>  'thththrrerkekejssisisjs1221',
        'social_media_account'      =>  '',
        'link_embedly_html'         =>  '',
        'link_embedly_author'       =>  '',
        'link_embedly_author_link'  =>  '',
        'link_embedly_thumb_url'    =>  '',
        'link_embedly_type'         =>  ''
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
        $this->linkFactory['link_modified'] = $today;
        $this->linkFactory['link_date'] = $today;
        $this->linkFactory['link_published_date'] = $today;

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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "links");
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tags");
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "totals");
        /**
         * Setup the totals table to the default
         */
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('published', 0)");
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('new', 0)");
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('discard', 0)");
    }
    /**
     * __construct() should throw an error if you pass a non \Resources\Tag object
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructShouldThrowErrorIfTagObjectDoesNotExist()
    {
        $link = $this->linkFactory;
        $linkResource = $this->setUpLinkResource('I AM NOT A TAG OBJECT');
        $linkResource->save($link);
    }
    /**
     * __construct() should throw an error if you pass a non \Resources\Total object
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructShouldThrowErrorIfTotalObjectDoesNotExist()
    {
        $link = $this->linkFactory;
        $linkResource = $this->setUpLinkResource(null, 'I AM NOT A TOTAL OBJECT');
        $linkResource->save($link);
    }
    /**
     * test that save() adds the record to the database
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldSaveALinkIntoTheDatabase()
    {
        $expected = $this->linkFactory;
        $expected['link_url_title'] = 'testSaveShouldSaveALinkIntoTheDatabase.com';
        $expected['link_url'] = 'http://www.yahoo.com';
        $expected['social_media_id'] = 'GHFFTTY78861232';
        $expected['social_media_account'] = 'M_Digerati';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($expected);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'GHFFTTY78861232'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals(count($actual), 1);
        $this->assertEquals($expected['link_url_title'], $actual[0]['link_url_title']);
        $this->assertEquals($expected['link_url'], $actual[0]['link_url']);
        $this->assertEquals($expected['social_media_id'], $actual[0]['social_media_id']);
        $this->assertEquals($expected['social_media_account'], $actual[0]['social_media_account']);
    }
    /**
     * test that save() strips tags on specific fields
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldStripTagsFromSpecificFields()
    {
        $link = $this->linkFactory;
        $expected = $this->linkFactory;
        $link['link_url_title'] = '<p>My Title With <strong>Tags</strong></p>';
        $link['social_media_id'] = 'testSaveShouldStripTagsFromSpecificFields';

        $expected['link_url_title'] = 'My Title With Tags';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldStripTagsFromSpecificFields'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected['link_url_title'], $actual[0]['link_url_title']);
    }
    /**
     * test save() sets link_randkey automatically
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldAutomaticallySetTheRandomLinkKey()
    {
        $expected = $this->linkFactory;
        $expected['social_media_id'] = 'testSaveShouldAutomaticallySetTheRandomLinkKey';
        $expected['link_randkey'] = null;
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($expected);
        $statement = $this->db->query("SELECT link_randkey FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldAutomaticallySetTheRandomLinkKey'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertNotEquals(0, intval($actual[0]['link_randkey']));
    }
    /**
     * save() should throw an error if link status does not equal a valid value
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testSaveShouldThrowErrorIfLinkStatusIsInvalid()
    {
        $expected = $this->linkFactory;
        $expected['link_status'] = 'pass_not_fail';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($expected);
    }
    /**
     * save() should automatically generate the link summary with only 150 characters
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldAutomaticallyGenerateTheLinkSummary()
    {
        $link = $this->linkFactory;
        $link['social_media_id'] = 'testSaveShouldAutomaticallyGenerateTheLinkSummary';
        $link['link_content'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor " .
            "incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco " .
            "laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate " .
            "velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt" .
            " in culpa qui officia deserunt mollit anim id est laborum.";
        $link['link_summary'] = '';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_summary FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldAutomaticallyGenerateTheLinkSummary'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertFalse($actual[0]['link_summary'] == '');
    }
    /**
     * save() should auto generate the title based on the content removing elements that do not look good in the title
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldGenerateATitleBasedOnTheContent()
    {
        $expected = '[Lore] ipsum dolor sit amet incididuntut ...';
        $link = $this->linkFactory;
        $link['link_title'] = '';
        $link['link_content'] = "<a href='http://www.google.com'>http://t.co/adouU8Q8eE</a>[Lore] ipsum dolor sit amet" .
        " incididuntut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco ";
        $link['social_media_id'] = '11221212';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_title FROM " . $this->dbTablePrefix . "links WHERE social_media_id = '11221212'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['link_title']);
    }
    /**
     * save() should not truncate a title's word even if it is over the limit
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldNotTruncateTitleWords()
    {
        $expected = '[Lore] ipsum dolor sit ...';
        $link = $this->linkFactory;
        $link['link_title'] = '';
        $link['link_content'] = "[Lore] ipsum dolor sit ametincididuntutlabore et dolore magna aliqua. Ut enim";
        $link['social_media_id'] = 'testSaveShouldNotTruncateTitleWords';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_title FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldNotTruncateTitleWords'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['link_title']);
    }
    /**
     * save() should not truncate if the string is shorter then truncate length
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldNotTruncateIfShorterThanTruncateLength()
    {
        $expected = '[New #mobmin Calendar Event] EMDC ...';
        $link = $this->linkFactory;
        $link['link_title'] = '';
        $link['link_content'] = "[New #mobmin Calendar Event] EMDC http://t.co/nuvRR3jYhk";
        $link['social_media_id'] = 'testSaveShouldNotTruncateIfShorterThanTruncateLength';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_title FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldNotTruncateIfShorterThanTruncateLength'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['link_title']);
    }
    /**
     * save() should not truncate title if you provide it
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldNotTruncateIfTitleProvided()
    {
        $expected = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod';
        $link = $this->linkFactory;
        $link['link_title'] = $expected;
        $link['link_content'] = "[Lore] ipsum dolor sit ametincididuntutlabore et dolore magna aliqua. Ut enim";
        $link['social_media_id'] = 'testSaveShouldNotTruncateIfTitleProvided';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_title FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldNotTruncateIfTitleProvided'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['link_title']);
    }
    /**
     * save() should not set link_summary to link_content if provided
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldNotSetSummaryToContentIfProvided()
    {
        $expected = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod';
        $link = $this->linkFactory;
        $link['link_summary'] = $expected;
        $link['link_content'] = "<p>[Lore] ipsum dolor sit ametincididuntutlabore et dolore magna aliqua. Ut enim</p>";
        $link['social_media_id'] = 'testSaveShouldNotSetSummaryToContentIfProvided';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_summary FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testSaveShouldNotSetSummaryToContentIfProvided'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['link_summary']);
    }
    /**
     * save() should throw an error if you link_author is not set
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testSaveThrowsErrorIfLinkAuthorIsNotSet()
    {
        $link = $this->linkFactory;
        $link['link_author'] = null;
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
    }
    /**
     * getLastID() should return the id of the last link inserted
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testGetLastIDShouldReturnTheLastInsertedId()
    {
        $link = $this->linkFactory;
        $link['social_media_id'] = 'testLastIDShouldReturnTheLastInsertedId';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_id FROM " . $this->dbTablePrefix . "links WHERE social_media_id = 'testLastIDShouldReturnTheLastInsertedId'");
        $savedLink = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $expected = $savedLink[0]['link_id'];

        $actual = $linkResource->getLastID();
        $this->assertEquals($expected, $actual);
    }
    /**
     * save() should trigger a \Resources\Tag->save() on each tag in a comma seperated string
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldSaveEachTagWhenSavingTheLink()
    {
        $link = $this->linkFactory;
        $link['link_tags'] = 'kermit, muppet, fozie bear, ms. piggy, gonzo';
        $tagObject = $this->getMock('\Resources\Tag', array('save'), array($this->db));
        $tagObject->expects($this->exactly(5))
                    ->method('save')
                    ->withConsecutive(
                        array(new \Support\CustomAssertions\ArrayHasEntries(array('tag_words'  =>  'kermit'))),
                        array(new \Support\CustomAssertions\ArrayHasEntries(array('tag_words'  =>  'muppet'))),
                        array(new \Support\CustomAssertions\ArrayHasEntries(array('tag_words'  =>  'fozie bear'))),
                        array(new \Support\CustomAssertions\ArrayHasEntries(array('tag_words'  =>  'ms. piggy'))),
                        array(new \Support\CustomAssertions\ArrayHasEntries(array('tag_words'  =>  'gonzo')))
                    );
        $linkResource = $this->setUpLinkResource($tagObject);
        $linkResource->save($link);
    }
    /**
     * save() should not call save() on tags if no tags provided
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldNotTriggerSaveOnTagsIfItIsEmpty()
    {
        $link = $this->linkFactory;
        $link['link_tags'] = '';
        $tagObject = $this->getMock('\Resources\Tag', array('save'), array($this->db));
        $tagObject->expects($this->never())->method('save');
        $linkResource = $this->setUpLinkResource($tagObject);
        $linkResource->save($link);
    }
    /**
     * save() should trigger \Resources\Total->increment() when inserting a link
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldCallTotalIncrementOnTheLinkStatus()
    {
        $link = $this->linkFactory;
        $link['link_status'] = 'new';
        $totalObject = $this->getMock('\Resources\Total', array('increment'), array($this->db));
        $totalObject->expects($this->once())->method('increment')->with('new');
        $linkResource = $this->setUpLinkResource(null, $totalObject);
        $linkResource->save($link);
    }
    /**
     * exists() Should return true if it exists
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testExistsShouldReturnTrueIfExists()
    {
        $id = rand(10000, 10000000);
        $linkResource = $this->setUpLinkResource();
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "links (link_author, link_url, social_media_id) VALUES(1, 'http://www.google.com', " . $id . ")");
        $exists = $linkResource->exists($id, 'social_media_id');
        $this->assertTrue($exists);
    }
    /**
     * exists() Should default column to the Models primary key
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testExistsShouldDefaultColumnToThePrimaryKey()
    {
        $id = rand(10000, 10000000);
        $linkResource = $this->setUpLinkResource();
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "links (link_author, link_url, social_media_id) VALUES(1, 'http://www.google.com', " . $id . ")");
        $id = $this->db->lastInsertId();
        $exists = $linkResource->exists($id);
        $this->assertTrue($exists);
    }
    /**
     * exists() Should return false if it does not exists
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testExistsShouldReturnFalseIfNotExists()
    {
        $id = rand(10000, 10000000);
        $linkResource = $this->setUpLinkResource();
        $exists = $linkResource->exists($id, 'social_media_id');
        $this->assertFalse($exists);
    }
    /**
     * exists() should throw an error if passed a non accessible attribute column name
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testExistsShouldThrowsErrorIfColumnNotAccessible()
    {
        $id = rand(10000, 10000000);
        $linkResource = $this->setUpLinkResource();
        $exists = $linkResource->exists($id, 'made_up_column');
    }
    /**
     * SetUp the Link Resource, and return the object
     *
     * @param \Resources\Tag $tagObject The \Resources\Tag object (default: null)
     * @param \Resources\Total $totalObject The \Resources\Total object (default: null)
     * @return \Resources\Link
     * @access private
     * @author Johnathan Pulos
     **/
    private function setUpLinkResource($tagObject = null, $totalObject = null)
    {
        if (is_null($tagObject)) {
            $tagObject = $this->getMock('\Resources\Tag', array('save'), array($this->db));
        }
        if (is_null($totalObject)) {
            $totalObject = $this->getMock('\Resources\Total', array('increment'), array($this->db));
        }
        $linkResource = new \Resources\Link($this->db, $tagObject, $totalObject);
        $linkResource->setTablePrefix($this->dbTablePrefix);
        return $linkResource;
    }
}
