<?php
class VIEW{
    protected $_view;
    protected $_template;
    protected $_controller;

    public function __construct($view, $template=NULL) {
        $this->_view = $view;
        $this->_template = $template;
    }

	public function render($data=null)
	{
		
	   $view_file = file_get_contents(VIEW_PATH.'/'.$this->_view.".html");

		if(isset($data)){
		   foreach($data as $key => $value)
		   {
			if(is_array($value)){
				$value = json_encode($value);
			}
			 $view_file = str_replace('{'.$key.'}', $value, $view_file);
		   }
		}
		
		if(!empty($this->_template)){
			$template_file = file_get_contents(TEMPLATE_PATH.'/'.$this->_template.".html");
			$view_file = str_replace('{%contents%}', $view_file, $template_file);
		}
	   return $view_file;
	}

    
}