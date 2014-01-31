<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This exception will be thrown when there is no static content
 * found which was requested.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class FileNotFound404Exception extends Exception{
    public function __construct($url) {
        parent::__construct("File $url not found on this server");
    }
}

?>
