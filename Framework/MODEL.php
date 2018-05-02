<?php

/**
 * Description of api
 *
 * @author DESMOND
 */

class MODEL
{        
    /**
     * Property: config array
     * Stores config data of the database
     */
    protected $_config = Null;

    /**
     * Property: project database
     * Stores the database object connection
     */
    protected $_conn = '';

    public function __construct($conn=NULL) {
        $this->_config = parse_ini_file("Configs/databases.ini",true);
        if(!empty($conn)){
            $this->_conn = $conn;
        }
    }

    public function _testDb(){
        echo 'DB OK';
    }

    public function _getDb($class, $db='default'){
		$class=$class.'Model';
        if(isset($this->_config[$db])){
            $servername = $this->_config[$db]['servername'];
            $username = $this->_config[$db]['username'];
            $password = $this->_config[$db]['password'];
            $database = $this->_config[$db]['database'];
            try {    
                $this->_conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);    
                $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      
            }catch(PDOException $e)    {    
                $this->_response('Database Connect Fail');
            }
        }else{
            throw new Exception('Database does not exist');
        }
        $model = new $class($this->_conn);
        return $model;
    }

    public function _nonQuery($query, $mode=null){
        $stmt = $this->_conn->prepare($query['statement']);
        if(!empty($query['params'])){
	        foreach($query['params'] as $k=>$v){
	            if(!isset($v['type'])){ $v['type'] = PDO::PARAM_STR; }
	            $stmt->bindParam($k, $v['var'], $v['type']);
	        }
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
		return $this->_conn->lastInsertId();
    }
	
	public function _query($query){
        $stmt = $this->_conn->prepare($query['statement']);
        if(!empty($query['params'])){
	        foreach($query['params'] as $k=>$v){
	            if(!isset($v['type'])){ $v['type'] = PDO::PARAM_STR; }
	            $stmt->bindParam($k, $v['var'], $v['type']);
	        }
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetch();
        
    }
	
	public function _queryAll($query){
        $stmt = $this->_conn->prepare($query['statement']);
        if(!empty($query['params'])){
	        foreach($query['params'] as $k=>$v){
	            if(!isset($v['type'])){ $v['type'] = PDO::PARAM_STR; }
	            $stmt->bindParam($k, $v['var'], $v['type']);
	        }
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();


        return $stmt->fetchAll();

    }
    
 }