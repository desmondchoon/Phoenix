<?php
ob_start();
class SECURITY
{
	/*Security config*/
	protected $_config = null;
	
	private $_db = null;
	
	public function __construct($package=NULL, $server=NULL) {
        $this->_config = parse_ini_file("Configs/security.ini",true);
        if($this->_config !== null && !empty($package) && !empty($server)){
			$this->secure($package, $server);
		}	
    }
	
	public function returnConfig(){
		return $this->_config;
	}
	
	protected function secure($package, $server){
		if(isset($this->_config['secure'])){
			$securedController = $this->_config['secure']['controller'];
			
			if (in_array($package, $securedController))
			{
				//print_r($server);
				$this->authenticate($server);
			}
		}	
	}
	
	protected function authenticate($server){
		$tokenField = $this->_config['tokenization']['server_name'];
		$this->startUserDbInstance();
		if(!empty($server['HTTP_X_REQUESTED_WITH']) && strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{    
			if(isset($server[$tokenField])){
				$this->check($_SERVER[$tokenField], true);
			}else{
				die('Access denied');
			}
		}else{
			if(isset($server[$tokenField])){
				$this->check($_SERVER[$tokenField]);
			}else{
				if(isset($server['security-action']) && $server['security-action'] == 'redirect'){
					$this->redirectLogin();
					exit;	
				}else{
					include_once 'security.php';
					exit;
				}
			}
		}
		
	}
	
	protected function check($token, $ajax=null){
		$stmt = $this->_db->prepare("SELECT * FROM ".$this->_config['db']['table']." WHERE ".$this->_config['db']['token_field']."='".$token."'");
		$stmt->execute();
		if(empty($stmt->fetch())){
			$this->redirectLogin();
			exit;
		}	
	}
	
	private function startUserDbInstance(){
		$db = new MODEL();
		$this->_db = $db->createSingleInstance($this->_config['db']['schema']);
	}
	
	private function redirectLogin(){
		include 'login.php';
	}
	
	public function checkUserDuplicate($id){
		if($this->_db == null){
			$this->startUserDbInstance();
		}
		
		$stmt = $this->_db->prepare("SELECT * FROM ".$this->_config['db']['table']." WHERE ".$this->_config['db']['id_field']."='".$id."'");
		$stmt->execute();
		if(empty($stmt->fetch())){
			return false;
		}else{
			return true;
		}
	}
	
	public function addUser($id, $password, $user_role=null){
		if($this->_db == null){
			$this->startUserDbInstance();
		}
		if(empty($user_role)){
			$user_role = $this->_config['user_role']['default'];
		}
		$password = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $this->_db->prepare("INSERT INTO ".$this->_config['db']['table'].
									" (".$this->_config['db']['id_field'].",".$this->_config['db']['password_field'].",".$this->_config['db']['user_role_field'].") ".
									" VALUES ('".$id."','".$password."','".$user_role."') ");
		$stmt->execute();
	}
	
	public function authenticateUser($id, $password){
		if($this->_db == null){
			$this->startUserDbInstance();
		}
		
		$stmt = $this->_db->prepare("SELECT * FROM ".$this->_config['db']['table']." WHERE ".$this->_config['db']['id_field']."='".$id."'");
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		$result = $stmt->fetch();
		if(empty($result)){
			return false;
		}else{
			if(password_verify($password, $result[$this->_config['db']['password_field']])){
				return $this->generateToken();
			}else{
				return false;
			}
		}
	}
	
	public function generateToken(){
		return bin2hex(random_bytes(16));
	}
}
ob_end_flush();
?>