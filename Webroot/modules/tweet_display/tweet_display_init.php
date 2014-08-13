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
if(defined('mnminclude')){

    define('td_tpl_path', '../modules/tweet_display/templates/');
    // tell pligg what pages this modules should be included in
    // pages are <script name> minus .php
    // index.php becomes 'index' and new.php becomes 'new'
    $do_not_include_in_pages = array();

    $include_in_pages = array('all');

    if( do_we_load_module() ) {
        module_add_action_tpl('tpl_pligg_head_start', td_tpl_path . 'tweet_display_tpl_pligg_head_start.tpl');
        module_add_action_tpl('tpl_pligg_story_body_start', td_tpl_path . 'tweet_display_before_story_body.tpl');
        module_add_action_tpl('tpl_pligg_story_body_end', td_tpl_path . 'tweet_display_after_story_body.tpl');
        module_add_action_tpl('tpl_pligg_story_body_start_full', td_tpl_path . 'tweet_display_before_story_body.tpl');
        module_add_action_tpl('tpl_pligg_story_body_end_full', td_tpl_path . 'tweet_display_after_story_body.tpl');
        module_add_action('lib_link_summary_fill_smarty', 'tb_link_summary_fill_smarty', '');

        include_once(mnmmodules . 'tweet_display/tweet_display_main.php');
    }
}
