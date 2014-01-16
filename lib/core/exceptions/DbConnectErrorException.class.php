<?php

/**
 * Description of DbConnectErrorException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class DbConnectErrorException extends Exception{
    public function __construct($message) {
        parent::__construct($message);
    }
    //put your code here
}

?>
