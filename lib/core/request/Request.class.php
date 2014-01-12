<?php

/**
 * This class will gather all the informations about the current
 * request and make available to all the components. This class should 
 * also follow singleton pattern because in each case there is only one 
 * request and it should be accessed from any where
 *
 * @author applect
 */
class Request {

    private static $_instance = null;

    const POST = 'post';
    const GET = 'get';
    const PATH_SYNTEX = "/^\/(\S+\/)*\S*$/";

    private $_host;
    private $_url;
    private $_request_arr=array();
    private $_data=array();
    private $_named=array();
    private $_args=array();
    private $_cookies=array();
    private $_is_static=false;
    private $_resource_path='';

    /**
     * This function will get gather all the request from the 
     * current request 
     */
    private function __construct() {
        $this->_setHost();
        $this->_setApp();
        $this->_setUrl();
        $this->_setRequestArr();
        $this->_setData();
        $this->_setCookie();
        $this->_setStatic();
    }

    /**
     * 
     * @return Request
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    public function __clone() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    /**
     * This function will get the hostname for which is the 
     * request is for
     */
    private function _setHost() {
        $this->_host = $_SERVER['HTTP_HOST'];
    }

    /**
     * Set the initial app value from the host config
     * later it will be set by the router object
     */
    private function _setApp(){
        $this->_named['app'] = Config::get('hosts.my_host.app');
    }
    /**
     * This function will get the request url by the which the 
     * request is made
     */
    private function _setUrl() {
        $this->_url = $_SERVER['REQUEST_URI'];
    }

    /**
     * This function will save the request url in the 
     * form of an array, which will help other functions
     * help routing it to appropiate functions
     */
    private function _setRequestArr() {
        $request_path = $_SERVER['REDIRECT_URL'];
        $this->_request_arr = self::getPathInArray($request_path);
    }

    /**
     * This funtion save the data(get and post) which is coming
     * with the current request.
     */
    private function _setData() {
        $this->_data = $_REQUEST;
    }

    /**
     * This function will save the cookie which is coming with
     * the current request.
     */
    private function _setCookie() {
        $this->_cookies = $_COOKIE;
    }

    /**
     * This function will check if this request is for
     * static content or not
     */
    private function _setStatic() {
        # Get the request array
        $request_arr = $this->getRequestArr();

        # To be a static content the size of RequestArr should be more than 1
        if (count($request_arr) > 1) {
            $static_path_config = Config::get("hosts.my_host.static_paths");
            # First element of the RequestArr should match any of the static_path_element
            if (in_array($request_arr[0], $static_path_config)) {
                # Ok then finally it should be a actual file
                $file_path = $this->getAppPath() . DS . 'web' . DS . implode(DS, $request_arr);
                if(is_file($file_path)){
                    $this->_is_static = true;
                    $this->_resource_path = $file_path;
                }else{
                    throw new FileNotFound404Exception($this->getUrl());
                }
            }
        }
    }

    /**
     * This is a getter function for the host
     * 
     * @return string
     */
    public function getHost() {
        return $this->_host;
    }

    /**
     * This is a getter function for the url
     *  
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * This is a getter function for the request array
     * 
     * @return array
     */
    public function getRequestArr() {
        return $this->_request_arr;
    }

    /**
     * This function tells wheather this function is post
     * request or not
     * 
     * @return bool
     */
    public function isPost() {
        return !empty($_POST);
    }

    /**
     * This function tells if the resource is a static content or not
     * 
     * @return bool
     */
    public function isStatic(){
        return $this->_is_static;
    }
    
    /**
     * If the content is static it will return the file path
     * of the resource
     * 
     * @return mixed path of the resource or false
     */
    public function getResourcePath(){
        if($this->isStatic()){
            return $this->_resource_path;
        }else{
            return false;
        }
    }
    /**
     * This function returns all the post and get data coming
     * with this request
     * 
     * @return array
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * This function returns all the cookie that is coming with this request
     * 
     * @return array
     */
    public function getCookie() {
        return $this->_cookies;
    }

    /**
     * To set named data Array
     * 
     * @param array $data
     */
    public function setNamed($data) {
        $this->_named = $data;
    }

    /**
     * To get named data
     * 
     * @return array
     */
    public function getNamed() {
        return $this->_named;
    }

    /**
     * To set args
     * 
     * @param type $data
     */
    public function setArgs($data) {
        $this->_args = $data;
    }

    /**
     * To get args
     * 
     * @return array
     */
    public function getArgs() {
        return $this->_args;
    }

    /**
     * To get the name of the app
     * 
     * @return string
     */
    public function getApp() {
        $named = $this->getNamed();
        return isset($named['app']) ? $named['app'] : null;
    }

    /**
     * To get the name of the controller
     * 
     * @return string
     */
    public function getController() {
        $named = $this->getNamed();
        return isset($named['controller']) ? $named['controller'] : null;
    }

    /**
     * To get the name of the action
     * 
     * @return string
     */
    public function getAction() {
        $named = $this->getNamed();
        return isset($named['action']) ? $named['action'] : null;
    }

    /**
     * getPathInArray
     * 
     * This function checks the url structure and convert it into
     * array. appending "/" is ignored.
     * 
     * @param type $path
     * @return mixed - it returns false if the path do not matches the url or
     * route structure and array of the path of the url do matches route structure. 
     */
    public static function getPathInArray($path) {
        $path = trim($path);
        if (preg_match(self::PATH_SYNTEX, $path) === 1) {
            $path_arr = explode("/", $path);
            array_shift($path_arr);
            $last_element = array_pop($path_arr);
            if ($last_element) {
                $path_arr[] = $last_element;
            }
            return $path_arr;
        } else {
            return false;
        }
    }

    /**
     * Function to return app path
     * 
     * @param string $app
     * @return string
     */
    public function getAppPath() {
        $app = $this->getApp();

        if ($app == 'system') {
            $path = ROOT . DS . 'lib' . DS . 'system' ;
        } else if ($app == 'setup') {
            $path = ROOT . DS . 'lib' . DS . 'setup';
        } else if (!empty($app)) {
            $path = ROOT . DS . 'apps' . DS . $app ;
        } else {
            $path = '';
        }
        return $path;
    }

}

?>
