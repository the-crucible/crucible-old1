<?php 
$config = array(
    'session' => array(
        'handler' => 'PhpSession',
        'arguments' => array(
            'auto_start' => 1,
            'name' => 'CRUCIBLE',
            'save_handler' => 'files',
            'use_cookies' => '1',
            'use_only_cookies' => '1',
            'cookie_lifetime' => '0',
            'cookie_path' => "/",
            'cookie_httponly' => '1'
        )
    )
)
?>
