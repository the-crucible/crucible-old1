<?php

/**
 * Description of NoLayoutDefinedException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoLayoutDefinedException extends Exception{
    public function __construct() {
        parent::__construct("No Layout defined");
    }
}

?>
