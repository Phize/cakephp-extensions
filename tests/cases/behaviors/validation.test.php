<?php
class TestValidationRule extends CakeTestModel {
	public $alias = 'ValidationRule';
	public $useDbConfig = 'test';
	public $useTable = false;
	public $actsAs = array('Validation');
	public $validate = array();
}

class TestValidationModelA extends CakeTestModel {
	public $alias = 'ValidationModelA';
	public $useDbConfig = 'test';
	public $useTable = 'validation_model_as';
	public $actsAs = array('Validation');
	public $validate = array();
	public $hasMany = array(
		'ModelB' => array(
			'className' => 'ValidationModelB',
			'foreignKey' => 'validation_model_a_id',
			'conditions' => null,
			'fields' => null,
			'order' => null,
			'limit' => null,
			'offset' => null,
			'dependent' => true,
			'exclusive' => null,
			'finderQuery' => null
		)
	);
}

class TestValidationModelB extends CakeTestModel {
	public $alias = 'ValidationModelB';
	public $useDbConfig = 'test';
	public $useTable = 'validation_model_bs';
	public $actsAs = array('Validation');
	public $validate = array();
	public $belongsTo = array(
		'ModelA' => array(
			'className' => 'ValidationModelA',
			'foreignKey' => 'validation_model_a_id',
			'conditions' => null,
			'fields' => null,
			'counterCache' => null
		)
	);
}

class ValidationBehaviorTestCase extends CakeTestCase {
	public $fixtures = array('validation_model_a', 'validation_model_b');

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
		$this->ModelA =& ClassRegistry::init('TestValidationModelA');
		$this->ModelB =& ClassRegistry::init('TestValidationModelB');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->ValidationRule);
		unset($this->ModelA);
		unset($this->ModelB);
		ClassRegistry::flush();
	}

	/**
	 * bindValidation()のテスト
	 */
	public function testBindValidation() {
		$expected = array('field' => array('rule' => 'notEmpty'));
		$this->ValidationRule->bindValidation($expected);
		$this->assertIdentical($this->ValidationRule->validate, $expected);

		$this->ValidationRule->validate = array('field' => array('rule' => 'notEmpty'));
		$expected = array('field' => array('rule' => 'numeric'));
		$this->ValidationRule->bindValidation($expected);
		$this->assertIdentical($this->ValidationRule->validate, $expected);

		$this->ValidationRule->validate = array('field1' => array('rule' => 'notEmpty'));
		$validate = array('field2' => array('rule' => 'numeric'));
		$expected = array('field1' => array('rule' => 'notEmpty'), 'field2' => array('rule' => 'numeric'));
		$this->ValidationRule->bindValidation($expected);
		$this->assertIdentical($this->ValidationRule->validate, $expected);
	}

	/**
	 * unbindValidation()のテスト
	 */
	public function testUnbindValidation() {
		$this->ValidationRule->validate = array('field' => array('rule' => 'notEmpty'));
		$this->ValidationRule->unbindValidation('field');
		$this->assertIdentical($this->ValidationRule->validate, array());

		$this->ValidationRule->validate = array('field1' => array('rule' => 'notEmpty'), 'field2' => array('rule' => 'numeric'));
		$this->ValidationRule->unbindValidation(array('field1', 'field2'));
		$this->assertIdentical($this->ValidationRule->validate, array());

		$this->ValidationRule->validate = array('field1' => array('rule' => 'notEmpty'), 'field2' => array('rule' => 'numeric'));
		$expected = array('field1' => array('rule' => 'notEmpty'));
		$this->ValidationRule->unbindValidation('field2');
		$this->assertIdentical($this->ValidationRule->validate, $expected);
	}

	/**
	 * isDatetime()のテスト
	 */
	public function testIsDatetime() {
		$result = $this->ValidationRule->isDateTime(array('field' => '0000-00-00'));
		$this->assertIdentical($result, false);

		$result = $this->ValidationRule->isDateTime(array('field' => '0000-00-00 00:00:00'));
		$this->assertIdentical($result, true);
	}

	/**
	 * primaryKeyExists()のテスト
	 */
	public function testPrimaryKeyExists() {
		$result = $this->ModelB->primaryKeyExists(array('validation_model_a_id' => 900), 'ModelA');
		$this->assertIdentical($result, false);

		$result = $this->ModelB->primaryKeyExists(array('validation_model_a_id' => 1), 'ModelA');
		$this->assertIdentical($result, true);

		$result = $this->ModelB->primaryKeyExists(array('validation_model_a_id' => 1), 'Plugin.ModelA');
		$this->assertIdentical($result, true);
	}

	/**
	 * isUniqueWith()のテスト
	 */
	public function testIsUniqueWith() {
		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), 'field2');
		$this->assertIdentical($result, true);

		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), array('field2' => 'orange'));
		$this->assertIdentical($result, false);

		$this->ModelA->set(array('field2' => 'orange', 'field3' => 'melon'));
		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), 'field2');
		$this->assertIdentical($result, false);

		$this->ModelA->set(array('field2' => 'orange', 'field3' => 'melon'));
		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), array('field2', 'field3'));
		$this->assertIdentical($result, false);

		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), array('field2' => 'peach'));
		$this->assertIdentical($result, true);

		$this->ModelA->set(array('field2' => 'peach', 'field3' => 'melon'));
		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), 'field2');
		$this->assertIdentical($result, true);

		$this->ModelA->set(array('field2' => 'peach', 'field3' => 'melon'));
		$result = $this->ModelA->isUniqueWith(array('field1' => 'apple'), array('field2', 'field3'));
		$this->assertIdentical($result, true);
	}
}
