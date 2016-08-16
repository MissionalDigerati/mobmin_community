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
 * Pligg Hook: Checks whether the email has a banned domain, and if so it will throw an error
 *
 * @param  Array    $vars   The variables from the form
 * @return Void
 * @access public
 */
function ban_email_url_check_email(&$vars) {
    global $form_email_error, $db;
    $banned = ban_email_url_get_banned_urls($db);
    $email = $vars['email'];
    if ($email) {
        $emailParts = explode('@', $email);
        if ((count($emailParts) === 2) && (in_array($emailParts[1], $banned))) {
            $vars['error'] = true;
            $vars['email_error'] = 'Your access has been revoked based on your email address. If this is an error, please contact us immediately.';
        }
    }
}
/**
 * Show the Admin Page
 *
 * @return Void
 * @access public
 */
function ban_email_url_show_page() {
    global $db, $main_smarty, $the_template;
    global $db, $main_smarty, $the_template;

    include_once('config.php');
    include_once(mnminclude.'html1.php');
    include_once(mnminclude.'link.php');
    include_once(mnminclude.'tags.php');
    include_once(mnminclude.'smartyvariables.php');

    $main_smarty = do_sidebar($main_smarty);

    force_authentication();
    $canIhaveAccess = 0;
    $canIhaveAccess = $canIhaveAccess + checklevel('admin');

    if ($canIhaveAccess == 1) {
        // breadcrumbs
        $navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Header_AdminPanel');
        $navwhere['link1'] = getmyurl('admin', '');
        $navwhere['text2'] = "Ban Email URL";
        $navwhere['link2'] = my_pligg_base . "/module.php?module=ban-email-url";
        $main_smarty->assign('navbar_where', $navwhere);
        $main_smarty->assign('posttitle', " | " . $main_smarty->get_config_vars('PLIGG_Visual_Header_AdminPanel'));

        define('modulename', 'ban-email-url');
        $main_smarty->assign('modulename', modulename);

        define('pagename', 'admin_modifydomains');
        $main_smarty->assign('pagename', pagename);

        $main_smarty->assign('tpl_center', ban_email_url_tpl_path . 'admin_settings_main');

        if ($_POST['submit']) {
            $domains = explode(',', $_POST['domain-names']);
            $final = [];
            foreach ($domains as $domain) {
                $final[] = trim($domain);
            }
            /**
             * Truncate the table
             */
            $db->query('TRUNCATE TABLE ' . table_prefix . 'banned_email_domains');
            if (count($final) > 0) {
                foreach ($final as $domain) {
                    $cleaned = $db->escape($domain);
                    $db->query(
                        'INSERT INTO ' . table_prefix . 'banned_email_domains (domain) VALUES("' . $cleaned . '")'
                    );
                }
            }
        }

        $forbidden = implode(', ', ban_email_url_get_banned_urls($db));
        $main_smarty->assign('banned_domains', $forbidden);
        $main_smarty->display($template_dir . '/admin/admin.tpl');
    }
}
/**
 * Get an array of banned domains
 *
 * @param  Object   $db   The global database object
 * @return array          The banned domains
 * @access public
 */
function ban_email_url_get_banned_urls($db) {
    $domains = $db->get_results('SELECT * FROM ' . table_prefix . 'banned_email_domains');
    $banned = [];
    foreach ($domains as $url) {
        $banned[] = $url->domain;
    }
    return $banned;
}
