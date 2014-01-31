<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Request
 * 
 * This class will gather all the informations about the current
 * request and make available to all the components. This class should 
 * also follow singleton pattern because in each case there is only one 
 * request and it should be accessed from any where
 *
 * @author applect
 */
class Request {
    /**
     * Instance to hold request object
     * 
     * @var Request
     */
    private static $_instance = null;

    # Constant for post request
    const POST = 'post';
    # Constant for get request
    const GET = 'get';
    # syntex for url path 
    const PATH_SYNTEX = "/^\/(\S+\/)*\S*$/";

    # Hold current host
    private $_host;
    # Hold current url
    private $_url;
    # Hold request path array
    private $_request_arr=array();
    # Hold post data
    private $_data=array();
    # Hold cookies data
    private $_cookies=array();
    # Flag for request for static content
    private $_is_static=null;
    # file path if the request is static
    private $_resource_path='';

    /**
     * This function will get gather all the request from the 
     * current request 
     */
    private function __construct() {
        # Set hostname
        $this->_setHost();
        # Set urlname
        $this->_setUrl();
        # Set request path into array
        $this->_setRequestArr();
        # Set post & get data
        $this->_setData();
        # Set cookie
        $this->_setCookie();
    }

    /**
     * This method will return the instance of the 
     * object itself.
     * 
     * @return Request
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    /**
     * In the event of cloning of this object this 
     * method will return the same object created
     * 
     * @return Request
     */
    public function __clone() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    /**
     * This function will get the hostname for which is the 
     * request is for and set it to _host variable
     */
    private function _setHost() {
        $this->_host = $_SERVER['HTTP_HOST'];
    }

    /**
     * This function will get the request url by the which the 
     * request is made and set it to _url variable
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
            $app = Config::get("hosts.my_host.app");
            
            # First element of the RequestArr should match any of the static_path_element
            if (in_array($request_arr[0], $static_path_config)) {
                # Ok then finally it should be a actual file
                $file_path = Router::getStaticAppPath($app) . DS . 'web' . DS . implode(DS, $request_arr);
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
        # If its not determined till now
        if(is_null($this->_is_static)){
            $this->_setStatic();
        }
        return $this->_is_static;
    }
    
    /**
     * If the content is static it will return the file path
     * of the resource
     * 
     * @return false|string path of the resource or false
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
     * getPathInArray
     * 
     * This function checks the url structure and convert it into
     * array. appending "/" is ignored.
     * 
     * @param string $path
     * @return false|array - it returns false if the path do not matches the url or
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
}
?>