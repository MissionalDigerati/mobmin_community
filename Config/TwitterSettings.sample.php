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
namespace Config;

/**
 * A class that holds the Twitter API settings, please rename to TwitterSettings.php
 *
 * @package default
 * @author Johnathan Pulos
 */
class TwitterSettings
{
    /**
     * The configuration for Twitter API access
     *
     * @var array
     * @access public
     */
    public $config = array(
        'consumer_key'          =>  'CONSUMER_KEY',
        'consumer_secret'       =>  'CONSUMER_SECRET',
        'oauth_token'           =>  '',
        'oauth_token_secret'    =>  '',
        'output_format'         =>  'object'
    );
}
