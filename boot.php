<?
// some utils
require('config.php');
require('utils.php');
require('model_utils.php');
require('html_utils.php');

// autoloading of classes
function __autoload($class_name) {
global $plugins;
	foreach (array('framework', 'models', 'controllers', 'lib') as $dir){
    $path = $dir.'/'.util_under_score($class_name).'.php';
    if(is_file($path)) require($path);
    foreach($plugins as $plugin){
      $path = "slices/$plugin/$dir/".util_under_score($class_name).'.php'; 
      if(is_file($path)) require($path);
    }
	}
}

//boot framework (session, db connection, etc.)
Framework::boot();