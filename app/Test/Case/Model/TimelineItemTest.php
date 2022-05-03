<?php
App::uses('TimelineItem', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * AddressType Test Case
 *
 */
class TimelineItemTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TimelineItem = ClassRegistry::init('TimelineItem');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TimelineItem);
		parent::tearDown();
	}

/**
 * testGetAllAddressTypeIds
 *
 * @return void
 */
	public function testGetTimelineItemList() {
		$expected = [
			'04cca8b8-d686-41a2-9540-61eba8514ba3' => 'Order Equip',
			'0a84d63f-6976-4764-97fb-236deaded207' => 'Kit Sent',
			'16490e3c-b6ee-46bb-9f1d-6853ec2d9f24' => 'Contact Merch',
			'17b613f5-58c6-4dcc-b081-a060387d0108' => 'Go-Live date',
			'2ce06a07-4572-4616-900b-7944fac74fc7' => 'File Built',
			'2d34d29e-3171-4933-b23c-313b4be4c013' => 'Equip Rec\'d',
			'320c49d0-631a-4fb2-a428-332099904692' => 'Declined',
			'3fb9e110-1110-4177-84f5-4acd79359960' => 'Tested',
			'51c3b92a-d0d8-45a9-93ee-4045ed7d3c0d' => 'Ts & Cs Sent',
			'572b98ae-cef7-45a7-bdb8-601866e6517f' => 'Rec\'d Signed Install Sheet',
			'611580af-0aea-426c-a859-3d1b9a3480b9' => 'Follow up call',
			'77ef5361-0f16-48f6-985c-45afa5df0044' => 'Keyed',
			'9ea45748-d05f-491f-9d5c-c77e2c66bae8' => 'Days to Go-Live',
			'9eff98d4-621e-4ed2-adfb-73eaacbcc38f' => 'Signed',
			'a2f754b7-dbbc-4249-bd2c-d129815ac4c7' => 'Submitted',
			'a9130700-0813-4a86-80cb-19796a054c5c' => 'Install Commissioned',
			'bf5b7135-f0c0-4ef2-a638-58e2b357bb66' => 'Approved',
			'e6ebcd50-60f6-4daf-a752-4cac0334fec3' => 'Download',
			'f14126a0-edf6-4e41-a6eb-2a88cb39c09b' => 'Received',
			'f8014e00-1acc-409e-8cc9-a9ac691b4cb4' => 'Month Paid',
			'bc8feb41-d006-4d07-8bc1-dafc30fafedc' => 'Expected to Go-Live'
		];
		$actual = $this->TimelineItem->getTimelineItemList();
		$this->assertEquals($expected, $actual);
	}
}
