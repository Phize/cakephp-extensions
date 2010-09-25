<?php
App::import('Controller', 'Controller', false);
App::import('Helper', 'TagCloud');

class TestTagCloudHelper extends TagCloudHelper {
	public function test_prune($tags, $minScore = 1) {
		return $this->_prune($tags, $minScore);
	}

	public function test_sort($tags, $direction = 'desc', $key = 'score') {
		return $this->_sort($tags, $direction, $key);
	}

	public function test_filter($tags, $limit, $direction = 'desc') {
		return $this->_filter($tags, $limit, $direction);
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
		$tags = array('tag40' => 9, 'tag02' => 1, 'tag1' => 10, 'TAG3' => 0, '5' => 5);

		$expected = array('tag1' => 10, 'tag40' => 9, '5' => 5, 'tag02' => 1, 'TAG3' => 0);
		$result = $this->TagCloud->test_sort($tags);
		$this->assertIdentical($result, $expected);

		$expected = array('tag40' => 9, 'tag02' => 1, 'tag1' => 10, 'TAG3' => 0, '5' => 5);
		$result = $this->TagCloud->test_sort($tags, 'asc', null);
		$this->assertIdentical($result, $expected);

		$expected = array('tag40' => 9, 'tag02' => 1, 'tag1' => 10, 'TAG3' => 0, '5' => 5);
		$result = $this->TagCloud->test_sort($tags, null, 'tag');
		$this->assertIdentical($result, $expected);

		$expected = array('tag40' => 9, 'tag02' => 1, 'tag1' => 10, 'TAG3' => 0, '5' => 5);
		$result = $this->TagCloud->test_sort($tags, 'unknown', 'tag');
		$this->assertIdentical($result, $expected);

		$expected = array('5' => 5, 'tag02' => 1, 'tag1' => 10, 'TAG3' => 0, 'tag40' => 9);
		$result = $this->TagCloud->test_sort($tags, 'asc', 'tag');
		$this->assertIdentical($result, $expected);

		$expected = array('tag40' => 9, 'TAG3' => 0, 'tag1' => 10, 'tag02' => 1, '5' => 5);
		$result = $this->TagCloud->test_sort($tags, 'desc', 'tag');
		$this->assertIdentical($result, $expected);

		$expected = array('TAG3' => 0, 'tag02' => 1, '5' => 5, 'tag40' => 9, 'tag1' => 10);
		$result = $this->TagCloud->test_sort($tags, 'asc', 'score');
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag40' => 9, '5' => 5, 'tag02' => 1, 'TAG3' => 0);
		$result = $this->TagCloud->test_sort($tags, 'desc', 'score');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _filter()のテスト
	 */
	public function test_filter() {
		$tags = array('tag1' => 10, 'tag2' => 1, 'tag3' => 9, 'tag4' => 0);

		$expected = array('tag1' => 10, 'tag3' => 9);
		$result = $this->TagCloud->test_filter($tags, 2);
		$this->assertIdentical($result, $expected);

		$expected = array('tag1' => 10, 'tag2' => 1, 'tag3' => 9, 'tag4' => 0);
		$result = $this->TagCloud->test_filter($tags, 10);
		$this->assertIdentical($result, $expected);

		$expected = array('tag2' => 1, 'tag3' => 9, 'tag4' => 0);
		$result = $this->TagCloud->test_filter($tags, 3, 'asc');
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
		$result = $this->TagCloud->calculate($tags, array('filter' => null));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag2' => array('score' => 1, 'rank' => 9),
			'tag3' => array('score' => 0, 'rank' => 1),
			'tag4' => array('score' => 9, 'rank' => 24)
		);
		$result = $this->TagCloud->calculate($tags, array('filter' => array('threshold' => 0)));
		$this->assertIdentical($result, $expected);

		$expected = array();
		$result = $this->TagCloud->calculate($tags, array('filter' => array('threshold' => 99)));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 13)
		);
		$result = $this->TagCloud->calculate($tags, array('filter' => array('direction' => null, 'limit' => 1)));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag2' => array('score' => 1, 'rank' => 13)
		);
		$result = $this->TagCloud->calculate($tags, array('filter' => array('direction' => 'asc', 'limit' => 1)));
		$this->assertIdentical($result, $expected);

		$expected = array();
		$result = $this->TagCloud->calculate($tags, array('filter' => array('direction' => null, 'limit' => 0)));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag2' => array('score' => 1, 'rank' => 1),
			'tag4' => array('score' => 9, 'rank' => 23)
		);
		$result = $this->TagCloud->calculate($tags, array('sort' => null));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag4' => array('score' => 9, 'rank' => 23),
			'tag2' => array('score' => 1, 'rank' => 1)
		);
		$result = $this->TagCloud->calculate($tags, array('sort' => array('key' => 'score', 'direction' => 'desc')));
		$this->assertIdentical($result, $expected);

		$expected = array(
			'tag1' => array('score' => 10, 'rank' => 25),
			'tag2' => array('score' => 1, 'rank' => 1),
			'tag4' => array('score' => 9, 'rank' => 23)
		);
		$result = $this->TagCloud->calculate($tags, array('sort' => array('key' => 'tag', 'direction' => 'asc')));
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
