<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when the action suggested is not 
 * found in the given controller.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoActionFoundException extends Exception{
    public function __construct($action) {
        parent::__construct("Action ($action) not found");
    }
}

?>
