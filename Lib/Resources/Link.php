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
class Link
{
    /**
     * The table prefix for the links table
     *
     * @var string
     **/
    private $tablePrefix = '';
    /**
     * The database object
     *
     * @var \PDO
     * @access private
     **/
    private $db;
    /**
     * Set the table prefix for the database table
     *
     * @param string $prefix the table prefix
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }
    /**
     * Set the PDO Database Object
     *
     * @param \PDO $db The database connection
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function setDatabaseObject($db)
    {
        if (is_a($db, 'PDO')) {
            $this->db = $db;
        } else {
            throw new \InvalidArgumentException('$db must be of the class \PDO.');
        }
    }
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
        $stmt = $this->db->prepare(
            "INSERT INTO " . $this->tablePrefix . "links " .
            "(link_author, link_status, link_randkey, link_votes, link_karma, link_modified, link_date, " .
            "link_published_date, link_category, link_url, link_url_title, link_title, link_title_url, link_content, " .
            "link_summary, link_tags) " .
            "VALUES(:link_author, :link_status, :link_randkey, :link_votes, :link_karma, :link_modified, :link_date, " .
            ":link_published_date, :link_category, :link_url, :link_url_title, :link_title, :link_title_url, " .
            ":link_content, :link_summary, :link_tags)"
        );
        $stmt->bindValue(':link_author', $data['link_author']);
        $stmt->bindValue(':link_status', $data['link_status']);
        $stmt->bindValue(':link_randkey', $data['link_randkey']);
        $stmt->bindValue(':link_votes', $data['link_votes']);
        $stmt->bindValue(':link_karma', $data['link_karma']);
        $stmt->bindValue(':link_modified', $data['link_modified']);
        $stmt->bindValue(':link_date', $data['link_date']);
        $stmt->bindValue(':link_published_date', $data['link_published_date']);
        $stmt->bindValue(':link_category', $data['link_category']);
        $stmt->bindValue(':link_url', $data['link_url']);
        $stmt->bindValue(':link_url_title', $data['link_url_title']);
        $stmt->bindValue(':link_title', $data['link_title']);
        $stmt->bindValue(':link_title_url', $data['link_title_url']);
        $stmt->bindValue(':link_content', $data['link_content']);
        $stmt->bindValue(':link_summary', $data['link_summary']);
        $stmt->bindValue(':link_tags', $data['link_tags']);
        return $stmt->execute();
    }

}
