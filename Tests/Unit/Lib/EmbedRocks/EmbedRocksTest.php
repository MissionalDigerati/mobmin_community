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
        $expected = new \stdClass();
        $expected->title = 'I Love Cows... On My Plate.';
        $expected->description = 'Learn how to cook a cow.';
        $expected->article = 'Here is the best way to cook cow...';
        $image = new \stdClass();
        $image->url = 'http://image.com/cow-steak.png';
        $image->height = 500;
        $image->width = 500;
        $expected->images = array($image);
        $curlMock = $this->getMock('\PHPToolbox\CachedRequest\CurlUtility', array('makeRequest'));
        $curlMock->expects($this->exactly(1))
                    ->method('makeRequest')
                    ->with('https://api.embed.rocks/api/?key=my-api-key&url=http%3A%2F%2Fmashable.com%2F', 'GET')
                    ->will($this->returnValue(json_encode($expected)));
        $embed = new \EmbedRocks\EmbedRocks('my-api-key', $curlMock);
        $actual = $embed->get('http://mashable.com/');
        $this->assertEquals($expected->title, $actual->title);
        $this->assertEquals($expected->description, $actual->description);
        $this->assertEquals($expected->article, $actual->article);
        $this->assertEquals($expected->images[0]->url, $actual->images[0]->url);
        $this->assertEquals($expected->images[0]->height, $actual->images[0]->height);
        $this->assertEquals($expected->images[0]->width, $actual->images[0]->width);
    }
}
