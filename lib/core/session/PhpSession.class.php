<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class will deal with the session in PHP and 
 * provide getter and setter functions
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class PhpSession {
    /**
     * This array will hold an array by reference of $_SESSION
     * 
     * @var array
     */
    private $_data = array();
    /**
     * Session config defined in factories.php
     * 
     * @var array
     */
    private $_config ;
    
    /**
     * This function will ini_set all the session
     * configurations
     * 
     * @param type $params
     */
    public function __construct($params){
        
        foreach($params as $key => $value){
            ini_set("session." . $key, $value);
        }
        $this->_config = $params;
    }
    
    /**
     * This function will start the session and 
     * get the $_SESSION variable into _data variable
     * by reference
     */
    public function init(){
        session_start();
        $this->_data = &$_SESSION;
    }
    
    /**
     * get
     * 
     * This function will read the value from the session array
     * 
     * @param string $path Period seperated path eg. "abc.xyz"
     * @return mixed The value of the variable
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
     * This function will set the value into the session array
     * 
     * @param string $path Period seperated path 
     * @param mixed $value value of the variable
     */
    public function set($path, $value){
        $path  = trim($path);
        $value = (is_array($value))? $value: trim($value);
        
        $path_arr = explode(".", $path);
        $session_arr = &$this->_data;
        
        foreach ($path_arr as $conf_value) {
            $conf_value = trim($conf_value);
            if(!array_key_exists($conf_value, $session_arr)){
                $session_arr[$conf_value] = array();
            }
            $session_arr = &$session_arr[$conf_value];
            # Check if the $session_arr is an array or not
            if(!is_array($session_arr)){
                $session_arr = array();
            }
        }
        $session_arr = $value;
    }
    
    public function destroy(){
        session_destroy();
    }
}

?>
