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
  * This script imports the Tweets from a PostGres database into the Pligg Engine
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
 * Autoload the PDO Database Class
 *
 * @author Johnathan Pulos
 */
$loader->add("PHPToolbox\PDODatabase\PDODatabaseConnect", $PHPToolboxDirectory);
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
 * Grab the PostGres Data
 */
$dbSettings = new \Config\DatabaseSettings();
$postGresSettings = $dbSettings->postgres;
$pgDatabase = new PDO("pgsql:dbname=" . $postGresSettings['name'] . ";host=" . $postGresSettings['host'] . ";");
$statement = $pgDatabase->query("SELECT * FROM social_media");
$data = $statement->fetchAll(\PDO::FETCH_ASSOC);
/**
 * Setup the mysql database
 */
$PDOClass = \PHPToolbox\PDODatabase\PDODatabaseConnect::getInstance();
$PDOClass->setDatabaseSettings($dbSettings);
$mysqlDatabase = $PDOClass->getDatabaseInstance();
/**
 * Grab the user who will get all the tweets attached
 */
$userResource = new \Resources\User($mysqlDatabase);
$userResource->setTablePrefix($dbSettings->default['table_prefix']);
$pliggUserData = $userResource->findByUserLogin($pliggUsername);
print_r($pliggUserData);
exit;
/**
 * Iterate over all the Tweets
 */
foreach ($data as $tweet) {
    $tweetLinks = array();
    $tweetHashTags =array();
    $tweetMentions = array();
    /**
     * Parse the content to get the data we need
     */
    $dom = new domDocument;
    $dom->loadHTML($tweet['content']);

    $linkAuthor = $tweet['account'];
    $linkContent = $tweet['content'];
    $linkContent .= "<p class='tweet-credits'><em>Tweeted By</em>: <a href='https://twitter.com/" . $linkAuthor . "' target='_blank'>" . $linkAuthor . "</a></p>";
    $linkProviderId = $tweet['provider_id'];
    $tweetedOn = new DateTime($tweet['provider_created_datetime']);
    // echo $tweetedOn->format('Y-m-d H:i:s');
    /**
     * Iterate over the links
     */
    $links = $dom->getElementsByTagName('a');
    foreach ($links as $link) {
        /**
         * Check the classes of the link, to determine the type of link
         */
        $linkClasses = array();
        $classNode = $link->attributes->getNamedItem('class');
        if ($classNode) {
            $linkClasses = explode(' ', $link->attributes->getNamedItem('class')->value);
        }
        $linkText = $link->nodeValue;
        if (in_array('username', $linkClasses)) {
            array_push($tweetMentions, $linkText);
        } elseif (in_array('hashtag', $linkClasses)) {
            array_push($tweetHashTags, $linkText);
        } else {
            array_push($tweetLinks, $linkText);
        }
    }
}

