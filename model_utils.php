<?
function find($model, $conditions = array(), $order=""){
  return Model::find($model, $conditions, $order);
}

function find_by_sql($model, $sql){
  return Model::find_by_sql($model, $sql);
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