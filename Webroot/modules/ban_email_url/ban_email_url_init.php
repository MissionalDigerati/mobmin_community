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
// the path to the module. the probably shouldn't be changed unless you rename the admin_snippet folder(s)
define('ban_email_url_path', my_pligg_base . '/modules/ban_email_url/');
define('ban_email_url_tpl_path', '../modules/ban_email_url/templates/');

if(defined('mnminclude')){

    $do_not_include_in_pages = array();

    $include_in_pages = array('all');

    if (do_we_load_module()) {
        module_add_action_tpl('tpl_header_admin_main_links', ban_email_url_tpl_path . 'ban_email_url_admin_link.tpl');

        include_once(mnmmodules . 'ban_email_url/ban_email_url_main.php');
        module_add_action('register_check_errors', 'ban_email_url_check_email', '');
    }

    $include_in_pages = array('module');
    if( do_we_load_module() ) {

        $moduleName = $_REQUEST['module'];

        if($moduleName == 'ban-email-url'){
            module_add_action('module_page', 'ban_email_url_show_page', '');
            include_once(mnmmodules . 'ban_email_url/ban_email_url_main.php');
        }
    }
}
