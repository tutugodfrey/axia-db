<?php
App::uses('Gateway', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * Gateway Test Case
 *
 */
class GatewayTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Gateway = ClassRegistry::init('Gateway');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Gateway);
		parent::tearDown();
	}

/**
 * testGetSortedGatewayList
 *
 * @covers Gateway::getSortedGatewayList()
 * @return void
 */
	public function testGetSortedGatewayList() {
		$actual = $this->Gateway->getSortedGatewayList();
		$expected = [
			'a536e089-4d6c-4401-ad5a-edb316aca2ca' => 'Authorize.net',
			'567b2192-9ff0-4c80-8417-77c234627ad4' => 'Axia ePay Gold',
			'567b2192-b654-40dc-a3bb-77c234627ad4' => 'Axia ePay Platinum',
			'567b2192-aa54-4a1e-838b-77c234627ad4' => 'Axia ePay Silver',
			'567b2192-44e0-40bf-ba17-77c234627ad4' => 'Blackline',
			'567b2192-34d4-4d20-a493-77c234627ad4' => 'Bridgepay',
			'567b2192-d21c-4eca-ae04-77c234627ad4' => 'Cayan',
			'3b2f923d-e296-4f6f-bc42-3f4f5c01ac2a' => 'CounterPASS',
			'567b2192-7158-42e9-a5dc-77c234627ad4' => 'Element',
			'9898ae02-3a93-484f-b41b-9261cdfe1db4' => 'MerchantLink',
			'567b2192-7a0c-4869-bd6e-77c234627ad4' => 'MerchantLink Dial',
			'567b2192-c9fc-4202-a06e-77c234627ad4' => 'MerchantLink IP/SSL',
			'5c7a73ef-8092-4e81-bf93-9f70a44df664' => 'No Gateway',
			'3db3c7ab-2b66-4fee-b89c-018197c85ce9' => 'Sage Virtual Terminal',
			'06be6bfd-2a79-4e23-9fca-270e6b2fcbf3' => 'ShopKeep',
			'33b95e84-3755-437a-9554-1531601e6e77' => 'Simply Swipe It',
			'da053da3-a406-4c0b-8726-95a477420c90' => 'TGate',
			'3616c30d-3167-4231-98e9-f486432e2899' => 'USA ePay',
			'31f426ff-d63a-4070-bc82-a3f558cf1961' => 'WebPASS'
		];
		$this->assertEquals($expected, $actual);
	}

/**
 * testGetList
 *
 * @covers Gateway::getList()
 * @return void
 */
	public function testGetList() {
		$actual = $this->Gateway->getList();
		$expected = [
			'a536e089-4d6c-4401-ad5a-edb316aca2ca' => 'Authorize.net',
			'567b2192-9ff0-4c80-8417-77c234627ad4' => 'Axia ePay Gold',
			'567b2192-b654-40dc-a3bb-77c234627ad4' => 'Axia ePay Platinum',
			'567b2192-aa54-4a1e-838b-77c234627ad4' => 'Axia ePay Silver',
			'567b2192-44e0-40bf-ba17-77c234627ad4' => 'Blackline',
			'567b2192-34d4-4d20-a493-77c234627ad4' => 'Bridgepay',
			'567b2192-d21c-4eca-ae04-77c234627ad4' => 'Cayan',
			'3b2f923d-e296-4f6f-bc42-3f4f5c01ac2a' => 'CounterPASS',
			'567b2192-7158-42e9-a5dc-77c234627ad4' => 'Element',
			'9898ae02-3a93-484f-b41b-9261cdfe1db4' => 'MerchantLink',
			'567b2192-7a0c-4869-bd6e-77c234627ad4' => 'MerchantLink Dial',
			'567b2192-c9fc-4202-a06e-77c234627ad4' => 'MerchantLink IP/SSL',
			'5c7a73ef-8092-4e81-bf93-9f70a44df664' => 'No Gateway',
			'3db3c7ab-2b66-4fee-b89c-018197c85ce9' => 'Sage Virtual Terminal',
			'06be6bfd-2a79-4e23-9fca-270e6b2fcbf3' => 'ShopKeep',
			'33b95e84-3755-437a-9554-1531601e6e77' => 'Simply Swipe It',
			'da053da3-a406-4c0b-8726-95a477420c90' => 'TGate',
			'3616c30d-3167-4231-98e9-f486432e2899' => 'USA ePay',
			'31f426ff-d63a-4070-bc82-a3f558cf1961' => 'WebPASS'
		];
		$this->assertEquals($expected, $actual);

		$actual = $this->Gateway->getList(['order' => ['position' => 'desc']]);

		$expected = [
			'567b2192-c9fc-4202-a06e-77c234627ad4' => 'MerchantLink IP/SSL',
			'567b2192-7a0c-4869-bd6e-77c234627ad4' => 'MerchantLink Dial',
			'567b2192-7158-42e9-a5dc-77c234627ad4' => 'Element',
			'567b2192-d21c-4eca-ae04-77c234627ad4' => 'Cayan',
			'567b2192-34d4-4d20-a493-77c234627ad4' => 'Bridgepay',
			'567b2192-44e0-40bf-ba17-77c234627ad4' => 'Blackline',
			'567b2192-aa54-4a1e-838b-77c234627ad4' => 'Axia ePay Silver',
			'567b2192-b654-40dc-a3bb-77c234627ad4' => 'Axia ePay Platinum',
			'567b2192-9ff0-4c80-8417-77c234627ad4' => 'Axia ePay Gold',
			'5c7a73ef-8092-4e81-bf93-9f70a44df664' => 'No Gateway',
			'a536e089-4d6c-4401-ad5a-edb316aca2ca' => 'Authorize.net',
			'33b95e84-3755-437a-9554-1531601e6e77' => 'Simply Swipe It',
			'06be6bfd-2a79-4e23-9fca-270e6b2fcbf3' => 'ShopKeep',
			'3b2f923d-e296-4f6f-bc42-3f4f5c01ac2a' => 'CounterPASS',
			'31f426ff-d63a-4070-bc82-a3f558cf1961' => 'WebPASS',
			'da053da3-a406-4c0b-8726-95a477420c90' => 'TGate',
			'9898ae02-3a93-484f-b41b-9261cdfe1db4' => 'MerchantLink',
			'3db3c7ab-2b66-4fee-b89c-018197c85ce9' => 'Sage Virtual Terminal',
			'3616c30d-3167-4231-98e9-f486432e2899' => 'USA ePay'
		];
		$this->assertSame($expected, $actual);
	}
}
