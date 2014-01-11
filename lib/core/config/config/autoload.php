<?php
$config = array(
    /**
     * Classes for config management
     */
    'Config' => 'config/Config.class.php',
    
    /**
     * Classes for request management
     */
    'AbstractRequest' => 'request/AbstractRequest.class.php',
    'WebRequest' => 'request/WebRequest.class.php',
    'CliRequest' => 'request/CliRequest.class.php',
    'Request'=> 'request/Request.class.php',
    'Router' =>  'router/Router.class.php',
    
    /**
     * Classes for exceptions
     */
    'NoFileFoundException' => 'exceptions/NoFileFoundException.class.php',
    'NoLegalMethodException' => 'exceptions/NoLegalMethodException.class.php',
    'NoHostFoundException'   => 'exceptions/NoHostFoundException.class.php',
    'NoRouteFoundException'   => 'exceptions/NoRouteFoundException.class.php'
)
?>
