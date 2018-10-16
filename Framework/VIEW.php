<?php

class VIEW {

    protected $_view;
    protected $_template;
    protected $_controller;

    public function __construct($controller, $view, $template = NULL, $templateData = NULL) {
        $this->_controller = $controller;
        $this->_view = $view;
        $this->_template = $template;
        $this->_templateData = $templateData;
    }

    public function render($data = null) {

        $view_file = file_get_contents(VIEW_PATH . '/' . $this->_view . ".html");
        
        if (!empty($this->_template)) {
            $template_file = file_get_contents(TEMPLATE_PATH . '/' . $this->_template . ".html");
            $view_file = str_replace('{%contents%}', $view_file, $template_file);  
        }
        
        if (isset($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $view_file = str_replace('{%' . $key . '%}', $value, $view_file);
            }
        }
        
        $view_file = $this->mapControllers($view_file);
        $view_file = $this->mapRedirects($view_file);
        $view_file = $this->cleanUpTags($view_file);
        if(PHOENIX_MODE == 'prod'){
            $view_file =str_replace(array("\r", "\n"), '', $view_file);        
        }
        return $view_file;
    }

    private function mapControllers($template) {
        if (preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $template, $arr)) {
            foreach ($arr[1] as $k => $v) {
                $controllerSplit = explode('/', $v);
                $controller = $controllerSplit[0];
                $func = $controllerSplit[1] . 'Action';

                $controllerReturns = $this->_controller->_controller($controllerSplit[0]);
                $template = str_replace('{{' . $v . '}}', $controllerReturns->$func(), $template);
            }
        }
        return $template;
    }
    
    private function cleanUpTags($template){
        if (preg_match_all("~\{\%\s*(.*?)\s*\%\}~", $template, $arr)) {
            foreach ($arr[0] as $k => $v) {
                $template = str_replace($v, '""', $template);
            }
        }
        return $template;
    }

    private function mapRedirects($template) {
        if (preg_match_all("~\{\_\s*(.*?)\s*\_\}~", $template, $arr)) {
            foreach ($arr[1] as $k => $v) {
                $completeLink = ROOT_PATH . $v;
                $template = str_replace('{_' . $v . '_}', $completeLink, $template);
            }
        }
        return $template;
    }

}
