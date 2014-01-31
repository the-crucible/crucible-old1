<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when view file is not found
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class ViewNotFoundException extends Exception{
    public function __construct($view_name) {
        parent::__construct("View ($view_name) not found");
    }
}

?>
