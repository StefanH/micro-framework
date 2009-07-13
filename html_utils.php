<?
function html_dl($items, $attributes=array()){
	$result = array();
	foreach($items as $key => $value)
		$result[] = "<dt>".$key.'</dt><dd>'.$value.'&nbsp;</dd>';
	return "<dl>".implode($result, "\n")."</dl>";
}

function html_input($name, $value, $attributes=array()){
	$attributes = array_merge(
		array('name' => $name, 'value' => $value), $attributes);

  return '<input type="text" '.xml_attributify($attributes).'>';
}

function html_password($name, $attributes=array()){
	$attributes = array_merge(array('name' => $name), $attributes);
  return '<input type="password" '.xml_attributify($attributes).'>';
}

function html_submit($text=false, $attributes=array()){
	if($text) $attributes['value'] = $text;
	return '<input type="submit" '.xml_attributify($attributes).'>';
}

function html_select($name, $options, $selected=false, $attributes=array()){
  foreach($options as $key=>$value){
    $option_attributes = array('value'=>$value);
    if($selected && $selected == $key) $option_attributes['selected'] = 'selected';
    $result[] = '<option '.xml_attributify($option_attributes).">$value</option>";
  }
	$attributes = array_merge(array('name' => $name), $attributes);
  return '<select '.xml_attributify($attributes).'>'.implode("\n", $result)."</select>";
}

function html_textarea($name, $value, $attributes = array()){
	$attributes = array_merge(array(
		'rows' => 10, 'cols'=>60, 'name'=> $name), $attributes);
		
	return '<textarea '.xml_attributify($attributes).'>'.$value.'</textarea>';
}

function html_span($content, $attributes=array()){
	return '<span '.xml_attributify($attributes).'>'.$content.'</span> ';
}

function html_form_open($url, $method='POST', $attributes=array()){
	$attributes = array_merge(
		array('action' => $url, 'method' => $method), $attributes);
	return '<form '.xml_attributify($attributes).'>';
}
function html_label($for, $text, $attributes = array()){
	$attributes = array_merge(
		array('for' => $for), $attributes);
	return '<label '.xml_attributify($attributes).'>'.$text.'</label> ';
}

function html_js_link($file, $attributes=array()){
	if(substr($file, -3) != '.js') $file .= '.js';
	$attributes = array_merge(
		array('src' => URL_BASE."/public/$file", 
		'type' 			=> 'text/javascript',
		'charset' 	=> 'utf-8'), $attributes);
	
	return '<script '.xml_attributify($attributes).'></script>';
}

function html_css_link($file, $attributes=array()){
	if(substr($file, -4) != '.css') $file .= '.css';
	$attributes = array_merge(array(
		'rel' => "stylesheet",
		'href' => URL_BASE."/public/$file",
		'type' => "text/css"), $attributes);
	
	return '<link '.xml_attributify($attributes).'/>';
}

function linkify($text, $path, $params = array(), $attributes = array()){
  return "<a href=\"".urlify($path, $params)."\" ".xml_attributify($attributes).">$text</a>";
}

function iconify($icon, $attributes=array()){
  $path = URL_BASE."/public/icons/$icon.png";
	$attributes = array_merge(array('src' => $path), $attributes);
  return '<img '.xml_attributify($attributes).'/>';
}

function xml_attributify($attributes){
	$result = array();
  foreach($attributes as $key=>$value){
    $result[] = "$key=\"$value\"";
  }
  return implode(" ", $result);
}