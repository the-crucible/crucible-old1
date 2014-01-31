<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This function creates a method to register the whole
 * folder for autoloading.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Autoload {
    /**
     * Array of dir to be in the list of autoloading
     * 
     * @var array
     */
    private $_autoload_dir = array();
    
    /**
     * __construct
     * 
     * This function will create a autoload object
     * and in the beginning it will set the <app>/lib
     * and <app>/modules/<controller>/lib folder.
     */
    public function __construct(){
        # Get controller | module
        $controller = Router::getInstance()->getController();
        
        # Get app path
        $app_path  = Router::getInstance()->getAppPath();
        
        # Get app lib path
        $this->setDirForAutoLoad($app_path . 'lib');
        
        # Get module lib path
        $this->setDirForAutoLoad($app_path . 'modules' . DS . $controller . DS . 'lib');
    }
    
    /**
     * init
     * 
     * This function will register the load function as a 
     * loading function for autoload.
     */
    public function init(){
        # Register the autoload function
        spl_autoload_register(array("Autoload", "load"),'',true);
    }
    
    /**
     * setDirForAutoLoad
     * 
     * This function adds a folder for autoloading
     * 
     * @param type $dirpath
     * @return int|boolean This function will return 
     *                      0 if the folder is already loaded
     *                      1 if the folder is added for loading
     *                      false if the folder do not exists
     */
    public function setDirForAutoLoad($dirpath){
        # Check if the folder is already there
        if(in_array($dirpath, $this->_autoload_dir)){
            return 0;
        }else{
            # Check if the folder exists or not
            if(is_dir($dirpath)){
                # Then finally add it
                $this->_autoload_dir[] = realpath($dirpath) . DS;
                return 1;
            }else{
                return FALSE;
            }
        }
    }
    
    /**
     * load
     * 
     * This function will loop through the _autoload_dir
     * and try to find all the possible combinations to
     * load the class. The main thing is that if the name 
     * of the class is 'Abc', the file loaded will be 
     * Abc.class.php. If class Abc is still not declaired
     * in Abc.class.php, there is no other way to do it. 
     * 
     * @param type $className
     */
    public function load($class_name){
        $class_file_name = $class_name . ".class.php";
       
        # Loop through the autoload folders
        foreach($this->_autoload_dir as $dir){
            if(is_file($dir . $class_file_name)){
                require_once ($dir . $class_file_name);
                break;
            }
        }
    }
}

?>
