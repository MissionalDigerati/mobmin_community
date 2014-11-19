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
 * The TagCache Resource for managing links to the Pligg site
 */
class TagCache extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'tag_cache';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = '';
    /**
     * A whitelist of all allowable statuses
     *
     * @var array
     * @access protected
     **/
    protected $whitelistStatuses = array();
    /**
     * Reset the tag cache data
     *
     * @link http://pligg.com/support/question/bug-fix-for-tags-disappearing/#question
     * @return void
     * @access public
     * @author Johnathan Pulos
     **/
    public function reset()
    {
        /**
         * Clear the tag_cache table
         *
         * @author Johnathan Pulos
         **/
        $this->db->query("DELETE FROM " . $this->dbTablePrefix . "tag_cache");
        $sql = "INSERT INTO " . $this->dbTablePrefix . "tag_cache select tag_words, count(DISTINCT link_id) as count " .
        "FROM " . $this->dbTablePrefix . "tags, " . $this->dbTablePrefix . "links WHERE tag_lang='en' and " .
        "link_id = tag_link_id and (link_status='published' OR link_status='new') GROUP BY tag_words order by count desc";
        $this->db->query($sql);
    }
}
