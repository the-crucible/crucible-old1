<?php

/**
 * Description of controller
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class Controller {
    /**
     * @var Crucible
     */
    private $_crucible;
    
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
     * @var PhpSession
     */
    protected $session;
    
    /**
     * Its an array to record the result of the array
     * 
     * @var array
     */
    private $_result_arr = array();
    
    /**
     * Contructor function
     * 
     * @param Crucible $crucible
     */
    public function __construct(Crucible $crucible) {
        $this->_crucible = $crucible;
        $this->request = Request::getInstance();
        $this->response= Response::getInstance();
        $this->session = $this->_crucible->getComponent('session');
        $this->view    = View::getInstance();
    }
    
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
            
            $this->response->setBody($this->view->getBody());
        }else{
            throw new NoActionFoundException($action);
        }
    }
    
    private function _initViewObject(){
        # Get the view config form the views.php config file
        $view_config = $this->_getViewConfig();
        # Get the different values in the views object from the config
        $this->_setViewObject($view_config);
        
    }
    
    private function _getViewConfig(){
        $module_name    = $this->request->getAction();
        $view_config    = Config::get('views');
        $common_config  = isset($view_config['default'])?$view_config['default']:array();
        $current_view_config = isset($view_config[$module_name])?$view_config[$module_name]:array();
        return array_merge($common_config,$current_view_config);
    } 
    
    
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
            if(!$this->view->setView($this->request->getAction())){
                throw new ViewNotFoundException($this->request->getAction());
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
        
        $this->view->setInputArr($this->_result_arr);
        
    }
    
    public function __set($name , $value){
        $this->_result_arr[$name] = $value;
    }
}

?>
