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
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = '';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = 'id';
    /**
     * An array of whitelisted attributes
     *
     * @var array
     * @access protected
     **/
    protected $accessibleAttributes = array();
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
     * The last inserted id. Set to null if the insert failed.
     *
     * @var integer
     * @access public
     **/
    protected $lastID = null;
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
     * get the last inserted id
     *
     * @return int The last id
     * @access public
     * @author Johnathan Pulos
     **/
    public function getLastID()
    {
        return $this->lastID;
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
    /**
     * Insert a new Link Resource
     *
     * @param array $data an array of the link data to save
     * @return boolean Did it save?
     * @author Johnathan Pulos
     **/
    protected function insertRecord($data)
    {
        $stmt = $this->db->prepare($this->getInsertQuery());
        $stmt = $this->bindValues($stmt, $data, 'insert');
        $saved = $stmt->execute();
        if ($saved === true) {
            $this->lastID = $this->db->lastInsertId();
        } else {
            $this->lastID =  null;
        }
        return $saved;
    }
    /**
     * Generates the insert SQL query based on the set $accessibleAttributes class variable
     *
     * @return string The final Query statement
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function getInsertQuery()
    {
        $query = "INSERT INTO " . $this->tablePrefix . $this->tableName . "(" .
            implode(', ', $this->accessibleAttributes) . ") VALUES(:" . implode(', :', $this->accessibleAttributes) . ")";
        return $query;
    }
    /**
     * Bind the values to the POD statement
     *
     * @param PDOStatement $statement The statement to bind values to
     * @param array $data The data to save regarding the Resource
     * @param string $queryType (insert, update)
     * @return \PDOStatement The statement object
     * @author Johnathan Pulos
     **/
    protected function bindValues($statement, $data, $queryType = 'insert')
    {
        foreach ($this->accessibleAttributes as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $value = $this->prepareAttribute($attribute, $data[$attribute]);
            } else {
                if (strtolower($queryType) == 'insert') {
                    $value = $this->prepareAttribute($attribute, '');
                }
            }
            $statement->bindValue(":" . $attribute, $value);
        }
        return $statement;
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
        return strip_tags($value);
    }
    /**
     * Checks if the given table has the id
     *
     * @return boolean Does it haven the id?
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function tableHasID($tableName, $primaryKey, $id)
    {
        $query = "SELECT * FROM " . $this->tablePrefix . $tableName . " WHERE " . $primaryKey . " = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id", intval($id));
        $stmt->execute();
        return (bool) $stmt->fetchColumn();

    }
}