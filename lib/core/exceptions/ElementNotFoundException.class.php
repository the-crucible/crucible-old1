<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception will be thrown when there is an error finding
 * the element file.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class ElementNotFoundException extends Exception{
    public function __construct($element_name) {
        parent::__construct("Element ($element_name) not found");
    }
}

?>
