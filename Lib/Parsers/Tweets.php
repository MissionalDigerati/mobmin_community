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
namespace Parsers;

/**
 * The Link Resource for managing links to the Pligg site
 */
class Tweets
{
    /**
     * The Embedly Object for retrieving the link information
     *
     * @var \Embedly\Embedly
     * @access protected
     **/
    protected $embedly;
    /**
     * The Slugify Object for creating a slug from a string
     *
     * @var \Cocur\Slugify\Slugify
     * @access protected
     **/
    protected $slugify;
    /**
     * The links that have been parsed from the Twittter response
     *
     * @var array
     * @access protected
     **/
    protected $tweetedLinks = array();
    /**
     * Construct the class
     *
     * @param \Embedly\Embedly $embedlyObj The Embedly object for retrieving link information
     * @param \Cocur\Slugify\Slugify $slugifyObj The Slugify object for turning strings into slugs
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function __construct($embedlyObj, $slugifyObj)
    {
        $this->setEmbedlyObject($embedlyObj);
        $this->setSlugifyObject($slugifyObj);
    }
    /**
     * Sets the $embedly class variable
     *
     * @param \Embedly\Embedly $embedlyObj The Embedly object for retrieving link information
     * @return void
     * @access protected
     * @throws InvalidArgumentException if $embedlyObj is not a \Embedly\Embedly Object
     * @author Johnathan Pulos
     **/
    protected function setEmbedlyObject($embedlyObj)
    {
        if (is_a($embedlyObj, '\Embedly\Embedly')) {
            $this->embedly = $embedlyObj;
        } else {
            throw new \InvalidArgumentException('$embedlyObj must be of the class \Embedly\Embedly.');
        }
    }
    /**
     * Sets the $slugify class variable
     *
     * @param \Cocur\Slugify\Slugify $slugifyObj The Slugify object for turning strings into slugs
     * @return void
     * @access protected
     * @throws InvalidArgumentException if $slugifyObj is not a \Cocur\Slugify\Slugify Object
     * @author Johnathan Pulos
     **/
    protected function setSlugifyObject($slugifyObj)
    {
        if (is_a($slugifyObj, '\Cocur\Slugify\Slugify')) {
            $this->slugify = $slugifyObj;
        } else {
            throw new \InvalidArgumentException('$slugifyObj must be of the class \Cocur\Slugify\Slugify.');
        }
    }
    /**
     * Parses the data that is retrieved from the API.  The data should be a JSON object.
     *
     * @param object $response The response object from the API
     * @return array The links to save
     * @access public
     * @author Johnathan Pulos
     **/
    public function parseLinksFromAPI($response)
    {
        foreach ($response->statuses as $tweet) {
            $links = $tweet->entities->urls;
            foreach ($links as $link) {
                $linkData = array(
                    "link_url"              =>  $link->expanded_url,
                    "social_media_id"       =>  $tweet->id_str,
                    "social_media_account"  =>  $tweet->user->screen_name
                );
                array_push($this->tweetedLinks, $linkData);
            }
        }
        return $this->tweetedLinks;
    }
}
