<?php
/**
 * MyPaginatorヘルパー
 *
 * Paginatorヘルパーの拡張
 *
 * @todo テストケースを作成
 */
class MyPaginatorHelper extends PaginatorHelper
{
	public $helpers = array('Html', 'Number');

	/**
	 * Paginator::counter()の拡張
	 *
	 * 数値をNumberヘルパーで整形して返す
	 *
	 * @param array $options オプション
	 * @return string テキスト
	 */
	public function counter($options = array()) {
		if (is_string($options)) {
			$options = array('format' => $options);
		}

		$options = array_merge(
			array(
				'model' => $this->defaultModel(),
				'format' => 'pages',
				'separator' => __(' of ', true)
			),
		$options);

		$paging = $this->params($options['model']);
		if ($paging['pageCount'] == 0) {
			$paging['pageCount'] = 1;
		}
		$start = 0;
		if ($paging['count'] >= 1) {
			$start = (($paging['page'] - 1) * $paging['options']['limit']) + 1;
		}
		$end = $start + $paging['options']['limit'] - 1;
		if ($paging['count'] < $end) {
			$end = $paging['count'];
		}

		switch ($options['format']) {
			case 'range':
				if (!is_array($options['separator'])) {
					$options['separator'] = array(' - ', $options['separator']);
				}
				$out = $start . $options['separator'][0] . $end . $options['separator'][1];
				$out .= $paging['count'];
			break;
			case 'pages':
				$out = $paging['page'] . $options['separator'] . $paging['pageCount'];
			break;
			default:
				// 変更
				$map = array(
					'%page%' => $this->Number->format($paging['page']),
					'%pages%' => $this->Number->format($paging['pageCount']),
					'%current%' => $this->Number->format($paging['current']),
					'%count%' => $this->Number->format($paging['count']),
					'%start%' => $this->Number->format($start),
					'%end%' => $this->Number->format($end)
				);
				$out = str_replace(array_keys($map), array_values($map), $options['format']);

				$newKeys = array(
					'{:page}', '{:pages}', '{:current}', '{:count}', '{:start}', '{:end}'
				);
				$out = str_replace($newKeys, array_values($map), $out);
			break;
		}
		return $out;
	}

	/**
	 * Paginator::__pagingLink()を拡張
	 *
	 * $disabledTitleがfalseの場合は空文字を返す
	 *
	 * @param string $which ページの方向 ('Prev', 'Next')
	 * @param string $title テキスト (有効時)
	 * @param array $options オプション (有効時)
	 * @param string $disabledTitle テキスト (無効時)
	 * @param array $disabledOptions オプション (無効時)
	 * @return <type>
	 */
	public function __pagingLink($which, $title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		$check = 'has' . $which;
		$_defaults = array(
			'url' => array(), 'step' => 1, 'escape' => true,
			'model' => null, 'tag' => 'span', 'class' => strtolower($which)
		);
		$options = array_merge($_defaults, (array)$options);
		$paging = $this->params($options['model']);
		if (empty($disabledOptions)) {
			$disabledOptions = $options;
		}

		if (!$this->{$check}($options['model']) && (!empty($disabledTitle) || !empty($disabledOptions))) {
			if (!empty($disabledTitle) && $disabledTitle !== true) {
				$title = $disabledTitle;
			}
			$options = array_merge($_defaults, (array)$disabledOptions);
		} elseif (!$this->{$check}($options['model'])) {
			return null;
		}

		foreach (array_keys($_defaults) as $key) {
			${$key} = $options[$key];
			unset($options[$key]);
		}
		$url = array_merge(array('page' => $paging['page'] + ($which == 'Prev' ? $step * -1 : $step)), $url);

		if ($this->{$check}($model)) {
			return $this->Html->tag($tag, $this->link($title, $url, array_merge($options, compact('escape', 'class'))));
		} else {
			// 追加
			if ($disabledTitle === false) {
				return '';
			}
			return $this->Html->tag($tag, $title, array_merge($options, compact('escape', 'class')));
		}
	}
}
