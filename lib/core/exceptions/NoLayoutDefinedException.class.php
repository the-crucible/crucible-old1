<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when there is no layout file
 * is defined at all.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoLayoutDefinedException extends Exception{
    public function __construct() {
        parent::__construct("No Layout defined");
    }
}

?>
