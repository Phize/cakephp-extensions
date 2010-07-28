<?php
class TestConfigurationModel extends CakeTestModel {
	public $alias = 'ConfigurationModel';
	public $useDbConfig = 'test';
	public $useTable = 'configuration_models';
	public $actsAs = array('Configuration' => array('namespace' => 'test'));
	public $validate = array(
		'id' =>  'decimal'
	);
}

class ConfigurationBehaviorTestCase extends CakeTestCase {
	public $fixtures = array('configuration_model');

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
		$this->ConfigurationModel =& ClassRegistry::init('TestConfigurationModel');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->ConfigurationModel);
		ClassRegistry::flush();
	}

	/**
	 * loadConfig()のテスト
	 */
	public function testLoadConfig() {
		$result = $this->ConfigurationModel->loadConfig();
		$this->assertIdentical($result, array('name' => 'Configuration behavior'));
		$this->assertIdentical(Configure::read('test.name'), 'Configuration behavior');

		Configure::delete('test.name');
		$result = $this->ConfigurationModel->loadConfig(true);
		$this->assertIdentical($result, array($this->ConfigurationModel->alias => array('name' => 'Configuration behavior')));
		$this->assertIdentical(Configure::read('test.name'), 'Configuration behavior');
	}

	/**
	 * getConfig()のテスト
	 */
	public function testGetConfig() {
		$result = $this->ConfigurationModel->getConfig();
		$this->assertIdentical($result, array('name' => 'Configuration behavior'));
		$this->assertIdentical(Configure::read('test.name'), 'Configuration behavior');

		$result = $this->ConfigurationModel->getConfig(true);
		$this->assertIdentical($result, array($this->ConfigurationModel->alias => array('name' => 'Configuration behavior')));
		$this->assertIdentical(Configure::read('test.name'), 'Configuration behavior');
	}

	/**
	 * saveConfig()のテスト
	 */
	public function testSaveConfig() {
		$result = $this->ConfigurationModel->saveConfig();
		$this->assertIdentical($result, false);

		$this->ConfigurationModel->create(null);
		$this->ConfigurationModel->set(array(
			$this->ConfigurationModel->alias => array(
				'id' => 'apple'
			)
		));
		$result = $this->ConfigurationModel->validates();
		$this->assertIdentical($result, false);

		$this->ConfigurationModel->create(null);
		$this->ConfigurationModel->set(array(
			$this->ConfigurationModel->alias => array(
				'id' => 1
			)
		));
		$result = $this->ConfigurationModel->saveConfig();
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 'i:1;');
		$this->assertIdentical(Configure::read('test.id'), 1);

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 2
			)
		);
		$result = $this->ConfigurationModel->saveConfig($data);
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 'i:2;');
		$this->assertIdentical(Configure::read('test.id'), 2);

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 'apple'
			)
		);
		$result = $this->ConfigurationModel->saveConfig($data, false);
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 's:5:"apple";');
		$this->assertIdentical(Configure::read('test.id'), 'apple');

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 3,
				'desc' => 'description 1'
			)
		);
		$this->ConfigurationModel->whitelist = array('id');
		$result = $this->ConfigurationModel->saveConfig($data);
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 'i:3;');
		$this->assertIdentical(Configure::read('test.id'), 3);
		$result = $this->ConfigurationModel->findByKey('desc');
		$this->assertIdentical($result, false);
		$this->assertIdentical(Configure::read('test.desc'), null);

		$data = array(
			$this->ConfigurationModel->alias => array(
				'food' => 'apple'
			)
		);
		$this->ConfigurationModel->whitelist = 'id';
		$result = $this->ConfigurationModel->saveConfig($data);
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('food');
		$this->assertIdentical($result, false);
		$this->assertIdentical(Configure::read('test.food'), null);

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 4,
				'desc' => 'description 2'
			)
		);
		$whitelist = array('desc');
		$this->ConfigurationModel->whitelist = $whitelist;
		$result = $this->ConfigurationModel->saveConfig($data, false, array('id'));
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 'i:4;');
		$this->assertIdentical(Configure::read('test.id'), 4);
		$result = $this->ConfigurationModel->findByKey('desc');
		$this->assertIdentical($result, false);
		$this->assertIdentical(Configure::read('test.desc'), null);
		$this->assertIdentical($this->ConfigurationModel->whitelist, $whitelist);

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 'orange',
				'desc' => 'description 3'
			)
		);
		$result = $this->ConfigurationModel->saveConfig($data, array('validate' => false, 'fieldList' => array('id', 'desc')));
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 's:6:"orange";');
		$this->assertIdentical(Configure::read('test.id'), 'orange');
		$result = $this->ConfigurationModel->findByKey('desc');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 's:13:"description 3";');
		$this->assertIdentical(Configure::read('test.desc'), 'description 3');

		$data = array(
			$this->ConfigurationModel->alias => array(
				'id' => 5,
				'created' => '0000-00-00 00:00:00',
				'updated' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			)
		);
		$result = $this->ConfigurationModel->saveConfig($data, array('fieldList' => null));
		$this->assertIdentical($result, true);
		$result = $this->ConfigurationModel->findByKey('id');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['value'], 'i:5;');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['created'], '0000-00-00 00:00:00');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['updated'], '0000-00-00 00:00:00');
		$this->assertIdentical($result[$this->ConfigurationModel->alias]['modified'], '0000-00-00 00:00:00');
		$this->assertIdentical(Configure::read('test.id'), 5);
		$this->assertIdentical(Configure::read('test.created'), null);
		$this->assertIdentical(Configure::read('test.updated'), null);
		$this->assertIdentical(Configure::read('test.modified'), null);
	}
}
