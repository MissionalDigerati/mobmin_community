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
 * Test the Total Resource
 *
 * @author Johnathan Pulos
 */
class TotalTest extends \PHPUnit_Framework_TestCase
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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "totals");
        /**
         * Setup the totals table to the default
         */
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('published', 0)");
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('new', 0)");
        $this->db->query("INSERT INTO " . $this->dbTablePrefix . "totals (name, total) VALUES('discard', 0)");
    }
    /**
     * increment('published') should increment by one the published total
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testIncrementShouldAddOneToATotal()
    {
        $expected = 1;
        $totalResource = new \Resources\Total($this->db);
        $totalResource->setTablePrefix($this->dbTablePrefix);
        $totalResource->increment('published');
        $statement = $this->db->query("SELECT total FROM " . $this->dbTablePrefix . "totals WHERE name = 'published'");
        $actual = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, intval($actual[0]['total']));
    }
    /**
     * increment() throws error if not passed a correct status
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testIncrementShouldThrowErrorIfWrongStatusProvided()
    {
        $totalResource = new \Resources\Total($this->db);
        $totalResource->setTablePrefix($this->dbTablePrefix);
        $totalResource->increment('not_right');
    }
}
