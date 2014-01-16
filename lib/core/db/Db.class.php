<?php

/**
 * Description of DB
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Db {
    const ROOT = '1';
    const GROUP= '2';
    const NODE = '3';
    
    const READ = '4';
    const WRITE= '5';
    
    private $_identifier;
    private $_db_arr;
    private $_masters = array();
    private $_slaves  = array();
    
    public function __construct($input) {
        $this->_identifier = $input['identifier'];
        # Now here we have to do few things
        # 1. Need to know who are my master servers
        # 2. Who is my slave server
        # Now where will warry acccording to who they are
        # A db object is of three types
        # 1. Global db object: Which has a global idebtifier eg ('db'),
        # 2. Group  db object: Which is a group of servers, generally attached 
        #    to some site.
        # 3. Node db object: Which is an actual server
        #
        # Now first know which object type it is
        
        $this->_type = $this->_getNodeType($this->_identifier);
        
        # Now according to that know the master and slave servers
        $result = $this->_getServers($this->_identifier);
        
        # Set the master servers
        if(!empty($result['master'])){
            $this->_masters = $result['master'];
        }
        
        # Set the slave servers
        if(!empty($result['slave'])){
            $this->_slaves = $result['slave'];
        }
    }
    
    private function _getServers($identifier){
        $server_type = $this->_getNodeType($identifier);
        switch ($server_type){
            case self::ROOT:
                $result = $this->_getServersForRootNode($identifier);
                break;
            case self::GROUP:
                $result = $this->_getServersForGroupNode($identifier);
                break;
            case self::NODE:
                $result = $this->_getSingleServerNode($identifier);
        }
        return $result;
    }
    
    private function _getServersForRootNode($identifier){
        /**
         * In case of root node it would go like following
         * 
         * 1. First it will search for appropiate group
         * 2. If it has multiple groups then
         * 3.   It will look for group named 'default'
         * 4.   if there is any group name 'default' then
         * 5.       look further in group 'default'
         * 6.   else
         * 7.       look further in the first group
         */
        $db_config = Config::get($identifier);
        
        # Check if there is a group with 'default' name present
        if(array_key_exists('default', $db_config)){
            $result = $this->_getServersForGroupNode($identifier . ".default");
        }else{
            # If not get the first one any way
            $first_group = reset($db_config);
            $result = $this->_getServersForGroupNode($identifier . ".$first_group");
        }
        return $result;
    }
    
    private function _getServersForGroupNode($identifier){
        /*
         * In group node, we have to loop through all the servers and 
         * 
         */
        $db_config = Config::get($identifier);
        $result = array('master' => array(), 'slave' => array());
        foreach($db_config as $name => $node){
            $node_result = $this->_getSingleServerNode($identifier . ".$name");
            $result['master'] = array_merge($result['master'], $node_result['master']);
            $result['slave'] = array_merge($result['slave'], $node_result['slave']);
        }
        return $result;
    }
    
    private function _getSingleServerNode($identifier){
        /**
         * Here we just check whether its a master or slave
         */
        $db_config = Config::get($identifier);
        $result = array('master' => array(), 'slave' => array());
        if($db_config['role'] == 'master'){
            $result['master'][] = $identifier;
        }else if($db_config['role'] == 'slave'){
            $result['slave'][] = $identifier;
        }
        return $result;
    }
    
    private function _getNodeType($identifier){
        $id_frag_arr = explode(".",  $identifier);
        $frag_count  = count($id_frag_arr);
        if($frag_count === 1){
            $type = self::ROOT; // or global
        }else if($frag_count === 2){
            $type = self::GROUP;
        }else if($frag_count === 3){
            $type = self::NODE;
        }else{
            throw new InvalidDbConfigException("Identifier $identifier is not valid");
        }
        return $type;
    }
    
    
    private function _getServerNode($identifier){
        $pdo = DbResourceContainer::get($identifier);
        if(!is_resource($pdo)){
            $pdo = $this->_createServer($identifier);
        }
        return $pdo;
    }
    
    private function _createServer($identifier){
        $db_config = Config::get($identifier);
        $host = $db_config['host'];
        $user = $db_config['user'];
        $pass = $db_config['password'];
        $dbnm = $db_config['dbname'];
        try{
            $pdo = new PDO("mysql:host=$host;dbname=$dbnm", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
            return $pdo;
        }  catch (Exception $e){
            throw new DbConnectErrorException($e->getMessage());
        }
    }
    
    public function init(){
        
    }
    
    public function query($sql,$params=array(),$identifier=null){
        # In this function 
        # 1. First you have to get the type of query
        # 2. Then you have to get one server to deal with that type
        #    for write query, a master server and for read query a slave server
        if(!is_null($identifier)){
            $servers = $this->_getServers($identifier);
        }else{
            $servers = array(
                'master' => $this->_masters,
                'slave'  => $this->_slaves
            );
        }
        
        $query_type = $this->_getQueryType($sql);
        
        if($query_type === self::WRITE){
            $query_server = $this->_getRandomServer($servers['master']);
            if(is_null($query_server)){
                throw new InvalidDbConfigException("No master node available to execute this query");
            }
        }else if($query_type == self::READ){
            $query_server = $this->_getRandomServer($servers['slave']);
            if(is_null($query_server)){
                $query_server = $this->_getRandomServer($servers['master']);
            }
            if(is_null($query_server)){
                throw new InvalidDbConfigException("No server is available to execute this query");
            }
        }else{
            throw new InvalidDbConfigException("Bad server type");
        }
        $server_node = $this->_getServerNode($query_server);
        $statement = $server_node->prepare($sql);
        $statement->execute($params);
        
        try{
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            return $statement->rowCount();
        }
    }
    
    private function _getRandomServer($server_arr){
        $server_count = count($server_arr);
        if ($server_count == 0){
            return null;
        }else if($server_count == 1){
            return $server_arr[0];
        }else{
            echo "rand:" . $server_index = rand(0, ($server_count - 1));
            return $server_arr[$server_index];
        }
    }
    
    private function _getQueryType($sql){
        $is_insert_query    = strripos($sql,'insert'); // insert
        $is_update_query    = strripos($sql,'update'); // update
        $is_delete_query    = strripos($sql,'delete'); // delete
        $is_SP_query        = strripos($sql,'call'); // stored procedure
        if($is_insert_query !== false || $is_update_query !== false || $is_delete_query !== false || $is_SP_query !== false){
            return self::WRITE;
        }else{
            return self::READ;
        }
    }


    public function __get($db_host){
        if(isset($this->_db_arr[$db_host])){
            return $this->_db_arr[$db_host];
        }else{
            # To check if the identifier is valid just check its type
            # If it doesn't throw any exception its fine
            $new_identifier = $this->_identifier . ".$db_host";
            $type = $this->_getNodeType($new_identifier);
            $new_node = new Db(array('identifier' => $new_identifier));
            $this->_db_arr[$db_host] = $new_node;
            return $new_node;
        }
    }
    
    public function showLayer(){
        print_r($this->_db_arr);
    }
    
    
}

?>
