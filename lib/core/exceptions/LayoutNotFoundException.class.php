<?php

/**
 * Description of LayoutNotFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class LayoutNotFoundException Extends Exception{
    public function __construct($layout_name) {
        parent::__construct("Layout ($layout_name) not found");
    }
}

?>
