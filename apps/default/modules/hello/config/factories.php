<?php

$config = array(
    'all' => array(
        /*
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
            )
         )
         */
        'mailer' => array(
            'handler' => 'SwiftMailer',
            'arguments' => array(
                'transport' => 'loadbalanced',
                'params' => array(
                    't1' => array(
                        'transport' => 'smtp',
                        'params' => array(
                            'host' => 'localhost',
                            'port' => '25',
                        )
                    ),
                    't2' => array(
                        'transport' => 'smtp',
                        'params' => array(
                            'host' => 'smtp.gmail.com',
                            'port' => '587',
                            'user' => 'tej.nri@gmail.com',
                            'pass' => '',
                            'encryption' => 'tls'
                        )
                    )
                )
            )
        )
    )
);
?>
