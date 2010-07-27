<?php
App::import('Helper', 'Html');

/**
 * MyHtmlヘルパー
 *
 * Htmlヘルパーの拡張
 */
class MyHtmlHelper extends HtmlHelper
{
	/**
	 * HtmlHelper::tag()の拡張
	 *
	 * $nameが空の場合はテキストのみを返す
	 *
	 * @param string $name 要素型名
	 * @param string $text テキスト
	 * @param array $options オプション
	 * @return string HTML
	 */
	public function tag($name, $text = null, $options = array()) {
		if (is_array($options) && isset($options['escape']) && $options['escape']) {
			$text = h($text);
			unset($options['escape']);
		}
		if (!is_array($options)) {
			$options = array('class' => $options);
		}
		if ($text === null) {
			$tag = 'tagstart';
		} else {
			$tag = 'tag';
		}
		// 変更
		return (!empty($name)) ? sprintf($this->tags[$tag], $name, $this->_parseAttributes($options, null, ' ', ''), $text, $name) : $text;
	}

	/**
	 * HtmlHelper::image()の拡張
	 *
	 * リンク生成機能を追加
	 *
	 * $myHtml->image(URI, array('url' => URI));
	 * $myHtml->image(URI, array('url' => array(URI, a要素のオプション)));
	 * @param string $path URI
	 * @param array $options オプション
	 * @return string HTML
	 */
	public function image($path, $options = array()) {
		if (is_array($path)) {
			$path = $this->url($path);
		} elseif (strpos($path, '://') === false) {
			if ($path[0] !== '/') {
				$path = IMAGES_URL . $path;
			}
			$path = $this->assetTimestamp($this->webroot($path));
		}

		if (!isset($options['alt'])) {
			$options['alt'] = '';
		}

		$url = false;
		if (!empty($options['url'])) {
			$url = $options['url'];
			unset($options['url']);
		}

		$image = sprintf($this->tags['image'], $path, $this->_parseAttributes($options, null, '', ' '));

		if ($url) {
			// 変更
			if (is_array($url)) {
				return sprintf($this->tags['link'], $this->url($url[0]), $this->_parseAttributes($url[1]), $image);
			}
			else {
				return sprintf($this->tags['link'], $this->url($url), null, $image);
			}
		}
		return $image;
	}

	/**
	 * HtmlHelper::getCrumbs()の拡張
	 *
	 * パンくずリストを返す
	 *
	 * @param string $startText トップページリンクのテキスト
	 * @param array $options オプション
	 * @return string HTML
	 */
	public function getCrumbs($startText = false, $options = array()) {
		// オプションを調整
		$array_options = array('outerTag', 'rowTag', 'innerTag', 'innerRowTag');
		foreach ($array_options as $option) {
			if (isset($options[$option]) && !is_array($options[$option])) {
				$options[$option] = array($options[$option], array());
			}

			if (isset($options[$option][1]) && !is_array($options[$option][1])) {
				$options[$option][1] = array('class' => $options[$option][1]);
			}
		}

		$__options = array(
			'escape' => true,
			'separator' => '',
			'outerTag' => array('ul', array()),
			'rowTag' => array('li', array()),
			'innerTag' => array('ul', array()),
			'innerRowTag' => array('li', array())
		);
		$options = array_merge($__options, $options);

		// 要素がある場合
		if (!empty($this->_crumbs)) {
			$items = array();

			// 要素を生成
			if ($startText) {
				$items[] = $this->link($startText, '/', array('escape' => $options['escape'])) . $options['separator'];
			}
			foreach ($this->_crumbs as $index => $crumb) {
				if (!empty($crumb[1])) {
					$items[] = $this->link($crumb[0], $crumb[1], $crumb[2]) . (isset($crumb[$index + 1]) ? $options['separator'] : '');
				} else {
					if (isset($crumb[2]['escape']) && !$crumb[2]['escape']) {
						$items[] = $crumb[0] . (isset($crumb[$index + 1]) ? $options['separator'] : '');
					} else {
						$items[] = h($crumb[0]) . (isset($crumb[$index + 1]) ? $options['separator'] : '');
					}
				}
			}

			// 要素をマークアップ
			$items = array_reverse($items);
			$html = '';
			foreach ($items as $index => $item) {
				if (isset($items[$index + 1]) !== false) {
					if (!empty($options['innerRowTag'][0])) {
						$html = sprintf($this->tags['tag'], $options['innerRowTag'][0], $this->_parseAttributes($options['innerRowTag'][1]), $item . $html, $options['innerRowTag'][0]);
					} else {
						$html = $item . $html;
					}
					if (!empty($options['innerTag'][0])) $html = sprintf($this->tags['tag'], $options['innerTag'][0], $this->_parseAttributes($options['innerTag'][1]), $html, $options['innerTag'][0]);
				} else {
					if (!empty($options['rowTag'][0])) {
						$html = sprintf($this->tags['tag'], $options['rowTag'][0], $this->_parseAttributes($options['rowTag'][1]), $item . $html, $options['rowTag'][0]);
					} else {
						$html = $item . $html;
					}
					if (!empty($options['outerTag'][0]))$html = sprintf($this->tags['tag'], $options['outerTag'][0], $this->_parseAttributes($options['outerTag'][1]), $html, $options['outerTag'][0]);
				}
			}

			return $this->output($html);
		}
		// トップページへのリンクのみの場合
		elseif (!empty($startText))
		{
			if (!empty($options['rowTag'][0])) {
				$html = sprintf($this->tags['tag'], $options['rowTag'][0], $this->_parseAttributes($options['rowTag'][1]), $this->link($startText, '/', array('escape' => $options['escape'])), $options['rowTag'][0]);
			} else {
				$html = $this->link($startText, '/', array('escape' => $options['escape']));
			}
			if (!empty($options['outerTag'][0])) $html = $this->tag($options['outerTag'][0], $html, $options['outerTag'][1]);
			return $this->output($html);
		}
		else
		{
			return '';
		}
	}
}
