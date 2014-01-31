<?php 

/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file will contain all the configs related to 
 * some crucial workings of the framework
 */

$config = array(
    'all' => array(
        'error_pages' => array(
            '404' => array(
                'app' => 'system',
                'controller' => 'errors',
                'action' => 'show404'
            ),
            '500' => array(
                'app' => 'system',
                'controller' => 'errors',
                'action' => 'show500'
            )
            
        )
    )
);        
?>
