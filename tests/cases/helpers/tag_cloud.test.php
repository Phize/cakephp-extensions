<?php
App::import('Controller', 'Controller', false);
App::import('Helper', 'TagCloud');

class TestTagCloudHelper extends TagCloudHelper {
	public function test_prune($tags, $minScore = 1) {
		return $this->_prune($tags, $minScore);
	}

	public function test_sort($tags, $direction = 'desc') {
		return $this->_sort($tags, $direction);
	}

	public function test_filter($tags, $direction = null, $limit = null) {
		return $this->_filter($tags, $direction, $limit);
	}

	public function test_calculateRate($tags) {
		return $this->_calculateRate($tags);
	}
}

class TagCloudHelperTestCase extends CakeTestCase {
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
		$this->TagCloud =& new TestTagCloudHelper();
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->TagCloud);
	}

	/**
	 * _prune()のテスト
	 */
	public function test_prune() {
		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag4' => 9);
		$result = $this->TagCloud->test_prune($tags);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);
		$result = $this->TagCloud->test_prune($tags, 0);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10);
		$result = $this->TagCloud->test_prune($tags, 10);
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _sort()のテスト
	 */
	public function test_sort() {
		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);

		$expected = array('tag1' => 10, 'tag4' => 9, 'tag2' => 1, 'tag3' => 0);
		$result = $this->TagCloud->test_sort($tags);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);
		$result = $this->TagCloud->test_sort($tags, null);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);
		$result = $this->TagCloud->test_sort($tags, 'unknown');
		$this->assertIdentical($result, $expected);

		$expected = array('tag3' => 0, 'tag2' => 1, 'tag4' => 9, 'tag1' => 10);
		$result = $this->TagCloud->test_sort($tags, 'asc');
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag4' => 9, 'tag2' => 1, 'tag3' => 0);
		$result = $this->TagCloud->test_sort($tags, 'desc');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _filter()のテスト
	 */
	public function test_filter() {
		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);
		$result = $this->TagCloud->test_filter($tags);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1);
		$result = $this->TagCloud->test_filter($tags, null, 2);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);
		$result = $this->TagCloud->test_filter($tags, null, 10);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag4' => 9);
		$result = $this->TagCloud->test_filter($tags, 'desc', 3);
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _calculateRate()のテスト
	 */
	public function test_calculateRate() {
		$tags = array('tag1' => 10, 'tag2' => 10, 'tag3' => 10, 'tag4' => 10);
		$expected = array(
			'tag1' => 0.5,
			'tag2' => 0.5,
			'tag3' => 0.5,
			'tag4' => 0.5
		);
		$result = $this->TagCloud->test_calculateRate($tags);
		$this->assertIdentical($result, $expected);
	}

	/**
	 * calculate()のテスト
	 */
	public function testCalculate() {
		$tags = array();
		$expected = array();
		$result = $this->TagCloud->calculate($tags);
		$this->assertIdentical($result, $expected);

		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag2' => array('score' => 1, 'rank' => 1),
			'tag4' => array('score' => 9, 'rank' => 23)
		);
		$result = $this->TagCloud->calculate($tags);
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 250),
			'tag2' => array('score' => 1, 'rank' => 10),
			'tag4' => array('score' => 9, 'rank' => 232)
		);
		$result = $this->TagCloud->calculate($tags, array('min' => 10, 'max' => 250));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag2' => array('score' => 1, 'rank' => 9),
			'tag3' => array('score' => 0, 'rank' => 1),
			'tag4' => array('score' => 9, 'rank' => 24)
		);
		$result = $this->TagCloud->calculate($tags, array('threshold' => 0));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag4' => array('score' => 9, 'rank' => 23),
			'tag2' => array('score' => 1, 'rank' => 1)
		);
		$result = $this->TagCloud->calculate($tags, array('sort' => 'desc'));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 13)
		);
		$result = $this->TagCloud->calculate($tags, array('filter' => array('sort' => null, 'limit' => 1)));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag2' => array('score' => 1, 'rank' => 13)
		);
		$result = $this->TagCloud->calculate($tags, array('filter' => array('sort' => 'asc', 'limit' => 1)));
		$this->assertIdentical($result, $expected);
	}

	/**
	 * shuffle()のテスト
	 */
	public function testShuffle() {
		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 0, 'tag4' => 9);

		$result = $this->TagCloud->shuffle($tags);
		$this->assertNotIdentical($result, $tags);
	}
}
