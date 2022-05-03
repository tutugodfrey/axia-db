<?php

App::uses('AppModel', 'Model');

/**
 * TimelineItem Model
 *
 */
class TimelineItem extends AppModel {

/**
 * Timeline Item
 *
 * @var UUID
 */
	const APPROVED = 'bf5b7135-f0c0-4ef2-a638-58e2b357bb66';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const CONTACT_MERCH = '16490e3c-b6ee-46bb-9f1d-6853ec2d9f24';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const DAYS_TO_INSTALL = '9ea45748-d05f-491f-9d5c-c77e2c66bae8';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const DECLINED = '320c49d0-631a-4fb2-a428-332099904692';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const DOWNLOAD = 'e6ebcd50-60f6-4daf-a752-4cac0334fec3';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const EQUIPMENT_RECIEVED = '2d34d29e-3171-4933-b23c-313b4be4c013';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const FILE_BUILT = '2ce06a07-4572-4616-900b-7944fac74fc7';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const FOLLOWUP_CALL = '611580af-0aea-426c-a859-3d1b9a3480b9';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const INSTALL_COMMISSIONED = 'a9130700-0813-4a86-80cb-19796a054c5c';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const GO_LIVE_DATE = '17b613f5-58c6-4dcc-b081-a060387d0108';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const EXPECTED_TO_GO_LIVE = 'bc8feb41-d006-4d07-8bc1-dafc30fafedc';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const KEYED = '77ef5361-0f16-48f6-985c-45afa5df0044';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const KIT_SENT = '0a84d63f-6976-4764-97fb-236deaded207';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const MONTH_PAID = 'f8014e00-1acc-409e-8cc9-a9ac691b4cb4';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const ORDER_EQUIPMENT = '04cca8b8-d686-41a2-9540-61eba8514ba3';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const RECIEVED_SIGNED_INSTALL_SHEET = '572b98ae-cef7-45a7-bdb8-601866e6517f';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const RECIEVED = 'f14126a0-edf6-4e41-a6eb-2a88cb39c09b';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const SIGNED = '9eff98d4-621e-4ed2-adfb-73eaacbcc38f';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const SUBMITTED = 'a2f754b7-dbbc-4249-bd2c-d129815ac4c7';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const TESTED = '3fb9e110-1110-4177-84f5-4acd79359960';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const TERMS_AND_CONDITION_SENT = '51c3b92a-d0d8-45a9-93ee-4045ed7d3c0d';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const SOLD_WOMPLY = '7a8cdf74-ef19-469e-8b84-3c6bbcfc345b';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const CANCELLED_WOMPLY = 'e79df2f4-957f-48e2-b0b2-1aac4cb27270';

/**
 * Timeline Item
 *
 * @var UUID
 */
	const AGREEMENT_ENDS = '33bdf1bf-d0e1-4cd7-b055-2a2b31701e23';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'timeline_item_description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Item description already exists, you must enter an unique one!',
				'allowEmpty' => false,
			),
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => true,
                'allowEmpty' => false,
            )
		)
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'TimelineEntry' => array(
			'className' => 'TimelineEntry',
			'foreignKey' => 'timeline_item_id'
		)
	);

/**
 * Function getTimelineItemList
 *
 * @return array
 */
	public function getTimelineItemList() {
		return $this->find('list', array('fields' => array('id', 'timeline_item_description')));
	}

}
