<?php
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
    'Model' => 'model/Model.class.php',
    'View' => 'view/View.class.php',
    'Router' =>  'router/Router.class.php',
    'Autoload' => 'autoload/Autoload.class.php',
    
    /**
     * Components classes
     */
    'PhpSession' => "session/PhpSession.class.php",
    
    /**
     * Classes for exceptions
     */
    'NoFileFoundException' => 'exceptions/NoFileFoundException.class.php',
    'NoLegalMethodException' => 'exceptions/NoLegalMethodException.class.php',
    'NoHostFoundException'   => 'exceptions/NoHostFoundException.class.php',
    'NoRouteFoundException'   => 'exceptions/NoRouteFoundException.class.php',
    'NoControllerFoundException'   => 'exceptions/NoControllerFoundException.class.php',
    'NoActionFoundException' => 'exceptions/NoActionFoundException.class.php',
    'LayoutNotFoundException' => 'exceptions/LayoutNotFoundException.class.php',
    'NoLayoutDefinedException'=> 'exceptions/NoLayoutDefinedException.class.php',
    'ViewNotFoundException'=> 'exceptions/ViewNotFoundException.class.php',
    'InvalidViewInputException'=> 'exceptions/InvalidViewInputException.class.php',
    'ElementNotFoundException' => 'exceptions/ElementNotFoundException.class.php',
)
?>
