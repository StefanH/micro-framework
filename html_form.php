<?
class HTMLForm extends MicroObject{
	/**
		* $fields contains a hash with field names and field properties:
		* name : a name for the field
		* id [optional] : an html id, default: name
		* type [optional] default: string, supported (extensible): string, password, textarea
		* value [optional] default: null
		* title [optional] default: field name
		* error [optional] default: null
		*/
	function __construct($fields, $url='', $method = 'POST'){		
		$this->fields = $fields;
		$this->url = $url;
		$this->method = $method;
	}
	
	function render_all(){
		$this->render_form_prefix();
		$this->render_fields();
		$this->render_form_suffix();
		$this->render_field_extras();
	}
	
	function render_form_prefix(){echo "\n".html_form_open($this->url, $this->method);}
	function render_fields(){
		echo "\n";
		foreach($this->fields as $props){
			if(!$this->done_by_callback('input', $props))	echo 'dunno';
			echo "\n";
		}
	}
	
	function done_by_callback($part, $props){
		return $this->do_callback("render_{$props['name']}_{$part}", $props) 
			|| $this->do_callback("render_".array_try($props, 'type', 'string')."_{$part}", $props)
			|| $this->do_callback("render_{$part}", $props);
	}
	
	function render_string_input($props){
		$id = array_try($props, 'id', $props['name']);
		echo '<p>';
		echo html_label($id, array_try($props, 'title', util_humanize($props['name'])));
		echo html_input($props['name'], array_try($props, 'value', ''), array('id' => $id));
		if($error = array_try($props, 'error', false))
			echo html_span($error, array('class'=>'error'));		
		echo '</p>';
	}
	function render_password_input($props){
		$id = array_try($props, 'id', $props['name']);
		echo '<p>';
		echo html_label($id, array_try($props, 'title', util_humanize($props['name'])));
		echo html_password($props['name'], array('id' => $id));
		if($error = array_try($props, 'error', false))		
			echo html_span($error, array('class'=>'error'));		
		echo '</p>';
	}
	function render_textarea_input($props){
		$id = array_try($props, 'id', $props['name']);		
		echo '<p class="textarea">';
		echo html_label($id, array_try($props, 'title', util_humanize($props['name'])));
		if($error = array_try($props, 'error', false))
			echo html_span($error, array('class'=>'error'));		
		echo html_textarea($props['name'], array_try($props, 'value', ''), array('id' => $id));
		echo '</p>';
	}
	function render_form_suffix(){echo "</form>\n";}
	
	function render_field_extras(){
		foreach($this->fields as $props)
			$this->done_by_callback('extras', $props);
	}
}