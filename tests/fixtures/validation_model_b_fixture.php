<?php
class ValidationModelBFixture extends CakeTestFixture {
	public $name = 'ValidationModelB';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'validation_model_a_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'validation_model_a_id' => array('column' => 'validation_model_a_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	public $records = array(
		array('id' => 1, 'validation_model_a_id' => 1)
	);
}
