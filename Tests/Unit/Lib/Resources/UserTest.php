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
 * Test the User Resource
 *
 * @author Johnathan Pulos
 */
class UserTest extends \PHPUnit_Framework_TestCase
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
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "users");
    }
    /**
     * findByUserLogin() should find a user by their username
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testFindByUserLoginShouldFindAUserByTheirUsername()
    {
        /**
         * Create a users
         */
        $query = "INSERT INTO " . $this->dbTablePrefix . "users (user_login, user_level, user_pass, user_email, " .
            "user_enabled) VALUES('MobMin-Hashtag', 'normal', '87452053b13f307f8ead48d528223fc4436f52428a88ba84e', " .
            "'johnathan@missionaldigerati.org', 1)";
        $this->db->query($query);
        $expected = intval($this->db->lastInsertId());
        /**
         * Just make sure it saved
         */
        $this->assertNotEquals($expected, 0);
        $userResource = new \Resources\User($this->db);
        $userResource->setTablePrefix($this->dbTablePrefix);
        $actual = $userResource->findByUserLogin('MobMin-Hashtag');
        $this->assertFalse(empty($actual));
        $this->assertEquals($expected, intval($actual[0]['user_id']));
    }
}
