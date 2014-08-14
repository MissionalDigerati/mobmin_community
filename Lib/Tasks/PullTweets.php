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
 * Iterate over all tweets, and isert into the database
 */
foreach ($response->statuses as $tweet) {
    $linkProviderId = $tweet->id_str;
    if ($linkResource->exists($linkProviderId, 'social_media_id') === false) {
        $links = $tweet->entities->urls;
        $tweetHashTags = array();
        foreach ($tweet->entities->hashtags as $hashTag) {
             array_push($tweetHashTags, $hashTag->text);
        }
        $tweetedOn = new DateTime($tweet->created_at);

        $linkCount = 1;
        foreach ($links as $link) {
            $titleSlug = "mobmin-tweet-" . $linkProviderId;
            if ($linkCount > 1) {
                $titleSlug .= "-" . $linkCount;
            }
            $linkTags = implode(',', $tweetHashTags);
            $linkData = array(
                'link_author'           =>  $pliggUserData[0]['user_id'],
                'link_status'           =>  'published',
                'link_randkey'          =>  0,
                'link_votes'            =>  1,
                'link_karma'            =>  1,
                'link_modified'         =>  '',
                'link_date'             =>  $tweetedOn->format("Y-m-d H:i:s"),
                'link_published_date'   =>  $tweetedOn->format("Y-m-d H:i:s"),
                'link_category'         =>  $pliggCategory,
                'link_url'              =>  $link->url,
                'link_url_title'        =>  '',
                'link_title'            =>  '',
                'link_title_url'        =>  $titleSlug,
                'link_content'          =>  $tweet->text,
                'link_summary'          =>  '',
                'link_tags'             =>  $linkTags,
                'social_media_id'       =>  $linkProviderId,
                'social_media_account'  =>  $tweet->user->screen_name
            );
            try {
                $linkResource->save($linkData);
                echo "Inserted the tweet from " . $linkData['social_media_account'] . " tweeted on " . $tweetedOn->format("Y-m-d H:i:s") . "\r\n";
            } catch (Exception $e) {
                echo "There was a problem iserting from " . $linkData['social_media_account'] . " tweeted on " . $tweetedOn->format("Y-m-d H:i:s") . "\r\n";
                echo "Error: " . $e->getMessage();
            }
            $linkCount++;
        }
    }
}
