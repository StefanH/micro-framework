<?
/*
= Controllers

Controllers manage the sites functionality. They can access the database and/or render a view. A request is mapped to a method on a controller based on the path contained in the request url. When, for example, a request is made to /static/index, the framework looks for StaticController in /controllers/static_controller.php, instantiates it, and calls the index action on it.

This StaticController can be as simple as this:
class StaticController extends Controller {
  function index(){
    $this->message = "Hello";
  }
}

this StaticController allows the application to respond /static/index. It also renders the view in /views/static/index.php using the layout in /views/layout/layout.php. The layout and index templates are simple php files. They are included in the context of the current controller instance. This means that instance variables defined on that controller instance, such as message, can be used in the php code of both /views/static/index.php and /views/layout/layout.php. /views/static/index.php might look like this:

<h1>Index</h1>
<?= $this->message ?>

== Callbacks

== Exceptions
*/

class Controller {
	function render_nothing(){
		$this->rendered = true;
	}
	
	function render_nolayout($view_name){
		$this->rendered = true;
		$view = $this->find_view($view_name);
		$this->render_file($view, false);
	}
	
	function render($view_name, $layout=false){
		$this->rendered = true;
		$view = $this->find_view($view_name);
		$this->render_file($view, true, $layout);		
	}
	
  private function find_view($view_name){
  global $plugins;
		if (strpos($view_name,'/'))
    	$view = 'views/'.$view_name.'.php';
		else
    	$view = 'views/'.$this->url_name()."/$view_name.php";
    if(is_file($view)) return $view;
    foreach($plugins as $plugin){
      $view = "plugins/$plugin/$view_name";
      if(is_file($view)) return $view;
    }
    throw new Exception("View not found");
	}
	
	private function render_file($view, $layout=true, $layoutfile = false){
    if($layout){
			$layoutfile = $layoutfile ? $layoutfile : 'layout';
			ob_start();
			include($view);
			$this->main_content = ob_get_clean();
			include "views/layout/$layoutfile.php";
		} 
    else include $view;
	}
		
	//layout stuff
	private $main_content = '';	
	private $partial_content = array();
	private $current_content = '';
	
	function content_for($var){
		$this->current_content = $var;
		ob_start();
	}
	
	function end_content(){
		$this->partial_content[$this->current_content] = ob_get_clean();
		$this->current_content = '';
	}
	
	function render_content($var = null){
		if($var) echo $this->partial_content[$var];
		else echo $this->main_content;
	}
	
	function redirect($method, $params = array()){
	  Framework::redirect("/".$this->url_name()."/$method", $params);
	}
	
	function url_name(){	  
	  return util_under_score(substr(get_class($this), 0, -10));
	}
	
	public $callbacks = array();
	public $error_callbacks = array();
	public $skip_callbacks = array();
		
	public $messages = array();
	public $errors = array();
	public $rendered = false;
}