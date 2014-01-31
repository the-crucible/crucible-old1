<?php

/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This config file contain all the classes and their corresponding
 * file to be autoloaded during the execution of the framework.
 */
$config = array(
    /**
     * Classes for config management
     */
    'Config' => 'config/Config.class.php',
    
    /**
     * Core classes
     */
    'Request'=> 'request/Request.class.php',
    'Response' => 'response/Response.class.php',
    'Controller' => 'controller/Controller.class.php',
    'View' => 'view/View.class.php',
    'Router' =>  'router/Router.class.php',
    'Autoload' => 'autoload/Autoload.class.php',
    
    /**
     * Components classes
     */
    'PhpSession' => "session/PhpSession.class.php",
    'Db'         => "db/Db.class.php",
    'DbResourceContainer' => 'db/DbResourceContainer.class.php',
    'SwiftMailer' => 'mailer/SwiftMailer.class.php',
    
    /**
     * Classes for exceptions
     */
    'NoFileFoundException' => 'exceptions/NoFileFoundException.class.php',
    'NoHostFoundException'   => 'exceptions/NoHostFoundException.class.php',
    'NoRouteFoundException'   => 'exceptions/NoRouteFoundException.class.php',
    'NoControllerFoundException'   => 'exceptions/NoControllerFoundException.class.php',
    'NoActionFoundException' => 'exceptions/NoActionFoundException.class.php',
    'LayoutNotFoundException' => 'exceptions/LayoutNotFoundException.class.php',
    'NoLayoutDefinedException'=> 'exceptions/NoLayoutDefinedException.class.php',
    'ViewNotFoundException'=> 'exceptions/ViewNotFoundException.class.php',
    'InvalidViewInputException'=> 'exceptions/InvalidViewInputException.class.php',
    'ElementNotFoundException' => 'exceptions/ElementNotFoundException.class.php',
    'FileNotFound404Exception' => 'exceptions/FileNotFound404Exception.class.php',
    'DbNoConnectException'     => 'exceptions/DbNoConnectException.class.php',
    'InvalidDbConfigException' => 'exceptions/InvalidDbConfigException.class.php',
    'DbConnectErrorException'  => 'exceptions/DbConnectErrorException.class.php',
    'InvalidMailerConfigException' => 'exceptions/InvalidMailerConfigException.class.php',
    'ForwardActionException' => 'exceptions/ForwardActionException.class.php'
)
?>
