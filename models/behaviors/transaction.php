<?php
/**
 * トランザクションビヘイビア
 */
class TransactionBehavior extends ModelBehavior {
	/**
	 * セットアップ
	 *
	 * @param AppModel $Model モデルインスタンス
	 */
	public function setup(&$Model, $config = array()) {

	}

	/**
	 * トランザクションの開始
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @return boolean 処理の成否
	 */
	public function beginTransaction(&$Model) {
		return $Model->getDataSource()->begin($Model);
	}

	/**
	 * トランザクションのコミット
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @return boolean 処理の成否
	 */
	public function commitTransaction(&$Model) {
		return $Model->getDataSource()->commit($Model);
	}

	/**
	 * トランザクションのロールバック
	 *
	 * @param AppModel $Model モデルインスタンス
	 * @return boolean 処理の成否
	 */
	public function rollbackTransaction(&$Model) {
		return $Model->getDataSource()->rollback($Model);
	}
}
