<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Response
 * 
 * This class deals with the final delivery of the response being generated
 * With this class you can set headers and response code. All the other things
 * like content type, content-length etc will be automatically taken care off.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Response {

    /**
     * @var Response
     */
    private static $_instance;
    /**
     * Array of headers to be set
     * 
     * @var array
     */
    private $_headers = array();
    /**
     * Response code to be set in the header
     * 
     * @var string
     */
    private $_code = '200';
    /**
     * Body in response to be set
     * 
     * @var string
     */
    private $_body = '';
    /**
     * Array of all the response codes and
     * their corresponding msgs to be set alongside
     * 
     * @var array 
     */
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

    /**
     * It will return the single instance of 
     * response object
     * 
     * @return Response
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Response();
        }
        return self::$_instance;
    }

    /**
     * Function to be called when the response 
     * object would be cloned.
     * 
     * @return Response
     */
    public function __clone() {
        return self::getInstance();
    }
    
    /**
     * This function will set the content-type header
     */
    private function _setContentTypeHeader() {
        # See if the content-type header is already set
        if (!isset($this->_headers['Content-Type'])) {
            # If not then get the content type
            $request_type = $this->_getContentType();
            # And put it into header array to be delivered later
            $this->setHeader("Content-Type", $request_type);
        }
    }

    /**
     * This function will set the content-lenght
     * header at the end of the response
     */
    private function _setContentLengthHeader(){
        # calculate the body length 
        $content_length = strlen($this->_body);
        # And put it as header
        $this->setHeader("Content-Length", $content_length);
    }
    
    /**
     * This function will return the content type
     * of the response by looking into its content
     * 
     * @return string content mime type
     */
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
            return $mime_config['default'];
        }
    }

    /**
     * This function will set all the headers in the response
     * which have been collected through out the execution
     * of the code
     */
    private function _setHeadersInResponse() {
        foreach ($this->_headers as $header_name => $value) {
            header("$header_name: $value", TRUE);
        }
    }

    /**
     * This function will set the response code into the response header
     */
    private function _setResponseCode() {
        $message = $this->_header_messages[$this->_code];
        header("HTTP/1.1 " . $this->_code . " $message crucible", TRUE, $this->_code);
    }

    /**
     * This function set the response header ro be delivered later
     * 
     * @param int $responseCode response code
     */
    public function setResponseCode($responseCode) {
        $this->_code = $responseCode;
    }

    /**
     * By this function you can save the headers to be set
     * later in response
     * 
     * @param string $name header name
     * @param string $value header value
     */
    public function setHeader($name, $value) {
        $this->_headers[$name] = $value;
    }

    /**
     * This function set the body of response to be 
     * delivered later
     * 
     * @param string $response
     */
    public function setBody($response) {
        $this->_body = $response;
    }

    /**
     * This function is used to finally drain the response
     * to the user. Further this there is no go backs :)
     */
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
