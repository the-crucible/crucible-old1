<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class handles the actual code written by the user. It 
 * initializes few core modules, execute pre and post part of
 * the controller and facilitate creation of the whole body
 * from the view class.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Controller {
    /**
     * @var Router
     */
    protected $router;
    
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var View
     */
    protected $view;
    
    /**
     * Its an array to record the result of the array
     * 
     * @var array
     */
    private $_result_arr = array();
    
    /**
     * __construct
     * 
     * Contructor function
     * 
     * @param Crucible $crucible
     */
    public function __construct() {
        # Make sure the autoload of the lib dirs is working
        Crucible::getInstance()->getComponent('autoload');
        # Make sure the session is started
        Crucible::getInstance()->getComponent('session');
        # Make sure the mailer component is working
        Crucible::getInstance()->getComponent('mailer');
        
        $this->router  = Router::getInstance();
        $this->request = Request::getInstance();
        $this->response= Response::getInstance();
        $this->view    = View::getInstance();
    }
    
    /**
     * execute
     * 
     * This function actually executes the action provided
     * along with the controller.
     * 
     * @param type $action
     * @throws NoActionFoundException if the action is not found in the controller
     */
    public function execute($action){
        if(method_exists(get_class($this),$action)){
            # Execute the pre function
            if(method_exists(get_class($this),'pre')){
                $this->pre();
            }
            
            # Execute the action
            $this->$action();
            
            # Execute the post function
            if(method_exists(get_class($this),'post')){
                $this->post();
            }
            
            # Start executing view part;
            $this->_initViewObject();
            
            # Set body in the response to be drained off
            $this->response->setBody($this->view->getBody());
        }else{
            # Action not found so throwing this exception
            throw new NoActionFoundException($action);
        }
    }
    
    /**
     * _initViewObject
     * 
     * This function get the view config and 
     * initializes the view object
     */
    private function _initViewObject(){
        # Get the view config form the views.php config file
        $view_config = $this->_getViewConfig();
        # Get the different values in the views object from the config
        $this->_setViewObject($view_config);
    }
    
    /**
     * _getViewConfig
     * 
     * This function merges the default part in the view config
     * and merges with the current action part and return it.
     *  
     * @return array view config
     */
    private function _getViewConfig(){
        $module_name    = $this->router->getAction();
        $view_config    = Config::get('views');
        # Get default part
        $common_config  = isset($view_config['default'])?$view_config['default']:array();
        # Get the action part
        $current_view_config = isset($view_config[$module_name])?$view_config[$module_name]:array();
        # Merge the two and return it
        return array_merge($common_config,$current_view_config);
    } 
    
    /**
     * _setViewObject
     * 
     * This function will be used to set differet parameters of the
     * view object from the view config and output produced from
     * the action function.
     * 
     * @param array $view_config all merged view config 
     * @throws LayoutNotFoundException If layout file is not found
     * @throws NoLayoutDefinedException if no layout defined
     * @throws ViewNotFoundException if no view file found
     */
    private function _setViewObject($view_config){
        # Set layout first
        if(!$this->view->getLayout()){
            if(isset($view_config['layout']) && $view_config['layout']){
                if(!$this->view->setLayout($view_config['layout'])){
                    throw new LayoutNotFoundException($view_config['layout']);
                }
            }else{
                throw new NoLayoutDefinedException();
            }
        }
        
        # Set View name
        if(!$this->view->getView()){
            if(!$this->view->setView($this->router->getAction())){
                throw new ViewNotFoundException($this->router->getAction());
            }
        }
        
        # Set title
        if(!$this->view->getTitle()){
            $this->view->setTitle($view_config['title']);
        }
        
        # Set js
        $this->view->addJs($view_config['js']);
        
        # Set css
        $this->view->addCss($view_config['css']);
        
        # Set meta
        $this->view->addMeta($view_config['meta']);
        
        # Set the output produced by the action into the view 
        $this->view->setInputArr($this->_result_arr);
        
    }
    
    /**
     * __set
     * 
     * This function will be used to get values from the
     * action function to be used to produce output
     * 
     * @param string $name name of the variable
     * @param mixed $value value of the variable
     */
    public function __set($name , $value){
        $this->_result_arr[$name] = $value;
    }
    
    protected function forwardRequest($action,$controller=null,$app=null){
        if(is_null($controller) && is_null($app)){
            throw new ForwardActionException($action);
        }else if(!is_null($controller) && is_null($app)){
            throw new ForwardActionException($action,$controller);
        }else if(!is_null($controller) && !is_null($app)){
            throw new ForwardActionException($action,$controller,$app);
        }else{
            throw new Exception("Not allowed request to forward");
        }
    }
    
    /**
     * forward404
     * 
     * This function could be called to set the 404 error
     * page as response
     */
    protected function forward404(){
        
    }
    
    /**
     * forwardError
     * 
     * This function could be called to set the 500 error
     * page as response
     */
    protected function forwardError(){
        
    }
}

?>
