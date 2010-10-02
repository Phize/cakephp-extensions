<?php
/**
 * ソーシャルメディアヘルパー
 *
 * ソーシャルメディアへの投稿リンクを生成
 * 各ソーシャルメディア用のメソッドはHtmlHelper::link()とほぼ同様
 * SocialMediaHelper::title()で投稿するページのタイトルを、
 * SocialMediaHelper::uri()で投稿するページのURIを設定することも可能
 */
class SocialMediaHelper extends AppHelper {
	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = array('Html');

	/**
	 * 設定
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * タイトル
	 *
	 * @var string
	 */
	protected $_title = null;

	/**
	 * URI
	 *
	 * @var string|array
	 */
	protected $_uri = null;



	/**
	 * コンストラクター
	 *
	 * @param array $settings 設定
	 */
	function __construct($settings = array()) {
		parent::__construct();

		$defaults = array('charset' => Configure::read('App.encoding'));
		$settings = array_merge($defaults, $settings);

		if (empty($settings['charset'])) {
			$settings['charset'] = 'UTF-8';
		}

		$this->_settings = $settings;
	}

	/**
	 * 文字エンコーディングをUTF-8へ変換
	 *
	 * @param string $text 文字列
	 * @return string 文字エンコーディング変換語の文字列
	 */
	protected function _convert_encoding($text) {
		if ($this->_settings['charset'] !== 'UTF-8' && function_exists('mb_convert_encoding')) {
			$text = mb_convert_encoding($text, 'UTF-8', $this->_settings['charset']);
		}

		return $text;
	}

	/**
	 * タイトルを設定
	 *
	 * @param string $title タイトル
	 * @return SocialMediaHelper SocialMediaHelperオブジェクト
	 */
	public function title($title = null) {
		$this->_title = $title;

		return $this;
	}

	/**
	 * ページのタイトルを取得後、文字エンコーディングをUTF-8へ変換
	 *
	 * @return string タイトル
	 */
	protected function _getTitle() {
		$title = $this->_title;

		if ($title !== null) {
			$title = $this->_convert_encoding($title);
		}

		return $title;
	}

	/**
	 * URIを設定
	 *
	 * @param string|array $uri URI
	 * @return SocialMediaHelper SocialMediaHelperオブジェクト
	 */
	public function uri($uri = null) {
		$this->_uri = $uri;

		return $this;
	}

	/**
	 * URIを取得後、文字エンコーディングをUTF-8へ変換
	 *
	 * @param string|array URI
	 * @return string URI
	 */
	protected function _getUri($uri = null) {
		if ($uri === null) {
			$uri = $this->_uri;
		}

		$uri = Router::url($uri, true);

		if ($uri !== null) {
			$uri = $this->_convert_encoding($uri);
		}

		return $uri;
	}

	/**
	 * はてなブックマーク用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function hatena($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://b.hatena.ne.jp/add?url=' . rawurlencode($rawUri);

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Delicious用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function delicious($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://www.delicious.com/save?url=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&title=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Livedoorクリップ用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function livedoor($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://clip.livedoor.com/clip/add?link=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&title=' . rawurlencode($title) : '';
		$uri .= '&ie=utf8';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * FC2ブックマーク用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function fc2($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://bookmark.fc2.com/user/post?url=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&title=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Yahoo!ブックマーク用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function yahoo($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://bookmarks.yahoo.co.jp/action/bookmark?u=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&t=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * @Niftyクリップ用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function nifty($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://clip.nifty.com/create?url=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&title=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Buzzurl用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function buzzurl($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://buzzurl.jp/config/add/confirm?url=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&title=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Choix用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function choix($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://www.choix.jp/submit/?bookurl=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&booktitle=' . rawurlencode($title) : '';
		$uri .= '&phase=1';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Newsing用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function newsing($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://newsing.jp/add?url=' . rawurlencode($rawUri);

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * トピックイット用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function topicIt($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://topic.nifty.com/up/add?mode=1&topic_url=' . rawurlencode($rawUri);
		$uri .= ($title !== null) ? '&topic_title=' . rawurlencode($title) : '';

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * CoRichニュースクリップ!用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function coRichNews($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://newsclip.corich.jp/clip/public_html/marklet.php?url=' . rawurlencode($rawUri);

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * CoRichブックマーク!用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function coRichBookmark($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://bookmark.corich.jp/bookmark/public_html/marklet.php?url=' . rawurlencode($rawUri);

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}

	/**
	 * Twitter用のリンクを生成
	 *
	 * @param string $content a要素の内容
	 * @param string|array $rawUri URI
	 * @param array $options オプション
	 * @param boolean $confirmMessage JavaScriptによる確認メッセージの有無
	 * @return string HTML
	 */
	public function twitter($content, $rawUri = null, $options = array(), $confirmMessage = false) {
		$title = $this->_getTitle();
		$rawUri = $this->_getUri($rawUri);
		$uri = 'http://twitter.com/home?status=';
		$uri .= ($title !== null) ? rawurlencode($title . ' ') : '';
		$uri .= rawurlencode($rawUri);

		return $this->Html->link($content, $uri, $options, $confirmMessage);
	}
}
