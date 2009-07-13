<?
/* The framework test
 * assumes that the application points to a database that contains
 * a table called test_table with no records
 */

class Test extends Model {
  public static $table = 'test_table';
}

//empty test db
Model::Truncate('Test');

require 'model_test.php';
require 'controller_test.php';
require 'view_helpers_test.php';