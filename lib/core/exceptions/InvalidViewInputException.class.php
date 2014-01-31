<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when the input array provided
 * while creating an output from the template file is not correct 
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class InvalidViewInputException extends Exception{
    public function __construct($view_file) {
        parent::__construct("Invalid input in file $view_file");
    }
}

?>
