<?

//testcontroller (also stubs some methods)
class TestController extends Controller {
	public $called = '';
	public $rendered_views = array();
	public $callbacks = array('testcallback1', 'testcallback2');
	public $performed_callbacks = array();
	public $error_callbacks = array('testerror');
	public $performed_error_callbacks = array();
	
	function index(){
		$this->called = 'index';
		$this->render_nothing();
	}
	
	function show(){
		$this->called = 'show';
	}
	
	function callbacktest(){
		$this->called = 'callbacktest';
	}
	
	function testcallback1(){
		$this->performed_callbacks[] = 'testcallback1';
		if($this->params['method'] == 'callbacktest' )
			throw new Exception('testerror');
	}
	
	function testcallback2(){
		$this->performed_callbacks[] = 'testcallback2';		
	}
	
	function testerror($e){
		if($e->getMessage() == 'testerror'){
			$this->performed_error_callbacks[] = 'testerror';
			return true;
		}
		return false;
	}
	
	// ###### stubs
	
	function render_file($file){
		$this->rendered_views[] = $file;
	}
	
	function render($view_name){
		try {
			parent::render($view_name, true);
		} catch(Exception $e){
			if($e->getMessage() != "View not found") throw $e;
		}
	}
}


log_info('####### test 1: dispatching');
Framework::dispatch('index.php/test/index');
assert('Framework::$controller instanceof TestController');
assert('Framework::$controller->called == "index"');

log_info('####### test 2: rendering');
Framework::dispatch('index.php/test/show');
assert('Framework::$controller instanceof TestController');
assert('Framework::$controller->called == "show"');
$rendered = Framework::$controller->rendered_views;
$expected = array('views/test/show.php');
$diff = array_diff($rendered, $expected);
assert('empty($diff)');

log_info('####### test 3: callbacks');
Framework::dispatch('index.php/test/show');
$performed = Framework::$controller->performed_callbacks;
$expected = array('testcallback1', 'testcallback2');
$diff = array_diff($performed, $expected);
assert('empty($diff)');
assert('empty(Framework::$controller->performed_error_callbacks)');

log_info('####### test 4: callbacks');
Framework::dispatch('index.php/test/callbacktest');
$performed = Framework::$controller->performed_callbacks;
$expected = array('testcallback1');
$diff = array_diff($performed, $expected);
assert('empty($diff)');
$performed = Framework::$controller->performed_error_callbacks;
$expected = array('testerror');
$diff = array_diff($performed, $expected);
assert('empty($diff)');
