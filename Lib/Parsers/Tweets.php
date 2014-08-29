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
     * The data for each link retrieved by Embedly
     *
     * @var array
     * @access protected
     **/
    protected $embedlyLinkData = array();
    /**
     * An array of defaults that should be set on all arrays
     *
     * @var array
     * @access protected
     **/
    protected $defaultLinkValues = array();
    /**
     * The chunk size for Embedly links
     *
     * @var integer
     * @access protected
     **/
    protected $chunkSize = 20;
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
     * Set the default values to add to all links
     *
     * @param array $defaults An array of default values to be added to every link
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setDefaultLinkValues($defaults)
    {
        $this->defaultLinkValues = $defaults;
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
        $embedlyLinks = array();
        foreach ($response->statuses as $tweet) {
            $links = $tweet->entities->urls;
            $tweetedOn = new \DateTime($tweet->created_at);
            $tweetHashTags = array();
            foreach ($tweet->entities->hashtags as $hashTag) {
                 array_push($tweetHashTags, $hashTag->text);
            }
            foreach ($links as $link) {
                $linkData = array(
                    "link_url"              =>  $link->expanded_url,
                    "social_media_id"       =>  $tweet->id_str,
                    "social_media_account"  =>  $tweet->user->screen_name,
                    "link_date"             =>  $tweetedOn->format("Y-m-d H:i:s"),
                    "link_published_date"   =>  $tweetedOn->format("Y-m-d H:i:s"),
                    "link_tags"             =>  implode(",", $tweetHashTags)
                );
                $mergedLinkData = array_merge($linkData, $this->defaultLinkValues);
                array_push($this->tweetedLinks, $mergedLinkData);
                array_push($embedlyLinks, $mergedLinkData['link_url']);
            }
        }
        if (!empty($embedlyLinks)) {
            $this->getEmbedlyData($embedlyLinks);
            $this->combineLinksWithEmbedlyData();
        }
        return $this->tweetedLinks;
    }
    /**
     * Grab the Embedly data for the links.  This method breaks the array into chunks, since Embedly restricts the total
     * results.  It will also append an original_url on embedlyLinkData class var to match the link_url to grab it's data.
     *
     * @param array $links An array of links to parse
     * @return object The data for each link
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function getEmbedlyData($links)
    {
        $chunks = array_chunk($links, $this->chunkSize);
        $chunkCount = 1;
        foreach ($chunks as $chunk) {
            $data = $this->embedly->oembed(array('urls' =>  $chunk));
            foreach ($data as $key => $linkData) {
                $linkData->original_url = $chunk[$key];
                array_push($this->embedlyLinkData, $linkData);
            }
            $chunkCount++;
        }
    }
    /**
     * Takes the current Embedly data (embedlyLinkData class var), and merges it with the the links (tweetedLinks class var)
     *
     * @return void
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function combineLinksWithEmbedlyData()
    {
        foreach ($this->tweetedLinks as $linkKey => $link) {
            foreach ($this->embedlyLinkData as $data) {
                /**
                 * We have the correct data for the link
                 */
                if ($data->original_url == $link['link_url']) {
                    if ((property_exists($data, 'title')) && ($data->title != '')) {
                        $title = strip_tags($data->title);
                        $this->tweetedLinks[$linkKey]['link_title'] = $title;
                        $this->tweetedLinks[$linkKey]['link_title_url'] = $this->slugify->slugify($title);
                    } else {
                        $this->tweetedLinks[$linkKey]['link_title'] = 'No Title Available';
                        $this->tweetedLinks[$linkKey]['link_title_url'] = uniqid("mobmin-tweet-");
                    }
                    if ((property_exists($data, 'description')) && ($data->description != '')) {
                        $description = strip_tags($data->description);
                        $this->tweetedLinks[$linkKey]['link_content'] = $description;
                        $this->tweetedLinks[$linkKey]['link_summary'] = $description;
                    } else {
                        $this->tweetedLinks[$linkKey]['link_content'] = 'No description available.';
                        $this->tweetedLinks[$linkKey]['link_summary'] = 'No description available.';
                    }
                    if ((property_exists($data, 'html')) && ($data->html != '')) {
                        $this->tweetedLinks[$linkKey]['link_embedly_html'] = $data->html;
                    } else {
                        $this->tweetedLinks[$linkKey]['link_embedly_html'] = '';
                    }
                    if ((property_exists($data, 'author_name')) && ($data->author_name != '')) {
                        $this->tweetedLinks[$linkKey]['link_embedly_author'] = $data->author_name;
                    } else {
                        $this->tweetedLinks[$linkKey]['link_embedly_author'] = '';
                    }
                    if ((property_exists($data, 'author_url')) && ($data->author_url != '')) {
                        $this->tweetedLinks[$linkKey]['link_embedly_author_link'] = $data->author_url;
                    } else {
                        $this->tweetedLinks[$linkKey]['link_embedly_author_link'] = '';
                    }
                    if ((property_exists($data, 'thumbnail_url')) && ($data->thumbnail_url != '')) {
                        $this->tweetedLinks[$linkKey]['link_embedly_thumb_url'] = $data->thumbnail_url;
                    } else {
                        $this->tweetedLinks[$linkKey]['link_embedly_thumb_url'] = '';
                    }
                }
            }
        }
    }
}
