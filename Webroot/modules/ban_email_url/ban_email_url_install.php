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
$module_info['name'] = 'Ban Email URL';
$module_info['desc'] = 'Ban from registering any email with specific domains.';
$module_info['version'] = 1.0;
$module_info['homepage_url'] = 'http://www.missionaldigerati.org';
$module_info['update_url'] = 'http://www.missionaldigerati.org';
$module_info['settings_url'] = '../module.php?module=ban-email-url';
$module_info['db_add_table'][]=array(
'name' => table_prefix . "banned_email_domains",
'sql' => "CREATE TABLE `".table_prefix . "banned_email_domains` (
  `id` int(11) NOT NULL auto_increment,
  `domain` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
