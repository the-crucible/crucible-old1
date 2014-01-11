<?php

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
     * variable to hold autoload config array
     * 
     * @var array
     */
    private static $_autoload = null;

    /**
     * variable to hold Request object
     * 
     * @var Request
     */
    private $_request = null;
    
    /**
     * Variable to hold router object
     * 
     * @var Router
     */
    private $_router  = null;
    
    /**
     * Constructor function for main crucible class
     */
    private function __construct() {
        # First setup simple autoload mechanism
        $this->_setupSimpleAutoload();
        # Then get config values from the core config values
        $this->_initCoreConfig();
        # Gather information about the request
        $this->_initRequestObject();
        # Get host configurations
        $this->_getHostConfig();
        # Init Router
        $this->_initRouter();
        
    }

    private function _initCoreConfig() {
        $root_config_path = ROOT . DS . 'config' . DS;
        $lib_config_path  = ROOT . DS . 'lib' . DS . 'core' . DS . 
                            'config' . DS . 'config' . DS;
        
        # Loading core config files
        Config::load($root_config_path . 'core.php');
        Config::load($root_config_path . 'hosts.php');
        Config::load($root_config_path . 'database.php');
        
        # Loading crucible config files
        Config::load($lib_config_path . 'factories.php');
    }

    /**
     * This function create an autoload mechanism for all the core classes 
     */
    private function _setupSimpleAutoload() {
        $autoload_file = ROOT . DS . 'lib' . DS . 'core' . DS . 'config' . DS . 'config' . DS . 'autoload.php';
        if (is_file($autoload_file)) {
            include $autoload_file;
            self::$_autoload = $config;
        }
        spl_autoload_register(array("Crucible", "_simpleAutoload"));
    }

    /**
     * _simpleAutoload
     * 
     * This function will include the file of the class
     * to be autoloaded
     * 
     * @param string $class_name
     * @throws Exception
     */
    
    private function _simpleAutoload($class_name) {
        if (array_key_exists($class_name, self::$_autoload)) {
            $file_path = self::$_autoload[$class_name];
            $full_file_path = ROOT . DS . 'lib' . DS . 'core' . DS . $file_path;
            if (is_file($full_file_path)) {
                require_once $full_file_path;
            } else {
                throw new Exception("Core class file $full_file_path not found");
            }
        }else{
            throw new Exception("Core class $class_name not found");
        }
    }

    /**
     * _initRequestObject
     * 
     * This function will basically gather all the information about the 
     * request and suppy to the main object.
     */
    private function _initRequestObject(){
        $this->_request = Request::getInstance();
    }
    
    /**
     * This function will find out the appropiate hosts from the hosts file
     * and get the appropiate hosts config;
     * 
     * @return array
     * @throws NoHostFoundException
     */
    private function _getHostConfig(){
        # Get the hosts from the request object
        $request= $this->getRequest();
        # Change the '.' to '_' in the host
        $host   = str_replace(".", "_", $request->getHost());
        
        # Try to get the config
        $host_config = Config::get("hosts.$host");
        
        # Check if the config found
        if(is_null($host_config)){
            # If not try again with www_ prepended
            $host_config = Config::get("hosts.www_$host");
        }
        
        #If still there is no host entry found.
        #Throw no host found exception
        if(is_null($host_config)){
            throw new NoHostFoundException($request->getHost());
        }else{
            Config::set("hosts.my_host", $host_config);
            Config::set("hosts.my_host.path", ROOT. DS . 'apps' . DS . $host_config['app']);
            return $host_config;
        }
    }
    
    private function _initRouter(){
        $this->_router = Router::getInstance();
        $this->getRequest()->setNamed($this->_router->getNamedParams());
        $this->getRequest()->setArgs($this->_router->getArguments());
    }
    
    /**
     * _realignConfigurations
     * 
     * This function is required to merge the config data from the
     * main container with the host specific container and then deal
     * with all the requests  
     */
    private function _realignConfigurations(){
        
    }
    
    /**
     * Getter function for request object
     * 
     * @return Request
     */
    public function getRequest(){
        return $this->_request;
    }
    
    public function getRouter(){
        return $this->_router;
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
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Crucible();
        }
        return self::$_instance;
    }

    /**
     * dispatch
     * 
     * This function will start the response gathering process
     * which it is suppossed to do.
     */
    public function dispatch() {
        print_r($this->_router->getNamedParams());
        print_r($this->_router->getArguments());
    }

}

?>
