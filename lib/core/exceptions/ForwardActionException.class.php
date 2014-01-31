<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This action will be thrown when the request will be 
 * diverted to some other action. This exception will
 * be thrown to make the script not execute any code 
 * past that point.
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class ForwardActionException extends Exception{
    private $_action;
    private $_controller;
    private $_app;
    
    /**
     * This constructor sets the forwarded action,
     * controller and app
     * 
     * @param type $action forwarded action
     * @param type $controller forwarded controller
     * @param type $app forwarded app
     */
    public function __construct($action,$controller=null,$app=null) {
        $this->_action = $action;
        $this->_controller = $controller;
        $this->_app = $app;
        
        $message  = "action=$action ";
        $message .= ($controller)? "controller=$controller":"";
        $message .= ($app)? "app=$app":"";
        parent::__construct("code flow is redirected to $message");
    }
    
    /**
     * This function returns the forwarded app
     * 
     * @return string forwarded app
     */
    public function getForwardedApp(){
        return $this->_app;
    }
    
    /**
     * This function returns the forwarded controller
     * 
     * @return string forwarded controller
     */
    public function getForwardedController(){
        return $this->_controller;
    }
    
    /**
     * This function returns the forwarded action
     * 
     * @return string forwarded action
     */
    public function getForwardedAction(){
        return $this->_action;
    }
}

?>
