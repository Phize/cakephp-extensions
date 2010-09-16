<?php
/**
 * コンフィギュレーションビヘイビア
 *
 * コア設定をデータベースから読み込み、コア設定とデータベースを更新
 * Model::validates()でのバリデーション、Model::whitelistに対応
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category	Behavior
 * @package		ConfigurationBehavior
 * @author		Phize
 * @copyright	2009-2010 Phize (http://phize.net/)
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @todo saveConfig()でのバリデーションに対応
 */
class ConfigurationBehavior extends ModelBehavior {
	/**
	 * ビヘイビアの設定
	 *
	 * @var array
	 */
	protected $config = array();



	/**
	 * セットアップ
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $config 設定
	 *						 'namespace' => コア設定のプレフィックス
	 *						 'key' => 設定名用のデータベースフィールド名
	 *						 'value' => 設定値用のデータベースフィールド名
	 * @todo ビヘイビアの読み込み時に、自動的にloadConfig()を呼び出す設定を追加(?)
	 */
	public function setup(&$Model, $config = array()) {
		$defaults = array('namespace' => 'App', 'key' => 'key', 'value' => 'value');

		$this->config[$Model->name] = isset($this->config[$Model->name]) ? $this->config[$Model->name] : $defaults;
		$this->config[$Model->name] = array_merge($this->config[$Model->name], $config);
	}

	/**
	 * 設定をデータベースから取得・コア設定に書き込み
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $array 結果をCakePHP形式の配列で返す場合は true
	 */
	public function loadConfig(&$Model, $array = false) {
		$configs = $Model->find('all', array(
			'fields' => array(
				$Model->alias . '.' . $this->config[$Model->name]['key'],
				$Model->alias . '.' . $this->config[$Model->name]['value']
			),
			'recursive' => -1
		));
		if (empty($configs)) return array();

		$data = array();
		foreach ($configs as $config) {
			$key = $config[$Model->alias][$this->config[$Model->name]['key']];
			$value = unserialize($config[$Model->alias][$this->config[$Model->name]['value']]);

			$data[$key] = $value;
			Configure::write($this->config[$Model->name]['namespace'] . '.' . $key, $value);
		}

		return ($array) ? array($Model->alias => $data) : $data;
	}

	/**
	 * コア設定を取得
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $array 結果をCakePHP形式の配列で返す場合は true
	 * @return mixed 設定
	 */
	public function getConfig(&$Model, $array = false) {
		$value = Configure::read($this->config[$Model->name]['namespace']);

		// 値を返す
		if (!$array) return $value;

		// 配列を返す
		if ($value === null) return array();
		$value = is_array($value) ? $value : array($value);
		$data = array();
		$data[$Model->alias] = $value;
		return $data;
	}

	/**
	 * データをコア設定とデータベースに保存
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @param array $data データ
	 * @param boolean $validate バリデーションを行う場合は true
	 * @param array $fieldList ホワイトリスト
	 * @return boolean 処理の成否
	 *
	 * @todo $validateへの対応
	 */
	public function saveConfig(&$Model, $data = null, $validate = true, $fieldList = array()) {
		$Model->set($data);
		if (empty($Model->data)) return false;

		$options = array('validate' => true, 'fieldList' => array());
		$_whitelist = $Model->whitelist;

		// オプションの設定
		if (is_array($validate)) {
			$options = array_merge($options, $validate);
		}
		else {
			$options = array_merge($options, compact('validate', 'fieldList'));
		}
		if (!empty($options['fieldList'])) {
			$Model->whitelist = $options['fieldList'];
		} elseif ($options['fieldList'] === null) {
			$Model->whitelist = array();
		}
		$Model->whitelist = is_array($Model->whitelist) ? $Model->whitelist : array($Model->whitelist);

		// ホワイトリストにあるフィールドのみを抽出
		$filteredData = array();
		$dateFields = array();
		foreach ($Model->data[$Model->alias] as $field => $value) {
			if (!empty($Model->whitelist) && !in_array($field, $Model->whitelist)) continue;
			switch ($field) {
				case 'created':
				case 'updated':
				case 'modified':
					$dateFields[$field] = $value;
					break;
				default:
					$filteredData[$field] = $value;
					break;
			}
		}
		if (empty($filteredData)) {
			$Model->whitelist = $_whitelist;
			return true;
		}

		// データベース更新用のデータを生成
		$settingsData = $Model->find('all', array(
			'fields' => array(
				$Model->alias . '.' . $Model->primaryKey,
				$Model->alias . '.' . $this->config[$Model->name]['key']
			),
//			'conditions' => array(),
			'recursive' => -1
		));
		$finalizedData = array();
		$row = 0;
		foreach ($filteredData as $key => $value) {
			$finalizedData[$row][$Model->alias] = array (
				$this->config[$Model->name]['key'] => $key,
				$this->config[$Model->name]['value'] => serialize($value)
			);
			$settingData = Set::extract('/' . $Model->alias . '[' . $this->config[$Model->name]['key'] . '=' . $key . ']', $settingsData);
			if (!empty($settingData)) {
				$finalizedData[$row][$Model->alias][$Model->primaryKey] = $settingData[0][$Model->alias][$Model->primaryKey];
			}
			if (!empty($dateFields)) {
				$finalizedData[$row][$Model->alias] = array_merge($finalizedData[$row][$Model->alias], $dateFields);
			}

			$row ++;
		}

		// 設定を更新
		$Model->whitelist = array();
//		if (!$Model->saveAll($finalizedData, array('validate' => 'only'))) return false;
		$Model->saveAll($finalizedData, array('validate' => false));
		Configure::write($this->config[$Model->name]['namespace'], $filteredData);
		$Model->whitelist = $_whitelist;

		return true;
	}
}
