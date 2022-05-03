<?php
App::uses('TimelineItem', 'Model');
class AddAggreementEndTimelineItem extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_aggreement_end_timeline_item';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$TimelineItem = ClassRegistry::init('TimelineItem');
		if ($direction === 'up') {
			$saved = $TimelineItem->save([
				'id' => TimelineItem::AGREEMENT_ENDS,
				'timeline_item_description' => 'Agreement End'
			]);

			if (!$saved) {
				echo 'Failed to save new timeline item';
				return false;
			}
		} else {
			$TimelineItem->delete(TimelineItem::AGREEMENT_ENDS, false);
		}
		return true;
	}
}
