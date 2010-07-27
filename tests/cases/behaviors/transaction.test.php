<?php
class TestTransactionModel extends CakeTestModel {
	public $alias = 'Model';
	public $useDbConfig = 'test';
	public $useTable = 'transaction_models';
	public $actsAs = array('Transaction');
	public $validate = array();
}

class TransactionBehaviorTestCase extends CakeTestCase {
	public $fixtures = array('transaction_model');

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
		$this->TransactionModel =& ClassRegistry::init('TestTransactionModel');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->TransactionModel);
		ClassRegistry::flush();
	}

	/**
	 * beginTransaction()のテスト
	 */
	public function testBeginTransaction() {
		$result = $this->TransactionModel->beginTransaction();
		$this->TransactionModel->commitTransaction();
		$this->assertIdentical($result, true);
	}

	/**
	 * commitTransaction()のテスト
	 */
	public function testCommitTransaction() {
		$result = $this->TransactionModel->commitTransaction();
		$this->assertIdentical($result, false);

		$this->TransactionModel->beginTransaction();
		$result = $this->TransactionModel->commitTransaction();
		$this->assertIdentical($result, true);

		$this->TransactionModel->beginTransaction();
		$data = array(
			$this->TransactionModel->alias => array(
				'name' => 'name 1'
			)
		);
		$this->TransactionModel->create();
		$this->TransactionModel->save($data);
		$data = array(
			$this->TransactionModel->alias => array(
				'name' => 'name 2'
			)
		);
		$this->TransactionModel->create();
		$this->TransactionModel->save($data);
		$this->TransactionModel->commitTransaction();
		$expected = array(
			0 => array(
				$this->TransactionModel->alias => array(
					$this->TransactionModel->primaryKey => '1',
					'name' => 'name 1'
				)
			),
			1 => array(
				$this->TransactionModel->alias => array(
					$this->TransactionModel->primaryKey => '2',
					'name' => 'name 2'
				)
			)
		);
		$result = $this->TransactionModel->find('all');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * rollbackTransaction()のテスト
	 */
	public function testRollbackTransaction() {
		$result = $this->TransactionModel->rollbackTransaction();
		$this->assertIdentical($result, false);

		$this->TransactionModel->beginTransaction();
		$result = $this->TransactionModel->rollbackTransaction();
		$this->assertIdentical($result, true);

		$this->TransactionModel->beginTransaction();
		$data = array(
			$this->TransactionModel->alias => array(
				'name' => 'name 1'
			)
		);
		$this->TransactionModel->create();
		$this->TransactionModel->save($data);
		$data = array(
			$this->TransactionModel->alias => array(
				'name' => 'name 2'
			)
		);
		$this->TransactionModel->create();
		$this->TransactionModel->save($data);
		$this->TransactionModel->rollbackTransaction();
		$expected = array();
		$result = $this->TransactionModel->find('all');
		$this->assertIdentical($result, $expected);
	}
}
