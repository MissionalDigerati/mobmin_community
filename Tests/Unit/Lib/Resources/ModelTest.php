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
 * Test the Model for all resources
 *
 * @author Johnathan Pulos
 */
class ModelTest extends \PHPUnit_Framework_TestCase
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
     * setUp the Test class
     *
     * @return void
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
     * __construct should throw an error if passed a non PDO object for a database
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructThrowsErrorIfGivenANonPDOObject()
    {
        $model = new \Resources\Model('Not a PDO object');
    }
    /**
     * getInsertQuery() should generate the correct query statement
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testGetInsertQueryShouldGenerateTheCorrectQuery()
    {
        $expected = "INSERT INTO " . $this->dbTablePrefix . "users(name, date) VALUES(:name, :date)";
        $data = array(
            'name'  =>  'Bob',
            'date'  =>  '2013-21-23'
        );
        $model = new \Resources\Model($this->db);
        $reflectionOfModel = new \ReflectionClass('\Resources\Model');

        $accessibleAttributes = $reflectionOfModel->getProperty('accessibleAttributes');
        $accessibleAttributes->setAccessible(true);
        $accessibleAttributes->setValue($model, array('name', 'date'));

        $tableName = $reflectionOfModel->getProperty('tableName');
        $tableName->setAccessible(true);
        $tableName->setValue($model, 'users');

        $method = $reflectionOfModel->getMethod('getInsertQuery');
        $method->setAccessible(true);
        $actual = $method->invoke($model, $data);
        $this->assertEquals($expected, $actual);
    }
    /**
     * getUpdateQuery() should generate the correct query statement
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testGetUpdateQueryShouldGenerateTheCorrectQuery()
    {
        $expected = "UPDATE " . $this->dbTablePrefix . "users SET hobbies = :hobbies, talents = :talents WHERE user_id = :user_id";
        $data = array(
            'hobbies'   =>  'Golfing, Swimming, Gaming',
            'talents'   =>  'Programming'
        );
        $model = new \Resources\Model($this->db);
        $reflectionOfModel = new \ReflectionClass('\Resources\Model');

        $accessibleAttributes = $reflectionOfModel->getProperty('accessibleAttributes');
        $accessibleAttributes->setAccessible(true);
        $accessibleAttributes->setValue($model, array('hobbies', 'talents'));

        $tableName = $reflectionOfModel->getProperty('tableName');
        $tableName->setAccessible(true);
        $tableName->setValue($model, 'users');

        $tableName = $reflectionOfModel->getProperty('primaryKey');
        $tableName->setAccessible(true);
        $tableName->setValue($model, 'user_id');

        $method = $reflectionOfModel->getMethod('getUpdateQuery');
        $method->setAccessible(true);
        $actual = $method->invoke($model, $data);
        $this->assertEquals($expected, $actual);
    }
    /**
     * setTablePrefix() should set the class attribute tablePrefix
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testSetTablePrefixShouldSetTheTablePrefix() {
        $expected = 'my_unique_table_prefix.';
        $model = new \Resources\Model($this->db);
        $reflectionOfModel = new \ReflectionClass('\Resources\Model');

        $method = $reflectionOfModel->getMethod('setTablePrefix');
        $method->setAccessible(true);
        $method->invoke($model, $expected);

        $tablePrefix = $reflectionOfModel->getProperty('tablePrefix');
        $tablePrefix->setAccessible(true);
        $actual = $tablePrefix->getValue($model);

        $this->assertEquals($expected, $actual);
    }
    /**
     * cleanNonWhitelistedData() should remove data that is not whitelisted
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function testCleanNonWhitelistedDataRemovesUnWantedData()
    {
        $expected = array(
            'hobbies'   =>  'Golfing, Swimming, Gaming',
            'talents'   =>  'Programming'
        );
        $data = array(
            'name'      =>  'Hacker',
            'hobbies'   =>  'Golfing, Swimming, Gaming',
            'talents'   =>  'Programming',
            'your_site' =>  'SUCKS!!!!!'
        );
        $model = new \Resources\Model($this->db);
        $reflectionOfModel = new \ReflectionClass('\Resources\Model');

        $accessibleAttributes = $reflectionOfModel->getProperty('accessibleAttributes');
        $accessibleAttributes->setAccessible(true);
        $accessibleAttributes->setValue($model, array('hobbies', 'talents'));

        $method = $reflectionOfModel->getMethod('cleanNonWhitelistedData');
        $method->setAccessible(true);
        $actual = $method->invoke($model, $data);
        $this->assertEquals($expected, $actual);
    }
}
