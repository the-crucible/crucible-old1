<?php

/**
 * NoFileFoundException
 * 
 * This exception can be thrown when a file whose path was given is not found
 *
 * @author Tejaswi Sharma<tejaswi@crucible-framework.org>
 */
class NoFileFoundException extends Exception{
    /**
     * __construct
     * 
     * This funtion will be used to create the exception
     * 
     * @param string $file_path Path of the file being given
     */
    public function __construct($file_path) {
        parent::__construct("File path $file_path is not found");
    }
    
}

?>
