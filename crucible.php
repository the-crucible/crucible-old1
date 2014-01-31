<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file is the frontend controller of the crucible framework.
 * It can support static files, dynamic files, files for different 
 * domains etc.
 */

define('DS', DIRECTORY_SEPARATOR);

/**
 * First of all defining the path of root of the framework
 */
define('ROOT' , realpath(dirname(__FILE__)));

/**
 * Defining the path of the lib dir
 */

define('LIB', ROOT . DS . 'lib' );

/**
 * Defining the path of the core dir
 */

define('CORE', LIB . DS . 'core' );

/**
 * Include the core crucible class
 */
require_once(CORE . DS . 'Crucible.class.php');

/**
 * Get the instance and produce the result
 */
Crucible::getInstance()->dispatch();


?>
