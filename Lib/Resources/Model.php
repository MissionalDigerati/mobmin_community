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
 * The Model object for eavh resource
 */
class Model
{
    /**
     * The table prefix for the links table
     *
     * @var string
     * @access protected
     **/
    protected $tablePrefix = '';
    /**
     * The database object
     *
     * @var \PDO
     * @access protected
     **/
    protected $db;
    /**
     * Construct the model object
     *
     * @param \PDO $db The database connection
     * @return void
     * @throws InvalidArgumentException if $db is not a \PDO Object
     * @author Johnathan Pulos
     **/
    public function __construct($db)
    {
        $this->setDatabaseObject($db);
    }
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
     * @access protected
     * @throws InvalidArgumentException if $db is not a \PDO Object
     * @author Johnathan Pulos
     **/
    protected function setDatabaseObject($db)
    {
        if (is_a($db, 'PDO')) {
            $this->db = $db;
        } else {
            throw new \InvalidArgumentException('$db must be of the class \PDO.');
        }
    }
}