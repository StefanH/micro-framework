<?
/*
Models can be accessed in controller methods to query and manipulate the database. The following simplified UsersController and User model demonstrate a lot of this functionality:

// controllers/users_controller.php:
class UsersController extends ApplicationController {
  public function index(){
    $this->users = find("User");
  }

  public function create_form(){
    $this->user = new User();
  }

  public function create(){
    $this->user = new User($_POST['User']);
    if($this->user->save()){
      flash("User created"); 
      $this->redirect("index");
    } else {
      $this->errors[] = "Update failed:";
      $this->render("create_form");
    }
  }
}

// models/user.php:
class User extends Model {
  const table = "users";
}

TODO
*/

class Model extends MicroObject{
  // find rows based on conditions
	public static function find($model, $conditions = null, $orderby = null){
		$sql = "SELECT * FROM " . self::table_for($model);
		if ($conditions) $sql .= " WHERE " . self::make_conditions($conditions);
		if ($orderby)	$sql .= " ORDER BY " . $orderby;
		
		$resultset = self::do_query($sql);
		return self::get_results($resultset, $model);
	}
	
	public static function find_by_sql($model, $sql){
		$resultset = self::do_query($sql);
		return self::get_results($resultset, $model);
	}
	
	function first($model, $conditions = null, $orderby = null) {
		$results = self::find($model, $conditions, $orderby);
		return reset($results);
	}
	
  // get a single row from an id
	public static function get($model, $id){		
    $results = self::find($model, array('id'=>$id));
		if (empty($results)){
			throw new Exception("cannot get $model $id");
		}		
		return reset($results);
	}
	
	//delete rows that conform to certain conditions
  public static function delete_where($model, $conditions){		
    $table = self::table_for($model);
    $condition_string = self::make_conditions($conditions);
    return self::do_query("DELETE FROM " . $table . " WHERE " . $condition_string);	
	}
	
	//get fields for a model
	public static function fields($model){
		$resultset = self::do_query("SHOW COLUMNS FROM " . self::table_for($model));
		return self::get_results_array($resultset, 'Field');
	}

	//get fields for a model	
	public static function field_names($model){
	  $fields = self::fields($model);
	  return array_keys($fields);
	}
  
	//get fields for the current model
	public function instance_fields(){
		return self::fields(get_class($this));
	}
	
  protected static function get_results_array($resultset, $index_key=false){
    $result = array();
		while($row = $resultset->fetch_assoc()){
			if($index_key) $result[$row[$index_key]] = $row;
			else $result[] = $row;
		}
		return $result;
  }
  
  protected static function get_results($resultset, $model, $index_key=false){
    $result = array();
    $reflect = new ReflectionClass($model);
		while($row = $resultset->fetch_assoc()){
			$temp = $reflect->newInstance($row);
			$temp->do_callback('after_load');
			if($index_key) $result[$row[$index_key]] = $temp;
			else $result[] = $temp;
		}
		return $result;
  }
	
	protected static function do_query($sql){
	  log_info($sql);
		$result = Framework::$db->query($sql);
		if (!$result) {
			throw new Exception("query failed: ".Framework::$db->error);
		}
		return $result;
	}
	
	protected static function make_conditions($conditions){
		$sql_conditions = array();
		
		foreach($conditions as $key => $value){
		  if(is_array($value)){
		    $values = array();
		    foreach($value as $part) $values[] = self::make_value($part);
		    $sql_conditions[] = "$key IN (".implode(",", $values).")";
		  } else {
		    $matches = array();
		    if (ereg("^(<|>|>=|<=|LIKE) ", $value, $matches))
		      $sql_conditions[] = "$key ".reset($matches)." ".
						self::make_value(substr($value, strlen(reset($matches))));
		    else $sql_conditions[] = "$key =" . self::make_value($value);
	    }
		}
		
		return join(" AND ", $sql_conditions);
	}
		
	protected static function make_value($value){
		return "'" . Framework::$db->escape_string($value) . "'";
	}
	
	private static function table_for($model){
	  $reflect = new ReflectionClass($model);
		return $reflect->getConstant('table');
	}
	
	public $attributes = array();
	public $errors = array();
  
  public function __construct($attributes = array()){
    $this->attributes = $attributes;
  }
	
	public function update_attributes($attributes){
	  $this->attributes = array_merge($this->attributes, $attributes);
  }
  
	// delete a single row from an id
	public function delete(){
	  if(!isset($this->attributes['id'])) 
			throw new Exception("Cannot delete new objects");
		$this->do_callback("before_delete");
		return self::do_query("DELETE FROM ".self::table_for(get_class($this)).
		    " WHERE id=".self::make_value($this->attributes['id']));	
	}
		
  public function save(){   
	  //validation
	  $this->do_callback("before_validate");      
	  if (!$this->get_callback_result("validate")) return false;
  
	  $this->do_callback("before_save");
  
		//value gathering
		$datavalues = array();   
		foreach ($this->instance_fields() as $fieldname => $field){
			if (isset($this->attributes[$fieldname]))
				$datavalues[$fieldname] =
			self::make_value($this->attributes[$fieldname]);
		}
		
		//insert or update
		if (isset($this->attributes['id'])){
			$this->dbupdate($datavalues, $this->id());
			$result = true;
		} else {
			$result = $this->dbinsert($datavalues);
			$this->attributes['id'] = $result;
		}
		$this->do_callback("after_save");

		return $result;
  }
	
  protected function dbupdate($array, $id){	
		$update = array();
		foreach(array_keys($array) as $key){
			$update[] = $key."=".$array[$key];
		}
		$sql = "UPDATE ".self::table_for(get_class($this))." SET ".join(', ', $update). 
					" WHERE id='$id';";
		self::do_query($sql);
	}

	protected function dbinsert($array){	

		// build query
		$sql = "INSERT INTO ".self::table_for(get_class($this)).
		  " (" . join(', ', array_keys($array)).") VALUES (".
		  join(', ',array_values($array)).");";
		
		self::do_query($sql);					
		return Framework::$db->insert_id;
	}
	//-----------------------------------------
	// metaprogramming
	//-----------------------------------------
	function __call($name, $arguments){
		if(empty($arguments) && isset($this->attributes[$name])) return $this->attributes[$name];
		else throw new ErrorException('Call to undefined function');
	}
	
	//-----------------------------------------
	//validation
	//-----------------------------------------
	
  // default = valid
	public function validate(){
	  return true;
	}
	
	public function require_field($field){
		if(!isset($this->attributes[$field]) || empty($this->attributes[$field])){
			$this->errors[$field] = "This field is required";			
		}
	}
	
	public function require_field_length($field, $length){
		if(!$this->attributes[$field] || strlen($this->attributes[$field]) < $length){
			$field = util_human($field);
			$this->errors[$field] = "This field should be longer than " . $length;
		}
	}
	
	
				
}

?>