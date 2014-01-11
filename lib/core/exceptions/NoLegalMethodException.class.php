<?php

/**
 * Description of NoLegalMethod
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoLegalMethodException extends Exception {
    public function __construct($method_name) {
        parent::__construct("This method name ($method_name) is not legal in this class");
    }
}

?>
