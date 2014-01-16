<?php

/**
 * Description of InvalidDbConfigException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class InvalidDbConfigException extends Exception{
    public function __construct($message) {
        parent::__construct($message);
    }
}

?>
