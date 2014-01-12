<?php

/**
 * Description of Response
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Response {
    private static $_instance;
    private $_headers = array();
    private $_body = '';
    
    private function __construct() {
        ;
    }
    
    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new Response();
        }
        return self::$_instance;
    }
    
    public function __clone(){
        return self::getInstance();
    }
    
    public function setBody($response){
        $this->_body = $response;
    }
    
    public function drain(){
        echo $this->_body;
    }
}

?>
