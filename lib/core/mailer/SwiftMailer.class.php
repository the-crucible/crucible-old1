<?php
/**
 * This file is part of Crucible.
 * (c) 2014 Tejaswi Sharma
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class will act as a wrapper for swiftmailer library
 * and create different transport structure defined in the
 * config
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */

# Include swift mailer
require_once(dirname(__FILE__) . DS . 'swiftmailer' . DS . 'swift_required.php');

class SwiftMailer {
    
    # Different transport type
    const TRANSPORT_SMTP = 'smtp';
    const TRANSPORT_SENDMAIL = 'sendmail';
    const TRANSPORT_PHPMAIL = 'phpmail';
    const TRANSPORT_LOADBALANCED = 'loadbalanced';
    const TRANSPORT_FAILSAFE = 'failsafe';

    /**
     * @var Swift_Mailer
     */
    private $_swiftmailer_instance;
    
    /**
     * __construct
     * 
     * This function will create different transport assigned in
     * the configurations and then create a swiftmailer instance
     * 
     * @param array $params
     */
    public function __construct($params) {
        if(!isset($params['transport']) || !isset($params['params'])){
            throw new InvalidMailerConfigException($params);
        }
        $transport = $this->_getTransport($params);
        $this->_swiftmailer_instance = Swift_Mailer::newInstance($transport);
    }
    
    public function init(){
        
    }
    
    /**
     * send
     * 
     * This function wraps the swftmailer send function
     * 
     * @param Swift_Mime_Message $message
     * @param array $failed_messages
     */
    public function send(Swift_Mime_Message $message,&$failed_messages=null){
        if(is_null($failed_messages)){
            return $this->_swiftmailer_instance->send($message);
        }else{
            return $this->_swiftmailer_instance->send($message,$failed_messages);
        }
        
    }
    
    /**
     * __call
     * 
     * This function will call all the functions in the
     * swiftmailer class through this method
     * 
     * @param type $name name of the swiftmailer function
     * @param type $arguments arguments to the function
     * @return type return of the swift mailer function
     */
    public function __call($name, $arguments) {
        return call_user_func_array(
            array($this->_swiftmailer_instance, $name), 
            $arguments
        );
    }
    
    /**
     * _getTransport
     * 
     * This is a factory method to create transport objects 
     * 
     * @param string $transport name of the transport
     * @param array $params params used to create a mail transport
     */
    private function _getTransport($params){
        $transport = null;
        switch ($params['transport']){
            case self::TRANSPORT_SMTP:
                $transport = $this->_getSmtpTransport($params['params']);
                break;
            case self::TRANSPORT_SENDMAIL:
                $transport = $this->_getSendmailTransport($params['params']);
                break;
            case self::TRANSPORT_PHPMAIL:
                $transport = $this->_getPhpMailTransport($params['params']);
                break;
            case self::TRANSPORT_LOADBALANCED:
                $transport = $this->_getLoadBalancedTransport($params['params']);
                break;
            case self::TRANSPORT_FAILSAFE:
                $transport = $this->_getFailSafeTransport($params['params']);
                break;
            default:
                throw new InvalidMailerConfigException($params['params']);
        }
        return $transport;
    }
    
    /**
     * _getSmtpTransport
     * 
     * This function will return a new SMTP transport object
     * 
     * @param type $params;
     * @return Swift_Transport Mail transport object
     */
    private function _getSmtpTransport($params){
        if(!isset($params['host']) || !isset($params['port'])){
            throw new InvalidMailerConfigException($params);
        }
        
        $transport = Swift_SmtpTransport::newInstance($params['host'], $params['port']);
        
        if(isset($params['user']) && isset($params['pass'])){
            $transport->setUsername($params['user'])->setPassword($params['pass']);
        }
        
        if(isset($params['encryption'])){
            $transport->setEncryption($params['encryption']);
        }
        
        return $transport;
    }
    
    /**
     * _getSendmailTransport
     * 
     * This function will return the Sendmail transport object
     * 
     * @param type $params
     * @return Swift_Transport Mail transport object
     */
    private function _getSendmailTransport($params){
        if(!isset($params['path'])){
            throw new InvalidMailerConfigException($params);
        }
        
        $transport = Swift_SendmailTransport::newInstance($params['path']);
        
        return $transport;
    }
    
    
    /**
     * _getPhpMailTransport
     * 
     * This function will return the Phpmailer transport object
     * 
     * @param type $params
     * @return Swift_Transport Mail transport object
     */
    private function _getPhpMailTransport($params){
        return Swift_MailTransport::newInstance();
    }
    
    /**
     * _getLoadBalancedTransport
     * 
     * This function will return the Loadalanced transport object
     * 
     * @param type $params
     * @return Swift_Transport Mail transport object
     */
    private function _getLoadBalancedTransport($params){
        $transports = array();
        foreach($params as $transport_arr){
            $transports[] = $this->_getTransport($transport_arr);
        }
        return Swift_LoadBalancedTransport::newInstance($transports);
    }
    
    /**
     * _getFailSafeTransport
     * 
     * This function will return the Failsafe transport object
     * 
     * @param type $params
     * @return Swift_Transport Mail transport object
     */
    private function _getFailSafeTransport($params){
        $transports = array();
        foreach($params as $transport_arr){
            $transports[] = $this->_getTransport($transport_arr);
        }
        return Swift_FailoverTransport::newInstance($transports);
    }
}

?>
