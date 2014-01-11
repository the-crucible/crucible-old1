<?php
/**
 * This class will become the central repository
 * of config and will have setter, getter and load
 * functions for the config files
 *
 * @author applect
 */
class Config {
    public static $_config = array();
    
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
    public static function load($file_path){
        $lable  = basename($file_path, ".php"); 
        $config = self::read($file_path);
        self::$_config[$lable] = $config;
    }
    
    /**
     * read
     * 
     * This function read the config files
     * 
     * @param type $file_path
     * @return array $config array in the config file
     * @throws NoFileFoundException
     */
    public static function read($file_path){
        if(is_file($file_path)){
            include $file_path;
            return $config;
        }else{
            throw new NoFileFoundException($file_path);
        }
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
        $path = trim($path);
        $path_arr = explode('.', $path);
        $search_arr = self::$_config;
        
        foreach ($path_arr as $value) {
            $value = trim($value);
            if(array_key_exists($value, $search_arr)){
                $search_arr = $search_arr[$value];
            }else{
                return null;
            }
        }
        return $search_arr;
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
        $path  = trim($path);
        $value = (is_array($value))? $value: trim($value);
        
        $path_arr = explode(".", $path);
        $config_arr = &self::$_config;
        
        foreach ($path_arr as $conf_value) {
            $conf_value = trim($conf_value);
            if(!array_key_exists($conf_value, $config_arr)){
                $config_arr[$conf_value] = array();
            }
            $config_arr = &$config_arr[$conf_value];
            # Check if the $config_arr is an array or not
            if(!is_array($config_arr)){
                $config_arr = array();
            }
        }
        $config_arr = $value;
    }
}

?>
