<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception will be thrown when there is an error
 * in db connection.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class DbConnectErrorException extends Exception{
    public function __construct($message) {
        parent::__construct($message);
    }
    //put your code here
}

?>
