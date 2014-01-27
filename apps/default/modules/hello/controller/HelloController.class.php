<?php

/**
 * Description of HelloController
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class HelloController extends Controller{
    //put your code here
    public function world(){
        $args = $this->request->getArgs();
        
        $this->name = ucwords($args[0]);
        $this->curr_time = date("Y-m-d H:i:s");
        
        $this->a = $this->db->query("update user set uname='charanjeet' where id=1");
        //$this->db->showLayer();
    }
    
    public function index(){
        
    }
    
    public function sendmail(){
        $message = Swift_Message::newInstance();
        $message->setFrom("tej.nri@gmail.com")
                ->setTo("tejaswi.sharma@meritnation1.com")
                ->setSubject("Testing")
                ->setBody("Hi There", "text/plain");
        //print_r($message);
        $failures=null;
        $this->success = $this->mailer->send($message, $failures);
        print_r($failures);
    }
    
}

?>
