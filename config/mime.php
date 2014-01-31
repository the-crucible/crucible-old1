<?php 
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is the mime type hash table for static content
 * You can add to it if you want to
 */

$config = array(
    # This is the mime type sent when not able to
    # decide which mime type its actually is
    'default'   => 'text/html',
    
    # Other standard mime types are
    'txt'       => 'text/plain',
    'js'        => 'application/javascript',
    'json'      => 'application/json',
    'css'       => 'text/css',
    'gif'       => 'image/gif',
    'jpeg'      => 'image/jpeg',
    'jpg'       => 'image/jpeg',
    'png'       => 'image/png'
)
?>
