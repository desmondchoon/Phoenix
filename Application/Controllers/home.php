<?php
class home extends CONTROLLER
{
    public function indexAction() {  

		if($this->method == 'GET'){
			echo "This is home page for the Application. Phoenix's routing is working fine. </br></br>";
			
			echo "Now testing Phoenix View Render...";
			echo $this->view('test', 'template')->render(array('name'=>'Phoenix'));
			
			echo "</br></br>Now testing Services...";
			echo $this->service('testservice')->testservicefunction();
			
			echo "</br></br>Now initiating Model...";
			print_r($this->model('testquery')->querythis());
		}else{
			$this->_response('Invalid Method', 405);
		}
		 
    }
    
}