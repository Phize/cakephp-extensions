<?php
class TestValidationRule extends CakeTestModel {
	public $useDbConfig = 'test';
	public $useTable = false;
	public $actsAs = array('Validation');
	public $validate = array();
}

class ValidationBehaviorTestCase extends CakeTestCase {
	public function start() {
		parent::start();
	}

	public function end() {
		parent::end();
	}

	public function startCase() {
		parent::startCase();
	}

	public function endCase() {
		parent::endCase();
	}

	public function startTest($method) {
		parent::startTest($method);
		$this->ValidationRule =& ClassRegistry::init('TestValidationRule');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->ValidationRule);
		ClassRegistry::flush();
	}
}
