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
 * Set the default date timezone
 *
 * @author Johnathan Pulos
 */
date_default_timezone_set('America/Los_Angeles');
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
$loader->add("Resources\TagCache", $libDirectory);
$loader->add("Resources\Total", $libDirectory);
$loader->add("Resources\TweetFeed", $libDirectory);
$loader->add("Resources\TweetFeedAvatar", $libDirectory);
$loader->add("Resources\User", $libDirectory);
$loader->add("Parsers\Tweets", $libDirectory);
/**
 * Connect OAuth to get tokens
 */
$twitterSettings = new \Config\TwitterSettings();
$twitterRequest = new \TwitterOAuth\TwitterOAuth($twitterSettings->config);
/**
 * Setup the mysql database
 */
$dbSettings = new \Config\DatabaseSettings();
$PDOClass = \PHPToolbox\PDODatabase\PDODatabaseConnect::getInstance();
$PDOClass->setDatabaseSettings($dbSettings);
$mysqlDatabase = $PDOClass->getDatabaseInstance();
/**
 * Grab the PostGres Data
 */
$postGresSettings = $dbSettings->postgres;
$pgDatabase = new PDO("pgsql:dbname=" . $postGresSettings['name'] . ";host=" . $postGresSettings['host'] . ";");
$statement = $pgDatabase->query("SELECT * FROM social_media");
$pgData = $statement->fetchAll(\PDO::FETCH_ASSOC);
/**
 * Set the avatar for each item
 */
$screenNames = array();
foreach ($pgData as $key => $val) {
    $statement = $pgDatabase->query("SELECT * FROM social_avatars WHERE (social_avatars.provider = 'twitterhash' and social_avatars.account = '" . $val['account'] . "') LIMIT 1");
    $avatar = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pgData[$key]['avatar'] = $avatar[0];
    if (!in_array($val['account'], $screenNames)) {
        array_push($screenNames, $val['account']);
    }
}
/**
 * Grab the Tweeter Id's from Twitter
 */
$screenNamesAndIds = array();
$chunks = array_chunk($screenNames, 100);
$chunkCount = 1;
foreach ($chunks as $chunk) {
    $screenNamesAsString = implode(",", $chunk);
    $params = array('screen_name' => $screenNamesAsString);
    $response = $twitterRequest->get('users/lookup', $params);
    foreach ($response as $userData) {
        $screenNamesAndIds[$userData->screen_name] = $userData->id_str;
    }
}
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
 * Instantiate the TweetFeed classes
 */
$tweetFeedResource = new \Resources\TweetFeed($mysqlDatabase);
$tweetFeedResource->setTablePrefix($dbSettings->default['table_prefix']);
$tweetFeedAvatarResource = new \Resources\TweetFeedAvatar($mysqlDatabase);
$tweetFeedAvatarResource->setTablePrefix($dbSettings->default['table_prefix']);
/**
 * Iterate over the data, save the tweets, and setup links for parsing
 */
$linksToProcess = array();
foreach ($pgData as $tweet) {
    /**
     * Check if the tweet was added to the TweetFeed Module
     */
    if ($tweetFeedResource->exists($tweet['provider_id'], 'tweet_id') === false) {
        $errorSaving = false;
        $today = new DateTime();
        $tweetedOn = new DateTime($tweet['provider_created_datetime']);
        /**
         * Grab the users Twitter.id
         */

        /**
         * Parse the content to get the data we need
         */
        $dom = new domDocument;
        $dom->loadHTML($tweet['content']);
        /**
         * Check if we have a Twitter id!  If we do not the account does not exist anymore, so ignore the tweet.
         */
        if (array_key_exists($tweet['account'], $screenNamesAndIds)) {
            $tweeterId = $screenNamesAndIds[$tweet['account']];
            $tweetData = array(
                'tweet_id'          =>  $tweet['provider_id'],
                'tweeter_id'        =>  $tweeterId,
                'tweeter_name'      =>  $tweet['account'],
                'content'           =>  strip_tags($tweet['content']),
                'published_date'    =>  $tweetedOn->format("Y-m-d H:i:s")
            );
            try {
                $tweetFeedResource->save($tweetData);
                echo "Saved the tweet posted by " . $tweetData['tweeter_name'] . " on " . $tweetedOn->format("Y-m-d H:i:s") . "\r\n";
            } catch (Exception $e) {
                echo "Unable to save the tweet posted by " . $tweetData['tweeter_name'] . " on " . $tweetedOn->format("Y-m-d H:i:s") . "\r\n";
                echo "Error: " . $e->getMessage() . "\r\n";
                $errorSaving = true;
            }
            if ($errorSaving === false) {
                /**
                 * If they do not have an avatar, we want to insert it.  Any avatars that already exist are probably outdated since these are older tweets. 
                 */
                if ($tweetFeedAvatarResource->exists($tweeterId, 'tweeter_id') === false) {
                    /**
                     * Save the avatar
                     */
                    $tweetAvatarData = array(
                        'tweeter_id'            =>  $tweeterId,
                        'tweeter_name'          =>  $tweet['account'],
                        'tweeter_avatar_url'    =>  $tweet['avatar']['avatar_url'],
                        'last_updated'          =>  $today->format("Y-m-d H:i:s")
                    );
                    try {
                        $tweetFeedAvatarResource->save($tweetAvatarData);
                        echo "Saved the tweet avatar for " . $tweet['account'] . "\r\n";
                    } catch (Exception $e) {
                        echo "Unable to save the tweet avatar for " . $tweet['account'] . "\r\n";
                        echo "Error: " . $e->getMessage() . "\r\n";
                    }
                }
            }
        } else {
            echo "We are missing an ID for the account: " . $tweet['account'] . "\r\n";
        }
    }
}

