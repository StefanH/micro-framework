<?
// core extensions
function array_insert($array, $element, $position){
  if($position > count($array) || $position < 0)
    throw new Exception('insert out of bounds');
  return array_merge(array_slice($array, 0, $position),
    array($element), array_slice($array, $position));
}

function array_try($array, $key, $default=false){
  if(isset($array[$key])) return $array[$key];
  return $default;
}

function array_flatten($array){
  $result = array();
  foreach($array as $key => $value){
    if(is_array($value)) $result = array_merge($result,array_flatten($value));
    else if(is_int($key)) $result[] = $value;
    else $result[$key] = $value;
  }
  return $result;
}

function array_reject($array){
	$result = array();
	$keys = array_flatten(array_slice(func_get_args(), 1));
	foreach($array as $key=>$value){
		if(!in_array($key, $keys)) $result[$key] = $value;
	}
	return $result;
}

function array_accept($array){
	$result = array();
	$keys = array_slice(func_get_args(), 1);
	foreach($array as $key=>$value){
		if(in_array($key, $keys)) $result[$key] = $value;
	}
	return $result;
}

// framework-specific
function util_capitalize($string){
	$elements = explode('_', $string);
	$result = "";
	foreach ($elements as $substring){
		$first = substr($substring, 0, 1);
		$result .= strtoupper($first) . substr($substring, 1, strlen($substring));
	}		
	return ($result);
}

function util_under_score($string){
	return strtolower(implode("_", util_split_caps($string)));
}

function util_humanize($string){
  if(ereg("^[A-Z]", $string))
    return ucfirst(strtolower(implode(" ", util_split_caps($string))));
  return ucfirst(implode(" ", explode("_", $string)));
}

function util_split_caps($string){  
	$matches = array();
	$result = array();
  
	while (ereg("^([A-Z]+)$", $string, $matches)
	  || ereg("^([A-Z]+)[A-Z][a-z]*", $string, $matches)
	  || ereg("^([A-Z][a-z]*)", $string, $matches)){
	  $result[] = $matches[1];
	  $string = substr($string, strlen($matches[1]));
	}	
	return $result;
}

function urlify($path, $params= array()){
  $encoded = array();
  $params = array_flatten($params);
  foreach($params as $k=>$v) $encoded[] = "$k=$v";
  return URL_BASE.'/index.php'.$path.(empty($encoded)?'':'?').implode('&', $encoded);
}

// application level:
function log_info(){
  foreach(func_get_args() as $string){
    if(is_string($string))
      fwrite(Framework::$log, $string."\n");
    else
      fwrite(Framework::$log, print_r($string, true)."\n");    
  }
}

function fatal(){
	echo '<pre>';
  foreach(func_get_args() as $string){
    if(is_string($string)) echo $string."\n";
    else print_r($string);
  }
  echo '</pre>';
  die;
}

function flash($message){
  if(!isset($_SESSION['flash'])) $_SESSION['flash'] = array();
  $_SESSION['flash'][] = $message;
}

function get_flash(){
  $flash = array_try($_SESSION, 'flash');
  unset($_SESSION['flash']);
  return $flash;
}