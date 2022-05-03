<?php

App::uses('AppModel', 'Model');
App::uses('SalesForce', 'Model');

/**
 * MultipurposeReport Model
 */
class MultipurposeReport extends AppModel {

	public $useTable = false;

	const CSV_HEADERS = "Active/Inactive,MID,DBA,Client,Corporate Name,Corporate Address,Corporate City,Corporate State,Corporate Zip,Corporate Phone,Mailing Address,Mailing City,Mailing State,Mailing Zip,Site Street,Site City,Site State,Site Zip,Tax ID,Bank Name,Bank Account,Bank Routing,Fees Bank Account,Fees Bank Routing,Monthly Volume,Average Ticket,Card Present Swiped %,Card Present Imprint %,Card Not Present Keyed %,Card Not Present Internet %,Visa Volume,MC Volume,Discover Volume,American Express Volume,Ownership Type,Owner/Partner/Officer(1) Name,Owner/Partner/Officer(1) Title,Owner/Partner/Officer(1) Equity,Owner/Partner/Officer(1) SSN,Owner/Partner/Officer(2) Name,Owner/Partner/Officer(2) Title,Owner/Partner/Officer(2) Equity,Owner/Partner/Officer(2) SSN,Owner/Partner/Officer(3) Name,Owner/Partner/Officer(3) Title,Owner/Partner/Officer(3) Equity,Owner/Partner/Officer(3) SSN,Owner/Partner/Officer(4) Name,Owner/Partner/Officer(4) Title,Owner/Partner/Officer(4) Equity,Owner/Partner/Officer(4) SSN,Merchant Contact,Products & Services Sold,Business Type,Business URL,Business Level,SIC,BIN,Acquirer,Network,Bet Network,Email Address,Rep,Manager,Manager2,Referrer Option,Referrer Value,Referrer Percent,Reseller Option,Reseller Value,Reseller Percent,Approval Date,Declined Date,Go-Live Date,Install Commissioned Date,Submitted Date,Recd Signed Install Sheet,Compliance Status,DBI Fee,Date of Last Activity,Date Cancelled,Referer Name,Reseller Name,Last Deposit Date,Visa/MC BET,Discover BET,Amex BET,Visa/MC Discount Processing Rate, Discover Discount Processing Rate, American Express Discount Processing Rate,V/MC Auth Fee, Discover Auth Fee, Amex Auth Fee, Discount Item Fee, Partner Name,Company,Organization,Region,Subregion,HaaS,Has ACH Product?,Termination Reason,Agreement End Date,SF Opportunity ID\n";
/**
 * var DEFAULT_OWNER_COL_DELIM_COUNT
 * 
 * This is the default number of column delimeters that will be added per merchant owner header defined in self::CSV_HEADERS.
 * Each Merchant has an n number of owners and each owner record has m number of fields.
 * This default should always equal the number of fields being queried in the $ownerAggField subquery string and will be used as
 * the number delimiters to add to the csv file to as empty filler coumns for each owner when no merchant owner data is available.
 */
	const DEFAULT_OWNER_COL_DELIM_COUNT = 3;

/**
 * getReport
 *
 * @param array $conditions query conditions
 *
 * @return array $results
 */
	public function getReport($conditions = null, $maskSecureData = true) {
		$this->Merchant = ClassRegistry::init('Merchant');
		/*Use limit and offset to retrieve data by subsets and avoid exhausting memory*/
		$limit = 2000;
		$offset = 0;
		//Special delimited string aggregation of all merchant owners:
		//Each merchant owner Field is delimeted by '~'
		//Each merchant owner record is delimeted by '<ER>'
		$ownerAggField = "((string_agg(COALESCE('\"' ||\"MerchantOwner\".\"owner_name\"|| '\"', '') || '~' || COALESCE('\"' ||\"MerchantOwner\".\"owner_title\"|| '\"', '') || '~' || COALESCE(\"MerchantOwner\".\"owner_equity\", 0) || '~' || COALESCE('*****' || \"MerchantOwner\".\"owner_social_sec_no_disp\", ''), '<ER>'))) AS \"MerchantOwner__list_of_owners\"";
		$settings = array(
				'fields' => array(
					"((\"Client\".\"client_id_global\" || ' - ' || \"Client\".\"client_name_global\")) AS \"Client__name\"",
					'Merchant.active',
					'Merchant.merchant_mid',
					'Merchant.merchant_dba',
					'Merchant.merchant_ownership_type',
					'Merchant.merchant_buslevel',
					'Merchant.merchant_bustype',
					'Merchant.merchant_url',
					'Merchant.merchant_tin',
					'SfOpportunityIdField.value',
					'MerchantBank.bank_name',
					'MerchantBank.bank_dda_number',
					'MerchantBank.bank_routing_number',
					'MerchantBank.fees_dda_number',
					'MerchantBank.fees_routing_number',
					'MerchantPricing.processing_rate',
					'MerchantPricing.ds_processing_rate',
					'MerchantPricing.amex_processing_rate',
					'MerchantPricing.mc_vi_auth',
					'MerchantPricing.ds_auth_fee',
					'MerchantPricing.amex_auth_fee',
					'MerchantPricing.discount_item_fee',
					'MerchantUwVolume.mo_volume',
					'MerchantUwVolume.average_ticket',
					'MerchantUwVolume.card_present_swiped',
					'MerchantUwVolume.card_present_imprint',
					'MerchantUwVolume.card_not_present_keyed',
					'MerchantUwVolume.card_not_present_internet',
					'MerchantUwVolume.visa_volume',
					'MerchantUwVolume.mc_volume',
					'MerchantUwVolume.ds_volume',
					'MerchantUwVolume.amex_volume',
					'VisaBet.name',
					'MasterCardBet.name',
					'DiscoverBet.name',
					'AmexBet.name',
					'Merchant.ref_p_type',
					'Merchant.ref_p_value',
					'Merchant.ref_p_pct',
					'Merchant.res_p_type',
					'Merchant.res_p_value',
					'Merchant.res_p_pct',
					'User.user_first_name',
					'User.user_last_name',
					"((\"Manager\".\"user_first_name\" || ' ' || \"Manager\".\"user_last_name\")) AS \"Manager__name\"",
					"((\"Manager2\".\"user_first_name\" || ' ' || \"Manager2\".\"user_last_name\")) as \"Manager2__name\"",
					'AddressCorp.address_title',
					'AddressCorp.address_street',
					'AddressCorp.address_city',
					'AddressCorp.address_state',
					'AddressCorp.address_zip',
					'AddressCorp.address_phone',
					'AddressCorp.address_fax',
					'AddressMail.address_title',
					'AddressMail.address_street',
					'AddressMail.address_city',
					'AddressMail.address_state',
					'AddressMail.address_zip',
					'AddressMail.address_phone',
					'AddressMail.address_fax',
					'AddressBusiness.address_street',
					'AddressBusiness.address_city',
					'AddressBusiness.address_state',
					'AddressBusiness.address_zip',
					$ownerAggField,
					'Merchant.merchant_ps_sold',
					'Merchant.merchant_contact',
					'Merchant.merchant_sic',
					'Merchant.merchant_email',
					'MerchantAcquirer.acquirer',
					'MerchantBin.bin',
					'UwStatusMerchantXref.datetime',
					'UwStatusDeclinedMerchant.datetime',
					'TimelineEntryAgreeEnds.timeline_date_completed',
					'TimelineEntryIns.timeline_date_completed',
					'TimelineEntryInsComm.timeline_date_completed',
					'TimelineEntrySub.timeline_date_completed',
					'TimelineEntryReSiInSh.timeline_date_completed',
					'SaqControlScan.pci_compliance',
					'MerchantPci.insurance_fee',
					'MerchantPci.saq_completed_date',
					'MerchantPci.saq_type',
					'MerchantCancellation.date_inactive',
					'MerchantCancellation.date_completed',
					'MerchantCancellation.reason',
					'MerchantCancellationSubreason.name',
					"((\"Referer\".\"user_first_name\" || ' ' || \"Referer\".\"user_last_name\")) AS \"Referer__name\"",
					"((\"Reseller\".\"user_first_name\" || ' ' || \"Reseller\".\"user_last_name\")) as \"Reseller__name\"",
					'LastDepositReport.last_deposit_date',
					'Network.network_description',
					'BetNetwork.name',
					"((\"Partner\".\"user_first_name\" || ' ' || \"Partner\".\"user_last_name\")) as \"Partner__name\"",
					'Entity.entity_name',
					'Organization.name',
					'Region.name',
					'Subregion.name',
					'PaymentFusion.is_hw_as_srvc',
					'AchProduct.products_services_type_id'
				),
				'joins' => array(
					array(
						'alias' => 'AchProduct',
						'table' => 'products_and_services',
						'type' => 'LEFT',
						'conditions' => array(
							'"Merchant"."id" = "AchProduct"."merchant_id"',
							'"AchProduct"."products_services_type_id" = (SELECT id from products_services_types where products_services_description = \'ACH\')'
						)
					),array(
						'alias' => 'SfOpportunityIdField',
						'table' => 'external_record_fields',
						'type' => 'LEFT',
						'conditions' => array(
							'"Merchant"."id" = "SfOpportunityIdField"."merchant_id"',
							'"SfOpportunityIdField"."field_name" = ' . "'" . SalesForce::OPPTY_ID . "'",
						)
					),array(
						'alias' => 'MerchantUwVolume',
						'table' => 'merchant_uw_volumes',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "MerchantUwVolume"."merchant_id"'
					),array(
						'alias' => 'Client',
						'table' => 'clients',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."client_id" = "Client"."id"'
					),array(
						'alias' => 'PaymentFusion',
						'table' => 'payment_fusions',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "PaymentFusion"."merchant_id"'
					),
					array(
						'alias' => 'MerchantPricing',
						'table' => 'merchant_pricings',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "MerchantPricing"."merchant_id"'
					),
					array(
						'alias' => 'VisaBet',
						'table' => 'bet_tables',
						'type' => 'LEFT',
						'conditions' => '"MerchantPricing"."visa_bet_table_id" = "VisaBet"."id"'
					),
					array(
						'alias' => 'MasterCardBet',
						'table' => 'bet_tables',
						'type' => 'LEFT',
						'conditions' => '"MerchantPricing"."mc_bet_table_id" = "MasterCardBet"."id"'
					),
					array(
						'alias' => 'DiscoverBet',
						'table' => 'bet_tables',
						'type' => 'LEFT',
						'conditions' => '"MerchantPricing"."ds_bet_table_id" = "DiscoverBet"."id"'
					),
					array(
						'alias' => 'AmexBet',
						'table' => 'bet_tables',
						'type' => 'LEFT',
						'conditions' => '"MerchantPricing"."amex_bet_table_id" = "AmexBet"."id"'
					),
					array(
						'alias' => 'MerchantAcquirer',
						'table' => 'merchant_acquirers',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."merchant_acquirer_id" = "MerchantAcquirer"."id"'
					),
					array(
						'alias' => 'MerchantBin',
						'table' => 'merchant_bins',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."merchant_bin_id" = "MerchantBin"."id"'
					),
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => '"Merchant"."user_id" = "User"."id"'
					),
					array(
						'alias' => 'Manager',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."sm_user_id" = "Manager"."id"'
					),
					array(
						'alias' => 'Manager2',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."sm2_user_id" = "Manager2"."id"'
					),
					array(
						'alias' => 'Referer',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."referer_id" = "Referer"."id"'
					),
					array(
						'alias' => 'Reseller',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."reseller_id" = "Reseller"."id"'
					),
					array(
						'alias' => 'AddressCorp',
						'table' => 'addresses',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = AddressCorp.merchant_id",
							"AddressCorp.address_type_id = " . "'" . AddressType::CORP_ADDRESS . "'"
						)
					),
					array(
						'alias' => 'AddressMail',
						'table' => 'addresses',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = AddressMail.merchant_id",
							"AddressMail.address_type_id = " . "'" . AddressType::MAIL_ADDRESS . "'"
						)
					),
					array(
						'alias' => 'AddressBusiness',
						'table' => 'addresses',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = AddressBusiness.merchant_id",
							"AddressBusiness.address_type_id = " . "'" . AddressType::BUSINESS_ADDRESS . "'"
						)
					),
					array(
						'alias' => 'MerchantOwner',
						'table' => 'merchant_owners',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = MerchantOwner.merchant_id",
						)
					),
					array(
						'alias' => 'MerchantBank',
						'table' => 'merchant_banks',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "MerchantBank"."merchant_id"'
					),
					array(
						'alias' => 'UwStatusMerchantXref',
						'table' => 'uw_status_merchant_xrefs',
						'type' => 'LEFT',
						'conditions' => array(
							'UwStatusMerchantXref.merchant_id = Merchant.id',
							"UwStatusMerchantXref.uw_status_id = (SELECT id from uw_statuses where name ='Approved')",
							"UwStatusMerchantXref.datetime IS NOT NULL"
						)
					),
					array(
						'alias' => 'UwStatusDeclinedMerchant',
						'table' => 'uw_status_merchant_xrefs',
						'type' => 'LEFT',
						'conditions' => array(
							'UwStatusDeclinedMerchant.merchant_id = Merchant.id',
							"UwStatusDeclinedMerchant.uw_status_id = (SELECT id from uw_statuses where name ='Declined')",
							"UwStatusDeclinedMerchant.datetime IS NOT NULL"
						)
					),
					array(
						'alias' => 'TimelineEntryAgreeEnds',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = TimelineEntryAgreeEnds.merchant_id",
							"TimelineEntryAgreeEnds.timeline_item_id = " . "'" . TimelineItem::AGREEMENT_ENDS . "'"
						)
					),
					array(
						'alias' => 'TimelineEntryIns',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = TimelineEntryIns.merchant_id",
							"TimelineEntryIns.timeline_item_id = " . "'" . TimelineItem::GO_LIVE_DATE . "'"
						)
					),
					array(
						'alias' => 'TimelineEntryInsComm',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = TimelineEntryInsComm.merchant_id",
							"TimelineEntryInsComm.timeline_item_id = " . "'" . TimelineItem::INSTALL_COMMISSIONED . "'"
						)
					),
					array(
						'alias' => 'TimelineEntrySub',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = TimelineEntrySub.merchant_id",
							"TimelineEntrySub.timeline_item_id = " . "'" . TimelineItem::SUBMITTED . "'"
						)
					),
					array(
						'alias' => 'TimelineEntryReSiInSh', //received signed install sheet
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							"Merchant.id = TimelineEntryReSiInSh.merchant_id",
							"TimelineEntryReSiInSh.timeline_item_id = " . "'" . TimelineItem::RECIEVED_SIGNED_INSTALL_SHEET . "'"
						)
					),
					array(
						'alias' => 'MerchantPci',
						'table' => 'merchant_pcis',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "MerchantPci"."merchant_id"'
					),
					array(
						'alias' => 'SaqMerchant',
						'table' => 'saq_merchants',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "SaqMerchant"."merchant_id"'
					),
					array(
						'alias' => 'SaqControlScan',
						'table' => 'saq_control_scans',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "SaqControlScan"."merchant_id"'
					),
					array(
						'alias' => 'MerchantCancellation',
						'table' => 'merchant_cancellations',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "MerchantCancellation"."merchant_id"'
					),
					array(
						'alias' => 'MerchantCancellationSubreason',
						'table' => 'merchant_cancellation_subreasons',
						'type' => 'LEFT',
						'conditions' => '"MerchantCancellationSubreason"."id" = "MerchantCancellation"."merchant_cancellation_subreason_id"'
					),
					array(
						'alias' => 'LastDepositReport',
						'table' => 'last_deposit_reports',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."id" = "LastDepositReport"."merchant_id"'
					),
					array(
						'alias' => 'Network',
						'table' => 'networks',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."network_id" = "Network"."id"'
					),
					array(
						'alias' => 'BetNetwork',
						'table' => 'bet_networks',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."bet_network_id" = "BetNetwork"."id"'
					),
					array(
						'alias' => 'Partner',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."partner_id" = "Partner"."id"'
					),
					array(
						'alias' => 'Entity',
						'table' => 'entities',
						'type' => 'LEFT',
						'conditions' => '"Merchant"."entity_id" = "Entity"."id"'
					),
					array(
						'table' => 'organizations',
						'alias' => 'Organization',
						'type' => 'LEFT',
						'conditions' => array(
							"Organization.id = Merchant.organization_id"
						)
					),
					array(
						'table' => 'regions',
						'alias' => 'Region',
						'type' => 'LEFT',
						'conditions' => array(
							"Region.id = Merchant.region_id"
						)
					),
					array(
						'table' => 'subregions',
						'alias' => 'Subregion',
						'type' => 'LEFT',
						'conditions' => array(
							"Subregion.id = Merchant.subregion_id"
						)
					),
				),
				'conditions' => $conditions,
				'order' => array(
					'Merchant.merchant_mid DESC'
				),
				'group' => array(
					'Client.client_id_global',
					'Client.client_name_global',
					'SfOpportunityIdField.value',
					'Merchant.active',
					'Merchant.merchant_mid',
					'Merchant.merchant_dba',
					'Merchant.merchant_ownership_type',
					'Merchant.merchant_buslevel',
					'Merchant.merchant_ps_sold',
					'Merchant.merchant_bustype',
					'Merchant.merchant_url',
					'Merchant.merchant_tin',
					'MerchantBank.bank_name',
					'MerchantBank.bank_dda_number',
					'MerchantBank.bank_routing_number',
					'MerchantBank.fees_dda_number',
					'MerchantBank.fees_routing_number',
					'VisaBet.name',
					'MasterCardBet.name',
					'DiscoverBet.name',
					'AmexBet.name',
					'MerchantPricing.processing_rate',
					'MerchantPricing.ds_processing_rate',
					'MerchantPricing.amex_processing_rate',
					'MerchantPricing.mc_vi_auth',
					'MerchantPricing.ds_auth_fee',
					'MerchantPricing.amex_auth_fee',
					'MerchantPricing.discount_item_fee',
					'Merchant.ref_p_type',
					'Merchant.ref_p_value',
					'Merchant.ref_p_pct',
					'Merchant.res_p_type',
					'Merchant.res_p_value',
					'Merchant.res_p_pct',
					'MerchantUwVolume.mo_volume',
					'MerchantUwVolume.average_ticket',
					'MerchantUwVolume.card_present_swiped',
					'MerchantUwVolume.card_present_imprint',
					'MerchantUwVolume.card_not_present_keyed',
					'MerchantUwVolume.card_not_present_internet',
					'MerchantUwVolume.visa_volume',
					'MerchantUwVolume.mc_volume',
					'MerchantUwVolume.ds_volume',
					'MerchantUwVolume.amex_volume',
					'User.user_first_name',
					'User.user_last_name',
					'Manager.user_first_name',
					'Manager.user_last_name',
					'Manager2.user_first_name',
					'Manager2.user_last_name',
					'Referer.user_first_name',
					'Referer.user_last_name',
					'Reseller.user_first_name',
					'Reseller.user_last_name',
					'Partner.user_first_name',
					'Partner.user_last_name',
					'AddressCorp.address_title',
					'AddressCorp.address_street',
					'AddressCorp.address_city',
					'AddressCorp.address_state',
					'AddressCorp.address_zip',
					'AddressCorp.address_phone',
					'AddressCorp.address_fax',
					'AddressMail.address_title',
					'AddressMail.address_street',
					'AddressMail.address_city',
					'AddressMail.address_state',
					'AddressMail.address_zip',
					'AddressMail.address_phone',
					'AddressMail.address_fax',
					'AddressBusiness.address_street',
					'AddressBusiness.address_city',
					'AddressBusiness.address_state',
					'AddressBusiness.address_zip',
					'Merchant.merchant_contact',
					'Merchant.merchant_sic',
					'Merchant.merchant_email',
					'MerchantAcquirer.acquirer',
					'MerchantBin.bin',
					'UwStatusMerchantXref.datetime',
					'UwStatusDeclinedMerchant.datetime',
					'TimelineEntryAgreeEnds.timeline_date_completed',
					'TimelineEntryIns.timeline_date_completed',
					'TimelineEntryInsComm.timeline_date_completed',
					'TimelineEntrySub.timeline_date_completed',
					'TimelineEntryReSiInSh.timeline_date_completed',
					'SaqControlScan.pci_compliance',
					'MerchantPci.insurance_fee',
					'MerchantPci.saq_completed_date',
					'MerchantPci.saq_type',
					'MerchantCancellation.date_inactive',
					'MerchantCancellation.date_completed',
					'MerchantCancellation.reason',
					'MerchantCancellationSubreason.name',
					'LastDepositReport.last_deposit_date',
					'Network.network_description',
					'BetNetwork.name',
					'Entity.entity_name',
					'PaymentFusion.is_hw_as_srvc',
					'Organization.name',
					'Region.name',
					'Subregion.name',
					'AchProduct.products_services_type_id',
				),
				'limit' => $limit,
				'offset' => $offset
			);
		$results = self::CSV_HEADERS;
		do {
			$subset = $this->Merchant->find('all', $settings);
			$results .= $this->_makeCsvString($subset, $maskSecureData);
			// Shift offset to fetch next data subset
			$offset += $limit;
			$settings['offset'] = $offset;
		} while (!empty($subset));

		return $results;
	}

/**
 * _makeCsvString
 * builds a CSV string containing data to be used to exported
 *
 * @param array &$reportData data to export as csv
 * @return string
 */
	protected function _makeCsvString(&$reportData, $maskSecureData = true) {
		if (empty($reportData)) {
			return "\n";
		}
		$csvRow = '';
		foreach ($reportData as $data) {
			if (!empty($data['SaqControlScan']['pci_compliance'])) {
				$compliant = $data['SaqControlScan']['pci_compliance'];
			} else {
				$saqType = $data['MerchantPci']['saq_type'];
				$saqCompletedDate = $data['MerchantPci']['saq_completed_date'];

				if ($saqType == 'A' || $saqType == 'B' || $saqType == 'C-VT') {
					// saq_completed_date should be within the last year
					if (strtotime($saqCompletedDate) >= strtotime(date_format(date_modify(new DateTime(date("Y-m-d")), '-365 day'), 'Y-m-d'))) {
						$compliant = 'Yes';
					} else {
						$compliant = 'No';
					}
				} elseif ($saqType == 'A-EP' || $saqType == 'B-IP' || $saqType == 'C' || $saqType == 'D-Merchant' || $saqType == 'D-Service Provider') {
					// saq_completed_date should be within the last 90 days
					if (strtotime($saqCompletedDate) >= strtotime(date_format(date_modify(new DateTime(date("Y-m-d")), '-90 day'), 'Y-m-d'))) {
						$compliant = 'Yes';
					} else {
						$compliant = 'No';
					}
				} else {
					$compliant = 'No';
				}
			}
			$mTaxId = ($this->isEncrypted($data['Merchant']['merchant_tin']))? $this->decrypt($data['Merchant']['merchant_tin'], Configure::read('Security.OpenSSL.key')) : $data['Merchant']['merchant_tin'];
			$accountNum = ($this->isEncrypted($data['MerchantBank']['bank_dda_number']))? $this->decrypt($data['MerchantBank']['bank_dda_number'], Configure::read('Security.OpenSSL.key')) : $data['MerchantBank']['bank_dda_number'];
			$routingNum = ($this->isEncrypted($data['MerchantBank']['bank_routing_number']))? $this->decrypt($data['MerchantBank']['bank_routing_number'], Configure::read('Security.OpenSSL.key')) : $data['MerchantBank']['bank_routing_number'];
			$feesAccountNum = ($this->isEncrypted($data['MerchantBank']['fees_dda_number']))? $this->decrypt($data['MerchantBank']['fees_dda_number'], Configure::read('Security.OpenSSL.key')) : $data['MerchantBank']['fees_dda_number'];
			$feesRoutingNum = ($this->isEncrypted($data['MerchantBank']['fees_routing_number']))? $this->decrypt($data['MerchantBank']['fees_routing_number'], Configure::read('Security.OpenSSL.key')) : $data['MerchantBank']['fees_routing_number'];

			if ($maskSecureData) {
				$mTaxId = (strlen($mTaxId)>0)? "****" . substr($mTaxId, -4) : null;
				$accountNum = (strlen($accountNum)>0)? "****" . substr($accountNum, -4) : null;
				$feesAccountNum = (strlen($feesAccountNum)>0)? "****" . substr($feesAccountNum, -4) : null;
			}

			$csvRow .= (Hash::get($data, 'Merchant.active') == 1)? "Active" : "Inactive";
			$csvRow .= ',="' . $data['Merchant']['merchant_mid'] . '"';
			$csvRow .= ',"' . trim($data['Merchant']['merchant_dba']) . '"';
			$csvRow .= (!empty($data['Client']['name']))? ',"' . trim($data['Client']['name']) . '"' : ",";
			$csvRow .= (!empty($data['AddressCorp']['address_title']))? "," . $this->csvPrepString($data['AddressCorp']['address_title']) : ",";
			$csvRow .= (!empty($data['AddressCorp']['address_street']))? "," . $this->csvPrepString($data['AddressCorp']['address_street']) : ",";
			$csvRow .= (!empty($data['AddressCorp']['address_city']))? "," . $this->csvPrepString($data['AddressCorp']['address_city']) : ",";
			$csvRow .= (!empty($data['AddressCorp']['address_state']))? "," . $this->csvPrepString($data['AddressCorp']['address_state']) : ",";
			$csvRow .= (!empty($data['AddressCorp']['address_zip']))? "," . $this->csvPrepString($data['AddressCorp']['address_zip']) : ",";
			$phone = preg_replace("/(\d{3})(\d{3})(\d{4})/", "($1) $2-$3", $data['AddressCorp']['address_phone']);
			$csvRow .= (!empty($phone))? "," . $this->csvPrepString($phone) : ",";
			$csvRow .= (!empty($data['AddressMail']['address_street']))? "," . $this->csvPrepString($data['AddressMail']['address_street']) : ",";
			$csvRow .= (!empty($data['AddressMail']['address_city']))? "," . $this->csvPrepString($data['AddressMail']['address_city']) : ",";
			$csvRow .= (!empty($data['AddressMail']['address_state']))? "," . $this->csvPrepString($data['AddressMail']['address_state']) : ",";
			$csvRow .= (!empty($data['AddressMail']['address_zip']))? "," . $this->csvPrepString($data['AddressMail']['address_zip']) : ",";
			$csvRow .= (!empty($data['AddressBusiness']['address_street']))? "," . $this->csvPrepString($data['AddressBusiness']['address_street']) : ",";
			$csvRow .= (!empty($data['AddressBusiness']['address_city']))? "," . $this->csvPrepString($data['AddressBusiness']['address_city']) : ",";
			$csvRow .= (!empty($data['AddressBusiness']['address_state']))? "," . $this->csvPrepString($data['AddressBusiness']['address_state']) : ",";
			$csvRow .= (!empty($data['AddressBusiness']['address_zip']))? "," . $this->csvPrepString($data['AddressBusiness']['address_zip']) : ",";
			$csvRow .= (!empty($mTaxId))? "," . $mTaxId : ",";
			$csvRow .= (!empty($data['MerchantBank']['bank_name']))? "," . $this->csvPrepString($data['MerchantBank']['bank_name']) : ",";
			$csvRow .= (!empty($accountNum))? ',="' . $accountNum . '"' : ",";
			$csvRow .= (!empty($routingNum))? ',="' . $routingNum . '"' : ",";
			$csvRow .= (!empty($feesAccountNum))? ',="' . $feesAccountNum . '"' : ",";
			$csvRow .= (!empty($feesRoutingNum))? ',="' . $feesRoutingNum . '"' : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['mo_volume']))? "," . $this->csvPrepString($data['MerchantUwVolume']['mo_volume']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['average_ticket']))? "," . $this->csvPrepString($data['MerchantUwVolume']['average_ticket']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['card_present_swiped']))? "," . $this->csvPrepString($data['MerchantUwVolume']['card_present_swiped']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['card_present_imprint']))? "," . $this->csvPrepString($data['MerchantUwVolume']['card_present_imprint']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['card_not_present_keyed']))? "," . $this->csvPrepString($data['MerchantUwVolume']['card_not_present_keyed']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['card_not_present_internet']))? "," . $this->csvPrepString($data['MerchantUwVolume']['card_not_present_internet']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['visa_volume']))? "," . $this->csvPrepString($data['MerchantUwVolume']['visa_volume']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['mc_volume']))? "," . $this->csvPrepString($data['MerchantUwVolume']['mc_volume']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['ds_volume']))? "," . $this->csvPrepString($data['MerchantUwVolume']['ds_volume']) : ",";
			$csvRow .= (!empty($data['MerchantUwVolume']['amex_volume']))? "," . $this->csvPrepString($data['MerchantUwVolume']['amex_volume']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_ownership_type']))? "," . $this->csvPrepString($data['Merchant']['merchant_ownership_type']) : ",";
			
			$tmpOwners = explode('<ER>', $data['MerchantOwner']['list_of_owners']);
			//the count of ~ represents the number of commas to print as fillers of empty CSV columns when there is no owner data
			if (!isset($ownerColumnNum)) {
				$ownerColumnNum =  !empty($tmpOwners[0])? substr_count($tmpOwners[0], '~') : self::DEFAULT_OWNER_COL_DELIM_COUNT;
			}
			$ownersCsv = '';
			//generate 4 sets of merchant owners each with a number of columns = $ownerColumnNum.
			//When there is less than 4 owners append a number of empty columns as commas equal to $ownerColumnNum
			for($x=0; $x<=3; $x++) {
				$ownerSubset = Hash::get($tmpOwners, $x);
				if (!empty($ownerSubset)) {
					$ownersCsv .= ",".str_replace ( '~' , ',' , $ownerSubset);
				} else {
					//append a bunch of empty CSV columns when no data exists
					$ownersCsv .= ",". str_repeat(',', $ownerColumnNum);
				}
			}
			$csvRow .= $ownersCsv;

			$csvRow .= (!empty($data['Merchant']['merchant_contact']))? "," . $this->csvPrepString($data['Merchant']['merchant_contact']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_ps_sold']))? "," . $this->csvPrepString($data['Merchant']['merchant_ps_sold']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_bustype']))? "," . $this->csvPrepString($data['Merchant']['merchant_bustype']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_url']))? "," . $this->csvPrepString($data['Merchant']['merchant_url']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_buslevel']))? "," . $this->csvPrepString($data['Merchant']['merchant_buslevel']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_sic']))? "," . $this->csvPrepString($data['Merchant']['merchant_sic']) : ",";
			$csvRow .= (!empty($data['MerchantBin']['bin']))? "," . $this->csvPrepString($data['MerchantBin']['bin']) : ",";
			$csvRow .= (!empty($data['MerchantAcquirer']['acquirer']))? "," . $this->csvPrepString($data['MerchantAcquirer']['acquirer']) : ",";
			$csvRow .= (!empty($data['Network']['network_description']))? "," . $this->csvPrepString($data['Network']['network_description']) : ",";
			$csvRow .= (!empty($data['BetNetwork']['name']))? "," . $this->csvPrepString($data['BetNetwork']['name']) : ",";
			$csvRow .= (!empty($data['Merchant']['merchant_email']))? "," . $this->csvPrepString($data['Merchant']['merchant_email']) : ",";
			$csvRow .= "," . $this->csvPrepString($data['User']['user_first_name'] . " " . $data['User']['user_last_name']);
			$csvRow .= (!empty($data['Manager']['name']))? "," . $this->csvPrepString($data['Manager']['name']) : ",";
			$csvRow .= (!empty($data['Manager2']['name']))? "," . $this->csvPrepString($data['Manager2']['name']) : ",";
			$csvRow .= (!empty($data['Merchant']['ref_p_type']))? "," . $this->csvPrepString($data['Merchant']['ref_p_type']) : ",";
			$csvRow .= (!empty($data['Merchant']['ref_p_value']))? "," . $this->csvPrepString($data['Merchant']['ref_p_value']) : ",";
			$csvRow .= (!empty($data['Merchant']['ref_p_pct']))? "," . $this->csvPrepString($data['Merchant']['ref_p_pct']) : ",";
			$csvRow .= (!empty($data['Merchant']['res_p_type']))? "," . $this->csvPrepString($data['Merchant']['res_p_type']) : ",";
			$csvRow .= (!empty($data['Merchant']['res_p_value']))? "," . $this->csvPrepString($data['Merchant']['res_p_value']) : ",";
			$csvRow .= (!empty($data['Merchant']['res_p_pct']))? "," . $this->csvPrepString($data['Merchant']['res_p_pct']) : ",";
			$csvRow .= (!empty($data['UwStatusMerchantXref']['datetime']))? "," . $this->csvPrepString($data['UwStatusMerchantXref']['datetime']) : ",";
			$csvRow .= (!empty($data['UwStatusDeclinedMerchant']['datetime']))? "," . $this->csvPrepString($data['UwStatusDeclinedMerchant']['datetime']) : ",";
			$csvRow .= (!empty($data['TimelineEntryIns']['timeline_date_completed']))? "," . $this->csvPrepString($data['TimelineEntryIns']['timeline_date_completed']) : ",";
			$csvRow .= (!empty($data['TimelineEntryInsComm']['timeline_date_completed']))? "," . $this->csvPrepString($data['TimelineEntryInsComm']['timeline_date_completed']) : ",";
			$csvRow .= (!empty($data['TimelineEntrySub']['timeline_date_completed']))? "," . $this->csvPrepString($data['TimelineEntrySub']['timeline_date_completed']) : ",";
			$csvRow .= (!empty($data['TimelineEntryReSiInSh']['timeline_date_completed']))? "," . $this->csvPrepString($data['TimelineEntryReSiInSh']['timeline_date_completed']) : ",";
			$csvRow .= "," . $compliant;
			$csvRow .= (!empty($data['MerchantPci']['insurance_fee']))? "," . $this->csvPrepString($data['MerchantPci']['insurance_fee']) : ",";
			$csvRow .= (!empty($data['MerchantCancellation']['date_inactive']))? "," . $this->csvPrepString($data['MerchantCancellation']['date_inactive']) : ",";
			$csvRow .= (!empty($data['MerchantCancellation']['date_completed']))? "," . $this->csvPrepString($data['MerchantCancellation']['date_completed']) : ",";
			$csvRow .= (!empty($data['Referer']['name']))? "," . $this->csvPrepString($data['Referer']['name']) : ",";
			$csvRow .= (!empty($data['Reseller']['name']))? "," . $this->csvPrepString($data['Reseller']['name']) : ",";
			$csvRow .= (!empty($data['LastDepositReport']['last_deposit_date']))? "," . $this->csvPrepString($data['LastDepositReport']['last_deposit_date']) : ",";
			$csvRow .= (!empty($data['VisaBet']['name']))? "," . $this->csvPrepString($data['VisaBet']['name'] .'/'. $data['MasterCardBet']['name']) : ",";
			$csvRow .= (!empty($data['DiscoverBet']['name']))? "," . $this->csvPrepString($data['DiscoverBet']['name']) : ",";
			$csvRow .= (!empty($data['AmexBet']['name']))? "," . $this->csvPrepString($data['AmexBet']['name']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['processing_rate']))? "," . $this->csvPrepString($data['MerchantPricing']['processing_rate']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['ds_processing_rate']))? "," . $this->csvPrepString($data['MerchantPricing']['ds_processing_rate']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['amex_processing_rate']))? "," . $this->csvPrepString($data['MerchantPricing']['amex_processing_rate']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['mc_vi_auth']))? "," . $this->csvPrepString($data['MerchantPricing']['mc_vi_auth']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['ds_auth_fee']))? "," . $this->csvPrepString($data['MerchantPricing']['ds_auth_fee']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['amex_auth_fee']))? "," . $this->csvPrepString($data['MerchantPricing']['amex_auth_fee']) : ",";
			$csvRow .= (!empty($data['MerchantPricing']['discount_item_fee']))? "," . $this->csvPrepString($data['MerchantPricing']['discount_item_fee']) : ",";
			$csvRow .= (!empty($data['Partner']['name']))? "," . $this->csvPrepString($data['Partner']['name']) : ",";
			$csvRow .= (!empty($data['Entity']['entity_name']))? "," . $this->csvPrepString($data['Entity']['entity_name']) : ",";
			$csvRow .= (!empty($data['Organization']['name']))? "," . $this->csvPrepString($data['Organization']['name']) : ",";
			$csvRow .= (!empty($data['Region']['name']))? "," . $this->csvPrepString($data['Region']['name']) : ",";
			$csvRow .= (!empty($data['Subregion']['name']))? "," . $this->csvPrepString($data['Subregion']['name']) : ",";
			$csvRow .= (!empty($data['PaymentFusion']['is_hw_as_srvc']))? ",YES" : ","; //only when true

			$csvRow .= (!empty($data['AchProduct']['products_services_type_id']))? ",TRUE" : ",FALSE";
			$cancelReason = trim(Hash::get($data, 'MerchantCancellationSubreason.name', '') . ' ' . Hash::get($data, 'MerchantCancellation.reason', ''));
			$csvRow .= (!empty($cancelReason))?  "," . $this->csvPrepString($cancelReason) : ",";
			$csvRow .= (!empty($data['TimelineEntryAgreeEnds']['timeline_date_completed']))? "," . $this->csvPrepString($data['TimelineEntryAgreeEnds']['timeline_date_completed']) : ",";
			$csvRow .= (!empty(Hash::get($data, 'SfOpportunityIdField.value')))? ',"' . trim(Hash::get($data, 'SfOpportunityIdField.value')) . '"' : ",";
			$csvRow .= "\n";
		}
		return $csvRow;
	}

/**
 * logUnsecureExport
 * 
 * @param  array $user data about current User exporting unsecure data
 * @param  array  $filterArgs the filter arguments used to generate the report
 * @return void
 */
	public function logUnsecureExport(array $user, array $filterArgs) {
		$SysTran = ClassRegistry::init('SystemTransaction');
		ClassRegistry::init('TransactionType');
		$transaction = [
			'transaction_type_id' => TransactionType::UNSECURED_DATA_EXPORTED,
			'system_transaction_date' => date('Y-m-d'),
			'system_transaction_time' => date('H:i:s'),
			'user_id' => Hash::get($user, 'id'),
			'login_date' => Hash::get($user, 'last_login_date'),
			'client_address' => Router::getRequest()->clientIp(false),
		];
		$SysTran->create();
		$SysTran->save($transaction);
	}

}
