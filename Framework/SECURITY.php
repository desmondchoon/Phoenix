<?php
class SECURITY {
    /* Security config */

    public $_config = null;
    private $_db = null;
    public $auth = null;
    
    public function __construct($package = NULL, $server = NULL) {
        $this->_config = parse_ini_file("Configs/security.ini", true);
        if ($this->_config !== null && !empty($package) && !empty($server)) {
            $this->secure($package, $server);
        }
    }

    public function returnConfig() {
        return $this->_config;
    }

    protected function secure($package, $server) {
        if (isset($this->_config['secure'])) {
            $securedController = $this->_config['secure']['controller'];
            if (in_array($package, $securedController)) {
                $this->authenticate($server);
            }
        }
    }

    protected function authenticate($server) {
        $tokenField = $this->_config['tokenization']['server_name'];
        $this->startUserDbInstance();

        if(!$this->checkAuth()){
            if (!empty($server['HTTP_X_REQUESTED_WITH']) && strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(array('authentication'=>false));
            }else{
                $this->redirectLogin();
            }
            exit;
        }
    }
    
    private function authUser($id){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['id'] = $id;
    }
    
    public function checkAuth(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id();
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $this->_config['session']['timeout_period'])) {
            // last request was more than configured session timeout
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
        if(isset($_SESSION['id']) && !empty($_SESSION['id'])){
            return true;
        }else{
            return false;
        }
    }
    
    public function logout(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['id']);
        session_destroy();
    }

    protected function check($token, $ajax = null) {
        $stmt = $this->_db->prepare("SELECT * FROM " . $this->_config['token_db']['table'] . " WHERE " . $this->_config['token_db']['token_field'] . "='" . $token . "'");
        $stmt->execute();
        if (empty($stmt->fetch())) {
            $this->redirectLogin();
            exit;
        }
    }

    private function startUserDbInstance() {
        $db = new MODEL();
        $this->_db = $db->_getDb('security',$this->_config['db']['schema']);
    }

    private function redirectLogin() {
        include 'login.php';
    }
    
    public function checkUserDuplicate($id) {
        if ($this->_db == null) {
            $this->startUserDbInstance();
        }

        $stmt = $this->_db->prepare("SELECT * FROM " . $this->_config['db']['table'] . " WHERE " . $this->_config['db']['id_field'] . "='" . $id . "'");
        $stmt->execute();
        if (empty($stmt->fetch())) {
            return false;
        } else {
            return true;
        }
    }

    public function addUser($id, $password, $user_role = null) {
        if ($this->_db == null) {
            $this->startUserDbInstance();
        }
        if (empty($user_role)) {
            $user_role = $this->_config['user_role']['default'];
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->_db->prepare("INSERT INTO " . $this->_config['db']['table'] .
                " (" . $this->_config['db']['id_field'] . "," . $this->_config['db']['password_field'] . "," . $this->_config['db']['user_role_field'] . ") " .
                " VALUES ('" . $id . "','" . $password . "','" . $user_role . "') ");
        $stmt->execute();
    }

    public function authenticateUser($id, $password) {
        if ($this->_db == null) {
            $this->startUserDbInstance();
        }
        
        $result = $this->_db->getUser($this->_config, $id);
        
        if (empty($result)) {
            return false;
        } else {
            $this->auth = $result;
            if (password_verify($password, $result[$this->_config['db']['password_field']])) {
                $this->authUser($id);
                return true;
            } else {
                return false;
            }
        }
    }

    public function generateToken($id = null) {
        if (empty($id)) {
            return bin2hex(random_bytes(16));
        } else {
            $token = bin2hex(random_bytes(16));
            if ($this->_db == null) {
                $this->startUserDbInstance();
            }

            $stmt = $this->_db->prepare("INSERT INTO  " . $this->_config['token_db']['table'] . " (" . $this->_config['token_db']['token_field'] . ",". $this->_config['token_db']['id_field'] .") VALUES ('" . $token . "','". $id ."')");
            $stmt->execute();
            
            return $token;
        }
    }

}

?>