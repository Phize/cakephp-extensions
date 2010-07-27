<?php
class TestFormatRule extends CakeTestModel {
	public $alias = 'FormatRule';
	public $useDbConfig = 'test';
	public $useTable = false;
	public $actsAs = array('Format');
	public $validate = array();
	public $format = array(
		'f1' => 'trim',
		'f2' => 'mb_trim',
		'f3' => 'keywords',
		'f4' => array(array('keywords', ',')),
		'f5' => array(array('replace', 'search', 'replace')),
		'f6' => array('trim'),
		'f7' => array('trim', 'keywords'),
		'f8' => array('mb_trim', array('replace', 'search', 'replace')),
		'f9' => 'custom'
	);

	public function custom($data) {
		$field = key($data);
		$this->data[$this->alias][$field] = 'custom';
	}
}

class FormatBehaviorTestCase extends CakeTestCase {
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
		$this->FormatRule =& ClassRegistry::init('TestFormatRule');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->FormatRule);
		ClassRegistry::flush();
	}

	public function testFormatTrim() {
		$data = array(
			'FormatRule' => array(
				'f1' => ' trim　'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f1' => 'trim　'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatMbTrim() {
		$data = array(
			'FormatRule' => array(
				'f2' => '　mb_trim　'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f2' => 'mb_trim'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatKeywords() {
		$data = array(
			'FormatRule' => array(
				'f3' => ' keyword1　 keyword2          keyword3　',
				'f4' => ' keyword1　, keyword2      ,    keyword3　'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f3' => 'keyword1 keyword2 keyword3',
				'f4' => 'keyword1,keyword2,keyword3'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatReplace() {
		$data = array(
			'FormatRule' => array(
				'f5' => 'abcdsearchefg'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f5' => 'abcdreplaceefg'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatArrayRule() {
		$data = array(
			'FormatRule' => array(
				'f6' => ' trim '
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f6' => 'trim'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatArrayRules() {
		$data = array(
			'FormatRule' => array(
				'f7' => ' keyword1　 keyword2          keyword3　'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f7' => 'keyword1 keyword2 keyword3'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatArrayRulesWithParameters() {
		$data = array(
			'FormatRule' => array(
				'f8' => ' abcdsearchefg　'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f8' => 'abcdreplaceefg'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}

	public function testFormatCustomFormatter() {
		$data = array(
			'FormatRule' => array(
				'f9' => 'abcdsearchefg'
			)
		);

		$expected = array(
			'FormatRule' => array(
				'f9' => 'custom'
			)
		);
		$this->assertTrue($this->FormatRule->create($data));
		$this->assertTrue($this->FormatRule->validates());
		$this->assertIdentical($this->FormatRule->data, $expected);
	}
}
