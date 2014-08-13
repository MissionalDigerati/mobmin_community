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
 * A hook for when the link summary is setting the Smarty variables.
 * We will set all variables for Twitter information
 *
 * @return void
 * @author Johnathan Pulos
 **/
function tb_link_summary_fill_smarty(&$vars) {
    global $db;
    $id = intval($vars['smarty']->_vars['link_id']);
    $linkTitleURL = $vars['smarty']->_vars['title_url'];
    if (strpos($linkTitleURL,'mobmin-tweet-') !== false) {
        /**
         * We have a link that was submitted by Twitter
         */
        $link = $db->get_row("SELECT social_media_id, social_media_account, link_published_date from " . table_links . " WHERE link_id = " . $id);
        $vars['smarty']->_vars['social_media_id'] = $link->social_media_id;
        $vars['smarty']->_vars['social_media_account'] = $link->social_media_account;
        $vars['smarty']->_vars['link_published_date'] = $link->link_published_date;
        $vars['smarty']->_vars['is_tweet'] = true;
    } else {
        $vars['smarty']->_vars['is_tweet'] = false;
    }
}
