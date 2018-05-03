<?php
class CONTROLLER extends FRAMEWORK{
    protected $args;
    protected $method;
    protected $_controller;

    public function _getController($controller){
        return new $controller;
    }
    
    protected function _view($view, $template=null){
    	if(!empty($template)){
        	return new VIEW($view, $template);
    	}else{
    		return new VIEW($view);
    	}
    }
    
    protected function _model($model, $db=null){
    	$modelObj = new MODEL();
    	if(!empty($db)){
        	return $modelObj->_getDb($model, $db);
    	}else{
    		return $modelObj->_getDb($model);
    	}
    }
    
    protected function _service($service){
		$servicePath = $service."Service";
		
		$serviceExplode = explode('/', $service);
		end($serviceExplode); 
		$key = key($serviceExplode); 
		$service = $serviceExplode[$key]."Service";
		
    	require_once SERVICE_PATH.'/'.$servicePath.'.php';
    	return new $service();
    }
	
	protected function _controller($controller){
		$contollerClass = $controller."Controller";
		require_once CONTROLLER_PATH . '/' . $contollerClass . '.php';
		return new $contollerClass($this->request, $controller, $this->args);
	}
	
	/*Custom add on function that should not be in framework, think of how to improve*/
	protected function authenticate($token=NULL){
		if(empty($token)){
			$token = $this->token;
		}
		$config = parse_ini_file("Configs/databases.ini",true);
		$db = 'default';
		if(isset($config[$db])){
            $servername = $config[$db]['servername'];
            $username = $config[$db]['username'];
            $password = $config[$db]['password'];
            $database = $config[$db]['database'];
            try {    
                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);    
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      
				
				$stmt = $conn->prepare('SELECT * FROM user_login WHERE login_token=:token');
				$stmt->bindParam(':token', $token, PDO::PARAM_STR);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$stmt->execute();
				$data = $stmt->fetch();
				
				if(empty($data)){
					$data = [];
					$data['auth_fail'] = 1;
					$data['auth_msg'] = 'Authentication Failed';
					$this->_response(data);
					die();
				}else{
					return $data['login_user_id'];
				}
            }catch(PDOException $e)    {    
                $this->_response('Database Connect Fail');
            }
        }
	}

}