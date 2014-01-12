<?php

/**
 * Description of NoActionFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoActionFoundException extends Exception{
    public function __construct($action) {
        parent::__construct("Action ($action) not found");
    }
}

?>
