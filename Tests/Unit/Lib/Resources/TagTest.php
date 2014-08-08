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
 * Test the Tag Resource
 *
 * @author Johnathan Pulos
 */
class TagTest extends \PHPUnit_Framework_TestCase
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
    private $tagFactory = array(
        'tag_link_id'   =>  0,
        'tag_date'      =>  null,
        'tag_words'     =>  'Dr. Who'
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
        $this->tagFactory['tag_date'] = $today;

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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tags");
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "links");
    }
    /**
     * save() should save a Tag in the database
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldSaveATagIntoTheDatabase()
    {
        $expected = $this->tagFactory;
        $expected['tag_words'] = 'testSaveShouldSaveATagIntoTheDatabase Method';
        $tagResource = $this->setUpTagResource();
        $tagResource->save($expected);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tags WHERE tag_words = 'testSaveShouldSaveATagIntoTheDatabase Method'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals(count($actual), 1);
        $this->assertEquals($expected['tag_words'], $actual[0]['tag_words']);
    }
    /**
     * save() should strip tags on the tag_words attribute
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSaveShouldStripTagsOnTagWords()
    {
        $expected = 'testSaveShouldStripTagsOnTagWords Method';
        $tag = $this->tagFactory;
        $tag['tag_words'] = '<p><strong>testSaveShouldStripTagsOnTagWords</strong> Method</p>';
        $tagResource = $this->setUpTagResource();
        $tagResource->save($tag);
        $statement = $this->db->query("SELECT * FROM " . $this->dbTablePrefix . "tags WHERE tag_words = 'testSaveShouldStripTagsOnTagWords Method'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual[0]['tag_words']);
    }
    /**
     * SetUp the Tag Resource, and return the object
     *
     * @return \Resources\Tag
     * @access private
     * @author Johnathan Pulos
     **/
    private function setUpTagResource()
    {
        $tagResource = new \Resources\Tag($this->db);
        $tagResource->setTablePrefix($this->dbTablePrefix);
        return $tagResource;
    }
}
