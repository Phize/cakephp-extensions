<?php
/**
 * SEOコンポーネント
 */
class SeoComponent extends Object {
	/**
	 * コントローラーインスタンス
	 *
	 * @var AppController
	 * @access private
	 */
	private $controller;

	/**
	 * オプション
	 *
	 * @var array
	 * @access public
	 */
	public $options = array();

	/**
	 * 初期化
	 *
	 * @param AppController $controller コントローラーインスタンス
	 * @param array $options オプション
	 *					'site_name' => サイト名
	 *					'site_desc' => サイトの説明
	 *					'separator' => セパレーター文字列
	 */
	public function initialize(&$controller, $options = array()) {
		$this->controller =& $controller;

		$defaults = array(
			'site_name' => null,
			'site_desc' => null,
			'separator' => ' | '
		);
		$this->options = array_merge($defaults, $options);
	}

	/**
	 * スタートアップ
	 *
	 * @param AppController $controller コントローラーインスタンス
	 */
	public function startup(&$controller) {
	}

	/**
	 * タイトルを生成
	 *
	 * @param string $pageTitle ページのタイトル
	 * @param array $options オプション
	 *					'site_name' => サイト名
	 *					'site_desc' => サイトの説明
	 *					'separator' => セパレーター文字列
	 */
	public function title($pageTitle = null, $options = array()) {
		$pageTitle = ($pageTitle != null) ? $this->mb_trim($pageTitle) : null;
		$options = array_merge($this->options, $options);

		// ページタイトルの設定
		if ($pageTitle === null) {
			if ($options['site_name'] !== null) {
				$pageTitle = $options['site_name'];
			}
			else {
				$pageTitle = '';
			}
		}

		// タイトルの生成
		if ($pageTitle === null || $pageTitle === $options['site_name']) {
			$title_for_layout = $options['site_desc'];
		}
		else {
			$title_for_layout = $pageTitle;
		}

		if (!$this->is_empty($options['site_name'])) {
			if (!$this->is_empty($title_for_layout)) {
				$title_for_layout .= $options['separator'];
			}

			$title_for_layout .= $options['site_name'];
		}

		$this->controller->set('title_for_layout', $title_for_layout);
		$this->controller->set('heading_for_layout', $pageTitle);
	}

	/**
	 * 空白文字のトリム(マルチバイト対応)
	 *
	 * @param string $string 対象となる文字列
	 * @param string $charset 文字エンコーディング
	 * @return string トリム後の文字列
	 */
	protected function mb_trim($string, $charset = null) {
		if (empty($charset)) {
			$charset = Configure::read('App.encoding');
		}

		if (empty($charset)) {
			$charset = 'UTF-8';
		}

		mb_regex_encoding($charset);

		$whitespace = '[\0\s]';
		$string = mb_ereg_replace(sprintf('(^%s+|%s+$)', $whitespace, $whitespace), '', $string);

		return $string;
	}

	/**
	 * 変数が空かどうかを判定
	 *
	 * @param mixed $var 変数
	 * @param boolean $allow_false false を空にしない場合は true
	 * @param boolean $allow_ws ホワイトスペースを空にしない場合は true
	 * @return boolean 変数が空の場合は true
	 */
	protected function is_empty($var, $allow_false = false, $allow_ws = false) {
		if (! isset ($var) || is_null($var) || ($allow_ws == false && $this->mb_trim($var) == '' && !is_bool($var)) || ($allow_false === false && is_bool($var) && $var === false) || (is_array($var) && empty($var))) {
			return true;
		}
		else {
			return false;
		}
	}
}
