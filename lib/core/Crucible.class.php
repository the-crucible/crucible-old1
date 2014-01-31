<?php

/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Crucible is the main class of the crucible framework and act as a glue 
 * for all the other components. Start looking from the constructor function 
 * and then the dispatch function 
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
     * variable to hold Response object
     * 
     * @var Response
     */
    private $_response = null;

    /**
     * Variable to hold router object
     * 
     * @var Router
     */
    private $_router = null;

    /**
     * This variable will hold all the components
     * 
     * @var array
     */
    private $_components = array();
    
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
     * Getter function for request object
     * 
     * @return Request
     */
    
    public function getRequest() {
        return $this->_request;
    }

    /**
     * Getter function for router object
     * 
     * @return Router
     */
    
    public function getRouter() {
        return $this->_router;
    }

    /**
     * Getter function for Response object;
     * 
     * @return Response
     */
    
    public function getResponse(){
        return $this->_response;
    }
    
    /**
     * This function will return the session component. It also creates
     * the session object internally if not untill created
     * 
     * @return PhpSession It returns an object of PhpSession class
     */
    public static function getSession(){
        return self::getInstance()->getComponent('session');
    }
    
    /**
     * This function will return the db component. It also creates the
     * db object internally if not created
     * 
     * @return Db It returns an object ob Db class
     */
    
    public static function getDb(){
        return self::getInstance()->getComponent('db');
    }
    
    /**
     * This function returns a object of SwiftMailer class which
     * emulates Swift_Mailer object.
     * 
     * @return Swift_Mailer It returns mailer component
     */
    
    public static function getMailer(){
        return self::getInstance()->getComponent('mailer');
    }
    
    /**
     * __construct
     * 
     * This function do following things
     * 
     * 1. First it setup a simple auto load function. (_setupSimpleAutoload)
     * 2. Load all the project config files. (_initCoreConfig)
     * 3. Init request object. (_initRequestObject)
     * 4. Get the config for the current host from hosts.php config file. (_getHostConfig)
     * 5. Init response object. (_initResponseObject)  
     * 6. It then tests if the request is for static object.(Request::isStatic())
     * 7. If it is not static, Init router object to know the current controller and action(_initRouter).
     */
    private function __construct() {
        # First setup simple autoload mechanism
        $this->_setupSimpleAutoload();

        # Then get config values from the core config values
        $this->_initCoreConfig();

        # Init request object
        $this->_initRequestObject();

        # Get host configurations
        $this->_getHostConfig();

        # Init response object
        $this->_initResponseObject();

        # If the content is static eg. .js,.css. image files etc 
        if (!$this->_request->isStatic()) {
            # If not static resume the usual bussiness 
            # Init Router
            $this->_initRouter();
        }
    }

    /**
     * _setupSimpleAutoload
     * 
     * This function creates a basic autoload mechanism for all the core classes
     * in which the files of the classes will be loaded from a lookup array (self::$_autoload)
     */
    private function _setupSimpleAutoload() {
        $autoload_file = CORE . DS . 'config' . DS . 'config' . DS . 'autoload.php';

        # Load the autoload.php file
        if (is_file($autoload_file)) {
            include $autoload_file;
            self::$_autoload = $config;
        } else {
            throw new Exception("Core autoload.php file is missing");
        }

        # Register the _simpleAutoload function as the autoload function
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

        # Checks if the class name present in the list
        if (array_key_exists($class_name, self::$_autoload)) {

            # If yes check if the listed file is present
            $file_path = self::$_autoload[$class_name];
            $full_file_path = ROOT . DS . 'lib' . DS . 'core' . DS . $file_path;

            if (is_file($full_file_path)) {

                # If the file is present include it
                require_once $full_file_path;
            } else {

                # Otherwise throw an exception
                throw new Exception("Core class file $full_file_path not found");
            }
        } else {

            // Class not present in autoload.php file 
            return null;
        }
    }

    /**
     * _initCoreConfig
     * 
     * This function loads all the core config files and put it into
     * the central registry in Config object
     */
    private function _initCoreConfig() {
        # Project config dir path
        $root_config_path = ROOT . DS . 'config' . DS;

        # Loading core config files
        # This loads hosts config file
        Config::load($root_config_path . 'hosts.php');

        # This loads database config file
        Config::load($root_config_path . 'database.php');

        # This loads mime type file which determine the 
        # content type of the request
        Config::load($root_config_path . 'mime.php');
    }

    /**
     * This function will find out the appropiate host configuration from the 
     * hosts file for the current http_host
     * 
     * @return array
     * @throws NoHostFoundException
     */
    private function _getHostConfig() {

        # Change the '.' to '_' in the host
        $host_original = $this->getRequest()->getHost();

        $host = str_replace(".", "_", $host_original);

        # Try to get the config
        $host_config = Config::get("hosts.$host");

        # Check if the config found
        if (is_null($host_config)) {
            # If not, try again with www_ prepended
            $host_config = Config::get("hosts.www_$host");
        }

        # If still there is no host entry found,
        # throw NoHostFoundException

        if (is_null($host_config)) {

            throw new NoHostFoundException($host_original);
        } else {

            # If the host is found. copy the defined config
            # into a seperate key named 'my_host'.
            # The benefit of doing this we can get this value even
            # without knowing the hostname
            Config::set("hosts.my_host", $host_config);
            return $host_config;
        }
    }

    /**
     * _initRequestObject
     * 
     * This function will create a Request object and suppy to 
     * the main object. Request object while creating itself gather
     * all the information related to the current request
     */
    private function _initRequestObject() {
        $this->_request = Request::getInstance();
    }

    /**
     * _initResponseObject
     * 
     * This function will create a response object and supply it to the
     * main Crucible object. This is the object which is responsible for
     * the final delivery of the response to the users.
     */
    private function _initResponseObject() {
        $this->_response = Response::getInstance();
    }

    /**
     * _initRouter
     * 
     * This function initiate router object and router object
     * determines which app, controller and action to be called.
     * This also determines some named parameters and arguments
     */
    private function _initRouter() {
        $this->_router = Router::getInstance();
    }

    /**
     * _initApp
     * 
     * This function first merge all the different configurations files
     * from core to app to module. Then it initializes all the modules
     * according to the merges configurations.
     * 
     */
    private function _initApp() {
        # Merge all the different config files;

        $this->_doConfigMerge('factories');
        $this->_doConfigMerge('filters');
        $this->_doConfigMerge('app');
        $this->_doConfigMerge('views');
        
        # Then reset all the components
        $this->_resetAllComponents();
    }

    /**
     * This function is required to merge the config data from the
     * core container, with the app container and then with the module 
     * contaner
     * 
     * @param type $namespace
     * @param type $mode
     */
    private function _doConfigMerge($namespace) {
        # Name of the file
        $config_file_name = $namespace . ".php";
        # Get name of the controller or the module name
        $controller = $this->getRouter()->getController();
        # Get the mode in which the app is running
        $mode = Config::get('hosts.my_host.mode');

        # Create path of different files
        $core_config_path = CORE . DS . 'config' . DS . 'config' . DS;
        $app_path = $this->getRouter()->getAppPath();
        $app_config_path = $app_path . 'config' . DS;
        $module_config_path = $app_path . 'modules' . DS . $controller . DS . 'config' . DS;

        # First get the file from the core config path and get the configurations;
        $core_config = Config::readMerged($core_config_path . $config_file_name, $mode);
        
        # Second get the file from the app config path and get the configurations;
        $app_config = Config::readMerged($app_config_path . $config_file_name, $mode);
        
        # Third get the file from the module config path and get the configurations;
        $module_config = Config::readMerged($module_config_path . $config_file_name, $mode);

        #finally merge them back to back
        $final_config = array_merge($core_config, $app_config, $module_config);
        Config::set($namespace, $final_config);
    }

    /**
     * _resetAllComponents
     * 
     * This function reset the components array
     * and start fresh
     */
    private function _resetAllComponents() {
        $this->_components = array();
    }

    /**
     * getComponent
     * 
     * This function returns the component object by its name
     * 
     * @param type $component_name
     * @return null|$object component object
     */
    public function getComponent($component_name) {
        # If ccomponent exists then return 
        if (isset($this->_components[$component_name])) {
            return $this->_components[$component_name];
        } else {
            
            # Get the config 
            $factories_config = Config::get('factories');
            
            # If not, check if the config exists for it
            if (isset($factories_config[$component_name])) {
                
                $config = $factories_config[$component_name];
                
                # If handler and argument exists return object
                if (isset($config['handler']) && isset($config['arguments'])) {
                    $object = new $config['handler']($config['arguments']);
                } 
                # If only handler exists
                else if (isset($config['handler'])) {
                    $object = new $config['handler'](array());
                }else{
                    return null;
                }
                # call init function
                $object->init();
                # Save it for the next time
                $this->_components[$component_name] = $object;
                # Return the object
                return $object;
            } 
            return null;
        }
    }

    /**
     * _setStaticContentAsResponse
     * 
     * This function will set the static content in response
     */
    private function _setStaticContentAsResponse() {
        # Get the file content
        $file_content = file_get_contents(Request::getInstance()->getResourcePath());
        # Set as response;
        Response::getInstance()->setBody($file_content);
    }

    /**
     * This function returns an instance of controller class 
     * or throws an exception
     * 
     * @return Controller
     * @throws NoControllerFoundException
     */
    private function _createController() {
        $app = $this->getRouter()->getApp();
        $controller = $this->getRouter()->getController();

        $app_path = $this->getRouter()->getAppPath();
        $controller_class_dir = $app_path . 'modules' . DS . $controller . DS . 'controller' . DS;
        $controller_class = ucfirst($controller) . "Controller";
        $controller_class_path = $controller_class_dir . $controller_class . ".class.php";
        if (is_file($controller_class_path)) {
            require_once ($controller_class_path);
            return new $controller_class();
        } else {
            throw new NoControllerFoundException($controller);
        }
    }

    /**
     * _go
     * 
     * This function create the controller object and execute the 
     * current action. If the Forward action exception is thrown
     * it checks of the app or controller change and then after
     * setting the same to the router, it calls the same function again 
     * 
     * @param bool $build represent of the config is to be build again
     */
    private function _go($build=true) {
        if($build)
            $this->_initApp();
        try{
            
            $this->_controller = $this->_createController();
            $this->_controller->execute($this->getRouter()->getAction());
            
        }catch(ForwardActionException $e){
            
            # Set the changed action
            $this->getRouter()->setAction($e->getForwardedAction());
            
            # set is build required as false in the beginning
            $is_build_required = false;
            
            # Check if the new controller is different as current
            # controller or not. If it is, set the is_build_required as true
            
            if(!is_null($e->getForwardedController())){
                # Check if controller is different
                if($this->getRouter()->getController() != $e->getForwardedController()){
                    $this->getRouter()->setController($e->getForwardedController());
                    $is_build_required = true;
                }
            }
            
            # Check if the new app is different as current
            # app or not. If it is, set the is_build_required as true
            
            if(!is_null($e->getForwardedApp())){
                # Check if the app is different;
                if($this->getRouter()->getApp() != $e->getForwardedApp()){
                    $this->getRouter()->setApp($e->getForwardedApp());
                    $is_build_required = true;
                }
            }
            
            # Call itself again
            $this->_go($is_build_required); // If app or controller is different then build again
        }
    }

    /**
     * dispatch
     * 
     * This function will start the response gathering process
     * and finally drain the response
     */
    public function dispatch() {
        #check if the request is for static content or not
        if ($this->getRequest()->isStatic()) {
            
            # Set static content in response
            $this->_setStaticContentAsResponse();
            
        } else {
            
            # Fire the controller/action chain
            $this->_go();
            
        }
        
        # In the end just drain the response
        $this->getResponse()->drain();
    }

}

?>
