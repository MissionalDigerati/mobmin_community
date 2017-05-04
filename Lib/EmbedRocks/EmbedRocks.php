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
namespace EmbedRocks;
/**
 * The EmbedRocks Tool
 *
 * @author Johnathan Pulos
 */
class EmbedRocks
{
    /**
     * The current API key for Embed.Rocks
     *
     * @param string $apiKey The API key
     * @access protected
     */
    protected $apiKey = '';
    /**
     * The URL to use to request the embed data.
     *
     * @param string url The URL
     * @access protected
     */
    protected $url = 'https://api.embed.rocks/api/?key=<key>&url=<url>';
    /**
     * The cURL library to make a request.
     *
     * @param \PHPToolbox\CachedRequest\CurlUtility
     * @access protected
     */
    protected $curl;

    /**
     * Setup the class
     *
     * @param string $apiKey The API key for Embed.rocks
     */
    public function __construct($apiKey, $curlUtility)
    {
        if (!$apiKey) {
            throw new \InvalidArgumentException('$apiKey must be a valid API key from Embed.Rocks.');
        } else if (!is_a($curlUtility, '\PHPToolbox\CachedRequest\CurlUtility')) {
            throw new \InvalidArgumentException('$curlUtility must be of the class \PHPToolbox\CachedRequest\CurlUtility.');
        } else {
            $this->apiKey = $apiKey;
            $url = str_replace('<key>', $apiKey, $this->url);
            $this->url = $url;
            $this->curl = $curlUtility;
        }
    }

    /**
     * Get the data for the given link.
     *
     * @param  string $url The URL to get data for
     * @return object      The Embedable Data
     */
    public function get($url) {
        $encoded = urlencode($url);
        $url = str_replace('<url>', $encoded, $this->url);
        $response = $this->curl->makeRequest($url, 'GET');
        return json_decode($response);
    }
}
