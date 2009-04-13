<?
function html_dl($items, $attributes=array()){
	$result = array();
	foreach($items as $key => $value)
		$result[] = "<dt>".$key.'</dt><dd>'.$value.'&nbsp;</dd>';
	return "<dl>".implode($result, "\n")."</dl>";
}

function html_input($name, $value, $attributes=array()){
	$attributes['name'] = $name;
	$attributes['value'] = $value;
  return '<input type="text" '.xml_attributify($attributes).'>';
}

function html_password($name, $attributes=array()){
	$attributes['name'] = $name;
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
	$attributes['name'] = $name;
  return '<select '.xml_attributify($attributes).'>'.implode("\n", $result)."</select>";
}

function html_span($content, $attributes=array()){
	return '<span '.xml_attributify($attributes).'>'.$content.'</span> ';
}

function html_form_open($url, $method='POST', $attributes=array()){
	$attributes['action'] = $url;
	$attributes['method'] = $method;
	return '<form '.xml_attributify($attributes).'>';
}

function html_js_link($file, $attributes=array()){
	if(substr($file, -3) != '.js') $file .= '.js';
	$attributes['src'] = URL_BASE."/public/$file";
	$attributes['type'] = 'text/javascript';
	$attributes['charset'] = 'utf-8';
	return '<script '.xml_attributify($attributes).'></script>';
}

function html_css_link($file, $attributes=array()){
	if(substr($file, -4) != '.css') $file .= '.css';
	$attributes['rel'] = "stylesheet"; 
	$attributes['href'] = URL_BASE."/public/$file";
	$attributes['type'] = "text/css";
	return '<link '.xml_attributify($attributes).'/>';
}

function linkify($text, $path, $params = array(), $attributes = array()){
  return "<a href=\"".urlify($path, $params)."\" ".attributify($attributes).">$text</a>";
}

function iconify($icon, $attributes=array()){
  $path = URL_BASE."/public/icons/$icon.png";
	$attributes['src'] = $path;
  return '<img '.xml_attributify($attributes).'/>';
}

function xml_attributify($attributes){
	$result = array();
  foreach($attributes as $key=>$value){
    $result[] = "$key=\"$value\"";
  }
  return implode(" ", $result);
}