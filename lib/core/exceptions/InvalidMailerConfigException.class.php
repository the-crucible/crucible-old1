<?php

/**
 * Description of InvalidMailerConfigException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class InvalidMailerConfigException extends Exception{
    public function __construct($params) {
        $par = print_r($params, TRUE);
        parent::__construct('Invalid mail params ' . $par);
    }
}

?>
