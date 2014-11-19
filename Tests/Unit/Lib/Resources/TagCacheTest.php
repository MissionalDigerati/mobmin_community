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
 * Test the TagCache Resource
 *
 * @author Johnathan Pulos
 */
class TagCacheTest extends \PHPUnit_Framework_TestCase
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
     * Setup the test
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setUp()
    {
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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tag_cache");
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "links");
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tags");
    }
    /**
     * reset() should reset the tag_cache data
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testResetShouldRecreateTheTagCache()
    {
        $expectedCount = 15;
        for ($i=1; $i <= $expectedCount; $i++) { 
            $this->db->query(
                "INSERT INTO " . $this->dbTablePrefix . "tags (tag_link_id, tag_lang, tag_date, tag_words) VALUES(" .
                $i . ", 'en', NOW(), 'clown')"
            );
            $this->db->query(
                "INSERT INTO " . $this->dbTablePrefix . "links (link_id, link_status)" .
                " VALUES(" . $i . ", 'new')"
            );
        }
        $tagCacheResource = new \Resources\TagCache($this->db);
        $tagCacheResource->setTablePrefix($this->dbTablePrefix);
        $tagCacheResource->reset();
        $statement = $this->db->query("SELECT count FROM " . $this->dbTablePrefix . "tag_cache WHERE tag_words = 'clown'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expectedCount, intval($actual[0]['count']));
    }
}
