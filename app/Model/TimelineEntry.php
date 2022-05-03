<?php

App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');

/**
 * TimelineEntry Model
 *
 * @property Merchant $Merchant
 */
class TimelineEntry extends AppModel {

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'SearchByMonthYear' => [
			'fulldateFieldName' => 'timeline_date_completed',
		],
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank')
			),
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant' => array(
			'className' => 'Merchant',
			'foreignKey' => 'merchant_id'
		),
		'TimelineItem' => array(
			'className' => 'TimelineItem',
			'foreignKey' => 'timeline_item_id'
		),
	);

/**
 * beforeSave callback
 *
 * @param array $options options for beforeSave
 * @return true
 */
	public function beforeSave($options = array()) {
		if ((Hash::get($this->data, 'TimelineEntry.timeline_item_id') === TimelineItem::EXPECTED_TO_GO_LIVE || Hash::get($this->data, 'TimelineEntry.timeline_item_id') === TimelineItem::GO_LIVE_DATE) && 
			!empty($this->data['TimelineEntry']['timeline_date_completed'])) {
			$externalId = $this->Merchant->ExternalRecordField->field('value', ['merchant_id' => $this->data['TimelineEntry']['merchant_id'], 'field_name' => SalesForce::ACCOUNT_ID]);
			if (!empty($externalId)) {
				if ($this->data['TimelineEntry']['timeline_item_id'] === TimelineItem::EXPECTED_TO_GO_LIVE) {
					ClassRegistry::init('SalesForce')->updateSalesforceField(SalesForce::EXPECTED_INST_DATE, $this->data['TimelineEntry']['timeline_date_completed'], $externalId);
				} elseif ($this->data['TimelineEntry']['timeline_item_id'] === TimelineItem::GO_LIVE_DATE) {
					ClassRegistry::init('SalesForce')->updateSalesforceField(SalesForce::INST_DATE, $this->data['TimelineEntry']['timeline_date_completed'], $externalId);
				}
			}
		}
		return true;
	}


/**
 * cleanRequestData method
 *
 * removes empty entries from array to avoid creating records with empty timeline_date_completed columns. Existing
 * records will not be removed.
 *
 * @param array $data data
 * @return array
 */
	public function cleanRequestData($data) {
		$result = array();
		if (array_key_exists('TimelineEntry', $data)) {
			//counter = length - 1
			foreach ($data['TimelineEntry'] as $key => $val) {
				/* Do not allow any new data to be saved if the date is empty except iff the date was cleared out from an existing entry */
				if ((!empty($data['TimelineEntry'][$key]['id'])) || (!empty($data['TimelineEntry'][$key]['timeline_date_completed']['month']) && !empty($data['TimelineEntry'][$key]['timeline_date_completed']['day']) && !empty($data['TimelineEntry'][$key]['timeline_date_completed']['year']))) {
					array_push($result, $data['TimelineEntry'][$key]);
				}
			}
		} else {
			//re-index
			$data = Hash::extract($data, '{n}');
			//counter = length - 1
			for ($x = count($data) - 1; $x >= 0; $x--) {
				/* Do not allow any new data to be saved if the date is empty except iff the date was cleared out from an existing entry */
				if ((!empty($data[$x]['TimelineEntry']['id'])) || (!empty($data[$x]['TimelineEntry']['timeline_date_completed']['month']) && !empty($data[$x]['TimelineEntry']['timeline_date_completed']['day']) && !empty($data[$x]['TimelineEntry']['timeline_date_completed']['year']))) {
					array_push($result, $data[$x]);
				}
			}
		}
		return $result;
	}

/**
 * Get timeline entries
 *
 * @param string $id Merchant.id
 * @return array
 */
	public function getTimelineEntries($id) {
		$contain = array('TimelineEntry' => array('conditions' => array('TimelineEntry.merchant_id' => $id)));

		return $this->TimelineItem->find('all', array(
				'recursive' => -1,
				'contain' => $contain,
				'order' => 'TimelineEntry.timeline_date_completed ASC NULLS last, TimelineItem.timeline_item_description ASC'
			)
		);
	}

/**
 * saveTimeEntry
 * Creates a new timeline entry or updates an existing one that matches data in first parameter.
 * 
 * @param array $data TimelineEntry data to save
 * @return array
 */
	public function saveTimeEntry($data) {
		if (array_key_exists($this->alias, $data)) {
			$data = $data[$this->alias];
		}
		$id = Hash::get($data, "id");

		if (empty($id)) {
			$merchantId = Hash::get($data, "merchant_id");
			$tItemId = Hash::get($data, "timeline_item_id");
			$id = $this->field('id', array(
				'merchant_id' => $merchantId,
				'timeline_item_id' => $tItemId
			));
		}

		if (!empty($id)) {
			$data['id'] = $id;
		} else {
			$this->create();
		}
		return $this->save($data);
	}

/**
 * removeDuplicates
 * Removes any entries that already exist from a previous save
 *
 * @param string $id Merchant.id
 * @return array
 */
	public function removeDuplicates($data) {
		if (!empty($data)) {
			foreach ($data as $idx => $entry) {
				//check for new data only
				if (empty(Hash::get($entry, 'TimelineEntry.id'))) {
					$entryExists = $this->hasAny([
						'merchant_id' => $entry['TimelineEntry']['merchant_id'],
						'timeline_item_id' => $entry['TimelineEntry']['timeline_item_id'],
						'timeline_date_completed IS NOT NULL'
					]);
					if ($entryExists) {
						unset($data[$idx]);
					}
				}
			}
		}
		return $data;
	}

}
