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
function tweet_feed_get_current_page($totalPages) {
    $currentPage = (isset($_REQUEST['current-page'])) ? intval($_REQUEST['current-page']) : 1;
    if ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    } elseif ($currentPage < 1) {
        $currentPage = 1;
    }
    return $currentPage;
}
function tweet_feed_get_tweets()
{
    global $db, $main_smarty;
    $resultsPerPage = 20;
    /**
     * Setup the pagination information
     */
    $query = "SELECT COUNT(*) FROM " . table_prefix . "tweet_feed";
    $totalRows = $db->get_var($query);
    $totalPages = ceil($totalRows / $resultsPerPage);
    $currentPage = tweet_feed_get_current_page($totalPages);
    $offset = ($currentPage - 1) * $resultsPerPage;
    /**
     * Generate the query
     */
    $query = "SELECT tf.tweet_feed_id, tf.tweet_id, tf.tweeter_id, tf.tweeter_name, tf.content, tf.published_date, " .
        "ta.tweeter_avatar_url FROM " . table_prefix . "tweet_feed as tf JOIN " . table_prefix . "tweet_feed_avatars as ta ON " .
        "tf.tweeter_id = ta.tweeter_id ORDER BY tf.published_date DESC LIMIT " . $offset . ", " . $resultsPerPage;
    $results = $db->get_results($query);
    $tweets = array();
    foreach ($results as $result) {
        $tweet = array(
            'tweet_feed_id'         =>  $result->tweet_feed_id,
            'tweet_id'              =>  $result->tweet_id,
            'tweeter_id'            =>  $result->tweeter_id,
            'tweeter_name'          =>  $result->tweeter_name,
            'content'               =>  $result->content,
            'published_date'        =>  $result->published_date,
            'tweeter_avatar_url'    =>  $result->tweeter_avatar_url
        );
        array_push($tweets, $tweet);
    }
    $main_smarty->assign('tweets' , $tweets);
    $main_smarty->assign('twitter_feed_current' , true);
    $main_smarty->assign('pagi_current_page' , $currentPage);
    $main_smarty->assign('pagi_total_pages' , $totalPages);
    $main_smarty->assign('max_pagi_count' , $totalPages+1);
    $pagi_previous_page = ($currentPage == 1) ? 1 : $currentPage-1;
    $main_smarty->assign('pagi_previous_page' , $pagi_previous_page);
    $pagi_next_page = ($currentPage == $totalPages) ? $totalPages : $currentPage+1;
    $main_smarty->assign('pagi_next_page' , $pagi_next_page);
    $main_smarty->assign('max_pagi_count' , $totalPages+1);
}
