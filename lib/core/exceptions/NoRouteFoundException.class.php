<?php

/**
 * Description of NoRouteFoundException
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoRouteFoundException extends Exception{
    public function __construct($app) {
        parent::__construct("No route found in app($app)");
    }
}
?>
