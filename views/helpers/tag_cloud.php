<?php
/**
 * タグクラウドヘルパー
 *
 * 以下の形式の連想配列からタグクラウドを生成
 * array(
 *		// タグ => タグ数
 *		'tag1' => 100,
 *		'tag2' => 200
 * )
 */
class TagCloudHelper extends AppHelper {
	/**
	 * タグのランクを算出
	 *
	 * 結果として以下の形式の連想配列を返す。
	 * array(
	 *		// タグ => array(
	 *		//	'score' => タグ数,
	 *		//	'rank' => タグのランク,
	 *		// )
	 *		'tag1' => array(
	 *			'score' => 100,
	 *			'rank' => 1
	 *		),
	 *		'tag2' => array(
	 *			'score' => 200,
	 *			'rank' => 25
	 *		)
	 * )
	 *
	 * 以下のオプションを指定することができる。
	 * min			タグランクの最小値
	 * max			タグランクの最大値
	 * sort			タグの並び替えオプション
	 *	key			タグの並び替えキー ('tag' = タグ, 'score' => スコア)
	 *	direction	タグの並び替え方向 ('asc' = 昇順, 'desc' = 降順)
	 * filter		タグのフィルタリングオプション (並び替え後に指定した件数が抽出される)
	 *	threshold	タグ件数の閾値 (指定した件数未満のタグが除去される)
	 *	direction	タグの並び替え方向 ('asc' = 昇順, 'desc' = 降順)
	 *				(並び替えは抽出のための一時的なもの)
	 *	limit		タグの件数
	 *
	 * @param array $tags タグのデータ
	 * @param array $options オプション
	 * @return array 算出後のデータ
	 */
	public function calculate($tags, $options = array()) {
		$defaults = array(
			'min' => 1,
			'max' => 25,
			'sort' => array(
				'key' => null,
				'direction' => null
			),
			'filter' => array(
				'threshold' => 1,
				'direction' => 'desc',
				'limit' => null
			)
		);
		$options = Set::merge($defaults, $options);

		if ($options['filter'] !== null && isset($options['filter']['threshold']) && $options['filter']['threshold'] !== null) {
			$tags = $this->_prune($tags, $options['filter']['threshold']);
		}
		if ($options['filter'] !== null && isset($options['filter']['limit']) && $options['filter']['limit'] !== null) {
			$tags = $this->_filter($tags, $options['filter']['limit'], $options['filter']['direction']);
		}
		if ($options['sort'] !== null && isset($options['sort']['key']) && isset($options['sort']['direction'])
				&& $options['sort']['key'] !== null && $options['sort']['direction'] !== null) {
			$tags = $this->_sort($tags, $options['sort']['direction'], $options['sort']['key']);
		}
		if (empty($tags)) return array();

		$rates = $this->_calculateRate($tags);

		$newTags = array();
		foreach ($rates as $tag => $rate) {
			$newTags[$tag]['score'] = $tags[$tag];
			$newTags[$tag]['rank'] = (int) round($rate * ($options['max'] - $options['min']) + $options['min']);
		}

		return $newTags;
	}

	/**
	 * タグを剪定
	 *
	 * 最小スコア未満のタグを除去する。
	 *
	 * @param array $tags タグのデータ
	 * @param integer $minScore 最小スコア
	 * @return array 剪定後のデータ
	 */
	protected function _prune($tags, $minScore = 1) {
		foreach ($tags as $tag => $score) {
			if ($score < $minScore) unset($tags[$tag]);
		}

		return $tags;
	}

	/**
	 * タグをスコアで並び替え
	 *
	 * @param array $tags タグのデータ
	 * @param string $direction 並び替えの方向 ('asc' = 昇順, 'desc' = 降順)
	 * @param string $key 並び替えのキー ('tag' = タグ, 'score' => スコア)
	 * @return array 並び替え後のデータ
	 */
	protected function _sort($tags, $direction = 'desc', $key = 'score') {
		$direction = strtolower($direction);
		$key = strtolower($key);

		switch ($key) {
			case 'score':
				switch ($direction) {
					case 'asc':
						asort($tags);
						break;
					case 'desc':
						arsort($tags);
						break;
					default:
						break;
				}
				break;
			case 'tag':
				switch ($direction) {
					case 'asc':
						uksort($tags, 'strnatcasecmp');
						break;
					case 'desc':
						if (uksort($tags, 'strnatcasecmp')) {
							$tags = array_reverse($tags, true);
						}
						break;
					default:
						break;
				}
				break;
			default:
				break;
		}

		return $tags;
	}

	/**
	 * タグの件数を限定
	 *
	 * @param array $tags タグのデータ
	 * @param integer $limit 件数
	 * @param string $direction 並び替えの方向 ('asc' = 昇順, 'desc' = 降順)
	 * @return array 限定後のデータ
	 */
	protected function _filter($tags, $limit, $direction = 'desc') {
		$_tags = $tags;
		$tags = $this->_sort($tags, $direction, 'score');
		$tags = array_slice($tags, 0, $limit);
		$tags = array_intersect_key($_tags, $tags);

		return $tags;
	}

	/**
	 * タグのレートを算出
	 *
	 * @param array $tags タグのデータ
	 * @return array 算出後のデータ
	 */
	protected function _calculateRate($tags) {
		$max = max($tags);
		$min = min($tags);

		if ($max - $min == 0) {
			foreach ($tags as $tag => $score) {
				$tags[$tag] = 0.5;
			}

			return $tags;
		}

		$sqrtMax = sqrt($max);
		$sqrtMin = sqrt($min);

		foreach ($tags as $tag => $score) {
			$sqrtScore = sqrt($score);
			$tags[$tag] = ($sqrtScore - $sqrtMin) / ($sqrtMax - $sqrtMin);
		}

		return $tags;
	}

	/**
	 * タグクラウドをシャッフル
	 *
	 * @param array $tags タグのデータ
	 * @return array シャッフル後のデータ
	 */
	public function shuffle($tags) {
		$newTags = array();
		while (count($tags) > 0) {
			$tag = array_rand($tags);
			$newTags[$tag] = $tags[$tag];
			unset($tags[$tag]);
		}

		return $newTags;
	}
}
