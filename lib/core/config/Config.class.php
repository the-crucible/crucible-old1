<?php
/**
 * This class will become the central repository
 * of config and will have setter, getter and load
 * functions for the config files
 *
 * @author applect
 */
class Config {
    private static $config = array();
    
    /**
     * load
     * 
     * This function will load any file containing the config array and 
     * save it into an array
     * 
     * @param string $file_path full path of the file from which the config 
     *                           is to be loaded 
     * @param string $lable     Starting lable of the config
     * @return bool 
     */
    public static function load($file_path, $lable){
        
    }
    
    /**
     * get
     * 
     * This function will read the config value from the config array
     * 
     * @param string $path Period seperated path eg. "db.default.username"
     * @return mixed The value of the config 
     */
    public static function get($path){
        
    }
    
    /**
     * set
     * 
     * This function will set the value into the config array
     * 
     * @param string $path Period seperated path eg. "db.default.username"
     * @param mixed $value value of the config
     */
    public static function set($path, $value){
        
    }
}

?>
