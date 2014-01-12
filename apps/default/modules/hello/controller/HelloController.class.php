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
    }
    
    public function index(){
        
    }
    
}

?>
