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
     * Constructor function for main crucible class
     */
    private function __construct() {
        # First setup simple autoload mechanism
        $this->_setupSimpleAutoload();
        # Then get config values from the core config values
        $this->_initCoreConfig();
        # Get host configurations
        $this->_getHostConfig();
        # Gather information about the request
        # and init request object
        $this->_initRequestObject();
        # Init response object
        $this->_initResponseObject();

        # If the content is static 
        if ($this->_request->isStatic()) {
            # Static content in response
            $this->_setStaticContentAsResponse();
        } else {
            # If not static resume the usual bussiness 
            # Init Router
            $this->_initRouter();
            # Now when we know all the basic parameters of the 
            # request, we will merge all the configurations defined
            # at different levels and then start processing the request.
            $this->_mergeConfigurations();
            # Now load all the components 
            $this->_initAllComponents();
        }
    }

    /**
     * This function will load all the core config files
     */
    private function _initCoreConfig() {
        $root_config_path = ROOT . DS . 'config' . DS;
        # Loading core config files
        Config::load($root_config_path . 'core.php');
        Config::load($root_config_path . 'hosts.php');
        Config::load($root_config_path . 'database.php');
        Config::load($root_config_path . 'mime.php');
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
        } else {
            //throw new Exception("Core class $class_name not found");
        }
    }

    /**
     * _initRequestObject
     * 
     * This function will basically gather all the information about the 
     * request and suppy to the main object.
     */
    private function _initRequestObject() {
        $this->_request = Request::getInstance();
    }

    /**
     * _initResponseObject
     * 
     * This function will basically create a response object
     */
    private function _initResponseObject() {
        $this->_response = Response::getInstance();
    }

    /**
     * This function will find out the appropiate hosts from the hosts file
     * and get the appropiate hosts config;
     * 
     * @return array
     * @throws NoHostFoundException
     */
    private function _getHostConfig() {
        # Change the '.' to '_' in the host
        $host_original = $_SERVER['HTTP_HOST'];

        $host = str_replace(".", "_", $host_original);

        # Try to get the config
        $host_config = Config::get("hosts.$host");

        # Check if the config found
        if (is_null($host_config)) {
            # If not try again with www_ prepended
            $host_config = Config::get("hosts.www_$host");
        }

        #If still there is no host entry found.
        #Throw no host found exception
        if (is_null($host_config)) {
            throw new NoHostFoundException($host_original);
        } else {
            Config::set("hosts.my_host", $host_config);
            Config::set("hosts.my_host.path", ROOT . DS . 'apps' . DS . $host_config['app']);
            return $host_config;
        }
    }

    private function _initRouter() {
        $this->_router = Router::getInstance();

        # Set the named parameter you set from the router
        $this->getRequest()->setNamed($this->_router->getNamedParams());
        # Set the arguments you get from the router
        $this->getRequest()->setArgs($this->_router->getArguments());

        # Set the name of the new app defined in the router.php if any
        $named_arg = $this->getRequest()->getNamed();
        $new_app_name = $named_arg['app'];

        $current_app_name = Config::get("hosts.my_host.app");
        if ($current_app_name !== $new_app_name) {
            Config::set("hosts.my_host.app", $new_app_name);
            Config::set("hosts.my_host.path", $this->getRouter()->getAppPath($new_app_name));
        }
    }

    /**
     * _mergeConfigurations
     * 
     * This function will do the config merge of different files
     * 
     */
    private function _mergeConfigurations() {
        #Merge all the different files;

        $this->_doConfigMerge('modules');
        $this->_doConfigMerge('factories');
        $this->_doConfigMerge('filters');
        $this->_doConfigMerge('app');
        $this->_doConfigMerge('views');
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
        $config_file_name = $namespace . ".php";
        $app = $this->getRequest()->getApp();
        $controller = $this->getRequest()->getController();
        $mode = Config::get('hosts.my_host.mode');

        $core_config_path = ROOT . DS . 'lib' . DS . 'core' . DS . 'config' . DS . 'config' . DS;
        $app_path = $this->getRouter()->getAppPath($app);
        $app_config_path = $app_path . 'config' . DS;
        $module_config_path = $app_path . 'modules' . DS . $controller . DS . 'config' . DS;

        # First get the file from the core config path;
        $core_config = array();
        $core_config_file_path = $core_config_path . $config_file_name;
        if (is_file($core_config_file_path)) {
            $tmp_core_config = Config::read($core_config_file_path);
            $all_tmp_core_config = isset($tmp_core_config['all']) ? $tmp_core_config['all'] : array();
            $mode_tmp_core_config = isset($tmp_core_config[$mode]) ? $tmp_core_config[$mode] : array();
            $core_config = array_merge($all_tmp_core_config, $mode_tmp_core_config);
        }
        # Second get the file from the app config path;
        $app_config = array();
        $app_config_file_path = $app_config_path . $config_file_name;
        if (is_file($app_config_file_path)) {
            $tmp_app_config = Config::read($app_config_file_path);
            $all_tmp_app_config = isset($tmp_app_config['all']) ? $tmp_app_config['all'] : array();
            $mode_tmp_app_config = isset($tmp_app_config[$mode]) ? $tmp_app_config[$mode] : array();
            $app_config = array_merge($all_tmp_app_config, $mode_tmp_app_config);
        }
        # Third get the file from the module config path;
        $module_config = array();
        $module_config_file_path = $module_config_path . $config_file_name;
        if (is_file($module_config_file_path)) {
            $tmp_module_config = Config::read($module_config_file_path);
            $all_tmp_module_config = isset($tmp_module_config['all']) ? $tmp_module_config['all'] : array();
            $mode_tmp_module_config = isset($tmp_module_config[$mode]) ? $tmp_module_config[$mode] : array();
            $module_config = array_merge($all_tmp_module_config, $mode_tmp_module_config);
        }

        #finally merge them back to back
        $final_config = array_merge($core_config, $app_config, $module_config);
        Config::set($namespace, $final_config);
    }

    private function _initAllComponents() {
        $factories_config = Config::get('factories');
        foreach ($factories_config as $name => $config) {
            if ($config['handler']) {
                if (isset($config['arguments'])) {
                    $object = new $config['handler']($config['arguments']);
                } else {
                    $object = new $config['handler'](array());
                }
                $object->init();
                $this->_components[$name] = $object;
            } else {
                // Wrong config
            }
        }
    }

    public function getComponent($component_name) {
        if (isset($this->_components[$component_name])) {
            return $this->_components[$component_name];
        } else {
            return null;
        }
    }

    /**
     * This function will set the static content in response
     */
    private function _setStaticContentAsResponse() {
        # Get the file content
        $file_content = file_get_contents(Request::getInstance()->getResourcePath());
        # Set as response;
        Response::getInstance()->setBody($file_content);
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

    private function _createController() {
        $app = $this->getRequest()->getApp();
        $controller = $this->getRequest()->getController();

        $app_path = $this->getRouter()->getAppPath($app);
        $controller_class_dir = $app_path . 'modules' . DS . $controller . DS . 'controller' . DS;
        $controller_class = ucfirst($controller) . "Controller";
        $controller_class_path = $controller_class_dir . $controller_class . ".class.php";
        if (is_file($controller_class_path)) {
            require_once ($controller_class_path);
            return new $controller_class($this);
        } else {
            throw new NoControllerFoundException($controller);
        }
    }

    /**
     * dispatch
     * 
     * This function will start the response gathering process
     * which it is suppossed to do.
     */
    public function dispatch() {
        #check if the request is for static content or not
        if (!$this->getRequest()->isStatic()) {
            # If not..
            # Create a controller
            $this->_controller = $this->_createController();
            # Execute the controller;
            $this->_controller->execute($this->getRequest()->getAction());
            # Drain the response 
            Response::getInstance()->drain();
        } else {
            # In case of static content
            # Just drain the response
            Response::getInstance()->drain();
        }
    }

}

?>
