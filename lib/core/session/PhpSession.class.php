<?php

/**
 * Description of PhpSession
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class PhpSession {
    private $_data = array();
    private $_config ;
    
    public function __construct($params){
        //ob_start();
        foreach($params as $key => $value){
            ini_set("session." . $key, $value);
        }
        $this->_config = $params;
    }
    
    public function init(){
        session_start();
        $this->_data = &$_SESSION;
    }
    
    /**
     * get
     * 
     * This function will read the config value from the config array
     * 
     * @param string $path Period seperated path eg. "db.default.username"
     * @return mixed The value of the config 
     */
    public function get($path){
        $path = trim($path);
        $path_arr = explode('.', $path);
        $search_arr = $this->_data;
        
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
    public function set($path, $value){
        $path  = trim($path);
        $value = (is_array($value))? $value: trim($value);
        
        $path_arr = explode(".", $path);
        $config_arr = &$this->_data;
        
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
    
    public function destroy(){
        session_destroy();
    }
}

?>
