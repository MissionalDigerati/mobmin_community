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
namespace Support\CustomAssertions;

/**
 * This class adds a CustomAssertion to Check an array's entries
 *
 * @link http://stackoverflow.com/a/11910178
 * @package default
 * @author Johnathan Pulos
 **/
class ArrayHasEntries extends \PHPUnit_Framework_Constraint
{
    /**
     * @var array
     */
    protected $array;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    protected function matches($other)
    {
        foreach ($this->array as $key => $value) {
            if (!array_key_exists($key, $other) || $other[$key] != $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        $msg = array();
        foreach ($this->array as $key => $value) {
            array_push($msg, $key . " = " . $value);
        }
        return 'has the entries [' . implode(", ", $msg) . "]";
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
        return 'an array ' . $this->toString();
    }
}
