<?php

/**
 * Description of Router
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
     * @return Router
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Router();
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
     * This function will read the router file and
     * find out the correct route its going to hit.
     * It could also update the request variable with
     * more data
     */
    private function __construct() {
        $route_info = $this->_processRoutes(Request::getInstance()->getRequestArr(), Config::get('hosts.my_host.app'));
        $this->_arguments = $route_info['args'];
        $this->_named_params = $route_info['named'];
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

        $result_match = false;
        $current_route= null;
        foreach ($routes as $route_name => $route) {
            # Examine the current route
            $current_route  = $route;
            $route_frag_arr = Request::getPathInArray($current_route['route']);

            # If the return is an array the route is valid and could be processed
            if (is_array($route_frag_arr)) {
                
                $result_match = $this->_matchRoute($route_frag_arr, $request_arr);
                if (is_array($result_match)) {
                    # Good.. its a match
                    break;
                }
            } else {
                // Ignore this
            }
        }
        
        if(is_array($result_match)){
            # First merge the result
            if(isset($current_route['params'])){
                $named_arr = array_merge($result_match['named'],$current_route['params']);
            }else{
                $named_arr = $result_match['named'];
            }
            
            # Put the app name here
            if(!isset($named_arr['app'])){
                $named_arr['app'] = $app;
            }
            
            $result_match['named'] = $named_arr;
            
            return $result_match;
        }else{
            throw new NoRouteFoundException($app);
        }
    }

    /**
     * This function will take one route and try to match 
     * with the path. If matched it will return an array 
     * containing the result otherwise false
     * 
     * @param type $route_frag_arr
     * @param type $request_arr
     * @return mixed
     */
    private function _matchRoute($route_frag_arr, $request_arr) {
        # One very simple check before any check should start
        # is that count of $request_arr should be equal or greater 
        # than $route_frag_arr;
        
        # Check the reverse
        if(count($request_arr) < count($route_frag_arr)){
            return false;
        }

        # There will be two buckets if the result is true
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
            if ($params_started === TRUE) {
                $params[] = $arg;
            } else if (strpos($current_route_frag , ":") === 0) {
                $current_route_frag_name = str_replace(":", "", $current_route_frag);
                $names_arg_bucket[$current_route_frag_name] = $arg;
            } else if ($current_route_frag === $arg) {
                # Just nothing to do but its a match so far
            } else if ($current_route_frag === '*' && $params_started === FALSE) {
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
     * Function to return app path
     * 
     * @param string $app
     * @return string
     */
    public function getAppPath($app){
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
    
}

?>
