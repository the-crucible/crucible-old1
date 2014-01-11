<?php 
$config = array(
    /**
     * This route will route the requests to setup app
     */
    'setup_route_all' => array(
        'route' => '/setup/:controller/:action/*',
        'params'=> array(
            'app' => 'setup'
        )
    ),
    
    'setup_route_simple' => array(
        'route' => '/setup/:controller/:action',
        'params'=> array(
            'app' => 'setup'
        )
    ),
    
    'setup_route_only_controller' => array(
        'route' => '/setup/:controller',
        'params'=> array(
            'app' => 'setup',
            'action'=> 'index'
        )
    ),
    
    'setup_route_default' => array(
        'route' => '/setup/',
        'params'=> array(
            'app' => 'setup',
            'controller'=> 'default',
            'action'=> 'index'
        )
    )
)

?>
