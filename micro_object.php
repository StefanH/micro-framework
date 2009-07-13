<?
class MicroObject {
	
	# Performs a callback, returns true on success
	function do_callback($method){
		$args = func_get_args();
		$args = array_slice($args, 1);
		if(method_exists($this, $method)){
	    call_user_func_array(array($this, $method), $args);
			return true;
	  }	    
	  return false;		
	}
	
	# Performs a callback, returns the result on success	
	function get_callback_result($method){
		$args = func_get_args();		
		$args = array_slice($args, 1);
		if(method_exists($this, $method)){
	    return call_user_func_array(array($this, $method), $args);
	  }	    
	  return false;
	}
}
?>