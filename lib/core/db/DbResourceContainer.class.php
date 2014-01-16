<?php

/**
 * Description of DbResourceContainer
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class DbResourceContainer {
    
    private static $_db_conn_arr = array();
    
    public static function get($identifier){
        if(isset(self::$_db_conn_arr[$identifier])){
            return self::$_db_conn_arr[$identifier];
        }else{
            return null;
        }
    }
    
    public static function set($identifier,$conn_object,$replace=true){
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
}

?>
