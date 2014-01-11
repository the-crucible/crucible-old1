<?php

/**
 * Description of NoHostFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoHostFoundException extends Exception{
    public function __construct($host_name) {
        parent::__construct("Hostname ($host_name) doesn't exist in the system");
    }
}

?>
