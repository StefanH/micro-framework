<?
class HTMLForm extends MicroObject{
	
	function __construct($object, $fields, $url='', $method = 'POST'){
		$this->object = $object;
		$this->fields = $fields;
		$this->url = $url;
		$this->method = $method;
		$this->db_fields = $object->fields();
		
		$this->render_prefix();
		$this->render_fields();
		$this->render_suffix();
	}
	
	function render_prefix(){echo html_form_open($this->url, $this->method);}
	function render_fields(){
		foreach($this->fields as $fieldname => $field){
			if(!$this->do_callback("render_$fieldname") 
			&& !$this->do_callback("render_field", $field)){
				echo html_input($fieldname, $object->$fieldname());
			}
		}
	}
	function render_suffix(){echo '</form>';}
}