<?
// a simple scenario
log_info("###### test 1: see if table is empty");
$result = find('Test');
assert('empty($result)');

log_info("###### test 2: insert an object");
$test = new Test(array('name'=>'he', 'name2'=>'ho'));
$id = $test->save();
$test2 = get("Test", $id);
assert('!empty($test2)');

log_info("###### test 3: attribute getters");
assert('$test2->get_id() == $test2->attributes[\'id\']');

log_info("###### test 4: update the object");
$test2->update_attributes(array('name'=>'ha'));
$test2->save();
$test3 = get("Test", $test2->attributes['id']);
$diff = array_diff($test2->attributes, $test3->attributes);
assert('empty($diff)');

log_info("###### test 5: delete the object");
$test3->delete();
$result = find('Test');
assert('empty($result)');

//util tests
log_info("###### test 6: get_field_names");
$fieldnames = get_field_names('Test');
$expected = array('id', 'name', 'name2');
$diff = array_diff($fieldnames, $expected);
assert('empty($diff)');


?>