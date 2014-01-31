<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * NoHostFoundException is thrown when there is no host 
 * configuration defined in hosts.php file for the current
 * http_host in the request.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class NoHostFoundException extends Exception{
    public function __construct($host_name) {
        parent::__construct("Hostname ($host_name) doesn't exist in the system");
    }
}

?>
