<?php

/**
 * Description of api
 *
 * @author DESMOND
 */

require_once 'FRAMEWORK.php';
class APP extends FRAMEWORK
{        
    /**
     * Property: class
     * Used to instantiate classes
     */
    protected $class = Null;
    
    /**
     * Property: class
     * Used to store the security class
     */
    protected $_security = null;
    
     
    public function __construct($request,$origin) {
        parent::__construct($request);
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{    
			$this->_isAjax = true;
		}
		
    }
    
    public function callAPP() {	
            spl_autoload_register(function($class_name) {
                $file =  CONTROLLER_PATH . '/' . $class_name . '.php';
                if(file_exists($file)) {
                    require_once $file;
                }
            });
            spl_autoload_register(function($class_name){
                $file =  MODEL_PATH . '/' . $class_name . '.php';
                if(file_exists($file)) {
                    require_once $file;
                }
            });

			$this->_security = new SECURITY($this->endpoint, $_SERVER);

			$controller_class = $this->endpoint."Controller";
            $this->class = new $controller_class($this->request, $this->endpoint, $this->args);
			if(!empty($this->args)){
				if(method_exists($this->class,$this->args[0]."Action")){
					$func = array_shift($this->args)."Action";
					$this->class->$func($this->args, $this->query);
				}else{
					if(method_exists($this->class,'indexAction')){
						
						$this->class->indexAction($this->args, $this->query);
					}else{
						$this->_response('Not found');
					}
				}
			}else{
				if(method_exists($this->class,'indexAction')){
					$this->class->indexAction($this->args, $this->query);
				}
			}
    }
    
 }
