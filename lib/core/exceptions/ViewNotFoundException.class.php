<?php

/**
 * Description of ViewNotFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class ViewNotFoundException extends Exception{
    public function __construct($view_name) {
        parent::__construct("View ($view_name) not found");
    }
}

?>
