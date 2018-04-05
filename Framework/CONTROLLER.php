<?php
class CONTROLLER extends FRAMEWORK{
    protected $args;
    protected $method;
    protected $_controller;

    public function _getController($controller){
        return new $controller;
    }
    
    protected function view($view, $template=null){
    	if(!empty($template)){
        	return new VIEW($view, $template);
    	}else{
    		return new VIEW($view);
    	}
    }
    
    protected function model($model, $db=null){
    	$modelObj = new MODEL();
    	if(!empty($db)){
        	return $modelObj->_getDb($model, $db);
    	}else{
    		return $modelObj->_getDb($model);
    	}
    }
    
    protected function service($service){
    	require_once SERVICE_PATH.'/'.$service.'.php';
    	return new $service();
    }
}