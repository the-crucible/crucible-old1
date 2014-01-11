<?php 
$config = array(
    /**
     * This route will provide a simple controller/action
     * interface and rest every thing else as params
     */
    'simple_action_with_params' => array(
        'route' => '/:controller/:action/*'
    ),
    
    /**
     * This route will provide a simple controller/action
     * interface
     */
    
    'simple_action' => array(
        'route' => '/:controller/:action'
    ),
    
    /**
     * This route will provide a default index to the 
     * given controller
     */
    'default_action' => array(
        'route' => '/:controller',
        'params'=> array(
            'action' => 'index'
        )
    ),
    
    /**
     * Please change this setting to point to the index page
     * of the website
     */
    'default' => array(
        'route' => '/',
        'params'=> array(
            'app' => 'system',
            'controller' => 'default',
            'action' => 'index'
        ),
    )
)

?>
