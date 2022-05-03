<?php

class SyncRecordBehavior extends ModelBehavior {

	public $settings = array(
			  'tables' => array(),
	);

	public function synchronize() {
		$records = $Model->find('all', array(
				  'contain' => array(),
				  'fields' => array(
							$Model->alias . '.' . $foreignKey
				  )
		));

		$recordIds = Set::extract('/' . $Model->alias . '/' . $foreignKey);

		$newRecords = $Model->{$table}->find('all', array(
				  'contain' => array(),
				  'conditions' => array(
							$table . '.' . $Model->{$table}->primaryKey . '!=' => $recordIds
				  ),
				  'fields' => array(
							$table . '.' . $Model->{$table}->primaryKey,
				  )
		));

		$recordIds = Set::extract('/' . $table . '/' . $Model->{$table}->primaryKey);
	}

	public function createRecords(Model $Model, $foreignKeys) {
		foreach ($foreignKeys as $key) {
			$Model->create();
			$Model->save(array(
					  $Model->alias => array(
					  )
					)
			);
		}
	}

}
