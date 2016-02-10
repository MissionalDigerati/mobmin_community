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
 * The User Resource for the Pligg site
 */
class User extends Model
{
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'users';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = 'user_id';
    /**
     * Find a user by their user_login
     *
     * @param string $userLogin The user login to lookup
     * @return array The PDO user database result using \PDO::FETCH_ASSOC
     * @access public
     * @author Johnathan Pulos
     **/
    public function findByUserLogin($userLogin)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->tablePrefix . $this->tableName . " where user_login = :user_login LIMIT 1");
        $stmt->bindValue(':user_login', strip_tags($userLogin));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
