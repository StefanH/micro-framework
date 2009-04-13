<?
class Framework {
	static $db;
	static $log;
	static $controller;
	
	static function boot(){
		session_start();
		self::db_connect();
		self::open_log();
	}
	
	static function dispatch($uri = false){
		if(!$uri) $uri = $_SERVER['REQUEST_URI'];
		$parts = explode('index.php', $uri);
		if(count($parts) > 1) list(,$path) = $parts;
		else $path = '';
		
		list($controller, $method) = self::get_info($path);
		log_info("############ call to $controller/$method");
		$reflect = new ReflectionClass(util_capitalize($controller) . "Controller");
		self::$controller = $reflect->newInstance();
		self::$controller->params = array('controller'=>$controller, 'method'=>$method);		
		try{
		  foreach(self::$controller->callbacks as $callback){
		    if(!in_array($callback, self::$controller->skip_callbacks))
		      call_user_func(array(self::$controller, $callback));
		  }
		  call_user_func(array(self::$controller, $method));
		  if(!self::$controller->rendered){		    
		    self::$controller->render($method);
		  }
		} catch (Exception $e){
		  $result = false;
		  foreach(self::$controller->error_callbacks as $callback){		    
		    if(call_user_func(array(self::$controller, $callback), $e)){
		      $result = true; break;
		    }
		  }
		  if(!$result) throw $e;
		}
	}
	
	static function redirect($path, $params = array()){
	  header("Location: ".urlify($path, $params));	      
	  die;
	}
		
	private static function db_connect(){
		$mysqli = new mysqli('localhost', constant('DB_USER'), constant('DB_PASSWORD'), constant('DB_NAME'));
		if ($mysqli->connect_error) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}
		$mysqli->set_charset("utf8");
		self::$db = $mysqli;
	}

	private static function open_log(){
	  self::$log = fopen('info.log', 'a');
	}
	
	// haalt methode en argumenten uit url:
	// /controller/method?args
	private static function get_info($path){
	  if(empty($path)) $path = LANDING_PAGE;
	  list(,$controller, $method) = explode('/', $path);
    if($method){
	    $tail = explode('?', $method);
	    return array($controller, reset($tail));
	  } else {
	    throw new Exception("no response to " . $path);
	  }
	}
	
}
