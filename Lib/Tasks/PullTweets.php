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
 * SET THIS TO THE MAXIMUM NUMBER OF LINKS THAT CAN BE SENT TO EMBEDLY
 */
$embedlyMaxLinks = 20;
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
 * Autoload the slugify library
 */
$loader->setClass("Cocur\Slugify\Slugify", $vendorDirectory . "cocur" . $DS . "slugify" . $DS . "src" . $DS . "Slugify.php");
$loader->setClass("Cocur\Slugify\SlugifyInterface", $vendorDirectory . "cocur" . $DS . "slugify" . $DS . "src" . $DS . "SlugifyInterface.php");
$slugify = new \Cocur\Slugify\Slugify();
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
$loader->add("Parsers\Tweets", $libDirectory);
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
$filteredResponse = $response;
/**
 * Let's filter the response, so we do not overburden Embedly
 */
foreach ($response->statuses as $key => $tweet) {
    $linkProviderId = $tweet->id_str;
    /**
     * Have we parsed this tweet?
     */
    if ($linkResource->exists($linkProviderId, 'social_media_id') === true) {
        unset($filteredResponse->statuses[$key]);
    }
}
/**
 * Now intialize the parser, and have it prepare all the links for the database
 */
$embedlyAPI = new \Embedly\Embedly(array('key'   =>  $embedlySettings->APIKey));
$parser = new \Parsers\Tweets($embedlyAPI, $slugify);
$defaults =  array(
    'link_author'   =>  $pliggUserData[0]['user_id'],
    'link_status'   =>  'published',
    'link_randkey'  =>  0,
    'link_votes'    =>  1,
    'link_karma'    =>  1,
    'link_modified' =>  '',
    'link_category' =>  $pliggCategory
);
$parser->setDefaultLinkValues($defaults);
$links = $parser->parseLinksFromAPI($filteredResponse);
foreach ($links as $link) {
    if ($linkResource->exists($link['link_url'], 'link_url') === false) {
        if ($link['link_embedly_type'] == 'error') {
            echo "This link " . $link['link_url'] . " returned an error.\r\n";
        } else {
            /**
             * Now save the link and break out of this loop
             */
            try {
                $linkResource->save($link);
                echo "Inserted the link '" . $link['link_url'] . "' titled '" . $link['link_title'] . "'\r\n";
            } catch (Exception $e) {
                echo "There was a problem inserting the link '" . $link['link_url'] . "' titled '" . $link['link_title'] . "'\r\n";
                echo "Error: " . $e->getMessage() . "\r\n";
            }
        }
    }
}
