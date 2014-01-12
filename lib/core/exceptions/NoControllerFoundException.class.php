<?php

/**
 * Description of NoControllerFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoControllerFoundException extends Exception{
    public function __construct($controller) {
        parent::__construct("Controller ($controller) not found");
    }
}

?>
