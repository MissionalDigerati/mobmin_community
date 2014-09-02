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
$module_info['name'] = 'Tweet Feed';
$module_info['desc'] = 'Displays tweets submitted with the #MobMin hashtags.  This requires a CRON task to pull the feed data.  This is intended to be used withonly this site.';
$module_info['version'] = 1.0;
$module_info['homepage_url'] = 'http://www.missionaldigerati.org';
$module_info['update_url'] = 'http://www.missionaldigerati.org';
// Add new table
$module_info['db_add_table'][]=array(
    'name' => table_prefix . "tweet_feed",
    'sql' => "CREATE TABLE `".table_prefix . "tweet_feed` (
      `tweet_feed_id` int(11) NOT NULL auto_increment,
      `tweet_id` varchar(255),
      `tweeter_id` varchar(255),
      `tweeter_name` varchar(255),
      `content` text NOT NULL,
      `published_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (`tweet_feed_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
);
// Add new table
$module_info['db_add_table'][]=array(
    'name' => table_prefix . "tweet_avatars",
    'sql' => "CREATE TABLE `".table_prefix . "tweet_avatars` (
      `tweet_avatar_id` int(11) NOT NULL auto_increment,
      `tweeter_id` varchar(255),
      `tweeter_name` varchar(255),
      `tweeter_avatar_url` text NOT NULL,
      `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY  (`tweet_avatar_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
);
?>