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
namespace support;

/**
 * A class that holds the database settings, please rename to DatabaseSettings.php
 *
 * @package default
 * @author Johnathan Pulos
 */
class DatabaseSettings
{
    /**
     * The default database to use.  Add the period after the table prefix!
     *
     * @var array
     * @access public
     */
    public $default = array(    'host'          =>  'HOST',
                                'name'          =>  'DATABASE_NAME',
                                'username'      =>  'USERNAME',
                                'password'      =>  'PASSWORD',
                                'table_prefix'  =>  ''
                            );
}
