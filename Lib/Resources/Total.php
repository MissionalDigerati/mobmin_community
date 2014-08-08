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
 * The Total Resource for managing links to the Pligg site
 */
class Total extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'totals';
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
    protected $whitelistStatuses = array('published', 'new', 'discard');
    /**
     * Increment the total for the given status
     *
     * @param string $status The status to increment (published, new, discard)
     * @return boolean Did it save?
     * @access public
     * @throws InvalidArgumentException if $status is not 'published', 'new', or 'discard'
     * @author Johnathan Pulos
     **/
    public function increment($status)
    {
        if (!in_array($status, $this->whitelistStatuses)) {
            throw new \InvalidArgumentException(
                "Attribute link_status can only be: " . implode(', ', $this->whitelistStatuses) . "."
            );
            exit;
        }
        $stmt = $this->db->prepare("update " . $this->tablePrefix . $this->tableName . " set total = total + 1 where name = :name");
        $stmt->bindValue(':name', $status);
        return $stmt->execute();
    }
}
