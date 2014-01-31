<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class is used to be a container for PDO objects created for 
 * different db servers and be able to probide one when asked
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class DbResourceContainer {
    /**
     * @var array It holds all the PDO connection objects
     */
    private static $_db_conn_arr = array();
    
    /**
     * This returns the connection object corresponding to $identifier
     * 
     * @param string $identifier Unique identifier for the connection object
     * @return null|PDO It returns the PDO connection object or null
     */
    public static function get($identifier){
        if(isset(self::$_db_conn_arr[$identifier])){
            return self::$_db_conn_arr[$identifier];
        }else{
            return null;
        }
    }
    
    /**
     * This function stores the connection object based on the unique key
     * 
     * @param string $identifier it is the unique identifier for the connection object 
     * @param PDO $conn_object PDO connection object itself
     * @param type $replace if true it will replace the old connection object
     * @return boolean success or failure
     */
    public static function set($identifier,PDO $conn_object,$replace=true){
        if($replace){
            self::$_db_conn_arr[$identifier] = $conn_object;
            return true;
        }else{
            if(is_null(self::get($identifier))){
                self::$_db_conn_arr[$identifier] = $conn_object;
                return true;
            }else{
                return false;
            }
        }
    }
    
    /**
     * This will purge all the connections
     */
    public static function purge(){
        self::$_db_conn_arr = array();
    }
}

?>
