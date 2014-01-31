<?php

/**
 * Description of HelloController
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class HelloController extends Controller{
    //put your code here
    public function world(){
        $args = Router::getInstance()->getArguments();
        
        $this->name = ucwords($args[0]);
        $this->curr_time = date("Y-m-d H:i:s");
        
        $this->a = Crucible::getDb()->query("update user set uname='abc' where id=1");
        //$this->db->showLayer();
    }
    
    public function index(){
        
    }
    
    public function sendmail(){
        $testObj = new TestClass();
        $testObj->doSomeWork();
        /*
        $message = Swift_Message::newInstance();
        $message->setFrom("tej.nri@gmail.com")
                ->setTo("tejaswi.sharma@meritnation.com")
                ->setSubject("Testing")
                ->setBody("Hi There", "text/plain");
        //print_r($message);
        $failures=null;
         * 
         */
        //$this->success = Crucible::getMailer()->send($message, $failures);
        //print_r($failures);
        print_r(Crucible::getDb()->blog->query("SELECT NOW()"));
        $this->forwardRequest('world');
    }
    
}

?>
