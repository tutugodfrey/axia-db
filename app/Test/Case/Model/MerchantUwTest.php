<?php
App::uses('MerchantUw', 'Model');
App::uses('AxiaTestCase', 'Test');
App::uses('UwStatus', 'Model');
App::uses('AccessControl', 'Rbac.Auth');

/**
 * MerchantUw Test Case
 *
 */
class MerchantUwTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MerchantUw = ClassRegistry::init('MerchantUw');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MerchantUw);
		parent::tearDown();
	}

/**
 * testSetFormMenuData()
 *
 * @covers MerchantUw::setFormMenuData()
 * @return void
 */
	public function testSetFormMenuData() {
		$params = ['Merchant'=> ['id' => '3bc3ac07-fa2d-4ddc-a7e5-680035ec1040']];
		$actual = $this->MerchantUw->setFormMenuData($params);
		
		$this->assertContains('Square One Internet Solutions', $actual['merchant']['Merchant']);
		$this->assertNotEmpty($actual['merchant']['User']);
		$this->assertNotEmpty($actual['uwReceivedOptns']);
		$this->assertNotEmpty('merchant', $actual['uwVerifiedOptns']);
		$this->assertNotEmpty('merchant', $actual['sponsorBankOptns']);
	}

/**
 * testGetDefaultSearchValues
 *
 * @covers MerchantUw::getDefaultSearchValues()
 * @return void
 */
	public function testGetDefaultSearchValues() {
		$actual = $this->MerchantUw->getDefaultSearchValues();
		$expected = [
			'beginM' => date('n'),
			'beginY' => date('Y'),
			'endM' => date('n'),
			'endY' => date('Y'),
			'user_id' => 'user_id:'
		];
		$this->assertSame($expected, $actual);
	}

/**
 * getUwDataByMerchantId
 *
 * @covers MerchantUw::getUwDataByMerchantId
 * @return void
 */
	public function testGetUwDataByMerchantId() {
		$data = [
			'Merchant' => [
				'id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
				'merchant_id_old' => null,
				'user_id' => 'bb52eff5-2cc7-4d6c-983d-a1c2537c845a',
				'UwStatusMerchantXref' => [
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '10',
							'day' => '02',
							'year' => '2014',
							'hour' => '12',
							'min' => '55',
							'meridian' => 'pm'
						],
						'notes' => ''
					]
				],
				'UwInfodocMerchantXref' => [
					[
						'received_id' => '',
						'infodoc_id' => 'bf082025-52da-47a8-b41f-2edce29b86f5',
						'notes' => ''
					]
				],
				'UwApprovalinfoMerchantXref' => [
					[
						'verified_option_id' => '',
						'notes' => '',
						'approvalinfo_id' => 'a4c1dbd8-0ebf-4921-8f31-3956fe040752'
					]
				]
			],
			'MerchantUw' => [
				'id' => '56fc247e-dd04-4eb4-8a7e-3bcf34627ad4',
				'merchant_id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
				'expedited' => '1',
				'sponsor_bank_id' => '',
				'mcc' => '7531',
				'funding_delay_sales' => '',
				'funding_delay_credits' => '',
				'final_status_id' => '',
				'final_approved_id' => '',
				'final_date' => [
					'month' => '10',
					'day' => '02',
					'year' => '2014'
				],
				'credit_pct' => '0.0000',
				'chargeback_pct' => '0.0000',
				'credit_score' => ''
			],
			'MerchantUwVolume' => [
				'id' => '56fc247e-4e20-45e3-b9f4-3bcf34627ad4',
				'merchant_uw_id' => '00001ec2-01bd-4040-9c0a-fb723e3ef7ac',
				'mo_volume' => '40000.00',
				'average_ticket' => '400.00',
				'max_transaction_amount' => '2500.00',
				'sales' => '100',
				'mc_vi_volume' => '',
				'ds_volume' => '',
				'pin_debit_volume' => '10000.00',
				'pin_debit_avg_ticket' => '',
				'amex_volume' => '',
				'amex_avg_ticket' => '',
				'card_present_swiped' => '90.00',
				'card_present_imprint' => '0.00',
				'card_not_present_keyed' => '10.00',
				'card_not_present_internet' => '0.00',
				'direct_to_consumer' => '100.00',
				'direct_to_business' => '0.00',
				'direct_to_government' => '0.00',
				'te_amex_number' => '5360567275',
				'te_diners_club_number' => '',
				'te_discover_number' => '601121035060926',
				'te_jcb_number' => '',
				'encrypt_fields' => 'te_amex_number,te_discover_number',
				'merchant_id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5'
			],
			'MerchantNote' => [
				[
					'note_type_id' => '0bfee249-5c37-417c-aec7-83dcd2b2f566',
					'user_id' => '8ef7f0d5-0c53-46fa-ac09-864868cbfdb7',
					'merchant_id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
					'note_date' => '2016-03-30',
					'note_title' => 'Underwriting',
					'general_status' => 'COMP',
					'loggable_log_id' => '',
					'note' => 'asd'
				]
			]
		];
		$result = $this->MerchantUw->saveAll($data, array("validate" => false));
		$this->assertTrue($result);

		$result = $this->MerchantUw->getUwDataByMerchantId('7b658692-58aa-41aa-bfdd-0da105fdbaf5');
		$expected = array(
				'MerchantUwVolume' => array(
						'id' => '56fc247e-4e20-45e3-b9f4-3bcf34627ad4',
						'merchant_uw_id' => '56fc247e-dd04-4eb4-8a7e-3bcf34627ad4',
						'merchant_id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
						'mc_vi_ds_risk_assessment' => null,
						'mo_volume' => '40000.00',
						'average_ticket' => '400.00',
						'max_transaction_amount' => '2500.00',
						'sales' => 100,
						'ds_volume' => null,
						'pin_debit_volume' => '10000.00',
						'pin_debit_avg_ticket' => null,
						'amex_volume' => null,
						'amex_avg_ticket' => null,
						'card_present_swiped' => '90.00',
						'card_present_imprint' => '0.00',
						'card_not_present_keyed' => '10.00',
						'card_not_present_internet' => '0.00',
						'direct_to_consumer' => '100.00',
						'direct_to_business' => '0.00',
						'direct_to_government' => '0.00',
						'te_amex_number' => 'tdGYA+LVUNxE4dHOjlpt6C+cYLrPIl+pGyCm+t+p2e2dsVbeEFUWtGF32YUsoYliOisJovo+Q5PFDsYPvH8QMg==',
						'te_diners_club_number' => '',
						'te_discover_number' => 'tdGYA+LVUNxE4dHOjlpt6F7Ql4eWcpWNbu330DDhdWxeba8atvTJPFi5IgLLkgQCpTUWpLntDxM2IKIsqN0QLA==',
						'te_jcb_number' => '',
						'te_amex_number_disp' => '7275',
						'te_diners_club_number_disp' => null,
						'te_discover_number_disp' => '0926',
						'te_jcb_number_disp' => null,
						'mc_volume' => null,
						'visa_volume' => null,
						'discount_frequency' => null,
						'fees_collected_daily' => false,
						'next_day_funding' => false
				),
				'Merchant' => array(
						'id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
						'merchant_id_old' => '',
						'user_id' => 'bb52eff5-2cc7-4d6c-983d-a1c2537c845a',
						'user_id_old' => null,
						'merchant_mid' => null,
						'merchant_name' => null,
						'merchant_dba' => null,
						'merchant_contact' => null,
						'merchant_email' => null,
						'merchant_ownership_type' => null,
						'merchant_tin' => null,
						'merchant_d_and_b' => null,
						'inactive_date' => null,
						'active_date' => null,
						'referer_id' => null,
						'ref_seq_number_old' => null,
						'network_id' => null,
						'network_id_old' => null,
						'merchant_buslevel' => null,
						'merchant_sic' => null,
						'entity_old' => null,
						'entity_id' => null,
						'group_id' => null,
						'group_id_old' => null,
						'merchant_bustype' => null,
						'merchant_url' => null,
						'merchant_tin_disp' => null,
						'merchant_d_and_b_disp' => null,
						'active' => null,
						'cancellation_fee_id' => null,
						'cancellation_fee_id_old' => null,
						'merchant_contact_position' => null,
						'merchant_mail_contact' => null,
						'reseller_id' => null,
						'res_seq_number_old' => null,
						'merchant_bin_id' => null,
						'merchant_bin_id_old' => null,
						'merchant_acquirer_id' => null,
						'merchant_acquirer_id_old' => null,
						'reporting_user' => null,
						'merchant_ps_sold' => null,
						'ref_p_type' => null,
						'ref_p_value' => null,
						'res_p_type' => null,
						'res_p_value' => null,
						'ref_p_pct' => null,
						'res_p_pct' => null,
						'onlineapp_application_id' => null,
						'partner_id' => null,
						'partner_id_old' => null,
						'partner_exclude_volume' => null,
						'aggregated' => null,
						'cobranded_application_id' => null,
						'original_acquirer_id' => null,
						'bet_network_id' => null,
						'back_end_network_id' => null,
						'sm_user_id' => null,
						'sm2_user_id' => null,
						'UwStatusMerchantXref' => array(),
						'UwInfodocMerchantXref' => array(),
						'UwApprovalinfoMerchantXref' => array(),
						'brand_id' => null,
						'womply_status_id' => null,
						'womply_merchant_enabled' => false,
						'womply_customer' => false,
						'product_group_id' => null,
						'region_id' => null,
						'subregion_id' => null,
						'organization_id' => null,
						'source_of_sale' => null,
						'is_acquiring_only' => null,
						'is_pf_only' => null,
						'general_practice_type' => null,
						'specific_practice_type' => null,
						'client_id' => null,
						'chargebacks_email' => null,
						'merchant_type_id' => null
				),
				'MerchantUw' => array(
						'id' => '56fc247e-dd04-4eb4-8a7e-3bcf34627ad4',
						'merchant_id' => '7b658692-58aa-41aa-bfdd-0da105fdbaf5',
						'merchant_id_old' => null,
						'tier_assignment' => null,
						'credit_pct' => '0.0000',
						'chargeback_pct' => '0.0000',
						'merchant_uw_final_status_id' => null,
						'final_status_id_old' => null,
						'merchant_uw_final_approved_id' => null,
						'final_approved_id_old' => null,
						'final_date' => '2014-10-02',
						'final_notes' => null,
						'mcc' => '7531',
						'expedited' => true,
						'ebt_risk_assessment' => null,
						'debit_risk_assessment' => null,
						'sponsor_bank_id' => null,
						'credit_score' => null,
						'funding_delay_sales' => null,
						'funding_delay_credits' => null,
						'MerchantUwFinalStatus' => array(),
						'MerchantUwFinalApproved' => array(),
						'SponsorBank' => array(),
						'app_quantity_type' => null
				)
		);

		$this->assertEquals($expected, $result);
	}

/**
 * testGetAssociatedUwLists
 *
 * @covers MerchantUw::getAssociatedUwLists
 * @return void
 */
	public function testGetAssociatedUwLists() {
		$result = $this->MerchantUw->getAssociatedUwLists();
		$expected = [
			'uwSatuses' => [
				[	'UwStatus' => [
						'id' => '00000000-0000-0000-0000-000000000001',
						'id_old' => null,
						'name' => 'Lorem ipsum dolor sit amet',
						'priority' => 1
					],
				],
				[
					'UwStatus' => [
						'id' => '00000000-0000-0000-0000-000000000002',
						'id_old' => null,
						'name' => 'Approved',
						'priority' => 2
					],
				],
				[
					'UwStatus' => [
						'id' => '00000000-0000-0000-0000-000000000003',
						'id_old' => null,
						'name' => 'Received',
						'priority' => 3
					],
				],
			],
			'approvalInfos' => [
				[
					'UwApprovalinfo' => [
						'id' => '00000000-0000-0000-0000-000000000001',
						'id_old' => null,
						'name' => 'Lorem ipsum dolor sit amet',
						'priority' => 1,
						'verified_type' => 'yn'
					]
				]
			],
			'uwRequiredInfoDocs' => [
				[
					'UwInfodoc' => [
						'id' => '00000000-0000-0000-0000-000000000001',
						'id_old' => null,
						'name' => 'Lorem ipsum dolor sit amet',
						'priority' => 1,
						'required' => true
					]
				]
			],
			'uwOtherInfoDocs' => [],
			'finalStatusOptns' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor s',
				'2fb6e87b-bf77-4d3c-96db-f12e9147fa5a' => 'Approved'
			],
			'approversOptns' => [
				'00000000-0000-0000-0000-000000000001' => 'Lorem ipsum dolor s'
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testReIndexUwMerchantXrefsData
 *
 * @covers MerchantUw::reIndexUwMerchantXrefsData
 * @return void
 */
	public function testReIndexUwMerchantXrefsDataWithSamePriority() {
		$uwMerchantXtref = [
			[
				'id' => '33e5f14d-9203-4ce4-9025-bf0b89da7cb0',
				'merchant_id' => 'e2de1440-3b79-44b9-9356-29457b94957b',
				'merchant_id_old' => '7641110042404743',
				'uw_status_id' => '2a8ec517-ce32-46f3-b094-d8423d190dc7',
				'status_id_old' => '2',
				'datetime' => '2013-02-14 10:15:00',
				'notes' => '',
				'UwStatus' => [
					'id' => '2a8ec517-ce32-46f3-b094-d8423d190dc7',
					'id_old' => '2',
					'name' => 'Illegible',
					'priority' => 2
				]
			],
			[
				'id' => '33e5f14d-9203-4ce4-9025-bf0b89da7cb0',
				'merchant_id' => 'e2de1440-3b79-44b9-9356-29457b94957b',
				'merchant_id_old' => '7641110042404743',
				'uw_status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
				'status_id_old' => '1',
				'datetime' => '2013-02-14 10:15:00',
				'notes' => '',
				'UwStatus' => [
					'id' => '9441645d-b764-46f7-a68b-230f9d08037b',
					'id_old' => '1',
					'name' => 'Received',
					'priority' => 1
				]
			]
		];

		$uwList = [
			[
				'UwStatus' => [
					'id' => '9441645d-b764-46f7-a68b-230f9d08037b',
					'id_old' => '1',
					'name' => 'Received',
					'priority' => 1
				]
			],
			[
				'UwStatus' => [
					'id' => '2a8ec517-ce32-46f3-b094-d8423d190dc7',
					'id_old' => '2',
					'name' => 'Illegible',
					'priority' => 2
				]
			]
		];

		$result = $this->MerchantUw->reIndexUwMerchantXrefsData($uwList, $uwMerchantXtref);
		//expected is in ASC order which is the same order as $uwList
		$expected[] = $uwMerchantXtref[1];
		$expected[] = $uwMerchantXtref[0];

		$this->assertEquals($expected, $result);
	}

/**
 * testReIndexUwMerchantXrefsData
 *
 * @covers MerchantUw::reIndexUwMerchantXrefsData
 * @return void
 */
	public function testReIndexUwMerchantXrefsDataWithDifferentPriority() {
		$uwList = [
			[
				'id' => '33e5f14d-9203-4ce4-9025-bf0b89da7cb0',
				'merchant_id' => 'e2de1440-3b79-44b9-9356-29457b94957b',
				'merchant_id_old' => '7641110042404743',
				'uw_status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
				'status_id_old' => '1',
				'datetime' => '2013-02-14 10:15:00',
				'notes' => '',
				'UwStatus' => [
					'id' => '9441645d-b764-46f7-a68b-230f9d08037b',
					'id_old' => '1',
					'name' => 'Received',
					'priority' => 1
				]
			]
		];

		$uwMerchantXtref = [
			[
				'UwStatus' => [
					'id' => '2a8ec517-ce32-46f3-b094-d8423d190dc7',
					'id_old' => null,
					'name' => 'Lorem ipsum dolor sit amet',
					'priority' => 2
				]
			]
		];

		$result = $this->MerchantUw->reIndexUwMerchantXrefsData($uwList, $uwMerchantXtref);
		$expected = [];

		$this->assertEquals($expected, $result);
	}

/**
 * testExtractInfodocsXrefsDataByRequirementIsRequired
 *
 * @param array $uwMerchantXtref Test data
 * @param bool $isRequiredData Required flag
 * @dataProvider providerExtractInfodocsXrefsDataByRequirement
 * @covers MerchantUw::extractInfodocsXrefsDataByRequirement
 * @return void
 */
	public function testExtractInfodocsXrefsDataByRequirement($uwMerchantXtref, $isRequiredData) {
		$result = $this->MerchantUw->extractInfodocsXrefsDataByRequirement($uwMerchantXtref, $isRequiredData);
		$expected = [
			[
				'UwInfodoc' => [
					'id' => 'bf082025-52da-47a8-b41f-2edce29b86f5',
					'id_old' => '1',
					'name' => 'Completed and Signed Application',
					'priority' => 1,
					'required' => $isRequiredData
				]
			]
		];

		$this->assertEquals($expected, $result);
	}

/**
 * Provider for extractInfodocsXrefsDataByRequirement
 *
 * @return array
 */
	public function providerExtractInfodocsXrefsDataByRequirement() {
		return [
			[
				[
					[
						'UwInfodoc' => [
							'id' => 'bf082025-52da-47a8-b41f-2edce29b86f5',
							'id_old' => '1',
							'name' => 'Completed and Signed Application',
							'priority' => 1,
							'required' => true
						]
					]
				],
				true
			],
			[
				[
					[
						'UwInfodoc' => [
							'id' => 'bf082025-52da-47a8-b41f-2edce29b86f5',
							'id_old' => '1',
							'name' => 'Completed and Signed Application',
							'priority' => 1,
							'required' => false
						]
					]
				],
				false
			]
		];
	}

/**
 * testExtractInfodocsXrefsDataByRequirementEmptyMerchant
 *
 * @covers MerchantUw::extractInfodocsXrefsDataByRequirement
 * @return void
 */
	public function testExtractInfodocsXrefsDataByRequirementEmptyMerchant() {
		$uwMerchantXtref = [];
		$isRequiredData = true;
		$result = $this->MerchantUw->extractInfodocsXrefsDataByRequirement($uwMerchantXtref, $isRequiredData);
		$expected = [];

		$this->assertEquals($expected, $result);
	}

/**
 * testFilterElements
 *
 * @param array $modelData Model data
 * @param array $expected Expected result
 * @dataProvider providerForFilterElements
 * @covers MerchantUw::filterEmptyElements
 * @return void
 */
	public function testFilterElements($modelData, $expected) {
		$result = $this->MerchantUw->filterEmptyElements($modelData);
		$this->assertEquals($expected, $result);
	}

/**
 * Provider for testFilterElements
 *
 * @return array
 */
	public function providerForFilterElements() {
		return [
			[
				[
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '41',
							'meridian' => 'pm'
						],
						'notes' => 'Test-1',
					]
				],
				[
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '41',
							'meridian' => 'pm'
						],
						'notes' => 'Test-1',
					]
				]
			],
			[
				[
					[
						'id' => '26d12bd3-1b09-4eb7-9a34-93c72f8bd369',
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '41',
							'meridian' => 'pm'
						],
						'notes' => '',
					]
				],
				[
					[
						'id' => '26d12bd3-1b09-4eb7-9a34-93c72f8bd369',
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '41',
							'meridian' => 'pm'
						],
						'notes' => '',
					]
				]
			],
			[
				[
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '44',
							'meridian' => 'pm'
						],
						'notes' => '',
					]
				],
				[
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
						'datetime' => [
							'month' => '04',
							'day' => '01',
							'year' => '2016',
							'hour' => '17',
							'min' => '44',
							'meridian' => 'pm'
						]
					]
				]
			],
			[
				[
					[
						'status_id' => '9441645d-b764-46f7-a68b-230f9d08037b',
					]
				],
				[]
			]
		];
	}

/**
 * testSetPaginatorSettings
 *
 * @covers MerchantUw::setPaginatorSettings
 * @return void
 */
	public function testSetPaginatorSettings() {
		$params = [
			'RepFilter' => 'ce08dc3a-5e00-4a5c-97f0-e0568b020ba5',
			'dba_mid' => 'Testing',
			'beginM' => '3',
			'beginY' => '2016',
			'endM' => '3',
			'endY' => '2016',
		];

		$result = $this->MerchantUw->setPaginatorSettings($params);
		$expected = [
			'limit' => 500,
			'maxLimit' => 500,
			'fields' => [
				'Merchant.merchant_dba',
				'Merchant.merchant_mid',
				'Merchant.active',
				'User.user_first_name',
				'User.user_last_name',
				'Client.client_id_global',
				'((trim(BOTH FROM "Partner"."user_first_name" || \' \' || "Partner"."user_last_name"))) as "Partner__fullname"'
			],
			'contain' => [
				'TimelineEntry' => [
					'fields' => 'TimelineEntry.timeline_date_completed',
					'conditions' => [
						'TimelineEntry.timeline_item_id' => '9eff98d4-621e-4ed2-adfb-73eaacbcc38f'
					]
				],
				'MerchantUw' => [
					'fields' => ['MerchantUw.expedited', 'MerchantUw.app_quantity_type']
				],
				'UwStatusMerchantXref' => [
					'UwStatus',
					'order' => 'UwStatusMerchantXref.datetime ASC',
					'fields' => [
						'UwStatusMerchantXref.datetime'
					]
				]
			],
			'joins' => [
				[
					'table' => 'uw_status_merchant_xrefs',
					'alias' => 'UwStatusMerchantXref',
					'type' => 'INNER',
					'conditions' => [
						'UwStatusMerchantXref.merchant_id = Merchant.id',
						'UwStatusMerchantXref.datetime >=' => '2016-3-01',
						'UwStatusMerchantXref.datetime <=' => '2016-3-31'
					]
				],
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => [
						'Merchant.user_id = User.id'
					]
				],
				[
					'table' => 'users',
					'alias' => 'Partner',
					'type' => 'LEFT',
					'conditions' => [
						'Merchant.partner_id = Partner.id'
					]
				],
				[
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
						'conditions' => [
							"Merchant.client_id = Client.id",
						]
				],
			],
			'conditions' => [
				[
					'OR' => [
						'Merchant.merchant_mid ILIKE' => '%Testing%',
						'Merchant.merchant_dba ILIKE' => '%Testing%'
					]
				],
				'length(Merchant.merchant_mid) > 6'
			],
			'group' => [
				'Merchant.id',
				'Merchant.merchant_mid',
				'Merchant.merchant_dba',
				'Merchant.active',
				'Client.client_id_global',
				'User.user_first_name',
				'User.user_last_name',
				'Partner.user_first_name',
				'Partner.user_last_name',
				'MerchantUw.expedited',
				'MerchantUw.app_quantity_type']
			];

		$this->assertEquals($expected, $result);
	}

/**
 * testEmailRepUwStatus
 * The only aim of this test is to increaase code coverage since the covered function does not perform 
 * any testable logic. It simply sets up and sends an email.
 * Emailing is disabled in dev.
 * 
 * @covers MerchantUw::emailRepUwStatus
 * @return void
 */
	public function testEmailRepUwStatus() {
		$params = [
			'Merchant' => [
				'merchant_dba' => 'The Good Merchant',
				'UwStatusMerchantXref' => [
					[
						'id' => 'some status',
						'UwStatus' => ['name' => 'new status']
					]
				]],
			'User' => [
				'user_first_name' => 'John',
				'user_last_name' => 'Doe',
				'user_email' => 'test_no_email@axiamed.com'
			]
		];
		$statusXrefId = 'some status';
		$actual = $this->MerchantUw->emailRepUwStatus($params, $statusXrefId);
		$this->assertTrue($actual);
	}

/**
 * testMakeCsvString
 *
 * @covers MerchantUw::makeCsvString
 * @return void
 */
	public function testMakeCsvString() {
		$params = [
			'beginM' => '1',
			'beginY' => '2015',
			'endM' => '12',
			'endY' => '2015',
		];
		$actual = $this->MerchantUw->makeCsvString($params);
		$this->assertContains('Underwriting Report | Jan 2015 - Dec 2015', $actual);
	}
/**
 * testGetUwStatus
 *
 * @covers MerchantUw::_getUwStatus()
 * @return void
 */
	public function testGetUwStatus() {
		$reflection = new ReflectionClass('MerchantUw');
		$method = $reflection->getMethod('_getUwStatus');
		$method->setAccessible(true);

		$expected = ClassRegistry::init('UwStatus');
		$result = $method->invoke($this->MerchantUw);

		$this->assertEquals($expected, $result);
	}

/**
 * testSetFinalDate(&$data)
 *
 * @covers MerchantUw::setFinalDate()
 * @return void
 */
	public function testSetFinalDate() {
		//Empty set
		$data = [
			'Merchant' => [
				'UwStatusMerchantXref' => [
					[
						'uw_status_id' => '00000000-0000-0000-0000-000000000001',
						'datetime' => [
							'month' => '03',
							'day' => '08',
							'year' => '2016',
							'hour' => '04',
							'min' => '00',
							'meridian' => 'pm'
						]
					]
				]
			],
			'MerchantUw' => ['final_date' => []]
		];
		$this->MerchantUw->setFinalDate($data);
		$this->assertEmpty($data['MerchantUw']['final_date']);

		$data['Merchant']['UwStatusMerchantXref'][0]['uw_status_id'] = '00000000-0000-0000-0000-000000000002';
		$this->MerchantUw->setFinalDate($data);
		$this->assertNotEmpty($data['MerchantUw']['final_date']);
		$this->assertSame($data['MerchantUw']['final_date'], $data['Merchant']['UwStatusMerchantXref'][0]['datetime']);
	}
}
