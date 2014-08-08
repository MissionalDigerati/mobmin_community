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
 * The Tag Resource for managing tags to the Pligg site
 */
class Tag extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'tags';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = '';
    /**
     * An array of whitelisted attributes
     *
     * @var array
     * @access protected
     **/
    protected $accessibleAttributes = array(
        'tag_link_id', 'tag_date', 'tag_words'
    );
    /**
     * Insert/Update the tag in the database.  Pass an id to update.
     *
     * @param array $data an array of the tag data to save
     * @param integer $id the Link.link_id of the record to update
     * @return boolean Did it save the data?
     * @access public
     * @author Johnathan Pulos
     **/
    public function save($data, $id = null)
    {
        if (is_null($id)) {
            return $this->insertRecord($data);
        }
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
        return $newValue;
    }
}
