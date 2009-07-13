<?
function f_string($object, $field, $title = null){
	return array('name' => get_class($object)."[$field]",
		'id' => util_under_score(get_class($object))."-$field",
		'title' => ($title ? $title : util_humanize($field)),
		'value' => $object->attributes[$field],
		'error' => array_try($object->errors, $field, '')
	);
}

function f_password($object, $field, $title = null){
	return array_merge(array('type' => 'password'), 
		f_string($object, $field, $title));
}

function f_textarea($object, $field, $title = null){
	return array_merge(array('type' => 'textarea'), 
		f_string($object, $field, $title));
}