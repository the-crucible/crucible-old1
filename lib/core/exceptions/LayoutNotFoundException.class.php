<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception is thrown when the layout file is not found
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class LayoutNotFoundException Extends Exception{
    public function __construct($layout_name) {
        parent::__construct("Layout ($layout_name) not found");
    }
}

?>
