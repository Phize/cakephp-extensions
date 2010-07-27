<?php
/**
 * 整形ビヘイビア
 * モデルのformatプロパティに定義された整形ルールを基に、バリデーション前にモデルのデータを整形する
 *
 * <code>
 * public $format = array(
 *	'name'		=>	'mb_trim',
 * 	'alias'		=>	'mb_trim',
 * 	'keyword'	=>	'keywords',
 * );
 * </code>
 *
 * 複数のルールを適用する場合
 * <code>
 * public $format = array(
 *	'name'		=>	array('mb_trim', ...)
 * );
 * </code>
 *
 * ルールのパラメーターを指定する場合
 * <code>
 * public $format = array(
 *	'name'		=>	array(
 *						'mt_trim',
 *						array('custom', $param1, ...)
 *					)
 * );
 * </code>
 *
 * 整形後のデータをコントローラー、ビューで再利用する場合は、モデルのデータをコントローラーのデータにマージする
 * <code>
 * if ($this->{$this->modelClass}->validates()) {
 *	$this->data = array_merge($this->data, $this->{$this->modelClass}->data);
 *	$this->{$this->modelClass}->save($this->data, false);
 * }
 * </code>
 */
class FormatBehavior extends ModelBehavior
{
	/**
	 * セットアップ
	 *
	 * @param AppModel モデルインスタンス
	 */
	public function setup(&$Model, $config = array())
	{
	}

	/**
	 * バリデーション前のコールバックメソッド
	 *
	 * @param AppModel モデルインスタンス
	 * @todo 独立したメソッド(アソシエーション対応)に変更する
	 */
	public function beforeValidate(&$Model)
	{
		$data = $Model->data;
		$modelMethods = array_map('strtolower', get_class_methods($Model));
		$methods = array_map('strtolower', get_class_methods($this));

		if (isset($data[$Model->alias])) {
			$data = $data[$Model->alias];
		} elseif (!is_array($data)) {
			$data = array();
		}

		foreach ($Model->format as $fieldName => $ruleSet)
		{
			if (!array_key_exists($fieldName, $data))
			{
				continue;
			}

			if (!is_array($ruleSet))
			{
				$ruleSet = array($ruleSet);
			}

			foreach ($ruleSet as $formatter)
			{
				if (is_array($formatter))
				{
					$rule = $formatter[0];
					unset($formatter[0]);
					$ruleParams = array_merge(array($data[$fieldName]), array_values($formatter));
				}
				else
				{
					$rule = $formatter;
					$ruleParams = array($data[$fieldName]);
				}

				if (in_array(strtolower($rule), $modelMethods))
				{
					$ruleParams[0] = array($fieldName => $ruleParams[0]);
					$valid = $Model->dispatchMethod($rule, $ruleParams);
				}
				elseif (in_array(strtolower($rule), $methods))
				{
					$ruleParams[0] = array($fieldName => $ruleParams[0]);
					$valid = $this->dispatchMethod($Model, $rule, $ruleParams);
				}
			}
		}

		return true;
	}

	/**
	 * 空白文字のトリム
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @return void
	 */
	public function trim(&$Model, $data)
	{
		$field = key($data);

		$Model->data[$Model->alias][$field] = trim($Model->data[$Model->alias][$field]);
	}

	/**
	 * 空白文字のトリム(マルチバイト対応)
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @param string $charset 文字エンコーディング
	 * @return void
	 */
	public function mb_trim(&$Model, $data, $charset = null)
	{
		$field = key($data);

		if (empty($charset)) {
			$charset = Configure::read('App.encoding');
		}

		if (empty($charset)) {
			$charset = 'UTF-8';
		}

		mb_regex_encoding($charset);

		$whitespace = '[\0\s]';
		$Model->data[$Model->alias][$field] = mb_ereg_replace(sprintf('(^%s+|%s+$)', $whitespace, $whitespace), '', $Model->data[$Model->alias][$field]);
	}

	/**
	 * キーワードの区切り文字を整形
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @param string $separator セパレーター
	 * @return void
	 */
	public function keywords(&$Model, $data, $separator = ' ', $charset = null)
	{
		$this->mb_trim($Model, $data, $charset);

		if ($separator === ' ' || $separator === '　')
		{
			$this->replace($Model, $data, '[\0\s]+', $separator);
		}
		else
		{
			$this->replace($Model, $data, '[\0\s]+' . $separator . '[\0\s]+', $separator);
		}
	}

	/**
	 * 文字列の置換
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @param string $search 検索文字列(正規表現)
	 * @param string $replace 置換文字列(正規表現)
	 * @return void
	 */
	public function replace(&$Model, $data, $search, $replace, $charset = null)
	{
		$field = key($data);

		if (empty($charset)) {
			$charset = Configure::read('App.encoding');
		}

		if (empty($charset)) {
			$charset = 'UTF-8';
		}

		mb_regex_encoding($charset);

		$Model->data[$Model->alias][$field] = mb_ereg_replace($search, $replace, $Model->data[$Model->alias][$field]);
	}
}
