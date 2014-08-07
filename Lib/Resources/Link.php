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
 * The Link Resource for managing links to the Pligg site
 */
class Link extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'links';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = 'link_id';
    /**
     * An array of whitelisted attributes
     *
     * @var array
     * @access protected
     **/
    protected $accessibleAttributes = array(
        'link_author', 'link_status', 'link_randkey', 'link_votes', 'link_karma', 'link_modified', 'link_date',
        'link_published_date', 'link_category', 'link_url', 'link_url_title', 'link_title', 'link_title_url',
        'link_content', 'link_summary', 'link_tags'
    );
    /**
     * Insert/Update the link in the database.  Pass an id to update.
     *
     * @param array $data an array of the link data to save
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
     * Insert a new Link Resource
     *
     * @param array $data an array of the link data to save
     * @return boolean Did it save?
     * @author Johnathan Pulos
     **/
    protected function insertRecord($data)
    {
        $query = $this->getInsertQuery();
        $stmt = $this->db->prepare($query);
        $stmt = $this->bindValues($stmt, $data, 'insert');
        return $stmt->execute();
    }

}
