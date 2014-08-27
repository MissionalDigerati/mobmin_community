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
 /**
  * This script pulls the latests tweets
  */
$DS = DIRECTORY_SEPARATOR;
$rootDirectory = __DIR__ . $DS . ".." . $DS . "..";
$libDirectory = $rootDirectory . $DS . "Lib" . $DS;
$vendorDirectory = $rootDirectory . $DS . "Vendor" . $DS;
$PHPToolboxDirectory = $vendorDirectory . "PHPToolbox" . $DS . "src" . $DS;
/**
 * SET THIS TO THE USER THAT THESE STORIES WILL BE ATTRIBUTED TO
 */
$pliggUsername = 'MobMin';
/**
 * SET THIS TO THE CATEGORY ID THAT THESE STORIES WILL BE ATTRIBUTED TO
 */
$pliggCategory = 1;
/**
 * SET THIS TO THE HASHTAG THAT YOU WANT TO PULL
 */
$hashTagToSearch = '#MobMin';
/**
 * Load up the Aura
 *
 * @author Johnathan Pulos
 */
$loader = require $vendorDirectory . "aura" . $DS . "autoload" . $DS . "scripts" . $DS . "instance.php";
$loader->register();
/**
 * Silent the Autoloader so we can see correct errors
 *
 * @author Johnathan Pulos
 */
$loader->setMode(\Aura\Autoload\Loader::MODE_SILENT);
/**
 * Setup the database object
 *
 * @author Johnathan Pulos
 */
$loader->add("Config\DatabaseSettings", $rootDirectory);
/**
 * Setup the Twitter settings object
 *
 * @author Johnathan Pulos
 */
$loader->add("Config\TwitterSettings", $rootDirectory);
/**
 * Setup the Embedly settings object
 *
 * @author Johnathan Pulos
 */
$loader->add("Config\EmbedlySettings", $rootDirectory);
/**
 * Autoload the PDO Database Class
 *
 * @author Johnathan Pulos
 */
$loader->add("PHPToolbox\PDODatabase\PDODatabaseConnect", $PHPToolboxDirectory);
/**
 * Autoload the Twitter OAuth
 *
 * @author Johnathan Pulos
 */
$loader->add("TwitterOAuth\TwitterOAuth", $vendorDirectory . "ricardoper" . $DS . "twitteroauth");
$loader->add("TwitterOAuth\Exception\TwitterException", $vendorDirectory . "ricardoper" . $DS . "twitteroauth");
/**
 * Autoload Embedly Library
 */
$loader->add("Embedly\Embedly", $vendorDirectory . "embedly" . $DS . "embedly-php" . $DS . "src");
$embedlySettings = new \Config\EmbedlySettings();
/**
 * Setup the Embedly API key
 */
/**
 * Autoload the lib classes
 *
 * @author Johnathan Pulos
 */
$loader->add("Resources\Model", $libDirectory);
$loader->add("Resources\Link", $libDirectory);
$loader->add("Resources\Tag", $libDirectory);
$loader->add("Resources\Total", $libDirectory);
$loader->add("Resources\User", $libDirectory);
/**
 * Setup the mysql database
 */
$dbSettings = new \Config\DatabaseSettings();
$PDOClass = \PHPToolbox\PDODatabase\PDODatabaseConnect::getInstance();
$PDOClass->setDatabaseSettings($dbSettings);
$mysqlDatabase = $PDOClass->getDatabaseInstance();
/**
 * Grab the user who will get all the tweets attached
 */
$userResource = new \Resources\User($mysqlDatabase);
$userResource->setTablePrefix($dbSettings->default['table_prefix']);
$pliggUserData = $userResource->findByUserLogin($pliggUsername);
/**
 * Instantiate the link class
 */
$linkResource = new \Resources\Link($mysqlDatabase, new \Resources\Tag($mysqlDatabase), new \Resources\Total($mysqlDatabase));
$linkResource->setTablePrefix($dbSettings->default['table_prefix']);
/**
 * Connect OAuth to get tokens
 */
$twitterSettings = new \Config\TwitterSettings();
$twitterRequest = new \TwitterOAuth\TwitterOAuth($twitterSettings->config);
/**
 * Get the current tweets
 */
$params = array('count' => 100, 'q' => urlencode($hashTagToSearch));
$response = $twitterRequest->get('search/tweets', $params);
/**
 * Arrays to hold all the links, and link data
 */
$linkResources = array();
$linksToEmbedly = array();
/**
 * Iterate over all tweets, and isert into the database
 */
foreach ($response->statuses as $tweet) {
    $linkProviderId = $tweet->id_str;
    $links = $tweet->entities->urls;
    $tweetedOn = new DateTime($tweet->created_at);
    $dateOfTweet = $tweetedOn->format("Y-m-d H:i:s");
    $tweetHashTags = array();
    foreach ($tweet->entities->hashtags as $hashTag) {
         array_push($tweetHashTags, $hashTag->text);
    }
    $linkTags = implode(',', $tweetHashTags);
    /**
     * Check if the tweet has already been processed
     */
    if ($linkResource->exists($linkProviderId, 'social_media_id') === false) {
        if (!empty($links)) {
            foreach ($links as $link) {
                $expandedLink = $link->expanded_url;
                /**
                 * Check if the link has been processed already
                 */
                if (in_array($expandedLink, $linksToEmbedly) === false) {
                    /**
                     * Check if the link is already in the database
                     */
                    if ($linkResource->exists($expandedLink, 'link_url') === false) {
                        $linkData = array(
                            'link_date'             =>  $dateOfTweet,
                            'link_published_date'   =>  $dateOfTweet,
                            'link_url'              =>  $expandedLink,
                            'link_tags'             =>  $linkTags
                        );
                        array_push($linkResources, $linkData);
                        array_push($linksToEmbedly, $expandedLink);
                    } else {
                        /**
                         * TODO: Apply tags to the existing link
                         */
                    }
                }
            }
        }
    }
}
$embedlyAPI = new \Embedly\Embedly(array('key'   =>  $embedlySettings->APIKey));
$embedlyResults = $embedlyAPI->oembed(array('urls' =>  $linksToEmbedly));
/**
 * We now have 2 arrays that we can use:
 * $linkResources - This array holds some of the link information we need to save to the database
 * $embedlyResults - This is an array of objects providing detailed information, and embed code for each link
 */
