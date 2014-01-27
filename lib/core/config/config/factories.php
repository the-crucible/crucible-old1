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
        ),
        'mailer' => array(
            'handler' => 'SwiftMailer',
            'arguments' => array(
                'transport' => 'smtp',
                'params' => array(
                    'host' => 'smtp.gmail.com',
                    'port' => '587',
                    'user' => 'tej.nri@gmail.com',
                    'pass' => 'patna@123',
                    'encryption' => 'tls'
                )
                /*
                # If transport is smtp
                'params' => array(
                    'host' => 'localhost',
                    'port' => '25',
                    'user' => 'sample',
                    'pass' => 'sample',
                    'encryption' => 'ssl' or 'tls'
                )
                # If transport is sendmail
                'params' => array(
                    'path' => '/usr/bin/sendmail -bs'
                )
                # If transport is phpmail
                'params' => array(
                    # Nothing is required
                )
                # If transport is loadBalanced
                'params' => array(
                    't1' => array(
                        'transport' => 'smtp',
                        'params' => array(
                            'host' => 'localhost',
                            'port' => '25'
                        )
                    ),
                    't2' => array(
                        'transport' => 'sendmail',
                        'params' => array(
                            'path' => '/usr/bin/sendmail -bs'
                        )
                    )
                    't3' => array(
                        'transport' => 'phpmail',
                        'params' => array(
                            # Nothing required
                        )
                    )
                )
                */
            )
        )
    )
)
?>
