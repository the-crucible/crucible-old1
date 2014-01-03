<?php
define('ROOT_CONFIG', ROOT . DS . 'config');


/**
 * Crucible is the main class of the crucible framework.
 *
 * @author Tejaswi Sharma<tejaswi@crucible-framework.org>
 */
class Crucible {
    /**
     * variable to hold instance of class Crucible
     * 
     * @var Crucible 
     */
    private static $_instance = null;
    
    /**
     * 
     */
    private function __construct() {
        
    }
    
    /**
     * __clone
     * 
     * This function is called when clone function is called on any object.
     * This arrangement will return the same object even if its cloned
     * 
     * @return Crucible
     */
    private function __clone() {
        return self::getInstance();
    }
    
    /**
     * getInstance
     * 
     * This function will give a singleton interface 
     * for Crucible class
     * 
     * @return Crucible
     */
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new Crucible();
        }
        return self::$_instance;
    }
    
    public function getCoreConfig(){
        require_once(ROOT_CONFIG . DS . 'core.php');
        return $config;
    }
    
    public function getDbConfig(){
        require_once(ROOT_CONFIG . DS . 'database.php');
        return $config;
    }
    
    public function getHostConfig(){
        require_once(ROOT_CONFIG . DS . 'hosts.php');
        return $config;
    }
    
    
    /**
     * dispatch
     * 
     * This function will start the response gathering process
     * which it is suppossed to do.
     */
    public function dispatch(){
        
    }
}

?>
