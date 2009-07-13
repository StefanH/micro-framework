<?
// make some test data
foreach(array(
	new Test(array('name' => 'bla', 'name2' => 'blabla')),
	new Test(array('name' => 'blaaa', 'name2' => 'blab')),
	new Test(array('name' => 'blaa3', 'name2' => 'blab')),
	new Test(array('name' => 'blaa3', 'name2' => 'blb')),
	) as $object) {
	$object->save();	
} 

// test form
$object = reset(find('Test'));
$f = new HTMLForm(array(
	f_string($object, 'name'),
	f_password($object, 'name2'),
	f_textarea($object, 'name2'),
	));
	
$f->render_all();

// test table
$objects = find('Test');
$t = new HTMLTable(array(
		t_string('name'),
		t_link('name', '/tests/test'),
		t_icon('table', '/tests/test')
	), collection_get_property($objects, 'attributes'));
	
$t->render_all();

