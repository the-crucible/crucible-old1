<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class will help interact with databases. This class uses PDO
 * so pdo extension should be there in the php. This class will give you
 * a easy wayout to interact with single-master and multi-slave or multi-master
 * and multi-slave configurations.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Db {
    # Types of nodes
    const ROOT = '1';
    const GROUP= '2';
    const NODE = '3';
    
    # Type of operations
    const READ = '4';
    const WRITE= '5';
    
    /**
     * Identifier for a server group or node
     * 
     * @var string
     */
    private $_identifier;
    /**
     *
     * @var array of Db objects
     */
    private $_db_arr;
    /**
     * Array of master servers at its disposal
     * 
     * @var array
     */
    private $_masters = array();
    
    /**
     * Array of slave servers at its disposal
     * 
     * @var array
     */
    private $_slaves  = array();
    
    /**
     * __construct
     * 
     * This function will basically help object to know its node type
     * and then get its master and slave servers.
     * 
     * @param type $input array of inputs
     */
    public function __construct($input) {
        $this->_identifier = $input['identifier'];
       
        # A db object is of three types
        # 1. Global db object: Which has a global idebtifier eg ('db'),
        # 2. Group  db object: Which is a group of servers, generally attached 
        #    to some site.
        # 3. Node db object: Which is an actual server
        #
        # Now first know which object type it is
        
        $this->_type = $this->_getNodeType($this->_identifier);
        
        # Now according to that we should know the master and slave servers
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
    
    /**
     * _getServers
     * 
     * This function will identigy which are the master and which 
     * are the slave servers and return them into two buckets
     * 
     * @param string $identifier Identifier of the node or group
     * @return array array of the identifiers of master and slave servers
     */
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
    
    /**
     * _getServersForRootNode
     * 
     * It will return the master and slave servers if the root
     * of the config is given as identifier
     * 
     * @param type $identifier Identifier of the root node of the config
     * @return array array of master and slave servers
     */
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
    
    /**
     * _getServersForGroupNode
     * 
     * This will find the master and slave servers if the identifier
     * of the group node is given
     * 
     * @param type $identifier Identifier of the particular group
     * @return array array of master and slave servers
     */
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
    
    /**
     * _getSingleServerNode
     * 
     * This will find the master or slave server if the identifier
     * of the server node is given
     * 
     * @param type $identifier Identifier of the particular node
     * @return array array of master and slave server
     */
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
    
    /**
     * _getNodeType
     * 
     * This function will return the node type of the identifier
     * 
     * @param type $identifier 
     * @return int Node type
     * @throws InvalidDbConfigException This exception will be thrown if the 
     *                                   db config is not correct
     */
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
    
    /**
     * _getServerNode
     * 
     * This function will return the PDO object of the server node
     * specified by the identifier. It will check in the DbResourceContainer
     * first if the already created PDO object is there other wise it will
     * call the _createServer to create a PDO object fresh
     * 
     * @param type $identifier identifier of the server node
     * @return PDO
     */
    private function _getServerNode($identifier){
        $pdo = DbResourceContainer::get($identifier);
        if(!is_resource($pdo)){
            $pdo = $this->_createServer($identifier);
        }
        return $pdo;
    }
    
    /**
     * _createServer
     * 
     * This function will just create a PDO object from the settings
     * given in the node identifier
     * 
     * @param type $identifier server node identifier
     * @return PDO
     * @throws DbConnectErrorException if the connection is not made
     */
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
    
    /**
     * init
     * 
     * Called after __construct function but no use here
     */
    public function init(){
        
    }
    
    /**
     * query
     * 
     * This function will pass the query to the appropiate function
     * and return the result. If the query is CRUD type then the 
     * query will be redirected to master server. Otherwise the
     * query will be redirected to slave servers. 
     * 
     * @param type $sql 
     * @param type $params Optional params to be passed in the query
     * @param type $identifier optional server indentifier
     * @return int|array int if the query is CRUD which is the number
     *                    of row affected. Otherwise the array od results
     * @throws InvalidDbConfigException if the server configuration is incorrect
     */
    public function query($sql,$params=array(),$identifier=null){
        # In this function 
        # 1. First you have to get the type of query
        # 2. Then you have to get one server to deal with that type
        #    for write query, a master server and for read query a slave server
        
        if(!is_null($identifier)){
            # If an identifier is given get the appropiate server
            $servers = $this->_getServers($identifier);
        }else{
            # Or deal with the default servers in the pool
            $servers = array(
                'master' => $this->_masters,
                'slave'  => $this->_slaves
            );
        }
        
        # Get the query type
        $query_type = $this->_getQueryType($sql);
        
        # If the query is CRUD type
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
        
        # Get the PDO object for server
        $server_node = $this->_getServerNode($query_server);
        # Prepare the query
        $statement = $server_node->prepare($sql);
        # Finally execute the query
        $statement->execute($params);
        
        try{
            # If this throws an exception then it would be a CRUD query
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            # And we should get the rowcount instead.
            return $statement->rowCount();
        }
    }
    
    /**
     * _getRandomServer
     * 
     * This function will get a random server from an
     * array of servers.
     * 
     * @param type $server_arr array of possible servers
     * @return null|string random server identifier
     */
    private function _getRandomServer($server_arr){
        $server_count = count($server_arr);
        if ($server_count == 0){
            return null;
        }else if($server_count == 1){
            return $server_arr[0];
        }else{
            $server_index = rand(0, ($server_count - 1));
            return $server_arr[$server_index];
        }
    }
    
    /**
     * _getQueryType
     * 
     * This function will returns the type of query
     * @param type $sql
     * @return int query type CRUD or SELECT
     */
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


    /**
     * __get
     * 
     * This funtion will create new DB objects from the current one 
     * to point to more direct objects.
     * 
     * @param type $db_host
     * @return Db
     */
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
}

?>
