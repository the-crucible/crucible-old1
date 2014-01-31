<?php

/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Router
 * 
 * This class has a responsibility to interpret the request url
 * and match it with the routing rule present in the router.php.
 * If the routing rule is matched it will find out the correct app,
 * controller and action to serve the request. Additionally, it will
 * also find out the interperated variables determined by routing rules
 * and the extra arguments sent in the request url.  
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */

class Router {

    /**
     * @var Router
     */
    private static $_instance = null;
    
    /**
     * Variable to store named arguments
     * 
     * @var array
     */
    private $_named_params = array();
    
    /**
     * Variable to store argument array
     * 
     * @var array
     */
    private $_arguments    = array();

    /**
     * It represent the app currently applying.
     * 
     * @var string 
     */
    private $_app = null;
    
    /**
     * It is the controller currently applying
     * 
     * @var string
     */
    private $_controller = null;
    
    /**
     * It is the action currently applying.
     * 
     * @var string
     */
    private $_action = null;
    
    /**
     * getInstance
     * 
     * It returns the stored instance of the class
     * 
     * @return Router
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            # For the first time the app will be loaded by the config file
            self::$_instance = new Router(Config::get('hosts.my_host.app'));
        }
        return self::$_instance;
    }

    /**
     * @return Router;
     */
    public function __clone() {
        return self::getInstance();
    }

    /**
     * 
     * This contructor function will read the router file and
     * find out the correct route its going to hit. Then it will
     * set the named-parameters and arguments derived from the hit routing 
     * rule. The it set the final app, controller and action for 
     * the current request.
     * 
     * @param string $app Starting app name
     */
    private function __construct($app) {
        
        $route_info = $this->_processRoutes(Request::getInstance()->getRequestArr(), $app);
        
        /**
         * Set derived arguments from request path 
         * and router rule
         */
        $this->_arguments = $route_info['args'];
        $this->_named_params = $route_info['named'];
        
        /**
         * set app,controller and action
         */
        $this->setApp($this->_named_params['app']);
        $this->setController($this->_named_params['controller']);
        $this->setAction($this->_named_params['action']);
    }

    /**
     * This will match the routes and return the route
     * 
     * @param array $request_arr
     * @param string $app 
     */
    private function _processRoutes($request_arr, $app) {
        # Get the route config file from that app
        $route_file_path = $this->getAppPath($app) . 'config' . DS . 'router.php';
        # Get the route config
        $routes = Config::read($route_file_path);
        
        # set result flag
        $result_match = false;
        
        $current_route= null;
        foreach ($routes as $route_name => $route) {
            # Examine the current route
            $current_route  = $route;
            # This will turn the routing rule into an array or return null if failed
            $route_frag_arr = Request::getPathInArray($current_route['route']);

            # Checking if the routing rule is ok
            if (is_array($route_frag_arr)) {
                
                # Now try to match both the arrays, 
                # first the real path array and second route array
                $result_match = $this->_matchRoute($route_frag_arr, $request_arr);
                if (is_array($result_match)) {
                    # If the result is a array 
                    # Good.. its a match :)
                    break;
                }else{
                    # Move to next rule
                    continue;
                }
            } else {
                // Ignore this
                continue;
            }
        }
        
        # Check if we finally get a match or not 
        if(is_array($result_match)){
            
            # Now merge the name params got from the rule string with
            # the one explicitly provided in an array.
            
            if(isset($current_route['params'])){
                $named_arr = array_merge($result_match['named'],$current_route['params']);
            }else{
                $named_arr = $result_match['named'];
            }
            
            # Put the app name here it does not come from the routing rule 
            if(!isset($named_arr['app'])){
                $named_arr['app'] = $app;
            }
            
            # Put the final result for named parameter array
            $result_match['named'] = $named_arr;
            
            return $result_match;
        }else{
            # No :(
            # Have to throw a exception here;
            throw new NoRouteFoundException($app);
        }
    }

    /**
     * This function will take one route and try to match 
     * with the path. If matched, it will return an array 
     * containing the result otherwise false
     * 
     * @param type $route_frag_arr array of the routing rule
     * @param type $request_arr array of the request url
     * @return false|array the result of the matching rule
     */
    private function _matchRoute($route_frag_arr, $request_arr) {
        # One very simple check before any check should start
        # is that count of $request_arr should be equal or greater 
        # than $route_frag_arr;
        
        # Check the reverse
        if(count($request_arr) < count($route_frag_arr)){
            return false;
        }

        # Now there will be two buckets if the result is true
        # 1. Named arguments bucket
        # 2. Arguments bucket

        $names_arg_bucket = array();
        $params = array();

        $params_started = FALSE;
        # Check each path element one by one
        foreach ($request_arr as $index => $arg) {
            $current_route_frag = isset($route_frag_arr[$index])? $route_frag_arr[$index] :'';
            # Is the current_route_frag is a named variable
            # For this it should start with ":",
            
            # If the params/argumets has already started coming
            if ($params_started === TRUE) {
                # Then all the path fragments should go into arguments bucket
                $params[] = $arg;
            } 
            # If the rule suggests its a named parameter pointer
            else if (strpos($current_route_frag , ":") === 0) {
                $current_route_frag_name = str_replace(":", "", $current_route_frag);
                $names_arg_bucket[$current_route_frag_name] = $arg;
            } 
            # If it continue to be a match
            else if ($current_route_frag === $arg) {
                # Just nothing to do but its a match so far
            } 
            # If its a start of argumnts coming
            else if ($current_route_frag === '*' && $params_started === FALSE) {
                $params[] = $arg;
                $params_started = TRUE;
            } else {
                # if nothing has happened then this route is a no match
                # and the whole process could be terminated
                return false;
            }
        }

        # If the match survived the checking of each element
        # then its certainly a match
        return array(
            'named' => $names_arg_bucket,
            'args' => $params
        );
    }
    
    
    /**
     * Function to return app path. It supplies the 
     * current app if not provided
     * 
     * @param string $app
     * @return string path of the current app folder
     */
    public function getAppPath($app = null){
        
        # If the app is not provided take the current app
        if(is_null($app)){
            $app = $this->getApp();
        }
        
        return self::getStaticAppPath($app);
    }
    
    /**
     * Function to return the path of the folder of the 
     * app given
     * 
     * @param string $app
     * @return string path of the current app folder
     */
    public static function getStaticAppPath($app){
        
        if($app == 'system'){
            $path = ROOT . DS . 'lib' . DS . 'system' . DS;
        }else if($app == 'setup'){
            $path = ROOT . DS . 'lib' . DS . 'setup' . DS;
        }else{
            $path = ROOT . DS . 'apps' . DS . $app . DS;
        }
        return $path;
    }
    
    /**
     * Getter function for argument array
     * 
     * @return array
     */
    public function getArguments(){
        return $this->_arguments;
    }
    
    
    /**
     * Getter function for named params
     * 
     * @return array
     */
    public function getNamedParams(){
        return $this->_named_params;
    }
    
    /**
     * Setter function for app variable
     * 
     * @param string $app
     */
    public function setApp($app){
        $this->_app = $app;
    }
    
    /**
     * Getter function for app
     * 
     * @return string app
     */
    public function getApp(){
        return $this->_app;
    }
    
    /**
     * Setter function for current controller
     * 
     * @param string $controller
     */
    public function setController($controller){
        $this->_controller = $controller;
    }
    
    /**
     * Getter function for current controller
     * 
     * @return string current controller
     */
    public function getController(){
        return $this->_controller;
    }
    
    /**
     * Setter function for current action
     * 
     * @param string $action
     */
    public function setAction($action){
        $this->_action = $action;
    }
    
    /**
     * Getter function for current action
     * 
     * @return string currnt action
     */
    public function getAction(){
        return $this->_action;
    }
    
}

?>
