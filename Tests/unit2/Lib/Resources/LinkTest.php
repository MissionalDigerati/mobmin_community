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
     * A factory for creating a link
     *
     * @var array
     * @access private
     **/
    private $linkFactory = array(
        'link_author'           =>  'A Link Author',
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
        'link_summary'          =>  'I really love this place!',
        'link_tags'             =>  'google, rocks'
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
        $this->linkFactory['link_randkey'] = rand(10000,10000000);
        $today = date('Y-m-d H:i:s',time());
        $this->linkFactory['link_modified'] = $today;
        $this->linkFactory['link_date'] = $today;
        $this->linkFactory['link_published_date'] = $today;

        $pdoDb = \PHPToolbox\PDODatabase\PDODatabaseConnect::getInstance();
        $pdoDb->setDatabaseSettings(new \support\DatabaseSettings);
        $this->db = $pdoDb->getDatabaseInstance();
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
        $link = $this->linkFactory;
        $link['link_title'] = 'testSaveShouldSaveALinkIntoTheDatabase';
        $linkResource = new \Resources\Link($this->db);
        $linkResource->save($link);
    }
}
