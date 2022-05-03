<?php
App::uses('TimelineItem', 'Model');
class AddNewTimelineItem extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_new_timeline_item';

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
				'id' => TimelineItem::EXPECTED_TO_GO_LIVE,
				'timeline_item_description' => 'Expected to Install'
			]);

			if (!$saved) {
				echo 'Failed to save new timeline item';
				return false;
			}
		} else {
			$TimelineItem->delete(TimelineItem::EXPECTED_TO_GO_LIVE, false);
		}
		return true;
	}
}
