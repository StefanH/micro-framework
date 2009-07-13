<?
function t_string($column, $title = null){
	return array('column' => $column,
		'title' => ($title ? $title : util_humanize($column))
	);
}

function t_link($column, $url, $title = null){
	return array_merge(
		array('type' => 'link', 'url' => $url),
		t_string($column, $title));
}

function t_icon($column, $url, $title = null){
	return array_merge(
		t_string($column),
		array('type' => 'icon', 'url' => $url, 'title' => ($title ? $title : '')));
}