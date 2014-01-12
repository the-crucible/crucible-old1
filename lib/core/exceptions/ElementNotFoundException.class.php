<?php

/**
 * Description of ElementNotFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class ElementNotFoundException extends Exception{
    public function __construct($element_name) {
        parent::__construct("Element ($element_name) not found");
    }
}

?>
