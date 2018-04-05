<?php
/**
 * Description of center
 *
 * @author DESMOND
 */

class test extends CONTROLLER
{
    public function indexAction() {  

		if($this->method == 'GET'){
			
		$this->_response('method is get!');
		}else{
		$this->_response('Invalid Method', 405);
		}
		 
    }
	 
	 public function testAction(){
		//$test = $this->_injectService('testservice');
		//$test->testservicefunction();
		print_r($this->args);
		$data = array();
		$data2 = array();
		$data['name'][0] = 'test1';
		$data['name'][1] = 'test2';
		//$data['name'] = 'David';
		
		$this->_response($this->_render('test', $data));
		
	 }

	 public function asdAction(){
		 echo 'asd';
		 $this->_model->_getDb('testquery')->querythis();
	 }
	 
	 
}