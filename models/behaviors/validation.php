<?php
/**
 * 拡張バリデーションビヘイビア
 *
 * @todo テストケースを作成
 */
class ValidationBehavior extends ModelBehavior
{
	/**
	 * セットアップ
	 *
	 * @param AppModel $Model モデルインスタンス
	 */
	public function setup(&$Model, $config = array()) {
	}

	/**
	 * Datetime型の検証
	 *
	 * @param array $data データ
	 * @return boolean 検証の結果
	 */
	public function isDatetime(&$Model, $data) {
		$field = key($data);

		return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data[$field]) ? true : false;
	}

	/**
	 * キーが存在するか検証
	 *
	 * $modelは Model または Plugin.Model の形式で指定する
	 * 関連するテーブルのデータとともにsaveAll()でレコードを作成する場合は、
	 * 外部キーの参照先レコードが挿入される前に検証が行われる場合があるため、正しい検証が行えない
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @param string model モデル名
	 * @return boolean 検証の結果
	 */
	public function primaryKeyExists(&$Model, $data, $model) {
		$field = key($data);

		if (strpos($model, '.') !== false) {
			list($plugin, $class) = pluginSplit($model);
		}

		$result = $Model->{$class}->find('count',
			array(
				'conditions' => array($Model->{$class}->alias . '.' . $Model->{$class}->primaryKey => $data),
				'recursive' => -1
			)
		);

		return ($result > 0) ? true : false;
	}

	/**
	 * 複数フィールドの組み合わせがユニークか検証
	 *
	 * 正しい検証を行うには、$fieldsにarray(フィールド名 => 値)と指定するか、
	 * $fieldsにarray(フィールド名)と指定した場合に、フィールド全てのデータが$Model->dataに存在している必要がある
     * 指定したフィールドのデータが存在しない場合は、そのフィールド値はnullとして扱われる
	 * $fieldsで指定したフィールドを省略してデータを更新する場合や、
	 * $fieldsで指定したフィールドを省略してデータベースのデフォルト値を使用する場合には、正しい検証が行えない
	 * ただし、検証対象のフィールド群に外部キーのフィールドを含む場合で、
	 * かつ関連するテーブルのデータとともにsaveAll()でレコードを作成・更新する場合は、
	 * $Model->dataに外部キーのフィールドが自動的に追加されるため、外部キーのフィールドについては省略できる
	 *
	 * @param array $data データ
	 * @param array $fields フィールド名
	 * @return boolean 検証の結果
	 */
	public function isUniqueWith(&$Model, $data, $fields) {
		if (!is_array($fields)) $fields = array($fields);
		$fields = Set::merge($data, $fields);

		return $Model->isUnique($fields, false);
	}
}
