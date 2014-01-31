<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when the suggested controller is not
 * found in the given app
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoControllerFoundException extends Exception{
    public function __construct($controller) {
        parent::__construct("Controller ($controller) not found");
    }
}

?>
