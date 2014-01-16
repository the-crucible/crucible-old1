<?php 

/**
 * All the modules that are coming into factories file will be initiated in 
 * a very simple way. All the files will be initiated by the class in the 'handler'
 * element and then after creating its object an init function will be called.
 * After calling this function this object will be saved in components array of 
 * Crucible main object.
 * 
 */
$config = array(
    'all' => array(
        'autoload'=> array(
            'handler' => 'Autoload',
            'arguments' => array(
                
            ),
        ),
        'session' => array(
            'handler' => 'PhpSession',
            'arguments' => array(
                'name' => 'CRUCIBLE',
                'save_handler' => 'files',
                'use_cookies' => '1',
                'use_only_cookies' => '1',
                'cookie_lifetime' => '0',
                'cookie_path' => "/",
                'cookie_httponly' => '1',
                'auto_start' => 1
            )
        ),
        'db' => array(
            'handler' => 'Db',
            'arguments' => array(
                'identifier' => 'database'
            )
        )
    )
)
?>
