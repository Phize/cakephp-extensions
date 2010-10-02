<?php
App::import('Controller', 'Controller', false);
App::import('Helper', 'Html');
App::import('Helper', 'SocialMedia');

class TestSocialMediaHelper extends SocialMediaHelper {
	public function test_convert_encoding($text) {
		return $this->_convert_encoding($text);
	}

	public function test_getTitle() {
		return $this->_getTitle();
	}

	public function test_getUri($uri = null) {
		return $this->_getUri($uri);
	}
}

class SocialMediaHelperTestCase extends CakeTestCase {
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
		$this->SocialMedia =& new TestSocialMediaHelper();
		$this->SocialMedia->Html =& new HtmlHelper();
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->SocialMedia);
	}

	/**
	 * _convert_encoding()のテスト
	 **/
	public function test_convert_encoding() {
		$text = 'テスト';
		$expected = 'テスト';
		$result = $this->SocialMedia->test_convert_encoding($text);
		$this->assertIdentical($result, $expected);
	}

	/**
	 * title()のテスト
	 **/
	public function testTitle() {
		$expected = $this->SocialMedia;
		$result = $this->SocialMedia->title();
		$this->assertIdentical($result, $expected);

		$expected = $this->SocialMedia;
		$result = $this->SocialMedia->title('タイトル');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _getTitle()のテスト
	 **/
	public function test_getTitle() {
		$expected = null;
		$result = $this->SocialMedia->test_getTitle();
		$this->assertIdentical($result, $expected);

		$expected = 'タイトル';
		$this->SocialMedia->title('タイトル');
		$result = $this->SocialMedia->test_getTitle();
		$this->assertIdentical($result, $expected);
	}

	/**
	 * uri()のテスト
	 **/
	public function testUri() {
		$expected = $this->SocialMedia;
		$result = $this->SocialMedia->uri();
		$this->assertIdentical($result, $expected);

		$expected = $this->SocialMedia;
		$result = $this->SocialMedia->uri('http://example.com');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * _getUri()のテスト
	 **/
	public function test_getUri() {
		$expected =  Router::url(null, true);
		$result = $this->SocialMedia->test_getUri();
		$this->assertIdentical($result, $expected);

		$expected = 'http://example.com';
		$this->SocialMedia->uri('http://example.com');
		$result = $this->SocialMedia->test_getUri();
		$this->assertIdentical($result, $expected);

		$expected = 'http://example.com';
		$result = $this->SocialMedia->test_getUri('http://example.com');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * hatena()のテスト
	 **/
	public function testHatena() {
		$expected = '<a href="http://b.hatena.ne.jp/add?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->hatena('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://b.hatena.ne.jp/add?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->hatena('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * delicious()のテスト
	 **/
	public function testDelicious() {
		$expected = '<a href="http://www.delicious.com/save?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->delicious('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://www.delicious.com/save?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->delicious('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://www.delicious.com/save?url=http%3A%2F%2Fexample.com&amp;title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->delicious('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * livedoor()のテスト
	 **/
	public function testLivedoor() {
		$expected = '<a href="http://clip.livedoor.com/clip/add?link=http%3A%2F%2Fexample.com&amp;ie=utf8">ブックマーク</a>';
		$result = $this->SocialMedia->livedoor('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://clip.livedoor.com/clip/add?link=http%3A%2F%2Fexample.com&amp;ie=utf8">ブックマーク</a>';
		$result = $this->SocialMedia->livedoor('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://clip.livedoor.com/clip/add?link=http%3A%2F%2Fexample.com&amp;title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB&amp;ie=utf8">ブックマーク</a>';
		$result = $this->SocialMedia->livedoor('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * fc2()のテスト
	 **/
	public function testFc2() {
		$expected = '<a href="http://bookmark.fc2.com/user/post?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->fc2('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://bookmark.fc2.com/user/post?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->fc2('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://bookmark.fc2.com/user/post?url=http%3A%2F%2Fexample.com&amp;title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->fc2('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * yahoo()のテスト
	 **/
	public function testYahoo() {
		$expected = '<a href="http://bookmarks.yahoo.co.jp/action/bookmark?u=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->yahoo('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://bookmarks.yahoo.co.jp/action/bookmark?u=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->yahoo('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://bookmarks.yahoo.co.jp/action/bookmark?u=http%3A%2F%2Fexample.com&amp;t=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->yahoo('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * nifty()のテスト
	 **/
	public function testNifty() {
		$expected = '<a href="http://clip.nifty.com/create?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->nifty('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://clip.nifty.com/create?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->nifty('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://clip.nifty.com/create?url=http%3A%2F%2Fexample.com&amp;title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->nifty('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * buzzurl()のテスト
	 **/
	public function testBuzzurl() {
		$expected = '<a href="http://buzzurl.jp/config/add/confirm?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->buzzurl('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://buzzurl.jp/config/add/confirm?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->buzzurl('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://buzzurl.jp/config/add/confirm?url=http%3A%2F%2Fexample.com&amp;title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->buzzurl('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * choix()のテスト
	 **/
	public function testChoix() {
		$expected = '<a href="http://www.choix.jp/submit/?bookurl=http%3A%2F%2Fexample.com&amp;phase=1">ブックマーク</a>';
		$result = $this->SocialMedia->choix('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://www.choix.jp/submit/?bookurl=http%3A%2F%2Fexample.com&amp;phase=1">ブックマーク</a>';
		$result = $this->SocialMedia->choix('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://www.choix.jp/submit/?bookurl=http%3A%2F%2Fexample.com&amp;booktitle=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB&amp;phase=1">ブックマーク</a>';
		$result = $this->SocialMedia->choix('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * newsing()のテスト
	 **/
	public function testNewsing() {
		$expected = '<a href="http://newsing.jp/add?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->newsing('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://newsing.jp/add?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->newsing('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * topicIt()のテスト
	 **/
	public function testTopicIt() {
		$expected = '<a href="http://topic.nifty.com/up/add?mode=1&amp;topic_url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->topicIt('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://topic.nifty.com/up/add?mode=1&amp;topic_url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->topicIt('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://topic.nifty.com/up/add?mode=1&amp;topic_url=http%3A%2F%2Fexample.com&amp;topic_title=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB">ブックマーク</a>';
		$result = $this->SocialMedia->topicIt('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * coRichNews()のテスト
	 **/
	public function testCoRichNews() {
		$expected = '<a href="http://newsclip.corich.jp/clip/public_html/marklet.php?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->coRichNews('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://newsclip.corich.jp/clip/public_html/marklet.php?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->coRichNews('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * coRichBookmark()のテスト
	 **/
	public function testCoRichBookmark() {
		$expected = '<a href="http://bookmark.corich.jp/bookmark/public_html/marklet.php?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->coRichBookmark('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://bookmark.corich.jp/bookmark/public_html/marklet.php?url=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->coRichBookmark('ブックマーク');
		$this->assertIdentical($result, $expected);
	}

	/**
	 * twitter()のテスト
	 **/
	public function testTwitter() {
		$expected = '<a href="http://twitter.com/home?status=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->twitter('ブックマーク', 'http://example.com');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://twitter.com/home?status=http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->twitter('ブックマーク');
		$this->assertIdentical($result, $expected);

		$this->SocialMedia->title('タイトル');
		$this->SocialMedia->uri('http://example.com');
		$expected = '<a href="http://twitter.com/home?status=%E3%82%BF%E3%82%A4%E3%83%88%E3%83%AB%20http%3A%2F%2Fexample.com">ブックマーク</a>';
		$result = $this->SocialMedia->twitter('ブックマーク');
		$this->assertIdentical($result, $expected);
	}
}
