<?php
class ValidationModelAFixture extends CakeTestFixture {
	public $name = 'ValidationModelA';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'field1' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'field2' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'field3' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	public $records = array(
		array('id' => 1, 'field1' => 'apple', 'field2' => 'orange', 'field3' => 'melon')
	);
}
