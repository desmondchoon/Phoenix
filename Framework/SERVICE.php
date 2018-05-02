<?php

/**
 * Description of api
 *
 * @author DESMOND
 */

class SERVICE
{        
	protected $obj = '';
	
	public function __construct() {
        
    }
	
	public function _injectObject($obj){
		$this->obj = $obj;
		return $this;
	}
	

}