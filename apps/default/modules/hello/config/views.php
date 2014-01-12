<?php 
$config = array(
    'all' => array(),
    'dev' => array(),
    'prod'=> array(
        'world' => array(
            'js' => array('main'),
            'css'=> array('main'),
            'title'=>"test title",
            'meta' =>array(
                array('name'=>"viewport",'content'=>"width=device-width")
            )
        )
    )
)
?>