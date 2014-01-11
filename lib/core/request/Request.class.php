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
    
    const POST='post';
    const GET='get';
    const PATH_SYNTEX="/^\/(\S+\/)*\S*$/";
    
    
    private $_host;
    private $_url;
    private $_request_arr;
    private $_data;
    private $_named;
    private $_args;
    private $_cookies;
    private $_session_data;

    /**
     * This function will get gather all the request from the 
     * current request 
     */
    private function __construct() {
        $this->_setHost();
        $this->_setUrl();
        $this->_setRequestArr();
        $this->_setData();
        $this->_setupSession();
    }
    
    /**
     * 
     * @return Request
     */
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new Request();
        }
        return self::$_instance;
    }
    
    public function __clone() {
        if(is_null(self::$_instance)){
            self::$_instance = new Request();
        }
        return self::$_instance;
    }
    
    /**
     * This function will get the hostname for which is the 
     * request is for
     */
    private function _setHost(){
        $this->_host = $_SERVER['HTTP_HOST'];
    }
    
    /**
     * This function will get the request url by the which the 
     * request is made
     */
    private function _setUrl(){
        $this->_url = $_SERVER['REQUEST_URI'];
    }
    
    /**
     * This function will save the request url in the 
     * form of an array, which will help other functions
     * help routing it to appropiate functions
     */
    private function _setRequestArr(){
        $request_path = $_SERVER['REDIRECT_URL'];
        $this->_request_arr = self::getPathInArray($request_path);
    }
    
    /**
     * This funtion save the data(get and post) which is coming
     * with the current request.
     */
    private function _setData(){
        $this->_data = $_REQUEST;
    }
    
    /**
     * This function will save the cookie which is coming with
     * the current request.
     */
    private function _setCookie(){
        $this->_cookies = $_COOKIE;
    }
    
    /**
     * This will start gathering information about the current request
     */
    public function gatherInfo(){
        
    }
    
    /**
     * This function will setup a session handling mechanism
     */
    private function _setupSession(){

    }
    
    /**
     * This is a getter function for the host
     * 
     * @return string
     */
    public function getHost(){
        return $this->_host;
    }
    
    /**
     * This is a getter function for the url
     *  
     * @return string
     */
    public function getUrl(){
        return $this->_url;
    }
    
    /**
     * This is a getter function for the request array
     * 
     * @return array
     */
    public function getRequestArr(){
        return $this->_request_arr;
    }
    
    /**
     * This function tells wheather this function is post
     * request or not
     * 
     * @return bool
     */
    public function isPost(){
        return !empty($_POST);
    }
    
    /**
     * This function returns all the post and get data coming
     * with this request
     * 
     * @return array
     */
    public function getData(){
        return $this->_data;
    }
    
    /**
     * This function returns all the cookie that is coming with this request
     * 
     * @return array
     */
    public function getCookie(){
        return $this->_cookies;
    }
    
    /**
     * To set named data Array
     * 
     * @param array $data
     */
    public function setNamed($data){
        $this->_named = $data;
    }
    
    /**
     * To get named data
     * 
     * @return array
     */
    public function getNamed(){
        return $this->_named;
    }
    
    /**
     * To set args
     * 
     * @param type $data
     */
    public function setArgs($data){
        $this->_args = $data;
    }
    
    /**
     * To get args
     * 
     * @return array
     */
    public function getArgs(){
        return $this->_args;
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
    public static function getPathInArray($path){
        $path = trim($path);
        if(preg_match(self::PATH_SYNTEX, $path) === 1){
            $path_arr = explode("/", $path);
            array_shift($path_arr);
            $last_element = array_pop($path_arr);
            if($last_element){
                $path_arr[] = $last_element;
            }
            return $path_arr;
        }else{
            return false;
        }
    }
}

?>
