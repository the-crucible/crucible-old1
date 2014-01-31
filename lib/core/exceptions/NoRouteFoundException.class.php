<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when then there is no route found
 * to serve the request
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoRouteFoundException extends Exception{
    public function __construct($app) {
        parent::__construct("No route found in app($app)");
    }
}
?>
