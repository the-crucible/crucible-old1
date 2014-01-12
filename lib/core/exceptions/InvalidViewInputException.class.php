<?php

/**
 * Description of InvalidViewInputException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class InvalidViewInputException extends Exception{
    public function __construct($view_file) {
        parent::__construct("Invalid input in file $view_file");
    }
}

?>
