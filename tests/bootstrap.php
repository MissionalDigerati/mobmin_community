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
$DS = DIRECTORY_SEPARATOR;
$libDirectory = __DIR__ . $DS . ".." . $DS . "lib" . $DS;
$vendorDirectory = __DIR__ . $DS . ".." . $DS . "vendor" . $DS;
$PHPToolboxDirectory = $vendorDirectory . "PHPToolbox" . $DS . "src" . $DS;
/**
 * Load up the Aura
 *
 * @author Johnathan Pulos
 */
$loader = require $vendorDirectory . "aura" . $DS . "autoload" . $DS . "scripts" . $DS . "instance.php";
$loader->register();
/**
 * Silent the Autoloader so we can see correct errors
 *
 * @author Johnathan Pulos
 */
$loader->setMode(\Aura\Autoload\Loader::MODE_SILENT);
/**
 * Setup the database object
 *
 * @author Johnathan Pulos
 */
$loader->add("support\DatabaseSettings", __DIR__);
/**
 * Autoload the PDO Database Class
 *
 * @author Johnathan Pulos
 */
$loader->add("PHPToolbox\PDODatabase\PDODatabaseConnect", $PHPToolboxDirectory);
/**
 * Autoload the lib classes
 *
 * @author Johnathan Pulos
 */
$loader->add("Resources\Link", $libDirectory);
/**
 * autoload models & test files
 *
 * @package default
 * @author Johnathan Pulos
 */
spl_autoload_register(
    function ($class) {
        $file = dirname(__DIR__). DIRECTORY_SEPARATOR
              . 'tests' . DIRECTORY_SEPARATOR
              . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
);
