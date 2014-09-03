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
namespace Resources;

/**
 * The TweetFeedAvatar Resource.  This requires the TweetFeed Module to be installed
 */
class TweetFeedAvatar extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'tweet_feed_avatars';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = 'tweet_feed_avatar_id';
    /**
     * An array of whitelisted attributes
     *
     * @var array
     * @access protected
     **/
    protected $accessibleAttributes = array(
        'tweeter_id', 'tweeter_name', 'tweeter_avatar_url', 'last_updated'
    );
    /**
     * Construct the model object
     *
     * @param \PDO $db The database connection
     * @return void
     * @author Johnathan Pulos
     **/
    public function __construct($db)
    {
        parent::__construct($db);
    }
    /**
     * Insert a tweet feed avatar to the avatar table
     *
     * @param array $data The data to be saved
     * @return boolean Did it save the data?
     * @access public
     * @author Johnathan Pulos
     **/
    public function save($data)
    {
        return $this->insertRecord($data);
    }
    /**
     * Update a record
     *
     * @param array $data The data to be saved
     * @param integer $id The id of the record to save
     * @return boolean Did it save the data?
     * @access public
     * @throws InvalidArgumentException if record does not exist
     * @author Johnathan Pulos
     **/
    public function update($data, $id)
    {
        return $this->updateRecord($data, $id);
    }
    /**
     * prepare the attribute before binding to the PDOStatement
     *
     * @param string $key The attribute name
     * @param mixed $value The given value to save
     * @return mixed The final prepared value
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function prepareAttribute($key, $value)
    {
        $newValue = parent::prepareAttribute($key, $value);
        return strip_tags($newValue);
    }
}
