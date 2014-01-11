<?php 
$config = array(
    /**
     * This route will route the requests to system app
     */
    'system_route_all' => array(
        'route' => '/system/:controller/:action/*',
        'params'=> array(
            'app' => 'system'
        )
    ),
    
    'system_route_simple' => array(
        'route' => '/system/:controller/:action',
        'params'=> array(
            'app' => 'system'
        )
    ),
    
    'system_route_only_controller' => array(
        'route' => '/system/:controller',
        'params'=> array(
            'app' => 'system',
            'action'=> 'index'
        )
    ),
    
    'system_route_default' => array(
        'route' => '/system/',
        'params'=> array(
            'app' => 'system',
            'controller'=> 'default',
            'action'=> 'index'
        )
    )
)

?>
