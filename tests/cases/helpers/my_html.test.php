<?php
App::import('Controller', 'Controller', false);
App::import('Helper', 'MyHtml');

class MyHtmlHelperTestCase extends CakeTestCase {
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
		$this->MyHtml =& new MyHtmlHelper();
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->MyHtml);
	}

	public function testTag() {
		$result = $this->MyHtml->tag('p', 'テキスト');
		$this->assertIdentical($result, '<p>テキスト</p>');

		$result = $this->MyHtml->tag(null, 'テキスト');
		$this->assertIdentical($result, 'テキスト');

		$result = $this->MyHtml->tag(false, 'テキスト');
		$this->assertIdentical($result, 'テキスト');

		$result = $this->MyHtml->tag('false', 'テキスト');
		$this->assertIdentical($result, '<false>テキスト</false>');

		$result = $this->MyHtml->tag(0, 'テキスト');
		$this->assertIdentical($result, 'テキスト');

		$result = $this->MyHtml->tag(-1, 'テキスト');
		$this->assertIdentical($result, '<-1>テキスト</-1>');

		$result = $this->MyHtml->tag('', 'テキスト');
		$this->assertIdentical($result, 'テキスト');

		$result = $this->MyHtml->tag('0', 'テキスト');
		$this->assertIdentical($result, 'テキスト');
	}

	public function testImage() {
		$result = $this->MyHtml->image('image.png');
		$this->assertIdentical($result, '<img src="img/image.png" alt="" />');

		$result = $this->MyHtml->image('image.png', array('alt' => 'alt'));
		$this->assertIdentical($result, '<img src="img/image.png" alt="alt" />');

		$result = $this->MyHtml->image('image.png', array('url' => '/image.png'));
		$this->assertIdentical($result, '<a href="/image.png"><img src="img/image.png" alt="" /></a>');

		$result = $this->MyHtml->image('image.png', array('url' => array('/image.png', array('rel' => 'nofollow'))));
		$this->assertIdentical($result, '<a href="/image.png" rel="nofollow"><img src="img/image.png" alt="" /></a>');
	}

	public function testGetCrumbsWithoutOptions() {
		$this->MyHtml->_crumbs = array();
		$result = $this->MyHtml->getCrumbs();
		$this->assertIdentical($result, '');

		$this->MyHtml->_crumbs = array();
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ');
		$result = $this->MyHtml->getCrumbs();
		$this->assertIdentical($result, '<ul><li>ページ</li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ');
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a><ul><li>ページ</li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ', '/page');
		$result = $this->MyHtml->getCrumbs();
		$this->assertIdentical($result, '<ul><li><a href="/page">ページ</a></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ', '/page');
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a><ul><li><a href="/page">ページ</a></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs();
		$this->assertIdentical($result, '<ul><li><a href="/page1">ページ1</a><ul><li><a href="/page2">ページ2</a></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a><ul><li><a href="/page1">ページ1</a><ul><li><a href="/page2">ページ2</a></li></ul></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('"ページ1"', '/page1');
		$this->MyHtml->addCrumb('"ページ2"');
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a><ul><li><a href="/page1">&quot;ページ1&quot;</a><ul><li>&quot;ページ2&quot;</li></ul></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$this->MyHtml->addCrumb('"ページ1"', '/page1', array('escape' => false));
		$this->MyHtml->addCrumb('"ページ2"', null, array('escape' => false));
		$result = $this->MyHtml->getCrumbs('ホーム');
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a><ul><li><a href="/page1">"ページ1"</a><ul><li>"ページ2"</li></ul></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$result = $this->MyHtml->getCrumbs('"ホーム"');
		$this->assertIdentical($result, '<ul><li><a href="/">&quot;ホーム&quot;</a></li></ul>');
	}

	public function testGetCrumbsWithOptions() {
		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => array('ul', array('class' => 'outer')),
			'rowTag' => array('li', array('class' => 'row')),
			'innerTag' => array('ul', array('class' => 'inner')),
			'innerRowTag' => array('li', array('class' => 'innerRow'))
		);
		$result = $this->MyHtml->getCrumbs(false, $options);
		$this->assertIdentical($result, '');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => array('ul', array('class' => 'outer')),
			'rowTag' => array('li', array('class' => 'row')),
			'innerTag' => array('ul', array('class' => 'inner')),
			'innerRowTag' => array('li', array('class' => 'innerRow'))
		);
		$result = $this->MyHtml->getCrumbs('ホーム', $options);
		$this->assertIdentical($result, '<ul class="outer"><li class="row"><a href="/">ホーム</a></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => array('ul', array('class' => 'outer')),
			'rowTag' => array('li', array('class' => 'row')),
			'innerTag' => array('ul', array('class' => 'inner')),
			'innerRowTag' => array('li', array('class' => 'innerRow'))
		);
		$this->MyHtml->addCrumb('ページ');
		$result = $this->MyHtml->getCrumbs(false, $options);
		$this->assertIdentical($result, '<ul class="outer"><li class="row">ページ</li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => array('ul', array('class' => 'outer')),
			'rowTag' => array('li', array('class' => 'row')),
			'innerTag' => array('ul', array('class' => 'inner')),
			'innerRowTag' => array('li', array('class' => 'innerRow'))
		);
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs(false, $options);
		$this->assertIdentical($result, '<ul class="outer"><li class="row"><a href="/page1">ページ1</a> | <ul class="inner"><li class="innerRow"><a href="/page2">ページ2</a></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => 'ul',
			'rowTag' => 'li',
			'innerTag' => 'ul',
			'innerRowTag' => 'li'
		);
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs('ホーム', $options);
		$this->assertIdentical($result, '<ul><li><a href="/">ホーム</a> | <ul><li><a href="/page1">ページ1</a> | <ul><li><a href="/page2">ページ2</a></li></ul></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => array('ul', 'outer'),
			'rowTag' => array('li', 'row'),
			'innerTag' => array('ul', 'inner'),
			'innerRowTag' => array('li', 'innerRow')
		);
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs(false, $options);
		$this->assertIdentical($result, '<ul class="outer"><li class="row"><a href="/page1">ページ1</a> | <ul class="inner"><li class="innerRow"><a href="/page2">ページ2</a></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'escape' => true
		);
		$result = $this->MyHtml->getCrumbs('"ホーム"', $options);
		$this->assertIdentical($result, '<ul><li><a href="/">&quot;ホーム&quot;</a></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'escape' => false
		);
		$result = $this->MyHtml->getCrumbs('"ホーム"', $options);
		$this->assertIdentical($result, '<ul><li><a href="/">"ホーム"</a></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'escape' => false
		);
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs('"ホーム"', $options);
		$this->assertIdentical($result, '<ul><li><a href="/">"ホーム"</a><ul><li><a href="/page1">ページ1</a><ul><li><a href="/page2">ページ2</a></li></ul></li></ul></li></ul>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => null,
			'rowTag' => null,
			'innerTag' => null,
			'innerRowTag' => null
		);
		$this->MyHtml->addCrumb('ページ1', '/page1');
		$this->MyHtml->addCrumb('ページ2', '/page2');
		$result = $this->MyHtml->getCrumbs('ホーム', $options);
		$this->assertIdentical($result, '<a href="/">ホーム</a> | <a href="/page1">ページ1</a> | <a href="/page2">ページ2</a>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => null,
			'rowTag' => null,
			'innerTag' => null,
			'innerRowTag' => null
		);
		$result = $this->MyHtml->getCrumbs('ホーム', $options);
		$this->assertIdentical($result, '<a href="/">ホーム</a>');

		$this->MyHtml->_crumbs = array();
		$options = array(
			'separator' => ' | ',
			'outerTag' => 'p',
			'rowTag' => null,
			'innerTag' => null,
			'innerRowTag' => null
		);
		$result = $this->MyHtml->getCrumbs('ホーム', $options);
		$this->assertIdentical($result, '<p><a href="/">ホーム</a></p>');
	}
}
