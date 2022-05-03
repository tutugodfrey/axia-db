<?php
App::uses('Ach', 'Model');

/**
 * Ach Test Case
 */
class AchTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.ach',
		'app.merchant',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Ach = ClassRegistry::init('Ach');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Ach);

		parent::tearDown();
	}

/**
 * testGetDataById method
 *
 * @return void
 */
	public function testGetDataById() {
		//get data with truncated bank accounts
		$data = $this->Ach->getDataById('55cc6dd8-1d68-4c4c-b3cb-18aa34627ad4', true);
		$this->assertNotEmpty($data['Ach']['id']);
		$this->assertContains('xxxxx6789', $data['Ach']);

		//get data with whole bank accounts
		$data = $this->Ach->getDataById('55cc6dd8-1d68-4c4c-b3cb-18aa34627ad4', false);
		$this->assertNotEmpty($data['Ach']['id']);
		$this->assertNotContains('xxxxx6789', $data['Ach']);
		$this->assertEquals($data['Ach']['ach_mi_w_dsb_routing_number'], '123456789');
		$this->assertEquals($data['Ach']['ach_mi_w_dsb_account_number'], '123456789');
	}

/**
 * testTruncateAccounts method
 *
 * @return void
 */
	public function testTruncateAccounts() {
		$data['Ach']['ach_ci_nw_rej_account_number'] = 123456789;
		$data['Ach']['ach_ci_nw_rej_routing_number'] = 987654321;
		$this->Ach->truncateAccounts($data);
		$this->assertNotContains('123456789', $data['Ach']);
		$this->assertNotContains('987654321', $data['Ach']);
		$this->assertContains('xxxxx6789', $data['Ach']);
		$this->assertContains('xxxxx4321', $data['Ach']);
	}

/**
 * testTruncateAccounts method
 *
 * @return void
 */
	public function testEncyptData() {
		$tstData = [
			'Ach' => [
				'ach_mi_w_dsb_routing_number' => '123456789',
				'ach_mi_w_dsb_account_number' => '987654321',
				'ach_mi_w_fee_routing_number' => '0123654789,',
				'ach_mi_w_fee_account_number' => '987456321',
				'ach_mi_w_rej_routing_number' => '789456123',
				'ach_mi_w_rej_account_number' => '987654321'
			]
		];
		$expected = [
			'Ach' => [
				'ach_mi_w_dsb_routing_number' => 'tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==',
				'ach_mi_w_dsb_account_number' => 'tdGYA+LVUNxE4dHOjlpt6JNTBFDdMtEgQJbGaKGseqkKnUZPG0jLKXXrCPaCwu3R4owy5mycYi6UkfFj9UTHeg==',
				'ach_mi_w_fee_routing_number' => 'tdGYA+LVUNxE4dHOjlpt6FowY8Hpb/eDnZpkD430V7PAwZbBgzx0bi0cd22mtOfRnetGcEeWvYYYLkUPwxEdvQ==',
				'ach_mi_w_fee_account_number' => 'tdGYA+LVUNxE4dHOjlpt6M2lwxg119cxJki727WmttoCXFqdD8T9cOnLzHA0wohJXW5Mdw79EsQ5wAGQxwceuA==',
				'ach_mi_w_rej_routing_number' => 'tdGYA+LVUNxE4dHOjlpt6LuNldaEB16+S57b1g0qtINaAg4fEbxs1cIEpPT8KR55PuZqVYTeHXNMHlG2rQfPZg==',
				'ach_mi_w_rej_account_number' => 'tdGYA+LVUNxE4dHOjlpt6JNTBFDdMtEgQJbGaKGseqkKnUZPG0jLKXXrCPaCwu3R4owy5mycYi6UkfFj9UTHeg=='
			]
		];
		$this->Ach->encryptData($tstData);

		foreach ($expected['Ach'] as $key => $encVal) {
			$this->assertSame($encVal, $tstData['Ach'][$key]);
		}
	}

}
