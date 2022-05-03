<?php
App::uses('ProductsServicesType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * ProductsServicesType Test Case
 *
 */
class ProductsServicesTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductsServicesType = ClassRegistry::init('ProductsServicesType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductsServicesType);
		parent::tearDown();
	}

/**
 * test
 *
 * @covers ProductsServicesType::afterFind()
 * @return void
 */
	public function testAfterFind() {
		$customeLabels = ['label1' => 'field1', 'label2' => 'field2'];
		$emulatedResults = [
			[
				'ProductsServicesType' => ['custom_labels' => serialize($customeLabels)]
			]
		];
		$actual = $this->ProductsServicesType->afterFind($emulatedResults);
		$this->assertSame($customeLabels, $actual[0]['ProductsServicesType']['custom_labels']);

		$actual = $this->ProductsServicesType->afterFind($emulatedResults[0]['ProductsServicesType']);
		$this->assertSame($customeLabels, $actual['custom_labels']);
	}

/**
 * test
 *
 * @covers ProductsServicesType::getList()
 * @return void
 */
	public function testGetList() {
		$expected = [
			'e8fa66a0-790f-4710-b7de-ef79be75a1c7' => 'ACH',
			'551038c4-3370-47c3-bb4c-1a3f34627ad4' => 'ACH - Web Based',
			'62d2eb5d-e3b8-44e9-b1ac-d770289d7f46' => 'American Express Dial Sales',
			'566f5f47-bac0-41f8-ac68-23f534627ad4' => 'American Express Discount',
			'551038c2-fb1c-4e6b-bbea-1a3f34627ad4' => 'American Express ESA',
			'69aa50fb-e11b-4239-985c-6bf05729fbba' => 'American Express Flat Rate',
			'e5324035-1074-4a40-90f4-5cfc7c4fd10e' => 'American Express Non Dial Sales',
			'cd7cc087-2c57-4875-8080-acba14bd5560' => 'American Express Sales',
			'4ae44a5f-2daf-4bfc-8c47-ec2e2a3fbefe' => 'Annual Fee',
			'762137fb-a8ef-457f-83b1-6591a1fd7596' => 'Check Guarantee',
			'551038c2-798c-4f58-8096-1a3f34627ad4' => 'Check Guarantee - Gross Profit',
			'551038c2-2220-4a58-b8d8-1a3f34627ad4' => 'Credit Monthly',
			'5c7f7e42-7eb0-44aa-90d2-b4eb8ed20973' => 'Debit Dial Sales',
			'12093cfa-fecd-4f17-894f-2f3430008bb0' => 'Debit Discount',
			'd3448d6a-0248-4293-8766-4170259a9b55' => 'Debit Flat Rate',
			'551038c2-b298-4429-8bdd-1a3f34627ad4' => 'Debit Monthly',
			'34599067-71df-415d-b757-1280031bfc5f' => 'Debit Non Dial Sales',
			'72a445f3-3937-4078-8631-1f569d6a30ed' => 'Debit Sales',
			'0582831b-4aaf-404e-8d92-b6aaf89ac3f3' => 'Discover',
			'28b4aa2e-2559-4eee-b29a-b25808e16a10' => 'Discover Dial Sales',
			'566f5f47-59c4-456a-9e1b-23f534627ad4' => 'Discover Discount',
			'94ecbcac-1e2b-4ae1-892b-0d9e9ee68763' => 'Discover Flat Rate',
			'ca336477-3c6a-417b-9594-7f452c2a266d' => 'Discover Non Dial Sales',
			'38d6c5b3-b6ca-4be8-9998-8b78976f0767' => 'Discover Sales',
			'7ea0907d-709e-4245-ab75-4a14b7c6e4c6' => 'EBT',
			'826f8c96-6f94-47fe-8f4c-1f67406bb07d' => 'EBT Dial Sales',
			'5da88a37-c3fa-4ee6-a69d-91ff643b3b54' => 'EBT Discount',
			'4fff2b76-6619-46f9-b5ff-501bba60dfc5' => 'EBT Flat Rate',
			'551038c2-2b7c-4330-982d-1a3f34627ad4' => 'EBT Monthly',
			'9bb75a3b-0a4d-4b1f-b75c-2392c52cc362' => 'EBT Non Dial Sales',
			'f615203f-b266-4147-9d53-039480371607' => 'EBT Sales',
			'f446b74f-ae19-4505-82c7-67cf93fcfe3d' => 'Gateway 1',
			'c468bf92-036c-4757-9609-d0dab4df1a3a' => 'Gateway 2',
			'ada3799b-1671-4239-b79b-4cabe2de2bbc' => 'Gift & Loyalty',
			'551038c2-8fac-46fa-b824-1a3f34627ad4' => 'MasterCard',
			'728ff26a-8209-4cc4-86ae-e79a2df21e7b' => 'MasterCard Dial Sales',
			'3d4f7842-1cb9-4bad-b9fd-415b1d0eee30' => 'MasterCard Discount',
			'8e4c2407-8d65-4e5b-a485-0cb4106c8de9' => 'MasterCard Flat Rate',
			'fdafb816-4a0c-4e81-84df-96d794041372' => 'MasterCard Non Dial Sales',
			'28dd4748-9699-41b9-9391-2524d3941018' => 'MasterCard Sales',
			'551038c4-8d80-42bb-b16d-1a3f34627ad4' => 'PYC',
			'551038c2-3044-47e4-be9b-1a3f34627ad4' => 'Visa',
			'c907b3b8-2cf5-475b-898c-53ed1b0adaf6' => 'Visa Dial Sales',
			'ec5b6684-6f5c-4881-b26c-5a8233bde091' => 'Visa Discount',
			'8e635b00-8866-4b08-a5c9-631980246991' => 'Visa Flat Rate',
			'624250b2-55ae-4423-b2e9-95c837943115' => 'Visa Non Dial Sales',
			'e6d8f040-9963-4539-ab75-3e19f679de16' => 'Visa Sales',
			'47b411c9-fdf3-4fc6-9c13-ba4e776c94da' => 'WebPASS',
			'5801936c-4498-498d-b0bc-398534627ad4' => 'American Express Discount (Converted Merchants)',
			'5806a480-cdd8-4199-8d6f-319b34627ad4' => 'Corral License Fee',
			'9db324ec-8365-4ae2-9b49-1e575113d5df' => 'Payment Fusion'
		];

		$this->assertEquals($expected, $this->ProductsServicesType->getList());

		// Check the expected are active
		$totalProducts = $this->ProductsServicesType->find('count');
		$inactiveProducts = $this->ProductsServicesType->find('count', [
			'conditions' => ['ProductsServicesType.is_active' => 0]
		]);
		$this->assertCount($totalProducts - $inactiveProducts, $expected);
	}

/**
 * test
 *
 * @covers ProductsServicesType::_findActive()
 * @return void
 */
	public function testFindActive() {
		$expectedCount = 51;

		$result = $this->ProductsServicesType->find('active');
		$this->assertCount($expectedCount, $result);

		$activeProducts = Hash::extract($result, '{n}.ProductsServicesType[is_active=1]');
		$this->assertCount($expectedCount, $activeProducts);
		$activeProducts = Hash::extract($result, '{n}.ProductsServicesType[is_active=0]');
		$this->assertCount(0, $activeProducts);
	}

/**
 * test
 *
 * @covers ProductsServicesType::getNameById()
 * @return void
 */
	public function testGetNameById() {
		$this->assertSame('Debit Sales', $this->ProductsServicesType->getNameById('72a445f3-3937-4078-8631-1f569d6a30ed'));
		$this->assertSame('Gateway 1', $this->ProductsServicesType->getNameById('f446b74f-ae19-4505-82c7-67cf93fcfe3d'));
		$this->assertSame('Discover Non Dial Sales', $this->ProductsServicesType->getNameById('ca336477-3c6a-417b-9594-7f452c2a266d'));
		$this->assertSame(false, $this->ProductsServicesType->getNameById('00000000-9999-0000-0000-000000000001'));
	}

/**
 * test
 *
 * @covers ProductsServicesType::getIdByName()
 * @return void
 */
	public function testGetIdByName() {
		$this->assertSame('72a445f3-3937-4078-8631-1f569d6a30ed', $this->ProductsServicesType->getIdByName('Debit Sales'));
		$this->assertSame('f446b74f-ae19-4505-82c7-67cf93fcfe3d', $this->ProductsServicesType->getIdByName('Gateway 1'));
		$this->assertSame('ca336477-3c6a-417b-9594-7f452c2a266d', $this->ProductsServicesType->getIdByName('Discover Non Dial Sales'));
		$this->assertSame(false, $this->ProductsServicesType->getIdByName('00000000-9999-0000-0000-000000000001'));
	}

/**
 * test
 *
 * @covers ProductsServicesType::_findLegacy()
 * @return void
 */
	public function test_findLegacy() {
		$reflection = new ReflectionClass('ProductsServicesType');
		$method = $reflection->getMethod('_findLegacy');
		$method->setAccessible(true);
		$actual = $method->invokeArgs($this->ProductsServicesType, ['before', []]);
		$expected = [
			'order' => 'products_services_description ASC',
			'conditions' => [
				'ProductsServicesType.is_legacy' => true
			]
		];
		$this->assertSame($expected, $actual);
	}

/**
 * test
 *
 * @covers ProductsServicesType::buildDdOptions()
 * @return void
 */
	public function testBuildDdOptions() {
		$actual = $this->ProductsServicesType->buildDdOptions(true);
		$this->assertContains('Visa Sales', $actual['Current Products']);
		$this->assertContains('Bankcard', $actual['Legacy Products']);
	}

/**
 * test
 *
 * @covers ProductsServicesType::getAllProductsList()
 * @return void
 */
	public function testGetAllProductsList() {
		$allCount = $this->ProductsServicesType->find('count');
		$this->assertCount($allCount, $this->ProductsServicesType->getAllProductsList());
	}

/**
 * test
 *
 * @covers ProductsServicesType::initProductModel()
 * @return void
 */
	public function testInitProductModel() {
		$productWithModels = [
			'Ach' => 'e8fa66a0-790f-4710-b7de-ef79be75a1c7',
			'WebBasedAch' => '551038c4-3370-47c3-bb4c-1a3f34627ad4',
			'CheckGuarantee' => '762137fb-a8ef-457f-83b1-6591a1fd7596',
			'Gateway1' => 'f446b74f-ae19-4505-82c7-67cf93fcfe3d',
			'GiftCard' => 'ada3799b-1671-4239-b79b-4cabe2de2bbc'
		];

		foreach ($productWithModels as $className => $id) {
			$Model = $this->ProductsServicesType->initProductModel($id);
			$this->assertEquals($Model->name, $className);
		}
		//Fake product any other product should return false
		$this->assertFalse($this->ProductsServicesType->initProductModel('00000000-0000-0000-0000-000000000001'));
	}
}
