<?php

require_once 'FRAMEWORK.php';
class APP extends FRAMEWORK {

    /**
     * Property: class
     * Used to instantiate classes
     */
    protected $class = Null;

    public function __construct($request, $origin) {
        parent::__construct($request);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->_isAjax = true;
        }
    }

    public function callAPP() {

        $controller_class = $this->endpoint . "Controller";

        if (class_exists($controller_class)) {
            $this->class = new $controller_class($this->request, $this->endpoint, $this->args);
        }else{
            $this->_response('Not found', 404);
            exit;
        }

        if (!empty($this->args)) {
            if (method_exists($this->class, $this->args[0] . "Action")) {
                $func = array_shift($this->args) . "Action";
                $this->class->$func($this->args, $this->query);
            } else {
                if (method_exists($this->class, 'indexAction') && empty($this->args)) {
                    $this->class->indexAction($this->args, $this->query);
                } else {
                    $this->_response('Not found', 404);
                    exit;
                }
            }
        } else {
            if (method_exists($this->class, 'indexAction')) {
                $this->class->indexAction($this->args, $this->query);
            }else{
                $this->_response('Not found', 404);
                exit;
            }
        }
    }

}
