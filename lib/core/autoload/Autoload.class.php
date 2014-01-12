<?php

/**
 * Description of Autoload
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Autoload {
    private $_app_lib_path;
    private $_module_lib_path;
    
    public function __construct(){
        $app  = Request::getInstance()->getApp();
        $controller = Request::getInstance()->getController();
        
        $app_path  = Router::getInstance()->getAppPath($app);
        $this->_app_lib_path    = $app_path . 'lib' . DS ;
        $this->_module_lib_path = $app_path . 'modules' . DS . $controller . DS . 'lib' . DS;
    }
    
    public function init(){
        # Register the autoload function
        spl_autoload_register(array("Autoload", "load"),'',true);
    }
    
    /**
     * This function will load the class file if its in the app lib folder
     * or in the module lib folder. Class file name should be appended with 
     * .class.php.
     * 
     * @param type $className
     */
    public function load($class_name){
        $class_file_name = $class_name . ".class.php";
        
        # first check in app lib folder
        if(is_file($this->_app_lib_path . $class_file_name)){
            require_once ($this->_app_lib_path . $class_file_name);
        }else if(is_file($this->_module_lib_path . $class_file_name)){
            require_once ($this->_module_lib_path . $class_file_name);
        }else{
            // bad luck
        }
    }
}

?>
