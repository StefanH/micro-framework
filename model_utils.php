<?
function find($model, $conditions = array(), $order=""){
  return Model::find($model, $conditions, $order);
}

function find_by_sql($sql){
	$args = array_slice(func_get_args(), 1);
  return Model::find_by_sql_array($sql, $args);
}

function find_by_sql_as_type($type, $sql){
	$args = array_slice(func_get_args(), 2);
  return Model::find_by_sql_as_type($type, $sql, $args);
}

function first($model, $conditions = null, $orderby = null) {
	return Model::find($model, $conditions, $orderby);
}

function get($model, $id){
  return Model::get($model, $id);
}

function delete_where($model, $conditions){
  return Model::delete_where($model, $conditions);
}

function get_fields($model){
  return Model::fields($model);
}

function get_field_names($model){
  return Model::field_names($model);
}