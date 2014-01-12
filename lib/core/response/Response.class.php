<?php

/**
 * Description of Response
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Response {

    private static $_instance;
    private $_headers = array();
    private $_code = '200';
    private $_body = '';
    private $_header_messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    private function __construct() {
        ;
    }

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Response();
        }
        return self::$_instance;
    }

    public function __clone() {
        return self::getInstance();
    }

    private function _setContentTypeHeader() {
        if (!isset($this->_headers['Content-Type'])) {
            $request_type = $this->_getContentType();
            $this->setHeader("Content-Type", $request_type);
        }
    }

    private function _setContentLengthHeader(){
        $content_length = strlen($this->_body);
        $this->setHeader("Content-Length", $content_length);
    }
    
    private function _getContentType() {
        # Get requestArr
        $request_arr = Request::getInstance()->getRequestArr();
        # Get the last part of request arr
        $last_frag = array_pop($request_arr);
        # split it by "."
        $last_frag_arr = explode(".", $last_frag);
        # Get the extension by it
        $extension = array_pop($last_frag_arr);
        
        # Get the mime config
        $mime_config = Config::get('mime');
        
        # Check if the mime config is present
        if(isset($mime_config[$extension])){
            # If it is return the mime-type
            return $mime_config[$extension];
        }else{
            # else return the mime type of default
            return 'default';
        }
    }

    private function _setHeadersInResponse() {
        foreach ($this->_headers as $header_name => $value) {
            header("$header_name: $value", TRUE);
        }
    }

    private function _setResponseCode() {
        $message = $this->_header_messages[$this->_code];
        header("HTTP/1.1 " . $this->_code . " $message crucible", TRUE, $this->_code);
    }

    public function setResponseCode($responseCode) {
        $this->_code = $responseCode;
    }

    public function setHeader($name, $value) {
        $this->_headers[$name] = $value;
    }

    public function setBody($response) {
        $this->_body = $response;
    }

    public function drain() {
        # Set the appropiate Content type header
        $this->_setContentTypeHeader();
        # Set content length header;
        $this->_setContentLengthHeader();
        # Set the response code in response
        $this->_setResponseCode();
        # Set the headers in response;
        $this->_setHeadersInResponse();
        # Finally echo the response body;
        echo $this->_body;
    }

}

?>
