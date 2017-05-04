<?php
/**
 * This file is part of #MobMin Community.
 *
 * #MobMin Community is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * #MobMin Community is distributed in the hope that it will be useful,
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
namespace Tests\Unit\Lib\EmbedRocks;

/**
 * Test the EmbedRocks Tool
 *
 * @author Johnathan Pulos
 */
class EmbedRocksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * A JSON Object that represents the response of Embed Rocks
     *
     * @var Object
     * @access private
     **/
    private $embedRocksFactory;
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
        $jsonFile = __DIR__ . $DS . ".." . $DS . ".." . $DS . ".." . $DS . "Support" . $DS . "Factories" . $DS . "EmbedRocksResults.json";
        $this->embedRocksFactory = file_get_contents($jsonFile);
    }
    /**
     * __construct should throw an error if api key is blank
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructThrowsErrorIfGivenBlankAPIKey()
    {
        $curlMock = $this->getMockBuilder('\PHPToolbox\CachedRequest\CurlUtility')->getMock();
        $embed = new \EmbedRocks\EmbedRocks('', $curlMock);
    }

    /**
     * __construct should throw an error if curl utility is not a class of \PHPToolbox\CachedRequest\CurlUtility
     *
     * @return void
     * @access public
     * @expectedException InvalidArgumentException
     * @author Johnathan Pulos
     **/
    public function testConstructThrowsErrorIfGivenWrongCurlUtility()
    {
        $embed = new \EmbedRocks\EmbedRocks('my-api-key', new \stdClass());
    }

    /**
     * get() should return the data.
     *
     * @return void
     * @access public
     * @author Johnathan Pulos
     */
    public function testGetShouldReturnTheData()
    {
        $expected = json_decode($this->embedRocksFactory);
        $curlMock = $this->getMock('\PHPToolbox\CachedRequest\CurlUtility', array('makeRequest'));
        $curlMock->expects($this->exactly(1))
                    ->method('makeRequest')
                    ->with('https://api.embed.rocks/api/?key=my-api-key&url=http%3A%2F%2Fmashable.com%2F', 'GET')
                    ->will($this->returnValue($this->embedRocksFactory));
        $embed = new \EmbedRocks\EmbedRocks('my-api-key', $curlMock);
        $actual = $embed->get('http://mashable.com/');
        $this->assertEquals($expected->title, $actual->title);
        $this->assertEquals($expected->description, $actual->description);
        $this->assertEquals($expected->article, $actual->article);
        $this->assertEquals($expected->html, $actual->html);
        $this->assertEquals($expected->site, $actual->site);
    }
}
