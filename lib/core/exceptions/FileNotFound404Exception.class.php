<?php

/**
 * Description of FileNotFound404Exception
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class FileNotFound404Exception extends Exception{
    public function __construct($url) {
        parent::__construct("File $url not found on this server");
    }
}

?>
