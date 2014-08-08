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
        'link_author'           =>  1,
        'link_status'           =>  'published',
        'link_randkey'          =>  0,
        'link_votes'            =>  1,
        'link_karma'            =>  1,
        'link_modified'         =>  '',
        'link_date'             =>  '',
        'link_published_date'   =>  '',
        'link_category'         =>  0,
        'link_url'              =>  'http://www.google.com',
        'link_url_title'        =>  'Google.com',
        'link_title'            =>  'Google',
        'link_title_url'        =>  'www-google-com',
        'link_content'          =>  'A great place to search for the best.',
        'link_summary'          =>  '',
        'link_tags'             =>  ''
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
        $expected['link_title'] = 'testSaveShouldSaveALinkIntoTheDatabase';
        $expected['link_url'] = 'http://www.yahoo.com';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($expected);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "links WHERE link_title = 'testSaveShouldSaveALinkIntoTheDatabase'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals(count($actual), 1);
        $this->assertEquals($expected['link_url_title'], $actual[0]['link_url_title']);
        $this->assertEquals($expected['link_title'], $actual[0]['link_title']);
        $this->assertEquals($expected['link_url'], $actual[0]['link_url']);
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
        $link['link_title'] = 'testSaveShouldStripTagsFromSpecificFields';
        $link['link_url_title'] = '<p>My Title With <strong>Tags</strong></p>';
        $link['link_content'] = '<p><strong>Really Bold Content</strong></p>';

        $expected['link_title'] = $link['link_title'];
        $expected['link_url_title'] = 'My Title With Tags';
        $expected['link_content'] = 'Really Bold Content';
        $expected['link_summary'] = 'Really Emphasized Content';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "links WHERE link_title = 'testSaveShouldStripTagsFromSpecificFields'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected['link_url_title'], $actual[0]['link_url_title']);
        $this->assertEquals($expected['link_content'], $actual[0]['link_content']);
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
        $expected['link_title'] = 'testSaveShouldAutomaticallySetTheRandomLinkKey';
        unset($expected['link_randkey']);
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($expected);
        $statement = $this->db->query("SELECT link_randkey FROM " . $this->dbTablePrefix . "links WHERE link_title = 'testSaveShouldAutomaticallySetTheRandomLinkKey'");
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
        $expected = 150;
        $link = $this->linkFactory;
        $link['link_title'] = 'testSaveShouldAutomaticallyGenerateTheLinkSummary';
        $link['link_content'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor " .
            "incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco " .
            "laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate " .
            "velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt" .
            " in culpa qui officia deserunt mollit anim id est laborum.";
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_summary FROM " . $this->dbTablePrefix . "links WHERE link_title = 'testSaveShouldAutomaticallyGenerateTheLinkSummary'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, strlen($actual[0]['link_summary']));
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
        $link['link_title'] = 'testLastIDShouldReturnTheLastInsertedId';
        $linkResource = $this->setUpLinkResource();
        $linkResource->save($link);
        $statement = $this->db->query("SELECT link_id FROM " . $this->dbTablePrefix . "links WHERE link_title = 'testLastIDShouldReturnTheLastInsertedId'");
        $savedLink = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $expected = $savedLink[0]['link_id'];

        $actual = $linkResource->getLastID();
        $this->assertEquals($expected, $actual);
    }
    /**
     * save() should trigger a tag save on each tag in a comma seperated string
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
     * SetUp the Link Resource, and return the object
     *
     * @param \Resources\Tag $tagObject The tag object (default: null)
     * @return \Resources\Link
     * @access private
     * @author Johnathan Pulos
     **/
    private function setUpLinkResource($tagObject = null)
    {
        if (is_null($tagObject)) {
            $tagObject = $this->getMock('\Resources\Tag', array('save'), array($this->db));
        }
        $linkResource = new \Resources\Link($this->db, $tagObject);
        $linkResource->setTablePrefix($this->dbTablePrefix);
        return $linkResource;
    }
}
