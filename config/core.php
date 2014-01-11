<?php
$config = array(
    
    /**
     * setup is crucible internal app which will be used
     * to create new app,modules and actions. It will be used 
     * to monitor created app (logging etc) and used to show system 
     * pages(ie 404 or 500 pages)
     */
    'system' => array(
        'is_active' => true
    ),
    
    /**
     * Configurations related to all the apps. This block contains 
     * configurations which is applicable to all the apps and configurations
     * in this block will override the settings in all the block
     */
    'apps' => array(
        /**
         * This setting can turn the whole website down
         */
        'is_active' => true,
    )
)
?>
