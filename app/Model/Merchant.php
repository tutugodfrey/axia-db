<?php

App::uses('AppModel', 'Model');
App::uses('Address', 'Model');
App::uses('MerchantUwVolume', 'Model');
App::uses('AddressType', 'Model');
App::uses('MerchantCancellation', 'Model');
App::uses('TimelineItem', 'Model');
App::uses('ProductsServicesType', 'Model');
App::uses('SalesForce', 'Model');
App::uses('AchProvider', 'Model');

/**
 * Merchant Model
 *
 */
/*********Begin API Annotations for swagger-php for MerchantsController::api_add() method ***/
/**
 * 
 * @OA\Post(
 *   path="/api/Merchants/add",
 *	 tags={"Merchants"},
 *   summary="Add a new merchant/client account.",
 *   @OA\RequestBody(ref="#/components/requestBodies/Merchants"),
 *	 @OA\Response(
 *     response=200,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *		   example={"status": "[success/failed]", "messages": "[string or single dimentional array of status related messages]"},
 *     ),
 *     description="
 * 			On status success: 
 *				{'status': 'success', 'messages': 'Merchant account created!'}
 *
 *			On status failed a string or array of operation-specific errors will be returned as the value for the 'messages' array key.
 *			Furthermore, if no data is sent operation will return status:failed"
 *   ),
 *   @OA\Response(
 *     response=405,
 *     description="HTTP method not allowed when request method is not POST",
 *	   @OA\MediaType(
 *         mediaType="application/json",
 *  	   example={"status": "failed", "messages": "HTTP method not allowed"},
 *     ),
 *   ),
 * )
 */

/**
 *
 * @OA\RequestBody(
 *     request="Merchants",
 *	   description="Example JSON data to create a merchant/client account",
 *     @OA\MediaType(
 *          mediaType="application/json",
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="MID",
 *					description="MID required",
 *                  type="integer",
 *                  maxLength=20,
 *					example="1239123654789632"
 *              ),
 *              @OA\Property(
 *                  property="CompanyBrandName",
 *                  type="string",
 *					description="Accepted Values: Axia Med, Axia Tech",
 *					example="Axia Med"
 *              ),
 *              @OA\Property(
 *                  property="CorpName",
 *                  type="string",
 *					maxLength=150,
 *					description="Corporation/Company Name (Required)",
 *					example="Family Health Service"
 *              ),
 *              @OA\Property(
 *                  property="DBA",
 *                  type="string",
 *					maxLength=100,
 *					description="DBA Name (Required)",
 *					example="Family Health"
 *              ),
 *              @OA\Property(
 *                  property="CorpAddress",
 *                  type="string",
 *					maxLength=100,
 *					description="Address (Required)",
 *					example="1234 Narrow Street"
 *              ),
 *              @OA\Property(
 *                  property="CorpCity",
 *                  type="string",
 *					maxLength=50,
 *					description="City (Required)",
 *					example="Red Bluff"
 *              ),
 *              @OA\Property(
 *                  property="CorpState",
 *                  type="string",
 *					maxLength=2,
 *					description="Two-letter State (Required)",
 *					example="CA"
 *              ),
 *              @OA\Property(
 *                  property="CorpZip",
 *                  type="string",
 *					maxLength=20,
 *					description="Zip code (Required)",
 *					example="96080"
 *              ),
 *              @OA\Property(
 *                  property="CorpPhone",
 *                  type="string",
 *					maxLength=20,
 *					description="Phone (Required)",
 *					example="(530)555-5789"
 *              ),
 *              @OA\Property(
 *                  property="EMail",
 *                  type="string",
 *					maxLength=50,
 *					description="Company contact email",
 *					example="janedoe@nomail.com"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Name",
 *                  type="string",
 *					maxLength=100,
 *					description="Owner/Officer Name (Required)",
 *					example="Jane Doe"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Title",
 *                  type="string",
 *					maxLength=40,
 *					description="Owner/Officer Title",
 *					example="CEO"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Equity",
 *                  type="integer",
 *					minimum=0,
 *					maximum=100,
 *					description="Owner/Officer Equity percent",
 *					example="55"
 *              ),
 *              @OA\Property(
 *                  property="OwnerSSN",
 *                  type="string",
 *					description="Owner/Officer SSN",
 *					example="123-45-6789"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Address",
 *                  type="string",
 *					maxLength=100,
 *					description="Owner/Officer Address",
 *					example="1234 Long Street"
 *              ),
 *              @OA\Property(
 *                  property="Owner1City",
 *                  type="string",
 *					maxLength=50,
 *					description="Owner/Officer City",
 *					example="Red Bluff"
 *              ),
 *              @OA\Property(
 *                  property="Owner1State",
 *                  type="string",
 *					maxLength=2,
 *					description="Owner/Officer State",
 *					example="CA"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Zip",
 *                  type="string",
 *					maxLength=20,
 *					description="Zip code",
 *					example="96080"
 *              ),
 *              @OA\Property(
 *                  property="Owner1Phone",
 *                  type="string",
 *					maxLength=20,
 *					description="Owner/Officer Phone",
 *					example="(530)555-9875"
 *              ),
 *              @OA\Property(
 *                  property="BankName",
 *                  type="string",
 *					description="Bank Name (Required)",
 *					example="Cornerstone Community Bank"
 *              ),
 *              @OA\Property(
 *                  property="RoutingNum",
 *                  type="string",
 *					description="Company Bank Routing Number (Required)",
 *					example="121144476"
 *              ),
 *              @OA\Property(
 *                  property="AccountNum",
 *                  type="string",
 *					description="Company Bank Account Number (Required)",
 *					example="1234567890"
 *              ),
 *              @OA\Property(
 *                  property="FeesRoutingNum",
 *                  type="string",
 *					description="Company Bank Routing Number for fees",
 *					example="121144476"
 *              ),
 *              @OA\Property(
 *                  property="FeesAccountNum",
 *                  type="string",
 *					description="Company Bank Account Number for fees",
 *					example="1234567890"
 *              ),
 *              @OA\Property(
 *                  property="oaID",
 *                  type="integer",
 *					description="OnlineAPP Id",
 *					example="12345"
 *              ),
 *              @OA\Property(
 *                  property="setup_referrer",
 *                  type="string",
 *					description="Referrer user full name must match existing database user.",
 *					example="Albert Smith"
 *              ),
 *              @OA\Property(
 *                  property="setup_referrer_pct_profit",
 *                  type="decimal",
 *					description="Referrer profit percent. This will be used if no referrer volume percent.",
 *					example="80.50"
 *              ),
 *              @OA\Property(
 *                  property="setup_referrer_pct_volume",
 *                  type="decimal",
 *					description="Referrer volume percent. This will be used if no referrer profit percent.",
 *					example="50"
 *              ),
 *              @OA\Property(
 *                  property="setup_referrer_pct_gross",
 *                  type="integer",
 *					description="Referrer percent of gross profit.",
 *					example="100"
 *              ),
 *              @OA\Property(
 *                  property="setup_reseller",
 *                  type="string",
 *					description="Reseller user full name must match existing database user.",
 *					example="Mr. Reseller Jackson"
 *              ),
 *              @OA\Property(
 *                  property="setup_reseller_pct_profit",
 *                  type="decimal",
 *					description="Reseller profit percent. This will be used if no reseller volume percent.",
 *					example="80.50"
 *              ),
 *              @OA\Property(
 *                  property="setup_reseller_pct_volume",
 *                  type="decimal",
 *					description="Reseller volume percent. This will be used if no reseller profit percent.",
 *					example="50"
 *              ),
 *              @OA\Property(
 *                  property="setup_reseller_pct_gross",
 *                  type="integer",
 *					description="Reseller percent of gross profit.",
 *					example="100"
 *              ),
 *              @OA\Property(
 *                  property="setup_partner",
 *                  type="string",
 *					description="Partner user full name must match existing database user.",
 *					example="Health Partner"
 *              ),
 *              @OA\Property(
 *                  property="org_name",
 *                  type="string",
 *					maxLength=100,
 *					description="Merchant/Client Organization name",
 *					example="Family Health Corporation"
 *              ),
 *              @OA\Property(
 *                  property="region_name",
 *                  type="string",
 *					maxLength=100,
 *					description="Merchant/Client Region name is a subset/child of and belonging to the parent Organization",
 *					example="Red Bluff Fam Health"
 *              ),
 *              @OA\Property(
 *                  property="subregion_name",
 *                  type="string",
 *					maxLength=100,
 *					description="Merchant/Client Sub-Region name is a subset/child of and belonging to parent Region",
 *					example=""
 *              ),
 *              @OA\Property(
 *                  property="expected_install_date",
 *                  type="date",
 *					pattern="YYYY-mm-dd",
 *					description="AKA Expected Go-live date, is the approximate date when any hardware/software install will be completed",
 *					example="2020-01-25"
 *              ),
 *              @OA\Property(
 *                  property="Gateway_Name",
 *                  type="string",
 *					maxLength=100,
 *					description="Gateway name must match an existing gateway in the database",
 *					example="Paytrace Pro"
 *              ),
 *              @OA\Property(
 *                  property="Gateway_ID",
 *                  type="integer",
 *					maxLength=20,
 *					description="A unique id for the specific client's gateway account",
 *					example="12345"
 *              ),
 *              @OA\Property(
 *                  property="Merch_Gateway_Rate",
 *                  type="decimal",
 *					description="A rate up to 3 decimals",
 *					example="3.25"
 *              ),
 *              @OA\Property(
 *                  property="Merch_Gateway_Per_Item_Fee",
 *                  type="decimal",
 *					description="Monetary per item fee up to 3 decimals",
 *					example="10.00"
 *              ),
 *              @OA\Property(
 *                  property="Merch_Gateway_Monthly_Fee",
 *                  type="decimal",
 *					description="Monetary monthly fee up to 3 decimals",
 *					example="25.50"
 *              ),
 *              @OA\Property(
 *                  property="Gateway_Monthly_Vol",
 *                  type="decimal",
 *					description="Monetary monthly gateway volume up to 3 decimals",
 *					example="9999999.999"
 *              ),
 *              @OA\Property(
 *                  property="Gateway_Item_Count",
 *                  type="integer",
 *					description="Monthly count of number items",
 *					example="99999999"
 *              ),
 *              @OA\Property(
 *                  property="PaymentFusion",
 *                  type="string",
 *					description="Whether payment fusion was accepted.",
 *					example="YES"
 *              ),
 *              @OA\Property(
 *                  property="PF_ID",
 *					maxLength=20,
 *                  type="integer",
 *					description="A unique id for the specific client's Payment Fusion product",
 *					example="YES"
 *              ),
 *              @OA\Property(
 *                  property="PF_Account_Fee",
 *                  type="decimal",
 *					description="A monthly fee aount of up to 3 decimals",
 *					example="25.00"
 *              ),
 *              @OA\Property(
 *                  property="PF_Rate",
 *                  type="decimal",
 *					description="A rate up to 3 decimals",
 *					example="2.10"
 *              ),
 *              @OA\Property(
 *                  property="PF_Per_Item_Fee",
 *                  type="decimal",
 *					description="Monetary per item fee up to 3 decimals",
 *					example="2.10"
 *              ),
 *              @OA\Property(
 *                  property="PF_Standard_Device_Qty",
 *                  type="integer",
 *					description="Quantity count of standard Payment Fusion devices aquired by client",
 *					example="1"
 *              ),
 *              @OA\Property(
 *                  property="PF_Standard_Device_Fee",
 *                  type="decimal",
 *					description="Fee per each standard Payment Fusion device, up to 3 decimals",
 *					example="5.00"
 *              ),
 *              @OA\Property(
 *                  property="PF_VP2PE_Device_Qty",
 *                  type="integer",
 *					description="Quantity count of VP2PE Payment Fusion devices aquired by client",
 *					example="1"
 *              ),
 *              @OA\Property(
 *                  property="PF_VP2PE_Device_Fee",
 *                  type="decimal",
 *					description="Fee per each VP2PE Payment Fusion device, up to 3 decimals",
 *					example="5.00"
 *              ),
 *              @OA\Property(
 *                  property="PF_PFCC_Device_Qty",
 *                  type="integer",
 *					description="Quantity count of PFCC Payment Fusion devices aquired by client",
 *					example="1"
 *              ),
 *              @OA\Property(
 *                  property="PF_PFCC_Device_Fee",
 *                  type="decimal",
 *					description="Fee per each PFCC Payment Fusion device, up to 3 decimals",
 *					example="5.00"
 *              ),
 *              @OA\Property(
 *                  property="PF_VP2PE_PFCC_Device_Qty",
 *                  type="integer",
 *					description="Quantity of Payment Fusion devices that support VP2PE and PFCC aquired by client",
 *					example="1"
 *              ),
 *              @OA\Property(
 *                  property="PF_VP2PE_PFCC_Device_Fee",
 *                  type="decimal",
 *					description="Fee per each Payment Fusion device that support VP2PE and PFCC, up to 3 decimals",
 *					example="10.00"
 *              ),
 *              @OA\Property(
 *                  property="ach_accepted",
 *                  type="string",
 *					description="Whether ACH processing service was accepted by client.",
 *					example="YES"
 *              ),
 *              @OA\Property(
 *                  property="ach_provider_name",
 *                  type="string",
 *					description="Name of ACH provider that matches an existing one in the database",
 *					example="Sage Payments"
 *              ),
 *              @OA\Property(
 *                  property="ach_annual_volume",
 *                  type="decimal",
 *					description="Expected annual ACH sales volume (monetary amount up to 3 decimals)",
 *					example="9999999999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_application_fee",
 *                  type="decimal",
 *					description="Application fee (monetary amount up to 3 decimals)",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_expedite_fee",
 *                  type="decimal",
 *					description="Expedited sign up service fee (monetary amount up to 3 decimals)",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_average_transaction",
 *                  type="decimal",
 *					description="Average ACH transaction amount (monetary amount up to 3 decimals)",
 *					example="99999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_max_transaction",
 *                  type="decimal",
 *					description="Estimated maximum ACH transaction amount (monetary amount up to 3 decimals)",
 *					example="999999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_rate",
 *                  type="decimal",
 *					description="Rate up to 3 decimals",
 *					example="99.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_per_item_fee",
 *                  type="decimal",
 *					description="Fee per each ACH item",
 *					example="999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_statement_fee",
 *                  type="decimal",
 *					description="Monthly statement fee",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_batch_upload_fee",
 *                  type="decimal",
 *					description="Batch upload fee",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_reject_fee",
 *                  type="decimal",
 *					description="Rejected ACH fee",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_monthly_gateway_fee",
 *                  type="decimal",
 *					description="Monthtly gateway fee",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_monthly_minimum_fee",
 *                  type="decimal",
 *					description="Monthtly minimum fee",
 *					example="9999.999"
 *              ),
 *              @OA\Property(
 *                  property="ach_disbursment_bank_name",
 *                  type="string",
 *                  maxLength=50,
 *					description="Name of Bank for disbursment",
 *					example="Bank of the West"
 *              ),
 *              @OA\Property(
 *                  property="ach_disbursment_routing",
 *                  type="string",
 *					description="Bank routing number for disbursment",
 *					example="123456789"
 *              ),
 *              @OA\Property(
 *                  property="ach_disbursment_account",
 *                  type="string",
 *					description="Bank account number for disbursment",
 *					example="123456789"
 *              ),
 *              @OA\Property(
 *                  property="ach_fees_bank_name",
 *                  type="string",
 *                  maxLength=50,
 *					description="Name of Bank for fees",
 *					example="Bank of the West"
 *              ),
 *              @OA\Property(
 *                  property="ach_fees_routing",
 *                  type="string",
 *					description="Bank routing number for fees",
 *					example="123456789"
 *              ),
 *              @OA\Property(
 *                  property="ach_fees_account",
 *                  type="string",
 *					description="Bank account number for fees",
 *					example="123456789"
 *              ),
 *              @OA\Property(
 *                  property="ach_rejects_bank_name",
 *                  type="string",
 *                  maxLength=50,
 *					description="Name of Bank for rejects",
 *					example="Bank of the West"
 *              ),
 *              @OA\Property(
 *                  property="ach_rejects_routing",
 *                  type="string",
 *					description="Bank routing number for rejects",
 *					example="123456789"
 *              ),
 *              @OA\Property(
 *                  property="ach_rejects_account",
 *                  type="string",
 *					description="Bank account number for rejects",
 *					example="123456789"
 *              ),
 *              example={
 *					"MID": "1239123654789632",
 *					"CompanyBrandName": "Axia Med",
 *					"CorpName": "Family Health Service",
 *					"DBA": "Family Health",
 *					"CorpAddress": "1234 Narrow Street",
 *					"CorpCity": "Red Bluff",
 *					"CorpState": "CA",
 *					"CorpZip": "96080",
 *					"CorpPhone": "(530)555-5789",
 *					"Owner1Name": "Jane Doe",
 *					"Owner1Title": "CEO",
 *					"Owner1Equity": "100",
 *					"OwnerSSN": "123-45-6789",
 *					"Owner1Address": "1234 Long Street",
 *					"Owner1City": "Red Bluff",
 *					"Owner1State": "CA",
 *					"Owner1Zip": "96080",
 *					"Owner1Phone": "(530)555-9875",
 *					"Owner1Email": "janedoe@nomail.com",
 *					"ContractorID": "Jess Noss",
 *					"Contact": "Jane Doe",
 *					"Address": "999 Wide Street",
 *					"City": "Red Bluff",
 *					"State": "CA",
 *					"Zip": "96080",
 *					"PhoneNum": "(530)555-9875",
 *					"EMail": "janedoe@nomail.com",
 *					"RoutingNum": "121144476",
 *					"AccountNum": "1234567890",
 *					"FeesRoutingNum": "121144476",
 *					"FeesAccountNum": "1234567890",
 *					"oaID": "99999",
 *					"setup_referrer": "Mr. Referrer Smith",
 *					"setup_reseller": "Mr. Reseller Jackson",
 *					"setup_referrer_pct_profit": "10",
 *					"setup_referrer_pct_volume": "5",
 *					"setup_referrer_pct_gross": "100",
 *					"setup_reseller_pct_profit": "0",
 *					"setup_reseller_pct_volume": "0",
 *					"setup_reseller_pct_gross": "100",
 *					"setup_partner": "Health Partner",
 *					"org_name": "Family Health Corporation",
 *					"region_name": "Red Bluff Fam Health",
 *					"subregion_name": "",
 *					"expected_install_date": "2020-01-25",
 *					"Gateway": "",
 *					"Gateway_Name": "Paytrace Pro",
 *					"Gateway_ID": "12345",
 *					"Merch_Gateway_Rate": "3.25",
 *					"Merch_Gateway_Per_Item_Fee": "10",
 *					"Merch_Gateway_Monthly_Fee": "25",
 *					"Gateway_Monthly_Vol": "5000",
 *					"Gateway_Item_Count": "1000",
 *					"PaymentFusion": "YES",
 *					"PF_ID": "12345",
 *					"PF_Account_Fee": "25",
 *					"PF_Rate": "2.10",
 *					"PF_Per_Item_Fee": ".50",
 *					"PF_Standard_Device_Qty": "1",
 *					"PF_Standard_Device_Fee": "5.00",
 *					"PF_VP2PE_Device_Qty": "1",
 *					"PF_VP2PE_Device_Fee": "2.00",
 *					"PF_PFCC_Device_Qty": "1",
 *					"PF_PFCC_Device_Fee": "5.00",
 *					"PF_VP2PE_PFCC_Device_Qty": "1",
 *					"PF_VP2PE_PFCC_Device_Fee": "10.00",
 *					"ach_accepted": "NO",
 *					"ach_provider_name": "",
 *					"ach_annual_volume": "",
 *					"ach_application_fee": "",
 *					"ach_expedite_fee": "",
 *					"ach_average_transaction": "",
 *					"ach_max_transaction": "",
 *					"ach_rate": "",
 *					"ach_per_item_fee": "",
 *					"ach_statement_fee": "",
 *					"ach_batch_upload_fee": "",
 *					"ach_reject_fee": "",
 *					"ach_monthly_gateway_fee": "",
 *					"ach_monthly_minimum_fee": "",
 *					"ach_disbursment_bank_name": "",
 *					"ach_disbursment_routing": "",
 *					"ach_disbursment_account": "",
 *					"ach_fees_bank_name": "",
 *					"ach_fees_routing": "",
 *					"ach_fees_account": "",
 *					"ach_rejects_bank_name": "",
 *					"ach_rejects_routing": "",
 *					"ach_rejects_account": ""
 *				}
 *          )
 *      )
 * )
 */
class Merchant extends AppModel {
	const PERCENTAGE = 'percentage';
	const PTS_CALC_ONLY = 'points-calculateonly';
	const PCT_MINUS_GP = "percentage-grossprofit";
	const POINTS_MINUS_GP = "points";
	const B_LVL_RETAIL = "Retail";
	const B_LVL_RESTAURANT = "Restaurant";
	const B_LVL_LODGE = "Lodging";
	const B_LVL_MOTO = "MOTO";
	const B_LVL_NET = "Internet";
	const B_LVL_GROCERY = "Grocery";
	const AX_TECH_DEFAULT_BIN = '424550';
	const AX_I3_DEFAULT_BIN = '449778';
	const AX_TECH_MID_PREFIX = '1239';
	const AX_PAY_MID_PREFIX = '7641';
	const AMEX_CSV_AMEXNUM = 'AmexNum';
	const AMEX_CSV_AENEW = 'AENew';
	const AMEX_CSV_AMEX = 'AmEx';
	const AMEX_CSV_ACCEPT_NEW = 'DoYouWantToAcceptAE-New';
	const AMEX_CSV_ACCEPT_NOTNEW = 'DoYouWantToAcceptAE-NotNew';
	const AMEX_CSV_ACCEPT_YES = 'DoYouWantToAcceptAE-Yes';
	const AMEX_CSV_ACCEPT_EXIST = 'DoYouAcceptAE-Exist';
	const AMEX_CSV_ACCEPT_NOT_EXIST = 'DoYouAcceptAE-NotExisting';
	const VERICHECK_DEFAULT_PARTNER = 'Experian Health';

/**
 *
 * @var array $csvAffirm various affirmation values commonly used in merchant upload csv files
 */
	public $saleResourceOrigin = [
		'Reseller' => 'Reseller',
		'Direct' => 'Direct'
	];
/**
 *
 * @var array $csvAffirm various affirmation values commonly used in merchant upload csv files
 */
	public $csvAffirm = [
		"Yes", "yes",
		"On",
		"TRUE",
	];
/**
 * The presence of these expected CSV file header names will be detected during upload of CSV merchant data
 *
 * @var array various headers used in merchant upload csv files
 */
	public $gw1CsvHeaders = ['Gateway', 'Gateway_Name', 'Gateway_ID', 'Merch_Gateway_Rate', 'Merch_Gateway_Per_Item_Fee', 'Merch_Gateway_Monthly_Fee', 'Gateway_Monthly_Vol', 'Gateway_Item_Count'];
	public $pmntFusionCsvHeaders = [
		'PaymentFusion',
		'PF_ID',
		'PF_Feature',
		'PF_Account_Fee',
		'PF_Rate',
		'PF_Per_Item_Fee',
		'PF_Standard_Device_Qty',
		'PF_Standard_Device_Fee',
		'PF_VP2PE_Device_Qty',
		'PF_VP2PE_Device_Fee',
		'PF_PFCC_Device_Qty',
		'PF_PFCC_Device_Fee',
		'PF_VP2PE_PFCC_Device_Qty',
		'PF_VP2PE_PFCC_Device_Fee',

	];
	public $achCsvHeaders = [
		'ach_accepted',
		'ach_provider_name',
		'ach_annual_volume',
		'ach_application_fee',
		'ach_expedite_fee',
		'ach_average_transaction',
		'ach_max_transaction',
		'ach_rate',
		'ach_per_item_fee',
		'ach_statement_fee',
		'ach_batch_upload_fee',
		'ach_reject_fee',
		'ach_monthly_gateway_fee',
		'ach_monthly_minimum_fee',
		'ach_disbursment_bank_name',
		'ach_disbursment_routing',
		'ach_disbursment_account',
		'ach_fees_bank_name',
		'ach_fees_routing',
		'ach_fees_account',
		'ach_rejects_bank_name',
		'ach_rejects_routing',
		'ach_rejects_account',
	];
	public $name = 'Merchant';

	public $actsAs = [
		'Search.Searchable',
		'SearchByUserId' => [
			'userRelatedModel' => 'Merchant'
		],
		'Containable',
		'ChangeRequest',
	];

	public $filterArgs = [
		'active' => ['type' => 'value'],
		'search' => ['type' => 'query', 'method' => 'orConditions'],
		'user_id' => [
			'type' => 'subquery',
			'method' => 'searchByUserId',
			'field' => '"Merchant"."user_id"',
			'searchByMerchantEntity' => true,
		],
		'partner_id' => [
			'type' => 'value',
			'field' => '"Merchant"."partner_id"',
		],
		'organization_id' => [
			'type' => 'value',
			'field' => '"Merchant"."organization_id"',
		],
		'region_id' => [
			'type' => 'value',
			'field' => '"Merchant"."region_id"',
		],
		'subregion_id' => [
			'type' => 'value',
			'field' => '"Merchant"."subregion_id"',
		],
		'location_description' => [
			'type' => 'subquery',
			'method' => 'searchByLocation',
			'field' => '"Merchant"."id"'
		],
	];

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Please select a Rep'
			)
		),
		'merchant_dba' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be blank'
			),
		),
        'merchant_mid' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'related_acquiring_mid' => array(
            'is_existing_mid' => array(
                'rule' => array('is_existing_mid'),
                'message' => 'This MID does not exist or is not yet assigned to any other merchants!',
                'required' => false,
                'allowEmpty' => true,
            ),
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'merchant_contact' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'merchant_email' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'merchant_tin' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'merchant_d_and_b' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'merchant_mail_contact' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
        'source_of_sale' => array(
            'input_has_only_valid_chars' => array(
                'rule' => array('inputHasOnlyValidChars'),
                'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
                'required' => false,
                'allowEmpty' => true,
            ),
        ),
		'ref_p_pct' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'res_p_pct' => array(
			'rule' => array('validPercentage'),
			'allowEmpty' => true,
		),
		'ref_p_value' => array(
			'validValue' => array(
				'rule' => array('validValue'),
				'allowEmpty' => true,
				'message' => '',
			)
		),
		'res_p_value' => array(
			'validValue' => array(
				'rule' => array('validValue'),
				'allowEmpty' => true,
				'message' => '',
			)
		),
		'bet_network_id' => array(
			'conditionallyRequired' => array(
				'rule' => array('conditionallyRequired'),
				'message' => 'This option is required for this merchant.'
			)
		),
		'merchant_acquirer_id' => array(
			'conditionallyRequired' => array(
				'rule' => array('conditionallyRequired'),
				'message' => 'This option is required for this merchant.'
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MerchantType' => array(
			'className' => 'MerchantType',
			'foreignKey' => 'merchant_type_id',
		),'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
        ),
		'WomplyStatus' => array(
			'className' => 'WomplyStatus',
			'foreignKey' => 'womply_status_id',
		),
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id',
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
		),
		'Subregion' => array(
			'className' => 'Subregion',
			'foreignKey' => 'subregion_id',
		),
		'Referer' => array(
			'className' => 'User',
			'foreignKey' => 'referer_id',
		),
		'Reseller' => array(
			'className' => 'User',
			'foreignKey' => 'reseller_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ManagerUser' => array(
			'className' => 'User',
			'foreignKey' => 'sm_user_id',
		),
		'SecondManagerUser' => array(
			'className' => 'User',
			'foreignKey' => 'sm2_user_id',
		),
		'Network' => array(
			'className' => 'Network',
			'foreignKey' => 'network_id',
		),
		'BackEndNetwork' => array(
			'className' => 'BackEndNetwork',
			'foreignKey' => 'back_end_network_id',
		),
		'MerchantAcquirer' => array(
			'className' => 'MerchantAcquirer',
			'foreignKey' => 'merchant_acquirer_id',
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
		'CancellationFee' => array(
			'className' => 'CancellationFee',
			'foreignKey' => 'cancellation_fee_id',
		),
		'MerchantBin' => array(
			'className' => 'MerchantBin',
			'foreignKey' => 'merchant_bin_id',
		),
		'MerchantAcquirer' => array(
			'className' => 'MerchantAcquirer',
			'foreignKey' => 'merchant_acquirer_id',
		),
		'OriginalAcquirer' => array(
			'className' => 'OriginalAcquirer',
			'foreignKey' => 'original_acquirer_id',
		),
		'BetNetwork' => array(
			'className' => 'BetNetwork',
			'foreignKey' => 'bet_network_id',
		),
		'Partner' => array(
			'className' => 'User',
			'foreignKey' => 'partner_id',
		),
		'Entity' => array(
			'className' => 'Entity',
			'foreignKey' => 'entity_id',
		),
		'Brand' => array(
			'className' => 'Brand',
			'foreignKey' => 'brand_id'
		),
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'ImportedDataCollection' => array(
			'className' => 'ImportedDataCollection',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'AssociatedExternalRecord' => array(
			'className' => 'AssociatedExternalRecord',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'SalesForceAccount' => array(
			'className' => 'ExternalRecordField',
			'foreignKey' => 'merchant_id',
			'dependent' => false,
			'conditions' => array('SalesForceAccount.field_name' => SalesForce::ACCOUNT_ID)
		),
		'UsersProductsRisk' => array(
			'className' => 'UsersProductsRisk',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantBank' => array(
			'className' => 'MerchantBank',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Ach' => array(
			'className' => 'Ach',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'WebBasedAch' => array(
			'className' => 'WebBasedAch',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'CheckGuarantee' => array(
			'className' => 'CheckGuarantee',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'SaqMerchant' => array(
			'className' => 'SaqMerchant',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantCancellation' => array(
			'className' => 'MerchantCancellation',
			'foreignKey' => 'merchant_id'
		),
		'MerchantPci' => array(
			'className' => 'MerchantPci',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'LastDepositReport' => array(
			'className' => 'LastDepositReport',
			'foreignKey' => 'merchant_id'
		),
		'SaqControlScan' => array(
			'className' => 'SaqControlScan',
			'foreignKey' => 'merchant_id'
		),
		'MerchantPricing' => array(
			'className' => 'MerchantPricing',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Gateway1' => array(
			'className' => 'Gateway1',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantUwVolume' => array(
			'className' => 'MerchantUwVolume',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantUw' => array(
			'className' => 'MerchantUw',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'AddressBus' => array(
			'className' => 'Address',
			'foreignKey' => 'merchant_id',
			'dependent' => true,
			'conditions' => array(
				'AddressBus.address_type_id' => AddressType::BUSINESS_ADDRESS,
			),
		),
		'AddressCorp' => array(
			'className' => 'Address',
			'foreignKey' => 'merchant_id',
			'dependent' => true,
			'conditions' => array(
				'AddressCorp.address_type_id' => AddressType::CORP_ADDRESS,
			),
		),
		'PaymentFusion' => array(
			'className' => 'PaymentFusion',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'CardType' => array(
			'className' => 'CardType',
			'joinTable' => 'merchant_card_types',
			'foreignKey' => 'merchant_id',
			'associationForeignKey' => 'card_type_id',
			'unique' => 'keepExisting'
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ExternalRecordField' => array(
			'className' => 'ExternalRecordField',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'ProfitProjection' => array(
			'className' => 'ProfitProjection',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'ProfitabilityReport' => array(
			'className' => 'ProfitabilityReport',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Orderitem' => array(
			'className' => 'Orderitem',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'ProductSetting' => array(
			'className' => 'ProductSetting',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantPricingArchive' => array(
			'className' => 'MerchantPricingArchive',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		//The alias is different from the class name because the class name is too long and Posgress was truncating some of its column names
		'CancellationsHistory' => array(
			'className' => 'MerchantCancellationsHistory',
			'foreignKey' => 'merchant_id'
		),
		'CommissionPricing' => array(
			'className' => 'CommissionPricing',
			'foreignKey' => 'merchant_id'
		),
		'GiftCard' => array(
			'className' => 'GiftCard',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Amex' => array(
			'className' => 'Amex',
			'foreignKey' => 'merchant_id'
		),
		'Authorize' => array(
			'className' => 'Authorize',
			'foreignKey' => 'merchant_id'
		),
		'CommissionReport' => array(
			'className' => 'CommissionReport',
			'foreignKey' => 'merchant_id'
		),
		'TimelineEntry' => array(
			'className' => 'TimelineEntry',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Discover' => array(
			'className' => 'Discover',
			'foreignKey' => 'merchant_id'
		),
		'MerchantReject' => array(
			'className' => 'MerchantReject',
			'foreignKey' => 'merchant_id'
		),
		'Order' => array(
			'className' => 'Order',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'SaqControlScanUnboarded' => array(
			'className' => 'SaqControlScanUnboarded',
			'foreignKey' => 'merchant_id'
		),
		'SystemTransaction' => array(
			'className' => 'SystemTransaction',
			'foreignKey' => 'merchant_id'
		),
		'UwStatusMerchantXref' => array(
			'className' => 'UwStatusMerchantXref',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Webpass' => array(
			'className' => 'Webpass',
			'foreignKey' => 'merchant_id'
		),
		'UwInfodocMerchantXref' => array(
			'className' => 'UwInfodocMerchantXref',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'OnlineappEpayment' => array(
			'className' => 'OnlineappEpayment',
			'foreignKey' => 'merchant_id'
		),
		'Gateway0' => array(
			'className' => 'Gateway0',
			'foreignKey' => 'merchant_id'
		),
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'UwApprovalinfoMerchantXref' => array(
			'className' => 'UwApprovalinfoMerchantXref',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'Ebt' => array(
			'className' => 'Ebt',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'EquipmentProgramming' => array(
			'className' => 'EquipmentProgramming',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantAch' => array(
			'className' => 'MerchantAch',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'ProductsAndService' => array(
			'className' => 'ProductsAndService',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantCardType' => array(
			'className' => 'MerchantCardType',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantChange' => array(
			'className' => 'MerchantChange',
			'foreignKey' => 'merchant_id'
		),
		'MerchantNote' => array(
			'className' => 'MerchantNote',
			'foreignKey' => 'merchant_id'
		),
		'MerchantOwner' => array(
			'className' => 'MerchantOwner',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'MerchantReference' => array(
			'className' => 'MerchantReference',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		),
		'ResidualPricing' => array(
			'className' => 'ResidualPricing',
			'foreignKey' => 'merchant_id'
		),
		'ResidualReport' => array(
			'className' => 'ResidualReport',
			'foreignKey' => 'merchant_id'
		),
		'VirtualCheck' => array(
			'className' => 'VirtualCheck',
			'foreignKey' => 'merchant_id',
			'dependent' => true
		)
	);

/**
 * Search conditions by location_description
 *
 * @param array $data
 * @return array
 */
	public function searchByLocation($data) {
		$settings = array(
			'conditions' => array(
				'Address.address_type_id' => AddressType::BUSINESS_ADDRESS,
				'Address.address_street' => $data['location_description']
			),
			'fields' => array(
				'Address.merchant_id'
			)
		);

		$query = $this->Address->getQuery('all', $settings);
		return $query;
	}

/**
 * Custom validation rule, check the field is required depending on the products the merchant has
 *
 * @param array $check value to check
 * @return bool
 */
	public function conditionallyRequired($check) {
		$productList = $this->ProductsAndService->ProductsServicesType->find('list', [
			'fields' => 'id',
			'conditions' => [
				'is_active' => true,
				'OR' => [
					["products_services_description ILIKE 'Visa%'"],
					["products_services_description ILIKE 'MasterCard%'"],
					["products_services_description ILIKE 'American Express%'"],
					["products_services_description ILIKE 'Discover%'"],
					["products_services_description ILIKE 'Debit%'"],
					["products_services_description ILIKE 'EBT%'"],
				]
			]
		]);
		$id = Hash::get($this->data, 'Merchant.id');
		if (!empty($id) && $this->ProductsAndService->hasAny(['merchant_id' => $id, 'products_services_type_id' => $productList])) {
			$val = Hash::get($check, 'merchant_acquirer_id', 'key_not_found');

			if ($val === 'key_not_found') {
				$val = Hash::get($check, 'bet_network_id', 'key_not_found');
			}

			if ($val === 'key_not_found') {
				return false;
			} else {
				//Fields are required when merchant has any of the products
				//and fulfill that requirement when  not empty === true.
				return !empty($val);
			}
		}
		//Option not required for merchants who do not have an id set nor any of the spcified products
		return true;
	}

/**
 * Custom validation rule, check the value type
 *
 * @param array $check value to check
 * @return bool|string
 */
	public function is_existing_mid($check) {
        $value = reset($check);
        //allow empty
        if (empty($value)) {
            return true;
        }
        if (strlen($this->data['Merchant']['merchant_mid']) == 16) {
            return 'An acquiring merchant cannot be related to another acquiring MID';
        }

        if (strlen($value) == 16) {
            return $this->hasAny(array('merchant_mid' => $value));
        } else {
            return 'Acquiring MIDs must be exactly 16 digist long.';
        }
    }

/**
 * Custom validation rule, check the value type
 *
 * @param array $check value to check
 * @return bool|string
 */
    public function validValue($check) {
		if (empty($check)) {
			return false;
		}
		$keys = array_keys($check);
		$fielName = array_pop($keys);
		$value = reset($check);
		$type = null;
		//all current defined ResidualTimeParameter values are decimals escept multiple
		if ($fielName === 'ref_p_value') {
			$type = (Hash::get($this->data, "{$this->alias}.ref_p_type"));
		} elseif ($fielName === 'res_p_value') {
			$type = (Hash::get($this->data, "{$this->alias}.res_p_type"));
		}

		if ( $type === self::PERCENTAGE || $type === self::PCT_MINUS_GP ) {
			return $this->validPercentage(array($value));
		} else {
			//number with max 4 precision
			$validates = (strlen(strrchr($value, '.')) - 1 <= 4);
			if (!$validates) {
				$validator = $this->validator();
				$validator[$fielName]['validValue']->message = 'Invalid value! Max 4 decimals';
			}
			return $validates;
		}
	}

/**
 * orConditions for search
 *
 * @param array $data array containing search form data
 * @return array or conditions
 */
	public function orConditions($data = array()) {
		$filter = $data['search'];
		$cond = array(
			'OR' => array(
				$this->alias . '.merchant_mid ILIKE' => '%' . $filter . '%',
				$this->alias . '.merchant_dba ILIKE' => '%' . $filter . '%',
		));
		return $cond;
	}

/**
 * findMerchantDBAorMID
 *
 * @param dtring $searchStr string to search for
 * @param bool $isMID wether search string is an MID (true) or a DBA (false)
 * @param bool $active search active/inactive merchants
 * @param int $limit amount of results
 * @return array
 */
	public function findMerchantDBAorMID($searchStr, $isMID, $active = null, $limit = null) {
		//Default max 10 results if nothing is passed
		$limit = (empty($limit)) ? $limit = 10 : $limit;

		if ($isMID) {
			if ($limit === 1) {
				return $this->field('id', array('Merchant.merchant_mid like' => '%' . $searchStr));
			} else {
				$dbCol = 'merchant_mid';
				$searchStr = '%' . $searchStr;
			}
		} else {
			$dbCol = 'merchant_dba';
			$searchStr = $searchStr . '%';
		}

		$options = array(
			'recursive' => 0,
			'conditions' => array(
				"Merchant." . $dbCol . " ilike" => $searchStr,
			),
			'fields' => array('Merchant.' . $dbCol),
			'limit' => $limit
		);
		$currentUserId = $this->_getCurrentUser('id');
		if (!$this->User->isAdmin($currentUserId)) {
			$allowedPartners = $this->User->getUsersByAssociatedUser($currentUserId, User::ROLE_ACCT_MGR);
			$allowedPartners = array_keys($allowedPartners);
			$allowedPartners[] = $currentUserId;
			$options['conditions']['OR'] = [
				'Merchant.user_id' => $currentUserId,
				'Merchant.sm_user_id' => $currentUserId,
				'Merchant.sm2_user_id' => $currentUserId,
				'Merchant.partner_id' => $allowedPartners,
				'Merchant.referer_id' => $currentUserId,
				'Merchant.reseller_id' => $currentUserId,
			];
		}
		if ($active !== null) {
			$options['conditions']['Merchant.active'] = $active;
		}

		return $this->find('list', $options);
	}

/**
 * findMerchantDataById
 *
 * @param string $id merchant id
 * @return array
 */
	public function findMerchantDataById($id) {
		$timelineItems = $this->TimelineEntry->TimelineItem->find('list',
			array(
				'fields' => array(
					'timeline_item_old', 'id'
				)
			)
		);

		$date = new DateTime(date('Y-m-d'));
		$date->sub(new DateInterval('P90D')); //90 days from now
		$noteDate = $date->format('Y-m-d');
		$contain = array(
			'Client',
			'AssociatedExternalRecord' => ['ExternalRecordField'],
			'SalesForceAccount',
			'AddressBus',
			'AddressCorp',
			'BetNetwork',
			'MerchantAcquirer',
			'OriginalAcquirer',
			'BackEndNetwork',
			'User',
			'ManagerUser' => array('fields' => array('id', 'fullname')),
			'SecondManagerUser' => array('fields' => array('id', 'fullname')),
			'Referer' => array('RefererOptions'),
			'Reseller' => array('ResellerOptions'),
            'MerchantUw' => array('fields' => array('id', 'merchant_id', 'mcc')),
			'Entity',
			'MerchantPricing' => array(
				'McBetTable',
				'VisaBetTable'
				),
			'UwStatusMerchantXref' => array(
				'fields' => 'datetime',
				'conditions' => ["uw_status_id = (select id from uw_statuses where name = 'Approved')"]),
			'MerchantNote' => array(
				'order' => "MerchantNote.note_date DESC",
				'conditions' => array(['OR' => ['MerchantNote.critical = 1', "MerchantNote.note_date >=" => $noteDate]]),
				'User' => array('fields' => array("User.fullname")), 'NoteType'),
			'Group',
			'TimelineEntry' => array(
				'conditions' => array(
					'OR' => array(
						array('TimelineEntry.timeline_item_id' => $timelineItems['SUB']),
						array('TimelineEntry.timeline_item_id' => $timelineItems['APP']),
						array('TimelineEntry.timeline_item_id' => $timelineItems['INS']),
						array('TimelineEntry.timeline_item_id' => TimelineItem::EXPECTED_TO_GO_LIVE),
						array('TimelineEntry.timeline_item_id' => TimelineItem::AGREEMENT_ENDS),
					)
				)
			),
			'LastDepositReport',
			'ProductsAndService' => array('ProductsServicesType'),
			'CardType',
			'SaqMerchant' => array('LastSaqPrequalification'),
			'MerchantPci',
			'Network',
            'MerchantType',
			'MerchantBin',
			'CancellationFee',
			'MerchantCancellation',
			'Partner',
			'SaqControlScan',
			'MerchantReject' => array(
				'fields' => array('id', 'merchant_id', 'trace', 'open'),
				'MerchantRejectLine' => array(
					'MerchantRejectStatus' => array('fields' => array('id', 'name')),
					'order' => array('MerchantRejectLine.status_date ASC NULLS LAST'),
					'fields' => array('status_date', 'merchant_reject_status_id', 'merchant_reject_id', 'notes'),
				),
			),
			'Brand',
			'Organization',
			'Region',
			'Subregion'
		);

		return $this->find('first', array('recursive' => -1, 'conditions' => array('Merchant.id' => $id),
			'contain' => $contain));
	}

/**
 * getApiMerchantData
 * Finds merchant data and returns it using configured DB fields aliased for API calls
 *
 * @param array $conditions search conditions
 * @return array Merchant PCI data
 */
	public function getApiMerchantData($conditions) {
		if (isset($conditions['ExternalRecordField.value'])) {
			//SF account ids are saved as unique values
			$id = $this->ExternalRecordField->field('merchant_id', ['field_name' => SalesForce::ACCOUNT_ID, 'value' => $conditions['ExternalRecordField.value']]);
		} else {
			unset($conditions['ExternalRecordField.value']);
			$id = $this->field('id', [$conditions]);
		}
		$data = [];
		if (!empty($id)) {
			$merchant = $this->findMerchantDataById($id);
			//Find expected install timeline entry and overwrite array
			foreach($merchant['TimelineEntry'] as $tEntry) {
				if ($tEntry['timeline_item_id'] === TimelineItem::EXPECTED_TO_GO_LIVE) {
					$merchant['TimelineEntry'] = ['timeline_date_completed' => $tEntry['timeline_date_completed']];
					break;
				}
			}
			$merchant['Merchant']['active'] = ($merchant['Merchant']['active'])?'YES':'NO';
			foreach(Configure::read('ApiFieldNames') as $apiFieldName => $schema) {
				if (strpos($apiFieldName, 'corp_') !== false) {
					$modelName = 'AddressCorp';
				} elseif (strpos($apiFieldName, 'business_') !== false) {
					$modelName = 'AddressBus';
				} elseif ($apiFieldName === 'setup_referrer') {
					$modelName = 'Referer';
				} elseif ($apiFieldName === 'setup_reseller') {
					$modelName = 'Reseller';
				} elseif ($apiFieldName === 'setup_partner') {
					$modelName = 'Partner';
				} else {
					$modelName = $schema['model_name'];
				}
							
				$data[$apiFieldName] = Hash::get($merchant, "$modelName.{$schema['field_name']}");
			}
		}

		return $data;
	}
/**
 * Retrive a PCI data for the given merchant id
 *
 * @param int|string $id The merchant id
 * @return array Merchant PCI data
 */
	public function getPCI($id) {
		$pci = $this->find(
				'first', array(
			'contain' => array(
				'AddressBus',
				'MerchantPci',
				'SaqControlScanUnboarded',
				'SaqMerchant' => array(
					'PciCompliance' => array(
						'PciComplianceDateType',
					),
					'SaqPrequalification' => array(
						'order' => array(
							'SaqPrequalification.date_completed' => 'DESC',
						),
					),
					'SaqMerchantSurveyXref' => array(
						'conditions' => array(
							'SaqMerchantSurveyXref.saq_eligibility_survey_id !=' => null,
							'SaqMerchantSurveyXref.saq_confirmation_survey_id !=' => null,
						),
						'order' => array(
							'SaqMerchantSurveyXref.datecomplete' => 'DESC',
						),
						'SaqSurvey'
					),
					'SaqMerchantPciEmailSent' => array(
						'SaqMerchantPciEmail',
						'order' => array(
							'SaqMerchantPciEmailSent.date_sent' => 'ASC',
						),
					)
				),
				'SaqControlScan',
			),
			'conditions' => array(
				'Merchant.id' => $id,
			),
				)
		);

		if (empty($pci['SaqMerchant']['id'])) {
			return $pci;
		}

		//set the first saq prequalification as the lastest
		$pci['SaqMerchant']['LastSaqPrequalification'] = current(
				$pci['SaqMerchant']['SaqPrequalification']
		);

		//set the first saq merchant survey as the latest
		$pci['SaqMerchant']['LastSaqMerchantSurveyXref'] = current(
				$pci['SaqMerchant']['SaqMerchantSurveyXref']
		);

		return $pci;
	}

/**
 * getSummarizedMerchantData
 *
 * @param string $id merchant id
 * @return array Returns basic merchant information
 */
	public function getSummaryMerchantData($id = null) {
		return $this->find('first', array(
			'conditions' => array('Merchant.id' => $id),
			'fields' => array(
				'Merchant.merchant_dba',
				'Merchant.merchant_mid',
				'Merchant.id',
				'Merchant.user_id'
			),
			'contain' => array(
				'User' => array(
					'fields' => array(
						'User.user_first_name',
						'User.user_last_name'
					)
				)
			)
		));
	}

/**
 * afterSave callback
 *
 * @param $created boolean
 * @param $options array
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if (!empty($this->data['Merchant']['merchant_mid'])) {
			$externalId = $this->ExternalRecordField->field('value', ['merchant_id' => $this->data['Merchant']['id'], 'field_name' => SalesForce::ACCOUNT_ID]);
			if (!empty($externalId)) {
				ClassRegistry::init('SalesForce')->updateSalesforceField(SalesForce::ACCT_NUM_MID, $this->data['Merchant']['merchant_mid'], $externalId, false);
			}
		}
	}
/**
 * beforeSave callback
 *
 * @param array $options options for beforeSave
 * @return true
 */
	public function beforeSave($options = array()) {
		//If save was initiated by the ChangeRequestBehavior the fields will be already encrypted
		//need to decrypt to extract last 4 digits
		$this->data['Merchant'] = $this->decryptFields($this->data['Merchant']);
		$this->data['Merchant'] = array_map('trim', $this->data['Merchant']);
        
        if (!empty($this->data['Merchant']['merchant_dba'])) {
            $this->data[$this->alias]['merchant_dba'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_dba']);
        }
        if (!empty($this->data['Merchant']['merchant_ownership_type'])) {
            $this->data[$this->alias]['merchant_ownership_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_ownership_type']);
        }
        if (!empty($this->data['Merchant']['merchant_buslevel'])) {
            $this->data[$this->alias]['merchant_buslevel'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_buslevel']);
        }
        if (!empty($this->data['Merchant']['merchant_bustype'])) {
            $this->data[$this->alias]['merchant_bustype'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_bustype']);
        }
        if (!empty($this->data['Merchant']['merchant_url'])) {
            $this->data[$this->alias]['merchant_url'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_url']);
        }
        if (!empty($this->data['Merchant']['merchant_contact_position'])) {
            $this->data[$this->alias]['merchant_contact_position'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_contact_position']);
        }
        if (!empty($this->data['Merchant']['merchant_mail_contact'])) {
            $this->data[$this->alias]['merchant_mail_contact'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_mail_contact']);
        }
        if (!empty($this->data['Merchant']['reporting_user'])) {
            $this->data[$this->alias]['reporting_user'] = $this->removeAnyMarkUp($this->data[$this->alias]['reporting_user']);
        }
        if (!empty($this->data['Merchant']['general_practice_type'])) {
            $this->data[$this->alias]['general_practice_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['general_practice_type']);
        }
        if (!empty($this->data['Merchant']['specific_practice_type'])) {
            $this->data[$this->alias]['specific_practice_type'] = $this->removeAnyMarkUp($this->data[$this->alias]['specific_practice_type']);
        }
        if (!empty($this->data['Merchant']['merchant_ps_sold'])) {
            $this->data[$this->alias]['merchant_ps_sold'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_ps_sold']);
        }
		//Extract last four digits for display fields
		if (!empty($this->data['Merchant']['merchant_tin'])) {
			$this->data['Merchant']['merchant_tin'] = str_replace('-', '', $this->data['Merchant']['merchant_tin']);
			$this->data['Merchant']['merchant_tin_disp'] = substr($this->data['Merchant']['merchant_tin'], -4);
		}
		if (!empty($this->data['Merchant']['merchant_d_and_b'])) {
			$this->data['Merchant']['merchant_d_and_b'] = str_replace('-', '', $this->data['Merchant']['merchant_d_and_b']);
			$this->data['Merchant']['merchant_d_and_b_disp'] = substr($this->data['Merchant']['merchant_d_and_b'], -4);
		}
		if (Hash::get($this->data, 'Merchant.res_p_type') === '') {
			$this->data['Merchant']['res_p_type'] = null;
		}
		if (Hash::get($this->data, 'Merchant.ref_p_type') === '') {
			$this->data['Merchant']['ref_p_type'] = null;
		}
		//Encrypt again
		$this->encryptData($this->data);
		return true;
	}

/**
 * encryptData
 *
 * @param type &$data save data reference with bank account fields to encrypt if they aren't already
 * @return bool
 */
	public function encryptData(&$data) {
		//Encrypt full numbers
		if (!empty($data['Merchant']['merchant_tin']) && !$this->isEncrypted($data['Merchant']['merchant_tin'])) {
			$data['Merchant']['merchant_tin'] = $this->encrypt($data['Merchant']['merchant_tin'], Configure::read('Security.OpenSSL.key'));
		}
		if (!empty($data['Merchant']['merchant_d_and_b']) && !$this->isEncrypted($data['Merchant']['merchant_d_and_b'])) {
			$data['Merchant']['merchant_d_and_b'] = $this->encrypt($data['Merchant']['merchant_d_and_b'], Configure::read('Security.OpenSSL.key'));
		}
	}

/**
 * beforeValidade method this callback is called before any validation
 *
 * @param array $options options data
 * @return void
 */
	public function beforeValidate($options = array()) {
		$merchantValidator = $this->validator();

		/*Load User to 'skip' the validation*/
		if (!empty($this->data['User']['id'])) {

			$user = $this->User->find('first', array(
				'conditions' => array(
					'User.id' => $this->data['User']['id']
				)
			));

			$this->data['User'] = $user['User'];
		}
		$partnerRepUcpId = null;
		/*Check if a partner was selected*/
		if (!empty($this->data['Merchant']['partner_id'])) {
			/*Check if the rep is associated with this partner through a compensation profile*/
			$partnerRepUcpId = $this->User->UserCompensationProfile->field('id', array(
					'UserCompensationProfile.user_id' => $this->data['Merchant']['user_id'],
					'UserCompensationProfile.partner_user_id' => $this->data['Merchant']['partner_id']
			));

			if ($partnerRepUcpId === false) {
				$merchantValidator['partner_id'] = array(
					'rule' => 'blank',
					'message' => __('A compensation profile associating the specified Rep with the Partner was not found.')
				);
			}
		}
		$this->_setMerchUsersAssocValidation($partnerRepUcpId, !empty($partnerRepUcpId));
	}

/**
 * _setMerchUsersAssocValidation
 * Checks if users assigned to merchants (rep/sm/sm2/etc..) are interrelated in the rep's UCP and if they are not a validation rule will be created
 *  
 * @param $string $partnerRepUcpId the rep/partner rep user compensation id
 * @param $string $isPartnerRep the rep/partner rep user compensation id
 * @return void
 */
	protected function _setMerchUsersAssocValidation($partnerRepUcpId, $isPartnerRep = false) {
		$fieldRoles = [
			'sm_user_id' => User::ROLE_SM,
			'sm2_user_id' => User::ROLE_SM2,
			'referer_id' => User::ROLE_REFERRER,
			'reseller_id' => User::ROLE_RESELLER,
		];
		$mValidator = $this->validator();
		foreach ($fieldRoles as $field => $role) {
			if (!empty($this->data['Merchant'][$field])) {
				if ($isPartnerRep) {
					$conditions = [
						'AssociatedUser.associated_user_id' => $this->data['Merchant'][$field],
						'AssociatedUser.permission_level' => $role,
						'AssociatedUser.user_compensation_profile_id' => $partnerRepUcpId,
					];
					$errMsg = __("Selected '$role' is not associated with the Rep. The Rep comp profile that is associated with the selected partner must have the selected '$role' assigned with '$role' permission level.", $role);

				} else {
					$conditions = [
						'AssociatedUser.associated_user_id' => $this->data['Merchant'][$field],
						'AssociatedUser.permission_level' => $role,
					];
					$errMsg = __("Selected '$role' is not associated with the Rep with '$role' permission level in Rep's default comp profile!", $role);
				}
				$assocManager = $this->User->AssociatedUser->find('first', ['conditions' => $conditions]);

				if (empty($assocManager)) {
					$mValidator[$field] = array(
						'rule' => 'blank',
						'message' => $errMsg
					);
				}
			}
		}
	}

/**
 * hasPedinggMerchantNote method this method will check if this merchant has some pending merchant note
 *
 * @param string $merchantId merchant id
 * @param string $typeId Id of the note type to filter
 * @return bool
 */
	public function hasPendingMerchantNote($merchantId, $typeId = null) {
		$options = array(
			'conditions' => array(
				'MerchantNote.merchant_id' => $merchantId,
				'MerchantNote.general_status' => MerchantNote::STATUS_PENDING,
			)
		);
		if (!empty($typeId)) {
			$options['conditions']['MerchantNote.note_type_id'] = $typeId;
		}
		return (bool)$this->MerchantNote->find('count', $options);
	}

/**
 * getViewDropDownsData
 *
 * @return array of data to populate front end dropdown menus
 */
	public function getViewDropDownsData() {
		$ddData = array();

		$ddData['User'] = $this->User->find('list', array(
			'fields' => array(
				'id',
				'fullname'
			),
			'order' => 'User.user_first_name'
		));
		$ddData['Sm'] = $this->User->getByRole(User::ROLE_GROUP_MGR1);
		$ddData['Sm2'] = $this->User->getByRole(User::ROLE_GROUP_MGR2);

		$ddData['TimelineItem'] = $this->TimelineEntry->TimelineItem->find('list', array(
			'fields' => array(
				'timeline_item_description',
				'id'
			)
		));

		return $ddData;
	}

/**
 * assocTrim
 * Removes white spaces at the both ends of both keys and values of associative single dim arrays
 *
 * @param array $data associative array
 * @return array
 */
	public function assocTrim($data) {
		$keys = array_map('trim', array_keys($data));
		$data = array_map('trim', $data);
		return array_combine($keys, $data);
	}

/**
 * externalIdsAreDistinct method
 * This method searches for duplicate 'oaID' and 'external_foreign_id' values in the CSV data used to upload merchant data
 *
 * @param array $csvData CSV data used to upload merchant data
 * @return mixed array|boolean true when no duplicate values are found otherwise an array of errors specifying what is duplicated
 */
	public function externalIdsAreDistinct($csvData) {
			$csvRowCount = count($csvData);
			$idxSearch = 0;
			$tracker = [];
			$errors = [];
			if ($csvRowCount >1) {
				foreach ($csvData as $xIdx => $data) {
					$appId = Hash::get($csvData, "$xIdx.oaID");
					$extId = Hash::get($csvData, "$xIdx.external_foreign_id");
					$idxSearch = $xIdx + 1;
					if (!empty($appId) || !empty($extId)) {
						//memoization check
						if(!empty($tracker[$appId]) || !empty($tracker[$extId])) {
							continue;
						}

						for ($idxSearch; $idxSearch <= $csvRowCount-1; $idxSearch++) {
							if (Hash::get($csvData, "$idxSearch.oaID") == $appId || Hash::get($csvData, "$idxSearch.external_foreign_id") == $extId) {
								if (!empty($appId) && Hash::get($csvData, "$idxSearch.oaID") == $appId) {
									//memoize a match found
									$tracker[$appId] = true;
									$errors[] = __("Data for online application with id $appId has already been uploaded. Cannot create more than one merchant account with the same application ID!");
								}
								if (!empty($extId) && Hash::get($csvData, "$idxSearch.external_foreign_id") == $extId) {
									//memoize a match found
									$tracker[$extId] = true;
									$errors[] = __("Duplicate value $extId found in external_foreign_id column. Cannot create more than one merchant account with the same external_foreign_id!");
								}
								
								if (Hash::get($tracker, $appId) === true || Hash::get($tracker, $extId) === true) {
									break;
								}
							}
						}
					}
				}
			}
		return (empty($errors)?:$errors);
	}
/**
 * setMerchantDataFromCsvData method
 *
 * Generates a data structure based on data uploaded from a csv file and converted to array.
 *
 * @param array $csvData data converted from csv to array using csv_to_array custom method in CsvHandlerComponent or a similar method
 * @return mixed array containung ids of all the saved merchants or a string of errors on falure
 */
	public function importMerchantData($csvData) {
		$errors = [];
		$extIdResult = $this->externalIdsAreDistinct($csvData);
		if ($extIdResult !== true) {
			$errors['Errors'] = $extIdResult;
		}

		foreach ($csvData as $data) {
			//cleanup
			$data = $this->assocTrim($data);

			$isOnlineApp = array_key_exists('oaID', $data);
			$isAchMerch = $this->_hasAch($data);
			if ($this->isValidUUID(Hash::get($data, 'ContractorID'))) {
				$repData = $this->User->find('first', array('conditions' => array('id' => Hash::get($data, 'ContractorID'))));
			} else {
				$repIdAndName = $this->User->getByNameAndRole(trim(Hash::get($data, 'ContractorID')), [User::ROLE_REP, User::ROLE_PARTNER_REP]);
				$repData = $this->User->find('first', array('conditions' => array('id' => Hash::get($repIdAndName, 'User.id'))));
			}
			$partner = trim(Hash::get($data, 'setup_partner'));
			//Set default partner if not specified for ACH merchants
			if (empty($partner) && $isAchMerch) {
				$partner = self::VERICHECK_DEFAULT_PARTNER;
			}
			$partnerUser = [];
			$partnerId = null;

			if (!empty($partner)) {
				if ($this->isValidUUID($partner)) {
					$partnerId = $partner;
					//Change the uuid into the partner name
					$partner = $this->User->field('fullname', ['id' => $partnerId]);
				} else {
					$partnerUser = $this->User->getByNameAndRole($partner, User::ROLE_PARTNER);
					$partnerId = Hash::get($partnerUser, 'User.id');
				}
				if (empty($partnerId)) {
					$errors['Errors'][] = 'The ' . User::ROLE_PARTNER . " $partner in this data import does not have an active user profile setup.";
				}
			}

			//Check Errors before doing anything:
			if (empty($data['MID'])) {
				$errors['Errors'][] = $data['DBA'] . __(" Merchant is missing MID number!");
			}
			if (empty($repData['User']['id'])) {
				$errors['Errors'][] = __("Rep '" . Hash::get($data, 'ContractorID') . "' not found in database for ") . $data['MID'] . " " . $data['DBA'];
			}
			//Check if rep has a rep/partnerRep UCP
			if (!empty($repData['User']['id'])) {
				$repUcp = $this->User->UserCompensationProfile->getUserOrPartnerUserCompProfile($repData['User']['id'], $partnerId);
				if (empty($repUcp['UserCompensationProfile']['id'])) {
					$tmpErrStr = $repData['User']['fullname'] . __(" Contractor does not have a User Comp Profile");
					$tmpErrStr .= (!empty($partnerId)) ? __(" associated with partner $partner") : '';
					$errors['Errors'][] = $tmpErrStr;
				}
			}

			//Is the MID in scientific notation? Cast to integer to eliminate all decimals
			if (!empty($data['MID']) && strpos(Hash::get($data, 'MID'), '.') !== false) {
				$errors['Errors'][] = __("Truncated MID as scientific notation is not allowed: ") . $data['MID'] . " " . $data['DBA'];
			}

			//Validate Gateway only merchants data
			if ($this->_hasGateway($data)) {
				try {
					$this->_validateGatewayCsvData($data);
				} catch (Exception $e) {
					$errors['Errors'][] = __($e->getMessage());
				}
			}

			//Validate Payment Fusion merchants data
			if ($this->_hasPmntFusion($data)) {
				try {
					$this->_validatePaymentFusionCsv($data);
				} catch (Exception $e) {
					$errors['Errors'][] = __($e->getMessage());
				}
			}
			//Validate ACH product data
			if ($isAchMerch) {
				try {
					$this->_validateAchCsvData($data);
				} catch (Exception $e) {
					$errors['Errors'][] = __($e->getMessage());
				}
			}
			if (strpos(Hash::get($data, 'RoutingNum'), '+') !== false || strpos(Hash::get($data, 'AccountNum'), '+') !== false ||
				strpos(Hash::get($data, 'FeesRoutingNum'), '+') !== false || strpos(Hash::get($data, 'FeesAccountNum'), '+') !==false) {
				$errors['Errors'][] = __("Merchant bank acounts and/or routing numbers appear to be in scientific notation!");
			}

			if (!empty(Hash::get($data, 'client_id_global'))) {
				try {
					$result = $this->Client->getSfClientNameByClientId(Hash::get($data, 'client_id_global'));
					if (empty($result)) {
						$errors['Errors'][] = __('Client id '. Hash::get($data, 'client_id_global') .' is invalid! It does not match any known id generated by SalesForce.');
					} elseif (Hash::get($data, 'client_name_global') !== Hash::get($result, SalesForce::GLOBAL_CLIENT_NAME)) {
						//if the client name does not match data returned from SF rectify it.
						$data['client_name_global'] = Hash::get($result, SalesForce::GLOBAL_CLIENT_NAME);
					}
				} catch (Exception $e) {
					$errors['Errors'][] = __('Unexpected SalesForce API connection error! Try again later.');
				}
			}

			//Continue validating data as long as there are errors to check for additional errors
			if (!empty($errors['Errors'])) {
				$errors['Errors'][] = ''; //add a blank entry to separate sets of Merchants' errros
				continue;
			}

			//Get rep's UCP if not set already
			if (empty($repUcp['UserCompensationProfile']['id'])) {
				$repUcp = $this->User->UserCompensationProfile->getUserOrPartnerUserCompProfile($repData['User']['id'], $partnerId);
			}
			$ref = trim(Hash::get($data, 'setup_referrer'));
			$res = trim(Hash::get($data, 'setup_reseller'));
			$refUser = $this->User->getByNameAndRole($ref, User::ROLE_REFERRER);
			$resUser = $this->User->getByNameAndRole($res, User::ROLE_RESELLER);
			$referrerId = Hash::get($refUser, 'User.id');
			$resellerId = Hash::get($resUser, 'User.id');

			if ((!empty($ref) && empty($referrerId)) || (!empty($res) && empty($resellerId))) {
				$errors['Errors'][] = 'A ' . User::ROLE_REFERRER . ' or ' . User::ROLE_RESELLER . ' in this data import does not have an active user profile setup.';
			}
			//check referer/reseller UCP association
			if (!empty($referrerId) || !empty($resellerId)) {
				$assocRefRes = $this->User->getAssociatedUsers($repData['User']['id'], $repUcp['UserCompensationProfile']['id'], [User::ROLE_REFERRER, User::ROLE_RESELLER]);
				$assocRefRes = Hash::extract($assocRefRes, '{n}.AssociatedUser.associated_user_id');
				$assocRefResError = $repData['User']['fullname'];
				$assocRefResError .= empty($repUcp['UserCompensationProfile']['partner_user_id'])? __( " Rep's "): __( " Partner-Rep's ");
				$assocRefResError .= __( "compensation profile ");
				$assocRefResError .= !empty($repUcp['UserCompensationProfile']['partner_user_id'])? __( "is associated with Partner $partner, but "): '';
				$assocRefResError .= 'is not associated with ';
				$assocRefError = null;
				$assocResError = null;

				if (!empty($assocRefRes)) {
					if (!empty($referrerId) && !in_array($referrerId, $assocRefRes)) {
						$assocRefError = "the " . User::ROLE_REFERRER . " $ref";
					}
					if (!empty($resellerId) && !in_array($resellerId, $assocRefRes)) {
						$assocResError .= (!is_null($assocRefError))? " nor the " . User::ROLE_RESELLER . " $res." : "the " . User::ROLE_RESELLER . " $res.";
					}
				} else {
					$assocRefError = (!empty($referrerId))? "the " . User::ROLE_REFERRER . " $ref": '';
					if (!empty($resellerId)) {
						$assocResError = !is_null($assocRefError)? " nor the " . User::ROLE_RESELLER . " $res." : "the " . User::ROLE_RESELLER . " $res.";
					}
				}
				if (!is_null($assocRefError) || !is_null($assocResError)) {
					$errors['Errors'][] = $assocRefResError . $assocRefError . $assocResError;
				}
			}

			//Define a new data structure for a saveAll(deep = true) operation in every iteration:
			$merchData = array();
			bcscale(2); //Set default scale of 2 decimals for all bc arithmetic operations
			//Does this merchant exist? Delete it and all possible duplicates
			if ($this->field('id', array('Merchant.merchant_mid' => $data['MID']))) {
				if ($this->deleteAll(array('Merchant.merchant_mid' => $data['MID']), true) !== true) {
					return [__("Error ocurred removing previously uploaded merchant data, please try again.")];
				}
			}

			if (!empty(Hash::get($data, 'oaID')) && $this->hasAny(['cobranded_application_id' => Hash::get($data, 'oaID')])) {
				$errors['Errors'][] = __("Data for online application with id " . Hash::get($data, 'oaID') . ' has already been uploaded. Cannot create merchant account with the same application ID!');
			}
			if (!empty(Hash::get($data, 'external_foreign_id')) && $this->ExternalRecordField->hasAny(['value' => Hash::get($data, 'external_foreign_id')])) {
				$errors['Errors'][] = __("Duplicate value " . Hash::get($data, 'external_foreign_id') . ' found in external_foreign_id column. Cannot create more than one merchant account with the same external_foreign_id!');
			}

			//Get rep's Associated managers
			$mgrSearchConditions = array(
				'user_compensation_profile_id' => $repUcp['UserCompensationProfile']['id'],
				'permission_level' => User::ROLE_SM,
				'main_association' => true
			);
			$smId = $this->User->AssociatedUser->field('associated_user_id', $mgrSearchConditions);
			unset($mgrSearchConditions['main_association']);
			if ($smId === false) {
				$smId = $this->User->AssociatedUser->field('associated_user_id', $mgrSearchConditions);
			}
			//find the id of any secondary managers
			$mgrSearchConditions['permission_level'] = User::ROLE_SM2;
			$sm2Id = $this->User->AssociatedUser->field('associated_user_id', $mgrSearchConditions);

			//Assign new UUID
			$merchantId = CakeText::uuid();
			$data['MID'] = preg_replace("/\D/", "", trim($data['MID'])); //remove any non-digits
			$merchData['Merchant']['id'] = $merchantId;
			$merchData['Merchant']['entity_id'] = $this->_getMerchantEntity($data['MID'], $repData['User']['entity_id']);
			$merchData['Merchant']['active'] = 1;
			$merchData['Merchant']['active_date'] = date('Y-m-d');
			$merchData['Merchant']['merchant_mid'] = $data['MID'];
			$merchData['Merchant']['merchant_dba'] = trim(Hash::get($data, 'DBA'));
			$merchData['Merchant']['user_id'] = $repData['User']['id'];
			$merchData['Merchant']['sm_user_id'] = (!empty($smId))? $smId : null;
			$merchData['Merchant']['sm2_user_id'] = (!empty($sm2Id))? $sm2Id : null;
			$merchData['Merchant']['partner_id'] = (!empty($partnerId))? $partnerId : null;
			$merchData['Merchant']['group_id'] = null; //data not yet included in csv
			$merchData['Merchant']['merchant_contact'] = trim(Hash::get($data, 'Contact'));
			$merchData['Merchant']['merchant_contact_position'] = ((Hash::get($data, 'Title'))) ? trim(Hash::get($data, 'Title')) : trim(Hash::get($data, 'LocTitle'));
			$merchData['Merchant']['merchant_email'] = trim(Hash::get($data, 'EMail'));
			$merchData['Merchant']['chargebacks_email'] = trim(Hash::get($data, 'ChargebkEmail'));
			$merchData['Merchant']['merchant_url'] = trim(Hash::get($data, 'WebAddress'));
			$merchData['Merchant']['reporting_user'] = trim(Hash::get($data, 'ReportingUser'));
			$merchData['Merchant']['merchant_bustype'] = !empty(Hash::get($data, 'BusinessType'))? trim(Hash::get($data, 'BusinessType')): $this->_getBusLvl($data);
			$merchData['Merchant']['merchant_ps_sold'] = trim(Hash::get($data, 'Products Services Sold'));
			$merchData['Merchant']['merchant_buslevel'] = $this->_getBusLvl($data);
			$merchData['Merchant']['merchant_sic'] = null; //data not yet included in csv
			$merchData['Merchant']['merchant_mail_contact'] = (Hash::get($data, 'Principal')) ? Hash::get($data, 'Principal') : Hash::get($data, 'Owner1Name');
            //default merchant MID type to Acquiring for the following MIDs
            if (strlen($merchData['Merchant']['merchant_mid']) == 16) {
                $merchData['Merchant']['merchant_type_id'] = $this->MerchantType->getAcquiringTypeId();
            } elseif (!empty(Hash::get($data, 'MID Type'))) {
                $merchType = $this->MerchantType->find('first', ['conditions' => ['type_description' => Hash::get($data, 'MID Type')]]);
                if (empty($merchType['MerchantType']['id'])) {
                    $errors['Errors'][] = __('An MID Type named: ' . Hash::get($data, 'MID Type') . ' does not exist! Make sure MID Type exactly matches existing values. (Error at merchant MID: ' . $merchData['Merchant']['merchant_mid'] . ')');
                } else {
                    $merchData['Merchant']['merchant_type_id'] = $merchType['MerchantType']['id'];
                }
            }
            if (!empty(Hash::get($data, 'Related Acquiring MID'))) {
                if (strlen($merchData['Merchant']['merchant_mid']) == 16) {
                    $errors['Errors'][] = "An acquiring merchant cannot have a Related Acquiring MID (Error at MID : {$merchData['Merchant']['merchant_mid']})";
                }
                if (strlen(Hash::get($data, 'Related Acquiring MID')) !== 16 ) {
                    $errors['Errors'][] = "Related Acquiring MIDs must be exactly 16 digist long. (Error at MID : {$merchData['Merchant']['merchant_mid']})";
                } elseif($this->hasAny(array('merchant_mid' => Hash::get($data, 'Related Acquiring MID'))) == false) {
                    $errors['Errors'][] = "The Related Acquiring MID for merchant {$merchData['Merchant']['merchant_mid']} does not exist. Make sure the Related Acquiring MIDs belong to existing merchant accounts.";
                }
                $merchData['Merchant']['related_acquiring_mid'] = preg_replace("/\D/", "", trim($data['Related Acquiring MID'])); //remove any non-digits
            }
			//Set Client's Organizational data
			if (!empty(Hash::get($data, 'org_name'))) {
				$orgId = $this->Organization->field('id', array(
					'name' => Hash::get($data, 'org_name')
				));
				if ($orgId !== false) {
					$merchData['Merchant']['organization_id'] = $orgId;
					$regionId = null;
					if (!empty(Hash::get($data, 'region_name'))) {
						$regionId = $this->Region->field('id', array(
							'name' => Hash::get($data, 'region_name'),
							'organization_id' => $orgId
						));
						if ($regionId !== false) {
							$merchData['Merchant']['region_id'] = $regionId;
						}
					}

					if (!empty(Hash::get($data, 'subregion_name')) && !empty($regionId)) {
						$subregionId = $this->Subregion->field('id', array(
							'name' => Hash::get($data, 'subregion_name'),
							'organization_id' => $orgId,
							'region_id' => $regionId
						));
						if ($subregionId !== false) {
							$merchData['Merchant']['subregion_id'] = $subregionId;
						}
					}
				}

			}
			if (!empty(Hash::get($data, 'CompanyBrandName'))) {
				$brandId = $this->Brand->field('id', ['name' => Hash::get($data, 'CompanyBrandName')]);
				$merchData['Merchant']['brand_id'] = ($brandId)?:null;
			}
			//Ownership info
			if ($isOnlineApp) {
				in_array(Hash::get($data, 'OwnerType-Corp'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Corporation' : '';
				in_array(Hash::get($data, 'OwnerType-SoleProp'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Sole Proprietor' : '';
				in_array(Hash::get($data, 'OwnerType-LLC'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Corporation - LLC' : '';
				in_array(Hash::get($data, 'OwnerType-Partnership'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Partnership' : '';
				in_array(Hash::get($data, 'OwnerType-NonProfit'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Tax Expempt (501c)' : '';
				in_array(Hash::get($data, 'OwnerType-Other'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Other' : '';
			} else {
				$merchData['Merchant']['merchant_ownership_type'] = Hash::get($data, 'CorpStatus');
				if (empty($merchData['Merchant']['merchant_ownership_type'])) {
					in_array(Hash::get($data, 'Owner Type - Corp'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Corporation' : '';
					in_array(Hash::get($data, 'Owner Type - Sole Prop'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Sole Proprietor' : '';
					in_array(Hash::get($data, 'Owner Type - LLC'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Corporation - LLC' : '';
					in_array(Hash::get($data, 'Owner Type - Partnership'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Partnership' : '';
					in_array(Hash::get($data, 'Owner Type - Non Profit'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Tax Expempt (501c)' : '';
					in_array(Hash::get($data, 'Owner Type - Other'), $this->csvAffirm) ? $merchData['Merchant']['merchant_ownership_type'] = 'Other' : '';
				}
			}
			$merchData['Merchant']['merchant_tin'] = str_replace('-', '', Hash::get($data, 'TaxID')); //value is encrypted in beforeSave()
			$merchData['Merchant']['merchant_d_and_b'] = null; //data not yet included in csv
			$merchData['Merchant']['cobranded_application_id'] = !empty(Hash::get($data, 'oaID'))? Hash::get($data, 'oaID'): null;

			//Set referer values
			if (!empty($referrerId)) {
				$merchData['Merchant']['referer_id'] = $referrerId;
				$merchData['Merchant']['ref_p_pct'] = Hash::get($data, 'setup_referrer_pct_gross');
				$setupPctPrft = Hash::get($data, 'setup_referrer_pct_profit');
				$setupPctVol = Hash::get($data, 'setup_referrer_pct_volume');
				if (is_numeric($setupPctPrft) && $setupPctPrft >= 0) {
					$merchData['Merchant']['ref_p_value'] = $setupPctPrft;
					$merchData['Merchant']['ref_p_type'] = self::PCT_MINUS_GP;
				} elseif (is_numeric($setupPctVol) && $setupPctVol >= 0) {
					$merchData['Merchant']['ref_p_value'] = $setupPctVol;
					$merchData['Merchant']['ref_p_type'] = self::POINTS_MINUS_GP;
				}
			}
			//Set reseller values
			if (!empty($resellerId)) {
				$merchData['Merchant']['reseller_id'] = $resellerId;
				$merchData['Merchant']['res_p_pct'] = Hash::get($data, 'setup_reseller_pct_gross');
				$setupPctPrft = Hash::get($data, 'setup_reseller_pct_profit');
				$setupPctVol = Hash::get($data, 'setup_reseller_pct_volume');
				if (is_numeric($setupPctPrft) && $setupPctPrft >= 0) {
					$merchData['Merchant']['res_p_value'] = $setupPctPrft;
					$merchData['Merchant']['res_p_type'] = self::PCT_MINUS_GP;
				} elseif (is_numeric($setupPctVol) && $setupPctVol >= 0) {
					$merchData['Merchant']['res_p_value'] = $setupPctVol;
					$merchData['Merchant']['res_p_type'] = self::POINTS_MINUS_GP;
				}
			}

			// Enable/disable womply for merchant
			$this->_setWomplyMerchant($merchData);

			//Check if client information is present 
			$globalClId = trim(Hash::get($data, 'client_id_global'));
			//set a memoization var to track multiple client ids found in the same csv file and enforce saving distinct Client records
			if (!isset($cliMem)) {
				$cliMem = [];
			}
			if (!empty($globalClId)) {
				$GlobalClient = $this->Client->find('first', array('conditions' => array('client_id_global' => $globalClId)));
				$clientId_UUID = Hash::get($GlobalClient, 'Client.id');
				if (!empty($clientId_UUID)) {
					$cliMem[$globalClId] = $clientId_UUID;
				} else {
					$clientId_UUID = CakeText::uuid();
				}
				//Add distinct new client data
				if (empty($cliMem[$globalClId])) {
					$cliMem[$globalClId] = $clientId_UUID;
					$merchData['Client'] = array(
						'id' => $clientId_UUID,
						'client_id_global' => $globalClId,
						'client_name_global' => trim(Hash::get($data, 'client_name_global')),
					);
				}
				
				$merchData['Merchant']['client_id'] = $cliMem[$globalClId];
			}

			if (!empty(Hash::get($data, 'external_foreign_id'))) {
				$SalesForce = ClassRegistry::init('SalesForce');
				$merchData['AssociatedExternalRecord'] = array(
					'merchant_id' => $merchData['Merchant']['id'],
					//currently only salesforce API is integrated, if additional external systems data needs to be saved this will need to be updated
					'external_system_name' => SalesForce::SF_CONFIG_NAME,
					'ExternalRecordField' => array(
						array(
							'merchant_id' => $merchData['Merchant']['id'],
							'field_name' => SalesForce::ACCOUNT_ID,
							'api_field_name' => $SalesForce->fieldNames[SalesForce::ACCOUNT_ID]['field_name'],
							'value' => Hash::get($data, 'external_foreign_id'),
						)
					)
				);
				if (!empty(Hash::get($data, 'sf_opportunity_id'))) {
					$merchData['AssociatedExternalRecord']['ExternalRecordField'][] = array(
						'merchant_id' => $merchData['Merchant']['id'],
						'field_name' => SalesForce::OPPTY_ID,
						'api_field_name' => $SalesForce->fieldNames[SalesForce::OPPTY_ID]['field_name'],
						'value' => Hash::get($data, 'sf_opportunity_id'),
					);
				}
			}
			//Check Possible TimelineEntry data
			if (!empty(Hash::get($data, 'expected_install_date'))) {

				$merchData['TimelineEntry'][] = array(
					'merchant_id' => $merchData['Merchant']['id'],
					'timeline_item_id' => TimelineItem::EXPECTED_TO_GO_LIVE,
					'timeline_date_completed' => date('Y-m-d', strtotime(Hash::get($data, 'expected_install_date'))),
					'action_flag' => false
				);
			}
			
			//Business Address
			$merchData['Address'][] = array(
				'address_type_id' => AddressType::BUSINESS_ADDRESS,
				'address_title' => Hash::get($data, 'DBA'),
				'address_street' => Hash::get($data, 'Address'),
				'address_city' => Hash::get($data, 'City'),
				'address_state' => Hash::get($data, 'State'),
				'address_zip' => Hash::get($data, 'Zip'),
				'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'PhoneNum')),
				'address_fax' => Hash::get($data, 'FaxNum'),
			);
			//Corp Address
			$merchData['Address'][] = array(
				'address_type_id' => AddressType::CORP_ADDRESS,
				'address_title' => Hash::get($data, 'CorpName'),
				'address_street' => Hash::get($data, 'CorpAddress'),
				'address_city' => Hash::get($data, 'CorpCity'),
				'address_state' => Hash::get($data, 'CorpState'),
				'address_zip' => Hash::get($data, 'CorpZip'),
				'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'CorpPhone')),
				'address_fax' => Hash::get($data, 'CorpFax')
			);
			//Mail address
			$merchData['Address'][] = array(
				'address_type_id' => AddressType::MAIL_ADDRESS,
				'address_title' => Hash::get($data, 'CorpName'),
				'address_street' => Hash::get($data, 'CorpAddress'),
				'address_city' => Hash::get($data, 'CorpCity'),
				'address_state' => Hash::get($data, 'CorpState'),
				'address_zip' => Hash::get($data, 'CorpZip'),
				'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'CorpPhone')),
				'address_fax' => Hash::get($data, 'CorpFax')
			);
			//Bank address
			$merchData['Address'][] = array(
				'address_type_id' => AddressType::BANK_ADDRESS,
				'address_title' => Hash::get($data, 'BankName'),
				'address_street' => Hash::get($data, 'BankAddress'),
				'address_city' => Hash::get($data, 'BankCity'),
				'address_state' => Hash::get($data, 'BankState'),
				'address_zip' => Hash::get($data, 'BankZip'),
				'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'BankPhone'))
			);
			//Merchant Bank
			$merchData['MerchantBank'] = array(
				'bank_name' => Hash::get($data, 'BankName'),
				'bank_routing_number' => Hash::get($data, 'RoutingNum'),
				'bank_dda_number' => Hash::get($data, 'AccountNum'),
				'fees_routing_number' => Hash::get($data, 'FeesRoutingNum'),
				'fees_dda_number' => Hash::get($data, 'FeesAccountNum')
			);

			//Set Merchant Reference check paper and online app fields.
			if (!empty(Hash::get($data, 'TradeRef1')) || !empty(Hash::get($data, 'TradeRefContact1')) || !empty(Hash::get($data, 'TradeRefName')) || !empty(Hash::get($data, 'TradeRefContact'))) {
				$merchData['MerchantReference'][] = array(
						'business_name' => !empty(Hash::get($data, 'TradeRefName'))? Hash::get($data, 'TradeRefName') : Hash::get($data, 'TradeRef1'),
						'person_name' => !empty(Hash::get($data, 'TradeRefContact'))? Hash::get($data, 'TradeRefContact') : Hash::get($data, 'TradeRefContact1'),
						'phone' => Hash::get($data, 'TradeRefPhone1'), //this is an online app field, data not yet included in csv (from paper apps)
					);
			}
			//The fields for the 2nd Merchant Reference are the same for both paper and online apps
			if (!empty(Hash::get($data, 'TradeRef2')) || !empty(Hash::get($data, 'TradeRefContact2'))) {
				$merchData['MerchantReference'][] = array(
						'business_name' => Hash::get($data, 'TradeRef2'),
						'person_name' => Hash::get($data, 'TradeRefContact2'),
						'phone' => Hash::get($data, 'TradeRefPhone2'),
					);
			}

			//Merchant Owners info
			$merchData['MerchantOwner'][0] = array(
				'owner_name' => (Hash::get($data, 'Principal')) ? Hash::get($data, 'Principal') : Hash::get($data, 'Owner1Name'),
				'owner_title' => Hash::get($data, 'Owner1Title'),
				'owner_equity' => (empty(Hash::get($data, 'OwnerEquity'))) ? Hash::get($data, 'Owner1Equity') : Hash::get($data, 'OwnerEquity'),
				'owner_social_sec_no' => Hash::get($data, 'OwnerSSN'),
				'Address' => array(
					'merchant_id' => $merchantId,
					'address_type_id' => AddressType::OWNER_ADDRESS,
					'address_street' => Hash::get($data, 'Owner1Address'),
					'address_city' => Hash::get($data, 'Owner1City'),
					'address_state' => Hash::get($data, 'Owner1State'),
					'address_zip' => Hash::get($data, 'Owner1Zip'),
					'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'Owner1Phone')),
					'address_fax' => Hash::get($data, 'Owner1Fax')
				)
			);

			//Is there an Owner 2?
			if (!empty(Hash::get($data, 'Owner2Name'))) {
				$merchData['MerchantOwner'][1] = array(
					'owner_name' => Hash::get($data, 'Owner2Name'),
					'owner_title' => Hash::get($data, 'Owner2Title'),
					'owner_equity' => (empty(Hash::get($data, 'Owner2Equity'))) ? Hash::get($data, 'Owner2%') : Hash::get($data, 'Owner2Equity'),
					'owner_social_sec_no' => Hash::get($data, 'Owner2SSN'),
					'Address' => array(
						'merchant_id' => $merchantId,
						'address_type_id' => AddressType::OWNER_ADDRESS,
						'address_street' => Hash::get($data, 'Owner2Address'),
						'address_city' => Hash::get($data, 'Owner2City'),
						'address_state' => Hash::get($data, 'Owner2State'),
						'address_zip' => Hash::get($data, 'Owner2Zip'),
						'address_phone' => preg_replace('/\D/i','', Hash::get($data, 'Owner2Phone')),
						'address_fax' => Hash::get($data, 'Owner2Fax')
					)
				);
			}

			$visaMcVol = Hash::get($data, 'VisaMcVol')? : Hash::get($data, 'MonthlyVol');
			$visaMcVol = !empty($visaMcVol)? preg_replace('/,/i', '', $visaMcVol) : 0;
			$discoverVol = !empty(Hash::get($data, 'DiscVol'))? preg_replace('/,/i', '', Hash::get($data, 'DiscVol')) : 0;
			$amexVol = !empty(Hash::get($data, 'AEMoVol'))? preg_replace('/,/i', '', Hash::get($data, 'AEMoVol')) : 0;
			$pinDebitVol = !empty(Hash::get($data, 'PINMoVol'))? preg_replace('/,/i', '', Hash::get($data, 'PINMoVol')) : 0;
			$monthlyVol = $visaMcVol + $discoverVol + $amexVol;
			$avgTicket = !empty(Hash::get($data, 'AvgTicket'))? preg_replace('/,/i', '', Hash::get($data, 'AvgTicket')): 0;
			$maxSalesAmt = !empty(Hash::get($data, 'MaxSalesAmt'))? preg_replace('/,/i', '', Hash::get($data, 'MaxSalesAmt')): 0;

			//Create Merchant's initial underwriting entry in irder to create associated MerchantUwVolume data
			$merchData['MerchantUw'] = array(
				'expedited' => false, //default to false
				'MerchantUwVolume' => array(
					'merchant_id' => $merchantId,
					'te_amex_number' => (is_numeric(Hash::get($data, 'AmexNum')))? Hash::get($data, 'AmexNum') : null,
					'average_ticket' => $avgTicket,
					'amex_avg_ticket' => $avgTicket, // data not yet included in csv using AvgTicket for now
					'pin_debit_avg_ticket' => $avgTicket, // data not yet included in csv using AvgTicket for now
					'mo_volume' => $monthlyVol,
					'visa_volume' => bcmul($visaMcVol, '0.65', 2), // assigning 65% of total VisaMcVol for visa volume
					'mc_volume' => bcmul($visaMcVol, '0.35', 2), // and 35% of total VisaMcVol for MC
					'ds_volume' => $discoverVol,
					'amex_volume' => $amexVol,
					'pin_debit_volume' => $pinDebitVol,
					'max_transaction_amount' => $maxSalesAmt,
					'sales' => (!empty($avgTicket))? (int)bcdiv($monthlyVol, $avgTicket,0) : 0,
					'card_present_swiped' => array_key_exists('Card Present Swiped', $data)? Hash::get($data, 'Card Present Swiped') : Hash::get($data, 'MethodofSales-CardPresentSwiped'),
					'card_not_present_internet' => array_key_exists('Card Not Present - Internet', $data)? Hash::get($data, 'Card Not Present - Internet') : Hash::get($data, 'MethodofSales-CardNotPresent-Internet'),
					'card_not_present_keyed' => array_key_exists('Card Not Present - Keyed', $data)? Hash::get($data, 'Card Not Present - Keyed') : Hash::get($data, 'MethodofSales-CardNotPresent-Keyed'),
					'card_present_imprint' => array_key_exists('Card Present Imprint', $data)? Hash::get($data, 'Card Present Imprint') : Hash::get($data, 'MethodofSales-CardPresentImprint'),
					'direct_to_consumer' => array_key_exists('Direct to Consumer', $data)? Hash::get($data, 'Direct to Consumer') : Hash::get($data, '%OfProductSold-DirectToCustomer'),
					'direct_to_business' => array_key_exists('Direct to Business', $data)? Hash::get($data, 'Direct to Business') : Hash::get($data, '%OfProductSold-DirectToBusiness'),
					'direct_to_government' => array_key_exists('Direct to Government', $data)? Hash::get($data, 'Direct to Government') : Hash::get($data, '%OfProductSold-DirectToGovernment'),
				)
			);

			$this->_setMiscMrchForeingKeys($merchData, $data, $isOnlineApp);
			if (!$isOnlineApp) {
				if (in_array(Hash::get($data, 'Daily Discount'), $this->csvAffirm)) {
					$merchData['MerchantUw']['MerchantUwVolume']['discount_frequency'] = MerchantUwVolume::DISCOUNT_FREQ_D;
				} elseif (in_array(Hash::get($data, 'Monthly Discount'), $this->csvAffirm)) {
					$merchData['MerchantUw']['MerchantUwVolume']['discount_frequency'] = MerchantUwVolume::DISCOUNT_FREQ_M;
				}
			}

			//Set cardtype should be added
			$this->_setMerchantCardTypes($merchData, $data, $isOnlineApp);

			//Set MerchantPricing
			try {
				$this->_setMerchantPricing($merchData, $data, $isOnlineApp);
			} catch (OutOfBoundsException $outEx) {
				$errors['Errors'][] = $outEx->getMessage();
			}
			//Activate/add products
			try {
				$this->_setMerchantProducts($data, $merchData, $repUcp['UserCompensationProfile']['id'], $isOnlineApp);
			} catch (Exception $e) {
				$errors['Errors'][] = $e->getMessage();
			}

			//Create invoices for every new merchant
			$bilOptnId = $this->MerchantAch->MerchantAchBillingOption->field('id', array(
				'billing_option_description' => 'Client'));
			if (Hash::get($data, 'CreditEquipmentFee') > 0) {
				$taxData = array();
				$taxAmount = null;
				$salesTax = null;
				$taxState = trim(Hash::get($data, 'State'));
				$taxZip = trim(Hash::get($data, 'Zip'));
				$taxCity = trim(Hash::get($data, 'City'));
				$isInCalState = strtolower($taxState) == 'ca';
				$noTaxReasonId = null;
				if ($isInCalState === false) {
					$noTaxReasonId = $this->MerchantAch->InvoiceItem->NonTaxableReason->field('id', ["reason ILIKE 'out of state'"]);
				}
				if ($isInCalState && !empty($taxZip)) {
					$requestTaxRate = $this->requestAPITaxRate($taxZip, $taxState, $taxCity);
					if (is_object($requestTaxRate) && $requestTaxRate->isOk()) {
						$taxData =  Hash::get(json_decode($requestTaxRate->body, true), 'results.0');
						$salesTax = Hash::get($taxData, 'taxSales');
					}
					$taxAmount = bcmul(Hash::get($data, 'CreditEquipmentFee'), $salesTax, 2);
				}

				$merchantAchId = CakeText::uuid();
				$merchData['MerchantAch'][] = array(
					'id' => $merchantAchId,
					'merchant_id' => $merchantId,
					'ach_date' => date("Y-m-d"),
					'acctg_month' => date("m"),
					'acctg_year' => date("Y"),
					'ach_amount' => Hash::get($data, 'CreditEquipmentFee'),
					'total_ach' => bcadd(Hash::get($data, 'CreditEquipmentFee'), $taxAmount, 2),
					'tax' => $taxAmount,
					'status' => 'PEND',
					'user_id' => $this->_getCurrentUser('id'),
					'invoice_number' => $this->MerchantAch->createInvoiceNum(),
					'merchant_ach_billing_option_id' => ($bilOptnId) ? $bilOptnId : null,
					'tax_rate_state' => Hash::get($taxData, 'stateSalesTax'),
					'tax_rate_county' => Hash::get($taxData, 'countySalesTax'),
					'tax_rate_city' => Hash::get($taxData, 'citySalesTax'),
					'tax_rate_district' => Hash::get($taxData, 'districtSalesTax'),
					'tax_amount_state' => bcmul(Hash::get($data, 'CreditEquipmentFee'), Hash::get($taxData, 'stateSalesTax'), 3),
					'tax_amount_county' => bcmul(Hash::get($data, 'CreditEquipmentFee'), Hash::get($taxData, 'countySalesTax'), 3),
					'tax_amount_city' => bcmul(Hash::get($data, 'CreditEquipmentFee'), Hash::get($taxData, 'citySalesTax'), 3),
					'tax_amount_district' => bcmul(Hash::get($data, 'CreditEquipmentFee'), Hash::get($taxData, 'districtSalesTax'), 3),
					'tax_state_name' => Hash::get($taxData, 'geoState'),
					'tax_city_name' => Hash::get($taxData, 'geoCity'),
					'tax_county_name' => Hash::get($taxData, 'geoCounty'),
					'InvoiceItem' => array(
						array(
							'merchant_ach_id' => $merchantAchId,
							'merchant_ach_reason_id' => MerchantAchReason::EQ_FEE,
							'commissionable' => true,
							'taxable' => $isInCalState,
							'non_taxable_reason_id' => $noTaxReasonId,
							'amount' => Hash::get($data, 'CreditEquipmentFee'),
							'tax_amount' => $taxAmount,
						)
					)
				);
			}
			$reasonId = $this->MerchantAch->MerchantAchReason->field('id', array('reason' => 'Application Fee'));
			$achAppStatusId = null;
			if ($isOnlineApp) {
				$achAppStatusId = $this->MerchantAch->MerchantAchAppStatus->field('id', array('app_status_description' => 'Online app + emailed S/K'));
			}
			$appFee = ($isAchMerch)? Hash::get($data, 'VCAppFee') : Hash::get($data, 'CreditAppFee');
			$noTaxReasonId = $this->MerchantAch->InvoiceItem->NonTaxableReason->field('id', ["reason ILIKE 'service/non equipment'"]);
			$merchantAchId = CakeText::uuid();
			$invoiceItems = array(
				array(
					'merchant_ach_id' => $merchantAchId,
					'merchant_ach_reason_id' => ($reasonId) ? $reasonId : null,
					'commissionable' => true,
					'taxable' => false,
					'non_taxable_reason_id' => $noTaxReasonId,
					'amount' => $appFee,
					'tax_amount' => null,
				)
			);
			//check non-zero non-empty gatway setup fee
			if (is_numeric(Hash::get($data, 'GatewaySetup')) && Hash::get($data, 'GatewaySetup') != null && (float)Hash::get($data, 'GatewaySetup') != 0) {
				$reasonId = $this->MerchantAch->MerchantAchReason->field('id', array('reason' => 'Account Setup Fee'));
				$invoiceItems[] = array(
					'merchant_ach_id' => $merchantAchId,
					'merchant_ach_reason_id' => ($reasonId) ? $reasonId : null,
					'commissionable' => true,
					'taxable' => false,
					'non_taxable_reason_id' => $noTaxReasonId,
					'amount' => Hash::get($data, 'GatewaySetup'),
					'tax_amount' => null,
				);
			}
			
			$merchData['MerchantAch'][] = array(
				'id' => $merchantAchId,
				'merchant_id' => $merchantId,
				'ach_date' => date("Y-m-d"),
				'acctg_month' => date("m"),
				'acctg_year' => date("Y"),
				'total_ach' => $appFee,
				'non_taxable_ach_amount' => $appFee,
				'status' => ($appFee > 0)? 'PEND' : 'COMP',
				'user_id' => $this->_getCurrentUser('id'),
				'invoice_number' => $this->MerchantAch->createInvoiceNum(),
				'merchant_ach_billing_option_id' => ($bilOptnId) ? $bilOptnId : null,
				'merchant_ach_app_status_id' => $achAppStatusId,
				'InvoiceItem' => $invoiceItems
			);
			//Add data structure to collection of data to saveAll
			$saveData[] = $merchData;
		}

		if (!empty($errors)) {
			$errors['Errors'][] = 'Failed to Upload Merchants! Resolve issue(s) above and try again.';
			return $errors;
		}
			//On import these fieds are not required
			$this->validator()->remove('bet_network_id');
			$this->validator()->remove('merchant_acquirer_id');
		try {
			//validate some data
			$this->Address->validator()->remove('address_street');
			$this->Address->validator()->remove('address_city');
			$this->Address->validator()->remove('address_state');
			$this->Address->validator()->remove('address_zip');
			$this->saveAll($saveData, array('deep' => true, 'validate' => 'only'));
			$invalidFields = array_unique(Hash::extract($this->validationErrors, '{n}.{s}.{n}.{s}.{n}'));
			if (!empty($invalidFields)) {
				$errors['Errors'][] = "Failed to save merchant data!*";
				$errors['Errors'] = array_merge($errors['Errors'], $invalidFields);
			} else {
				$this->saveAll($saveData, array('deep' => true, 'validate' => false));
					$newMerchants = $this->find('all', array(
						'recursive' => -1,
						'fields' => array(
							'id',
							'merchant_mid',
							'merchant_dba'
						),
						'conditions' => array(
									'Merchant.merchant_mid' => Hash::extract($saveData, '{n}.Merchant.merchant_mid'),
							)
						)
					);
					return $newMerchants;
			}
		} catch (Exception $e) {
			$errors['Errors'][] = "ERROR 500: Failed to upload merchant, some of the data may formatted incorrectly. Please review the data then report this error if problem persists.";
			$errors['Errors'][] = "Possible causes:";
			$errors['Errors'][] = "Owner title might be more than 40 characters long.";
			$errors['Errors'][] = "One of the addresses States might not be in the required two letter format ie. CA, TX, etc...";
		}
		if (!empty($errors)) {
			return $errors;
		}
	}

/**
 * _getMerchantEntity
 * Determines the proper entity based on the Merchant.merchant_mid.
 * If the merchant_mid starts with self::AX_PAY_MID_PREFIX it will assight the Entity::AX_PAY_ID
 * Otherwise if it starts with self::AX_TECH_MID_PREFIX then Entity::AX_TECH_ID will be assigned
 * If none of the above patterns match the merchant_mid then the user entity will be assigned to the merchant
 * if passed.
 * As long as merchant_mid is >= 16 characters, the entity will default to Entity::AX_PAY_ID if none of the above conditions. 
 *
 * @param string $merchantMid merchant_mid
 * @param string $userEntityId merchant_mid
 * @access protected
 * @return string
 */
	protected function _getMerchantEntity($merchantMid, $userEntityId = '') {
		if (substr($merchantMid, 0, 4) == self::AX_PAY_MID_PREFIX) {
			return Entity::AX_PAY_ID;
		} elseif (substr($merchantMid, 0, 4) == self::AX_TECH_MID_PREFIX) {
			return Entity::AX_TECH_ID;
		} elseif (!empty($userEntityId)) {
			return $userEntityId;
		}
		return null;
	}

/**
 * isAcquiringAxiaMed
 * Checks whether parameter identfying a merchant corresponds to an AxiaMed acquiring merchant
 * This method accepts ether a Merchant.id or a Merchant.merchant_mid as its parameter
 * 
 * @param  string  $id_Mid a Merchant.id or a Merchant.merchant_mid
 * @return boolean true of acquiring AxiaMed merchant otherwise false
 */
	public function isAcquiringAxiaMed($id_Mid) {
		if ($this->isValidUUID($id_Mid)) {
			return $this->hasAny(['id' => $id_Mid, "merchant_mid like '" . self::AX_TECH_MID_PREFIX ."%'"]);
		} elseif (strlen($id_Mid) == 16) {
			return ($this->_getMerchantEntity($id_Mid) === Entity::AX_TECH_ID);
		}
		return false;
	}
/**
 * _setWomplyMerchant
 * Enables womply for merchant being uploaded iff all associated users are also enabled
 *
 * @param array &$merchData merchant data originated from upload process
 * @access protected
 * @return void
 */
	protected function _setWomplyMerchant(&$merchData) {
		if ($this->User->womplyEnabled($merchData['Merchant']['user_id']) === false) {
			$merchData['womply_merchant_enabled'] = false;
			return;
		}
		if (!empty($merchData['Merchant']['partner_id'])) {
			if ($this->User->womplyEnabled($merchData['Merchant']['partner_id']) === false) {
				$merchData['womply_merchant_enabled'] = false;
				return;
			}
		}
		if (!empty($merchData['Merchant']['referer_id'])) {
			if ($this->User->womplyEnabled($merchData['Merchant']['referer_id']) === false) {
				$merchData['womply_merchant_enabled'] = false;
				return;
			}
		}
		if (!empty($merchData['Merchant']['reseller_id'])) {
			if ($this->User->womplyEnabled($merchData['Merchant']['reseller_id']) === false) {
				$merchData['womply_merchant_enabled'] = false;
				return;
			}
		}
		$merchData['womply_merchant_enabled'] = true;
	}

/**
 * _setMerchantPricing()
 *
 * @param array &$merchData reference to new merchant data 
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @param boolean $isOnlineApp whether the $csvData fields are from an online application or a physical one 
 * @return void
 */
	protected function _setMerchantPricing(&$merchData, &$csvData, $isOnlineApp) {
		//Only add main MerchantPricing if isOnlineApp or any of the main card types were added
		if ($isOnlineApp || !empty(Hash::extract($merchData, 'MerchantCardType.{n}.card_type_id'))) {
			//Merchant Pricing
			$mobileTranFee = (!empty(preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'MobileTran'))))? preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'MobileTran')) : preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'WirelessPerItem'));
			$merchData['MerchantPricing'] = array(
				'annual_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'AnnualFee')),
				'mc_vi_auth' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'TranFee')),
				'processing_rate' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'DiscRate1')),
				'mc_acquirer_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'MC Acquirer Fee')),
				'billing_mc_vi_auth' => bcadd(Hash::get($csvData, 'TranFee'), $mobileTranFee),
				'billing_discover_auth' => bcadd(Hash::get($csvData, 'TranFee'), $mobileTranFee),
				'billing_amex_auth' => bcadd(Hash::get($csvData, 'AmexPerItem'), $mobileTranFee),
				'billing_debit_auth' => bcadd(Hash::get($csvData, 'DebitTranFee'), $mobileTranFee),
				'billing_ebt_auth' => bcadd(Hash::get($csvData, 'EBTTranFee'), $mobileTranFee),
				'statement_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'StatementFee')),
				'wireless_access_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'MobileFee')),
				'gateway_access_fee' => (!empty(Hash::get($csvData, 'GatewayFee')))? preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'GatewayFee')) : null,
				'chargeback_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'ChargebackFee')),
				'min_month_process_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'MinimumFee')),
				'wireless_auth_fee' => $mobileTranFee,
				'aru_voice_auth_fee' => preg_replace("/[$|%|,]+/", "", Hash::get($csvData, 'VoiceAuth')),
			);
			if ($this->_hasAmex($csvData)) {
				$merchData['MerchantPricing']['amex_auth_fee'] = Hash::get($csvData, 'AmexPerItem');
				$merchData['MerchantPricing']['amex_processing_rate'] = Hash::get($csvData, 'Amex Discount Rate');
			}
			if ($this->_hasDiscover($csvData)) {
				$merchData['MerchantPricing']['ds_processing_rate'] = Hash::get($csvData, 'DiscRate1');
				$merchData['MerchantPricing']['ds_auth_fee'] = Hash::get($csvData, 'TranFee');
			}
			if ($this->_hasDebit($csvData)) {
				$merchData['MerchantPricing']['debit_auth_fee'] = Hash::get($csvData, 'DebitTranFee');
				$merchData['MerchantPricing']['debit_access_fee'] = Hash::get($csvData, 'DebitMonthlyAccessFee');
				$merchData['MerchantPricing']['debit_processing_rate'] = Hash::get($csvData, 'DebitDiscountRate');
			}
			if ($this->_hasEbt($csvData)) {
				$merchData['MerchantPricing']['ebt_auth_fee'] = Hash::get($csvData, 'EBTTranFee');
				$merchData['MerchantPricing']['ebt_access_fee'] = Hash::get($csvData, 'EBTStmtFee');
				$merchData['MerchantPricing']['ebt_processing_rate'] = Hash::get($csvData, 'EBTDiscRate');
			}
			//Set Visa/Mc/Discover/Amex bets
			$this->_setVisaMcDsAmexBets($merchData, $csvData);
		}
	}
/**
 * _setMiscMrchForeingKeys
 *
 * @param array &$merchData reference to new merchant data 
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return void
 */
	protected function _setMiscMrchForeingKeys(&$merchData, &$csvData, $isOnlineApp) {
		//These foreingKeys are set iff data is from online app
		$betNetworkId = null;
		$isNonAcquiring = $this->isNonAcquiringMid($merchData['Merchant']['merchant_mid']);
		if ($isOnlineApp) {
			$origAcquirerId = $this->OriginalAcquirer->field('id', array('acquirer' => 'Axia'));
			$betNetworkId = $this->BetNetwork->field('id', array('name' => 'TSYS'));
			//Set default networks and acquirers foreingKeys for online apps
			$merchData['Merchant']['original_acquirer_id'] = (!empty($origAcquirerId))? $origAcquirerId: null;			

			if (in_array(Hash::get($csvData, 'Discount PaidDaily'), $this->csvAffirm)) {
				$merchData['MerchantUw']['MerchantUwVolume']['discount_frequency'] = MerchantUwVolume::DISCOUNT_FREQ_D;
			} elseif (in_array(Hash::get($csvData, 'Discount PaidMonthly'), $this->csvAffirm)) {
				$merchData['MerchantUw']['MerchantUwVolume']['discount_frequency'] = MerchantUwVolume::DISCOUNT_FREQ_M;
			}
		} elseif (!$isNonAcquiring) {
			$cancelFeeId = $this->CancellationFee->field('id', array('cancellation_fee_description' => '$25 Per Month Remaining On Term'));
			$merchData['Merchant']['cancellation_fee_id'] = ($cancelFeeId)? : null;
		}
		$networkId = $this->Network->field('id', array('network_description' => 'TSYS'));
		$bEndNetworkId = $this->BackEndNetwork->field('id', array('network_description' => 'TSYS'));
		
		$midLength = strlen($merchData['Merchant']['merchant_mid']);
		$mBinId = null;
		if ($midLength === 16) {
			//Online apps coming from Axia Payments having a 16 digit MID starting with self::AX_PAY_MID_PREFIX should default to self::AX_I3_DEFAULT_BIN
			if (substr($merchData['Merchant']['merchant_mid'], 0, 4) == self::AX_PAY_MID_PREFIX) {
				$mBinId = $this->MerchantBin->field('id', array('bin' => self::AX_I3_DEFAULT_BIN));
			} elseif (substr($merchData['Merchant']['merchant_mid'], 0, 4) == self::AX_TECH_MID_PREFIX) {
				//Online Apps coming from Axia Tech having a 16 digit MID starting with self::AX_TECH_MID_PREFIX should default to self::AX_TECH_DEFAULT_BIN
				$mBinId = $this->MerchantBin->field('id', array('bin' => self::AX_TECH_DEFAULT_BIN));
			}
		}
		if ($isNonAcquiring) {
			//Set Non acquiring merchants misc account attributes
			//Wave cancellation fee
			$cancelFeeId = $this->CancellationFee->field('id', array('cancellation_fee_description' => 'Waived'));
			$merchData['Merchant']['cancellation_fee_id'] = ($cancelFeeId)? : null;
			$mAcquirerId = $this->MerchantAcquirer->field('id', array('acquirer' => 'Non Acquiring'));
			$betNetworkId = $this->BetNetwork->field('id', array('name' => 'Non Acquiring'));
			$apprStatus = $this->UwStatusMerchantXref->UwStatus->field('id', ['name' => 'Approved']);
			$recStatus = $this->UwStatusMerchantXref->UwStatus->field('id', ['name' => 'Received']);
            $apprDate = $this->_getNow()->format('Y-m-d H:i:s');
            $recDate = $apprDate;
            if (!empty($csvData['Submitted Date'])) {
                $recDate = new DateTime($csvData['Submitted Date']);
                $recDate = $recDate->format('Y-m-d H:i:s');
            }
            if (!empty($csvData['Approval Date'])) {
                $apprDate = new DateTime($csvData['Approval Date']);
                $apprDate = $apprDate->format('Y-m-d H:i:s');
            } 
			//Merchant hasMany UwStatusMerchantXref
			//Set approval and Received dates. UwStatusMerchantXref::beforeSave callback will be triggered
			$merchData['UwStatusMerchantXref'][] = array(
				'merchant_id' => $merchData['Merchant']['id'],
				'uw_status_id' => $apprStatus,
				'datetime' => $apprDate,
				'notes' => 'Approval date set automatically upon creation of merchant account.',
			);
			$merchData['UwStatusMerchantXref'][] = array(
				'merchant_id' => $merchData['Merchant']['id'],
				'uw_status_id' => $recStatus,
				'datetime' => $recDate,
				'notes' => 'Received date set automatically upon creation of merchant account.',
			);
		} else {
			$mAcquirerId = $this->MerchantAcquirer->field('id', array('acquirer' => 'Axia'));
		}

		$merchData['Merchant']['network_id'] = (!empty($networkId))? $networkId: null;
		$merchData['Merchant']['back_end_network_id'] = (!empty($bEndNetworkId))? $bEndNetworkId: null;
		$merchData['Merchant']['merchant_acquirer_id'] = (!empty($mAcquirerId))? $mAcquirerId: null;
		$merchData['Merchant']['bet_network_id'] = (!empty($betNetworkId))? $betNetworkId: null;
		$merchData['Merchant']['merchant_bin_id'] = (!empty($mBinId))? $mBinId: null;
	}

/**
 * isNonAcquiring
 *
 * @param string mid a Merchant.merchant_mid
 * @return void
 */
	public function isNonAcquiringMid($mid) {
		return (strlen($mid) === 5 ||  strlen($mid) === 6);
	}
/**
 * _setMerchantCardTypes
 *
 * @param array &$merchData reference to new merchant data 
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @param boolean $isOnlineApp whether the $csvData fields are from an online application or a physical one 
 * @return void
 */
	protected function _setMerchantCardTypes(array &$merchData, array &$csvData, $isOnlineApp = false) {
		$cardTypes = $this->MerchantCardType->CardType->find('list', array('fields' => array(
				'CardType.card_type_description', 'CardType.id'
			)));

		//Add V/MC card types for all merchants that are not Gateway-only or PaymentFusion-only which have an mid.length < 16
		if (strlen($merchData['Merchant']['merchant_mid']) === 16) {
			$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "Visa");
			$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "Mastercard");
		}
		if ($isOnlineApp) {
			$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "Discover");
		} else {
			if ($this->_hasDiscover($csvData)) {
				$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "Discover");
			}
		}
		if ($this->_hasAmex($csvData)) {
				$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "American Express");
		}
		if ($this->_hasDebit($csvData)) {
				$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "Debit");
		}
		if ($this->_hasEbt($csvData)) {
				$merchData["MerchantCardType"][]["card_type_id"] = Hash::get($cardTypes, "EBT");
		}
	}

/**
 * _setVisaMcDsAmexBets()
 *
 * @param array &$merchData reference to new merchant data
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _setVisaMcDsAmexBets(array &$merchData, array &$csvData) {
		$RateStructure = ClassRegistry::init('RateStructure');
		$amexRtSt = [];
		//Fields on paper app and online app csvData are the same, get V/Mc/DS bets based on rate tructures
		$vMcDsRtSt = $RateStructure->getRateStructureBets(Hash::get($csvData, 'Rate Structure'), Hash::get($csvData, 'Downgrades'));

		//Get Amex bets paper app fields
		if (array_key_exists('AERateStructure', $csvData)) {
			$amexRtSt = $RateStructure->getRateStructureBets(Hash::get($csvData, 'AERateStructure'), Hash::get($csvData, 'AEQualifiedExemptions'));
		} elseif (array_key_exists('Amex Rate Structure', $csvData)) { //check online app fields
			$amexRtSt = $RateStructure->getRateStructureBets(Hash::get($csvData, 'Amex Rate Structure'), Hash::get($csvData, 'Amex Downgrades'));
		}
		//Set Bet ids in merchant data
		$merchData['MerchantPricing']['visa_bet_table_id'] = Hash::get($vMcDsRtSt, 'Visa');
		$merchData['MerchantPricing']['mc_bet_table_id'] = Hash::get($vMcDsRtSt, 'Mastercard');
		$merchData['MerchantPricing']['ds_bet_table_id'] = Hash::get($vMcDsRtSt, 'Discover');
		$merchData['MerchantPricing']['amex_bet_table_id'] = Hash::get($amexRtSt, 'American Express');
	}

/**
 * _hasGateway
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasGateway(array &$csvData) {
		$gatewayHeaders = $this->gw1CsvHeaders;
		//if $gatewayHeaders are present in the $csvData keys then return true
		return (in_array(Hash::get($csvData, 'Gateway'), $this->csvAffirm) && !empty(array_intersect($gatewayHeaders, array_keys($csvData))));
	}

/**
 * _hasAch
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasAch(array &$csvData) {
		$achHeaders = $this->achCsvHeaders;
		//if $achHeaders are present in the $csvData keys then return true
		return (in_array(Hash::get($csvData, 'ach_accepted'), $this->csvAffirm) && !empty(array_intersect($achHeaders, array_keys($csvData))));
	}

/**
 * _validateGatewayCsvData
 * Checks gateway data in the csv array param and verifies that the specified gateway name mathes an existing gateway.
 * Also checks that Gateway_ID is not blank
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is the csv data related to Gateway validates
 * @throws Exception
 */
	protected function _validateAchCsvData(array &$csvData) {
		$achProvder = Hash::get($csvData, 'ach_provider_name');
		// Default providers to vericheck 
		if (empty($achProvder)) {
			$achProvder = AchProvider::NAME_VERICHECK;
		}
		//Check mathes an existing enabled gateway name
		$validName = $this->Ach->AchProvider->hasAny(['provider_name' => $achProvder]);

		if (!$validName) {
			throw new Exception('Ach Provider name specified in the CSV file does not match any existing providers.');
		}
		return true;
	}

/**
 * _validateGatewayCsvData
 * Checks gateway data in the csv array param and verifies that the specified gateway name mathes an existing gateway.
 * Also checks that Gateway_ID is not blank
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is the csv data related to Gateway validates
 * @throws Exception
 */
	protected function _validateGatewayCsvData(array &$csvData) {
		$csvGwName = Hash::get($csvData, 'Gateway_Name');
		$gwMid = Hash::get($csvData, 'Gateway_ID');
		//Check mathes an existing enabled gateway name
		$validName = $this->Gateway1->Gateway->hasAny(['name' => $csvGwName, 'enabled' => true]);

		if (!$validName) {
			throw new Exception('Gateway Name specified in the CSV file does not match any existing enabled gateways.');
		}

		//check gateway mid is not empty or in scientific notation
		$validGwMid = (!empty($gwMid) && strpos ($gwMid ,'.') === false);
		if (!$validGwMid) {
			$msg = empty($gwMid)? 'cannot be blank.' : "is formatted incorrectly or is invalid: $gwMid";
			throw new Exception('Gateway ID in the CSV file ' . $msg);
		}
		$exists = $this->Gateway1->hasAny(['gw1_mid' => $gwMid]);
		if ($exists) {
			throw new Exception('Gateway ID is already in use by another merchant. Please use a different ID');
		}
		return true;
	}

/**
 * _hasPmntFusion
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasPmntFusion(array &$csvData) {
		$pfHeaders = $this->pmntFusionCsvHeaders; //these are some but not all headers related to payment fusion
		//if $pfHeaders are present in the $csvData keys then return true
		return (in_array(Hash::get($csvData, 'PaymentFusion'), $this->csvAffirm) && !empty(array_intersect($pfHeaders, array_keys($csvData))));
	}

/**
 * _validatePaymentFusionCsv
 * Checks Payment Fusion product data in the csv array param and verifies that the specified feature name mathes an existing ProductFeature.
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is the csv data related to this product validates
 * @throws Exception
 */
	protected function _validatePaymentFusionCsv(array &$csvData) {
		if (empty(Hash::get($csvData, 'PF_ID')) || strlen(Hash::get($csvData, 'PF_ID')) !== 6 || $this->PaymentFusion->hasAny(['generic_product_mid' => Hash::get($csvData, 'PF_ID')])) {
			throw new Exception("The payment fusion id under PF_ID is invalid or already in use by another merchant");
		}
		if (!empty(Hash::get($csvData, 'PF_Feature')) && !ClassRegistry::init('ProductFeature')->hasAny(['feature_name' => Hash::get($csvData, 'PF_Feature')])) {
			throw new Exception("The value specified under PF_Feature in the CSV file does not match any Product Features.");
		}
		return true;
	}

/**
 * _hasDiscover
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasDiscover(array &$csvData) {
		if (in_array(Hash::get($csvData, 'Discover'), $this->csvAffirm) ||
			in_array(Hash::get($csvData, 'DoYouWantToAcceptDisc-New'), $this->csvAffirm) ||
			in_array(Hash::get($csvData, 'DoYouWantToAcceptDisc-NotNew'), $this->csvAffirm)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * _hasAmex
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasAmex(array &$csvData) {
		return (!empty(Hash::get($csvData, self::AMEX_CSV_AMEXNUM)) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_AENEW), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_AMEX), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_ACCEPT_NEW), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_ACCEPT_NOTNEW), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_ACCEPT_YES), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_ACCEPT_EXIST), $this->csvAffirm) ||
				in_array(Hash::get($csvData, self::AMEX_CSV_ACCEPT_NOT_EXIST), $this->csvAffirm));
	}

/**
 * _hasDebit
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasDebit(array &$csvData) {
		//Note: DebitPerItem == DebitTranFee but one is from online app and one is from physical app
		return (boolean)($this->sanitizeNumStr(Hash::get($csvData, "DebitDiscountRate")) +
				$this->sanitizeNumStr(Hash::get($csvData, "DebitPerItem")) +
				$this->sanitizeNumStr(Hash::get($csvData, "DebitTranFee")) +
				$this->sanitizeNumStr(Hash::get($csvData, "DebitMonthlyAccessFee")));
	}

/**
 * _hasEbt
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return boolean true when is determined in the csv data that merchant wants to have this product
 */
	protected function _hasEbt(array &$csvData) {
		//Note: EBTStmtFee == EBTAccess but one is from online app and one is from physical app
		return (boolean)($this->sanitizeNumStr(Hash::get($csvData, "EBTStmtFee")) +
				$this->sanitizeNumStr(Hash::get($csvData, "EBTAccess")) +
				$this->sanitizeNumStr(Hash::get($csvData, "EBTTranFee")) +
				$this->sanitizeNumStr(Hash::get($csvData, "EBTDiscRate")));
	}
/**
 * _getBusLvl
 *
 * @param array &$csvData reference to data that was converted from csv to array for merchant upload process
 * @return string
 */
	protected function _getBusLvl(array &$csvData) {
		if (!empty($csvData['BusinessLevel'])) {
			return trim(Hash::get($csvData, 'BusinessLevel'));
		} else {
			if (in_array(Hash::get($csvData, 'BusinessType-Retail'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Retail'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Retail'), $this->csvAffirm)) {
				return self::B_LVL_RETAIL;
			}
			if (in_array(Hash::get($csvData, 'BusinessType-Restaurant'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Restaurant'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Restaurant'), $this->csvAffirm)) {
				return self::B_LVL_RESTAURANT;
			}
			if (in_array(Hash::get($csvData, 'BusinessType-Lodging'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Lodging'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Lodging'), $this->csvAffirm)) {
				return self::B_LVL_LODGE;
			}
			if (in_array(Hash::get($csvData, 'BusinessType-MOTO'), $this->csvAffirm) || in_array(Hash::get($csvData, 'MOTO'), $this->csvAffirm) || in_array(Hash::get($csvData, 'MOTO'), $this->csvAffirm)) {
				return self::B_LVL_MOTO;
			}
			if (in_array(Hash::get($csvData, 'BusinessType-Internet'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Internet'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Internet'), $this->csvAffirm)) {
				return self::B_LVL_NET;
			}
			if (in_array(Hash::get($csvData, 'BusinessType-Grocery'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Grocery'), $this->csvAffirm) || in_array(Hash::get($csvData, 'Grocery'), $this->csvAffirm)) {
				return self::B_LVL_GROCERY;
			}
		}
		return null;
	}

/**
 * getProductsToAdd
 *
 * Expects an array built from a csv file with specific headers and assigns the products the merchant opted for, based on that data.
 *
 * @param array &$data reference of data extracted from csv file upon upload
 * @param array &$merchData reference to new merchant data
 * @param string $repUcpId the UUID of a rep/partner-rep's User Compensation Profile (UCP)
 * @param boolean $isOnlineApp wether the data in first param is from an online app
 * @return array an indexed array containing products to save activate
 * @throws Exception
 */
	protected function _setMerchantProducts(&$data, &$merchData, $repUcpId, $isOnlineApp = false) {
		if (empty($data) || empty($merchData) || empty($repUcpId)) {
			throw new Exception('Arguments passed to getProductsToAdd method cannot be empty');
		}
		$merchantId = Hash::get($merchData, 'Merchant.id');
		if (empty($merchantId)) {
			return null;
		}
		$isNonAcquiring = $this->isNonAcquiringMid($merchData['Merchant']['merchant_mid']);
		//Gateway 1 and payment fusion products are a special case
		$hasGateway = $this->_hasGateway($data);
		$hasPmntFusion = $this->_hasPmntFusion($data);
		if ($hasGateway || $hasPmntFusion) {
			if ($hasGateway) {
				$pId = $this->ProductsAndService->ProductsServicesType->field('id', ['products_services_description' => 'Gateway 1']);
				if (empty($pId)) {
					throw new Exception('Unable to find product named Gateway 1');
				}
				$merchProducts[] = array('merchant_id' => $merchantId, 'products_services_type_id' => $pId);
				$merchData['Gateway1'] = [
					'merchant_id' => $merchantId,
					'gateway_id' => $this->Gateway1->Gateway->field('id', ['name' => Hash::get($data, 'Gateway_Name')]),
					'gw1_mid' => Hash::get($data, 'Gateway_ID'),
					'gw1_monthly_volume' => Hash::get($data, 'Gateway_Monthly_Vol'),
					'gw1_monthly_num_items' => Hash::get($data, 'Gateway_Item_Count'),
					'gw1_rate' => Hash::get($data, 'Merch_Gateway_Rate'),
					'gw1_per_item' => Hash::get($data, 'Merch_Gateway_Per_Item_Fee'),
					'gw1_statement' => Hash::get($data, 'Merch_Gateway_Monthly_Fee'),
					'addl_rep_statement_cost' => Hash::get($data, 'Gateway_Additional_Rep_Monthly_Cost')
				];
			}
			if ($hasPmntFusion) {
				$pId = $this->ProductsAndService->ProductsServicesType->field('id', ['products_services_description' => 'Payment Fusion']);
				if (empty($pId)) {
					throw new Exception('Unable to find product named Payment Fusion');
				}
				$merchProducts[] = array('merchant_id' => $merchantId, 'products_services_type_id' => $pId);
				$hassThreshhold = Configure::read('App.productClasses.pst_6.min_haas_threshold');
				$isHaaS = (Hash::get($data, 'PF_Standard_Device_Fee') > $hassThreshhold ||
				Hash::get($data, 'PF_VP2PE_Device_Fee') > $hassThreshhold || 
				Hash::get($data, 'PF_PFCC_Device_Fee') > $hassThreshhold || 
				Hash::get($data, 'PF_VP2PE_PFCC_Device_Fee') > $hassThreshhold);
				$monthlyTotal = ((Hash::get($data, 'PF_Standard_Device_Qty') * Hash::get($data, 'PF_Standard_Device_Fee')) +
								(Hash::get($data, 'PF_VP2PE_Device_Qty') * Hash::get($data, 'PF_VP2PE_Device_Fee')) + 
								(Hash::get($data, 'PF_PFCC_Device_Qty') * Hash::get($data, 'PF_PFCC_Device_Fee')) + 
								(Hash::get($data, 'PF_VP2PE_PFCC_Device_Qty') * Hash::get($data, 'PF_VP2PE_PFCC_Device_Fee')));
				$merchData['PaymentFusion'] = [
						'merchant_id' => $merchantId,
						'generic_product_mid' => Hash::get($data, 'PF_ID'),
						'account_fee' => Hash::get($data, 'PF_Account_Fee'),
						'rate' => Hash::get($data, 'PF_Rate'),
						'monthly_total' => $monthlyTotal,
						'per_item_fee' => Hash::get($data, 'PF_Per_Item_Fee'),
						'standard_num_devices' => Hash::get($data, 'PF_Standard_Device_Qty'),
						'standard_device_fee' => Hash::get($data, 'PF_Standard_Device_Fee'),
						'vp2pe_num_devices' => Hash::get($data, 'PF_VP2PE_Device_Qty'),
						'vp2pe_device_fee' => Hash::get($data, 'PF_VP2PE_Device_Fee'),
						'pfcc_num_devices' => Hash::get($data, 'PF_PFCC_Device_Qty'),
						'pfcc_device_fee' => Hash::get($data, 'PF_PFCC_Device_Fee'),
						'vp2pe_pfcc_num_devices' => Hash::get($data, 'PF_VP2PE_PFCC_Device_Qty'),
						'vp2pe_pfcc_device_fee' => Hash::get($data, 'PF_VP2PE_PFCC_Device_Fee'),
						//setup as Hardware as a service when > threshold
						'is_hw_as_srvc' => $isHaaS
				];
			}
			$merchData['ProductsAndService'] = $merchProducts;
			//Don't add more products after adding PF or Gateway 1
			return;
		}
		if ($this->_hasAch($data)) {
			$pId = $this->ProductsAndService->ProductsServicesType->field('id', ['products_services_description' => 'ACH']);
			if (empty($pId)) {
				throw new Exception('Unable to find product named ACH');
			}
			//Default to vericheck when no ach provider is specified.
			$achProviderName = !empty(Hash::get($data, 'ach_provider_name'))? Hash::get($data, 'ach_provider_name') : AchProvider::NAME_VERICHECK;
			$merchProducts[] = array('merchant_id' => $merchantId, 'products_services_type_id' => $pId);
			$merchData['Ach'] = [
				'merchant_id' => $merchantId,
				'ach_provider_id' => $this->Ach->AchProvider->field('id', ['provider_name' => $achProviderName]),
				'ach_expected_annual_sales' => Hash::get($data, 'ach_annual_volume'),
				'ach_application_fee' => !empty(Hash::get($data, 'VCAppFee'))? Hash::get($data, 'VCAppFee') : Hash::get($data, 'ach_application_fee'),
				'ach_expedite_fee' => Hash::get($data, 'ach_expedite_fee'),
				'ach_average_transaction' => !empty(Hash::get($data, 'ACHDebAvgAmnt'))? Hash::get($data, 'ACHDebAvgAmnt') : Hash::get($data, 'ach_average_transaction'),
				'ach_estimated_max_transaction' => !empty(Hash::get($data, 'ACHDebHighTick'))? Hash::get($data, 'ACHDebHighTick') : Hash::get($data, 'ach_max_transaction'),
				'ach_rate' => !empty(Hash::get($data, 'VCDicRate'))? Hash::get($data, 'VCDicRate') : Hash::get($data, 'ach_rate'),
				'ach_per_item_fee' => !empty(Hash::get($data, 'VCTranFee'))? Hash::get($data, 'VCTranFee') : Hash::get($data, 'ach_per_item_fee'),
				'ach_statement_fee' => !empty(Hash::get($data, 'VCStatementFee'))? Hash::get($data, 'VCStatementFee') : Hash::get($data, 'ach_statement_fee'),
				'ach_batch_upload_fee' => Hash::get($data, 'ach_batch_upload_fee'),
				'ach_reject_fee' => Hash::get($data, 'ach_reject_fee'),
				'ach_monthly_gateway_fee' => !empty(Hash::get($data, 'VCGatewayFee'))? Hash::get($data, 'VCGatewayFee') : Hash::get($data, 'ach_monthly_gateway_fee'),
				'ach_monthly_minimum_fee' => !empty(Hash::get($data, 'VCMnthlyMinimum'))? Hash::get($data, 'VCMnthlyMinimum') : Hash::get($data, 'ach_monthly_minimum_fee'),
				'ach_mi_w_dsb_bank_name' => Hash::get($data, 'ach_disbursment_bank_name'),
				'ach_mi_w_dsb_routing_number' => Hash::get($data, 'ach_disbursment_routing'),
				'ach_mi_w_dsb_account_number' => Hash::get($data, 'ach_disbursment_account'),
				'ach_mi_w_fee_bank_name' => Hash::get($data, 'ach_fees_bank_name'),
				'ach_mi_w_fee_routing_number' => Hash::get($data, 'ach_fees_routing'),
				'ach_mi_w_fee_account_number' => Hash::get($data, 'ach_fees_account'),
				'ach_mi_w_rej_bank_name' => Hash::get($data, 'ach_rejects_bank_name'),
				'ach_mi_w_rej_routing_number' => Hash::get($data, 'ach_rejects_routing'),
				'ach_mi_w_rej_account_number' => Hash::get($data, 'ach_rejects_account'),
			];
		}

		//Continue adding products for acquiring merchants
		if ($isNonAcquiring === false) {
			//get products to activate on upload
			$configProds = Configure::read("OnUploadMerchantProducts");

			//Get list of products that are enabled in rep's comp profile
			$enabledProducts = $this->User->UserCompensationProfile->UserParameter->getEnabledProductsList($repUcpId);

			$hasAmex = $this->_hasAmex($data);
			$hasDiscover = $this->_hasDiscover($data);
			$hasDebit = $this->_hasDebit($data);
			$hasEbt = $this->_hasEbt($data);

			//Visa Mastercard and Discover products should only be added if MID is >= 12 digits long
			$visaMcDsAmexIsAllowed = (strlen(Hash::get($data, 'MID')) >= 12);
			if ($isOnlineApp) {
				$hasVisa = $hasMasterCard = $visaMcDsAmexIsAllowed;
			} else {
				$hasVisa = ($visaMcDsAmexIsAllowed && !empty($merchData['MerchantPricing']['visa_bet_table_id']));
				$hasMasterCard = ($visaMcDsAmexIsAllowed && !empty($merchData['MerchantPricing']['mc_bet_table_id']));
			}
			$merchantBetIds = [];
			//Remove Groups of products the merchant opted out or based on configuration
			if ($hasAmex === false || $visaMcDsAmexIsAllowed === false) {
				unset($configProds["AmexGroup"]);
			} else {
				$merchantBetIds[] = Hash::get($merchData, 'MerchantPricing.amex_bet_table_id');
			}
			if ($hasDiscover === false || $visaMcDsAmexIsAllowed === false) {
				unset($configProds["DiscoverGroup"]);
			} else {
				$merchantBetIds[] = Hash::get($merchData, 'MerchantPricing.ds_bet_table_id');
			}
			if ($hasVisa === false) {
				unset($configProds["VisaGroup"]);
			} else {
				$merchantBetIds[] = Hash::get($merchData, 'MerchantPricing.visa_bet_table_id');
			}
			if ($hasMasterCard === false) {
				unset($configProds["MasterCardGroup"]);
			} else {
				$merchantBetIds[] = Hash::get($merchData, 'MerchantPricing.mc_bet_table_id');
			}
			if ($hasDebit === false) {
				unset($configProds["DebitGroup"]);
			}
			if ($hasEbt === false) {
				unset($configProds["EbtGroup"]);
			}

			if (!empty($merchantBetIds)) {
				//McBetTable = BetTable model
				$mBetNames = $this->MerchantPricing->McBetTable->find('list', array('conditions' => array('id' => $merchantBetIds)));
			}

			$prodNames = Hash::extract($configProds, "{s}.{n}.product_name");

			//Remove Amex Discount if there is no Amex Rate Structure and threfore no amex bet table id
			if (empty(Hash::get($merchData, 'MerchantPricing.amex_bet_table_id'))) {
				foreach ($prodNames as $idx => $prodName) {
					if ($prodName === ProductsServicesType::AMEX_DISCOUNT_NAME) {
						unset($prodNames[$idx]);
						break;
					}
				}
			}
			$conditions = array('is_active' => true);
			if (array_key_exists('Term1-IP', $data)) {
				foreach ($prodNames as $idx => $prodName) {
					//if terminal is IP then we want Non Dial
					if (in_array($data['Term1-IP'], $this->csvAffirm)) {
						//Remove Dial products
						if ((stripos($prodName, '-Dial') !== false || stripos($prodName, ' Dial') !== false) && stripos($prodName, 'Non Dial') === false && stripos($prodName, 'Non-Dial') === false) {
							unset($prodNames[$idx]);
						}
					} else {
						//Remove Non Dial products
						if (stripos($prodName, 'Non-Dial') !== false || stripos($prodName, 'Non Dial') !== false) {
							unset($prodNames[$idx]);
						}
					}
				}
			} else {
				$userEntityId = $this->User->field('entity_id', ['id' => $merchData['Merchant']['user_id']]);
				if ($this->_getMerchantEntity(Hash::get($merchData, 'Merchant.entity_id')) === Entity::AX_TECH_ID || $userEntityId === Entity::AX_TECH_ID) {
					foreach ($prodNames as $idx => $prodName) {
						//if terminal is IP then we want Non Dial
						//Remove Dial products
						if ((stripos($prodName, '-Dial') !== false || stripos($prodName, ' Dial') !== false) && stripos($prodName, 'Non Dial') === false && stripos($prodName, 'Non-Dial') === false) {
							unset($prodNames[$idx]);
						}
					}
				} else {
					//We don't know whether is Dial or Non Dial and we definately don't want to add both at the same time
					//Include all product that do not have the word Dial and for good measure -Dial
					$conditions[] = "products_services_description NOT ILIKE '% Dial%'";
					$conditions[] = "products_services_description NOT ILIKE '%-Dial%'";
				}
			}
			//Add the final list of products to add to the conditions
			$conditions["products_services_description"] = $prodNames;

			//Get ids of all the active configured products
			$prodList = $this->ProductsAndService->ProductsServicesType->find('list', array(
				'fields' => array('products_services_description', 'id'),
				'conditions' => $conditions
			));

			foreach ($prodList as $pName => $pId) {
				$prodConditions = Hash::extract($configProds, "{s}.{n}[product_name=$pName].activate_when");
				$prodConditions = array_pop($prodConditions);

				if (!empty($prodConditions)) {
					//Add misc products that are always added to all merchants
					if ($prodConditions['always']) {
						$merchProducts[] = array('merchant_id' => $merchantId, 'products_services_type_id' => $pId);
					}
					//Add main product groups V/Mc/Ds/Amex:
					//Strict logic - will not activate the product if any condition is results false
					$activate = false;
					if (!empty($prodConditions['bet_table_is_not'])) {
						//activate when none of the mBetNames are this bet
						if (is_array($prodConditions['bet_table_is_not'])) {
							//Activate when the bet_table_is_not contained in the mBetNames
							$activate = empty(array_intersect($prodConditions['bet_table_is_not'], $mBetNames));
						} else {
							$activate = !in_array($prodConditions['bet_table_is_not'], $mBetNames);
						}
						if ($activate === false) {
							continue;
						}
					}
					if (!empty($prodConditions['bet_table_is'])) {
						//activate when one of the mBetNames are this bet
						if (is_array($prodConditions['bet_table_is'])) {
							$activate = !empty(array_intersect($prodConditions['bet_table_is'], $mBetNames));
						} else {
							$activate = in_array($prodConditions['bet_table_is'], $mBetNames);
						}
						if ($activate === false) {
							continue;
						}
					}
					if ($prodConditions['enabled_for_rep'] === true) {
						$activate = in_array($pName, $enabledProducts);
						if ($activate === false) {
							continue;
						}
					}
					if ($activate) {
						$merchProducts[] = array('merchant_id' => $merchantId, 'products_services_type_id' => $pId);
					}
				}
			}
		}

		if (!empty($merchProducts)) {
			$merchData['ProductsAndService'] = $merchProducts;
		}
	}

/**
 * Return the default search form values
 *
 * @return array
 */
	public function getDefaultSearchValues() {
		$searchParams['active'] = 1;
		if (!$this->User->isAdmin($this->_getCurrentUser('id'))) {
			$searchParams['user_id'] = $this->User->getDefaultUserSearch();
		}
		return $searchParams;
	}

/**
 * setPaginatorSettings
 *
 * @param array $filters seach filters that originate from the search form to use for seach
 * @return array
 */
	public function setPaginatorSettings($filters) {
		$complexUserIdArgs = $this->User->extractComplexId(Hash::get($filters, 'user_id'));
		$this->filterArgs['user_id'] = $this->getUserSearchFilterArgs($complexUserIdArgs);
		//Merchant Conditions
		$conditions = array();
		$dbaOrMid = (!empty($filters['dba_mid']))? $this->orConditions(array('search' => $filters['dba_mid'])) : '';

		if (!empty($dbaOrMid)) {
			$conditions[] = $dbaOrMid;
		}
		//Address conditions
		if ((!empty($filters['address_city']))) {
			$conditions['AddressBus.address_city ilike'] = $filters['address_city'];
		}
		if ((!empty($filters['address_state']))) {
			$conditions['AddressBus.address_state'] = $filters['address_state'];
		}
		if ((!empty($filters['name']))) {
			$conditions['Entity.id'] = $filters['name'];
		}
		//Merge parsed Entity/Rep conditions
		if ($filters['active'] == 2 || $filters['active'] == '') {
			unset($filters['active']);
		}

		$conditions = array_merge($conditions, $this->parseCriteria($filters));
		$settings = array(
			'limit' => 100,
			'order' => array('merchant_dba' => 'asc'),
			'conditions' => $conditions,
			'fiter' => true,
			'fields' => array(
				'Merchant.id',
				'Merchant.merchant_mid',
				'Merchant.user_id',
				'Merchant.active',
				'Merchant.merchant_dba',
				'Merchant.merchant_contact',
                'Client.client_id_global',
				'User.id',
				'User.user_first_name',
				'User.user_last_name',
				'EquipmentProgramming.hardware_serial',
				'Entity.entity_name',
				'Organization.name',
				'Region.name',
				'Subregion.name',
				'LastDepositReport.last_deposit_date',
			),
			'contain' => array(
				'AddressBus' => array('fields' => array('AddressBus.address_city',
					'AddressBus.address_street',
					'AddressBus.address_state',
					'AddressBus.address_phone'
				)),
			),
			'joins' => array(
				array('table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'conditions' => array(
						'Merchant.user_id = User.id'
					)
				),
                array(
                    'table' => 'clients',
                    'alias' => 'Client',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Merchant.client_id = Client.id'
                    )
                ),
				array('table' => '(SELECT DISTINCT ON (merchant_id) merchant_id, hardware_serial FROM equipment_programmings ORDER BY merchant_id, date_entered ASC)',
					'alias' => 'EquipmentProgramming',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.id = EquipmentProgramming.merchant_id'
					)
				),
				array(
					'table' => 'last_deposit_reports',
					'alias' => 'LastDepositReport',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.id = LastDepositReport.merchant_id'
					)
				),
				array(
					'table' => 'entities',
					'alias' => 'Entity',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.entity_id = Entity.id'
					)
				),
				array(
					'table' => 'organizations',
					'alias' => 'Organization',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.organization_id = Organization.id'
					)
				),
				array(
					'table' => 'regions',
					'alias' => 'Region',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.region_id = Region.id'
					)
				),
				array(
					'table' => 'subregions',
					'alias' => 'Subregion',
					'type' => 'LEFT',
					'conditions' => array(
						'Merchant.subregion_id = Subregion.id'
					)
				),
			),
		);
		//Include Pci compliance stuff
		$settings['contain']['MerchantPci'] = array('fields' => array('MerchantPci.saq_completed_date'));
		$settings['contain']['Partner'] = array('fields' => array('Partner.user_first_name', 'Partner.user_last_name'));

		$settings['contain']['SaqControlScan'] = array(
			'fields' => array(
				'((CASE WHEN "SaqControlScan"."pci_compliance" IS NULL THEN \'n/a\' ELSE "SaqControlScan"."pci_compliance" END)) as "SaqControlScan__pci_compliance"',
			)
		);

		if (!empty($filters['PCI_compliance'])) {
			if ($filters['PCI_compliance'] === 'yes') {
				$settings['conditions'][] = array('AND' => array(
					array(
						'OR' => array(
							"MerchantPci.saq_completed_date > now() - interval '1 year'",
							'SaqControlScan.pci_compliance =' => 'Yes'
							)
						)));
			} elseif ($filters['PCI_compliance'] === 'no') {
				$settings['conditions'][] = array('AND' => array(
					array(
						'OR' => array(
							'MerchantPci.saq_completed_date =' => '',
							"MerchantPci.saq_completed_date < now() - interval '1 year'",
							)
						)));
				$settings['conditions']['SaqControlScan.pci_compliance ='] = null;
			}
		}

		return $settings;
	}

/**
 * makeCsvString
 * builds a CSV string containing data to be used to exported
 *
 * @param array $merchantData data to export as csv
 * @return string
 */
	public function makeCsvString($merchantData) {
		$csvStr = "DBA,MID,Client ID,Rep,City,State,Phone,Contact,PCI,V#,Company,Organization,Region,Subregion,Location,Partner,Last Activity Date,Status" . "\n";
		foreach ($merchantData as $merchant) {
			$csvStr .= '"' . trim($merchant['Merchant']['merchant_dba']) . '"';
			$csvStr .= ',="' . $merchant['Merchant']['merchant_mid'] . '"';
			$csvStr .= (!empty($merchant['Client']['client_id_global']))? "," . $this->csvPrepString($merchant['Client']['client_id_global']) : ",";
			$csvStr .= "," . $this->csvPrepString($merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name']);
            $csvStr .= (!empty($merchant['AddressBus']['address_city']))? "," . $this->csvPrepString($merchant['AddressBus']['address_city']) : ",";
			$csvStr .= (!empty($merchant['AddressBus']['address_state']))? "," . $this->csvPrepString($merchant['AddressBus']['address_state']) : ",";
			$csvStr .= (!empty($merchant['AddressBus']['address_phone']))? "," . $this->csvPrepString($merchant['AddressBus']['address_phone']) : ",";
			$csvStr .= (!empty($merchant['Merchant']['merchant_contact']))? "," . $this->csvPrepString($merchant['Merchant']['merchant_contact']) : ",";
			$isLessThanOneYear = false;
			$mpCompletedDate = $merchant['MerchantPci']['saq_completed_date'];
			$smsxDateComplete = !empty($merchant['SaqMerchant']['SaqMerchantSurveyXref'][0]['datecomplete']) ? $merchant['SaqMerchant']['SaqMerchantSurveyXref'][0]['datecomplete'] : '';
			if (!empty($mpCompletedDate)) {
				$isLessThanOneYear = strtotime($mpCompletedDate) > strtotime("now -1 year");
			}
			if (!empty($smsxDateComplete) || $isLessThanOneYear === false) {
				$isLessThanOneYear = strtotime($smsxDateComplete) > strtotime("now -1 year");
			}
			$csvStr .= ",";
			if (in_array($merchant['SaqControlScan']['pci_compliance'], $this->csvAffirm) || $isLessThanOneYear) {
				$csvStr .= "Compliant";
			} else {
				$csvStr .= "Non-Compliant";
			}
			$csvStr .= (!empty($merchant['EquipmentProgramming']['hardware_serial'])) ? "," . $merchant['EquipmentProgramming']['hardware_serial'] : ",";
			$csvStr .= (!empty($merchant['Entity']['entity_name']))? "," . $this->csvPrepString($merchant['Entity']['entity_name']) : ",";
			$csvStr .= (!empty($merchant['Organization']['name']))? "," . $this->csvPrepString($merchant['Organization']['name']) : ",";
			$csvStr .= (!empty($merchant['Region']['name']))? "," . $this->csvPrepString($merchant['Region']['name']) : ",";
			$csvStr .= (!empty($merchant['Subregion']['name']))? "," . $this->csvPrepString($merchant['Subregion']['name']) : ",";
			$csvStr .= (!empty($merchant['Address']['address_street']))? "," . $this->csvPrepString($merchant['Address']['address_street']) : ",";
			$csvStr .= (!empty($merchant['Partner']['user_first_name']))? "," . $this->csvPrepString($merchant['Partner']['user_first_name'] . ' ' . $merchant['Partner']['user_last_name']) : ",";
			$csvStr .= (!empty($merchant['LastDepositReport']['last_deposit_date']))? "," . $this->csvPrepString($merchant['LastDepositReport']['last_deposit_date']) : ",";

			$csvStr .= "," . ($merchant['Merchant']['active'])? ",Active": ",Inactive";
			$csvStr .= "\n";
		}
		return $csvStr;
	}

/**
 * Determine if merchant is cancelled
 *
 * @param type $merchantId merchant id
 * @return bool true if merchant is cancelled
 */
	public function isCancelled($merchantId) {
		$merchant = $this->find('first', array(
			'fields' => array('Merchant.id'),
			'conditions' => array('Merchant.id' => $merchantId),
			'contain' => array(
				'MerchantCancellation' => array(
					'fields' => array('merchant_id', 'status')
				)
			)
		));
		$cancellationStatus = Hash::get($merchant, 'MerchantCancellation.status');
		return $cancellationStatus === MerchantCancellation::STATUS_COMPLETED;
	}

/**
 * Save the merchant changes in a log. If the changes are appoved, the changes
 * are also applied to the model record
 *
 * @param array $data merchant data to save
 * @param array $options saveAll options
 * @param string $editType Edit type
 * @throws InvalidArgumentException
 * @return bool
 */
	public function edit($data, $options = array(), $editType = null) {
		//Clean up timelineEntry
		$data['TimelineEntry'] = $this->TimelineEntry->cleanRequestData($data);
		return $this->MerchantChange->editChange($data, $this->alias, $options, $editType);
	}

/**
 * Create a change request log for the Merchant and approves it, making the changes permanent
 *
 * @param string $prodId a product id
 * @param bool $ascSort (optional) default false, if true will sort merchant dba in ascending order
 * @param int $active (optional) default null (no filter), 1 or 0 for active or inactive merchant
 * @throws \InvalidArgumentException array
 * @return array
 */
	public function getListByProductId($prodId, $ascSort = false, $active = null) {
		if ((!is_null($active)) && (!in_array($active, array(0, 1), true))) {
			throw new InvalidArgumentException("getListByProductId function third argument can only be null, or integers 1 or 0 as the third argument but " . gettype($active) . " was passed");
		}
		$settings = array(
			'fields' => array('Merchant.id', 'Merchant.merchant_dba'),
			'joins' => array(
				array(
					'table' => 'products_and_services',
					'alias' => 'ProductsAndService',
					'type' => 'INNER',
					'conditions' => array(
						'ProductsAndService.merchant_id = Merchant.id',
						'ProductsAndService.products_services_type_id' => $prodId,
					)
				)
			)
		);
		if ($ascSort) {
			$settings['order'] = array('Merchant.merchant_dba ASC');
		}
		if (!is_null($active)) {
			$settings['conditions'] = array("Merchant.active" => $active);
		}
		return $this->find('list', $settings);
	}

/**
 * Return the related data needed for edit form
 *
 * @param array $data Merchant data
 *
 * @return array
 */
	public function getEditFormRelatedData($data) {
		$allClients = $this->Client->find('all', array(
			'fields' => array('id', 'client_id_global', 'client_name_global'),
			'order' => 'client_name_global ASC'
		));
		$clients = [];
		foreach($allClients as $clRecord => $clData) {
			$clients[$clData['Client']['id']] = $clData['Client']['client_id_global'] . ' - ' . $clData['Client']['client_name_global'];
		}
		$cardTypes = $this->CardType->find('list', array(
			'fields' => array('id', 'card_type_description'	)
		));

		$partners = $this->User->getByRole(Configure::read('AssociatedUserRoles.Partner.roles.0'));

		$networks = $this->Network->find('list', array(
			'fields' => array(
				'id',
				'network_description'
			)
		));
		$merchantTypes = $this->MerchantType->getList();
        $acquiringTypeId = $this->MerchantType->getAcquiringTypeId();
        $betNetworks = $this->BetNetwork->getList();
		$backEndNetworks = $this->BackEndNetwork->find('list', array(
			'fields' => array('id', 'network_description')
		));

		$originalAcquirers = $this->OriginalAcquirer->find('list', array(
			'fields' => array('id', 'acquirer')
		));
		$merchantAcquirers = $this->MerchantAcquirer->find('list', array(
			'fields' => array('id', 'acquirer')
		));

		$merchantBins = $this->MerchantBin->find('list', array(
			'fields' => array('id', 'bin')
		));

		$cancellationFees = $this->CancellationFee->find('list', array(
			'fields' => array('id', 'cancellation_fee_description')
		));

		$referers = $this->User->getByRole(Configure::read('AssociatedUserRoles.Referrer.roles.0'));
		$resellers = $this->User->getByRole(Configure::read('AssociatedUserRoles.Reseller.roles.0'));

		$groups = $this->Group->find('list', array(
			'fields' => array('id', 'group_description'),
			'order' => array('group_description' => 'ASC'),
			'conditions' => array('active' => true),
		));

		$entities = $this->Entity->find('list', array(
			'fields' => array('id', 'entity_name'),
			'order' => array('entity_name' => 'ASC'),
		));

		$brands = $this->Brand->find('list', array(
			'fields' => array('id', 'name')
		));

		$ddOptns = $this->getViewDropDownsData();
		$merchant = $this->getSummaryMerchantData(Hash::get($data, 'Merchant.id'));

		$isEditLog = !empty(Hash::get($data, 'MerchantNote.0.loggable_log_id'));
		$userCanApproveChanges = $this->MerchantChange->userCanApproveChanges();

		$nonWomplyUsers = $this->extractNonWomplyUsers(Hash::get($data, 'Merchant'));
		$womplyStatuses = [];
		if (empty($nonWomplyUsers)) {
			$womplyStatuses = $this->WomplyStatus->find('list', array(
				'fields' => array('id', 'status')
			));
		}
		$betRateStructures = $this->MerchantPricing->getRateStructuresList(Hash::get($data, 'Merchant.id'));
		$organizations = $this->Organization->getList();
		$regions = $this->Region->getList();
		$subregions = $this->Subregion->getList();
		return compact(
			'merchantTypes',
            'acquiringTypeId',
            'clients',
			'organizations',
			'regions',
			'subregions',
			'merchant',
			'betRateStructures',
			'womplyStatuses',
			'ddOptns',
			'cardTypes',
			'partners',
			'networks',
			'merchantBins',
			'cancellationFees',
			'merchantAcquirers',
			'referers',
			'resellers',
			'groups',
			'entities',
			'backEndNetworks',
			'originalAcquirers',
			'betNetworks',
			'isEditLog',
			'userCanApproveChanges',
			'brands',
			'nonWomplyUsers'
		);
	}

/**
 * determine if merchant is pci qualified
 *
 * @param string $merchantId merchant id
 *
 * @return bool
 */
	public function isPciQualified($merchantId) {
		$results = $this->MerchantPci->find('first',
			array(
				'fields' => array(
					'MerchantPci.id'
				),
				'joins' => array(
					array(
						'alias' => 'SaqControlScan',
						'table' => 'saq_control_scans',
						'type' => 'LEFT',
						'conditions' => '"MerchantPci"."merchant_id" = "SaqControlScan"."merchant_id"'
					),
					array(
						'alias' => 'Merchant',
						'table' => 'merchants',
						'type' => 'LEFT',
						'conditions' => array(
							'"MerchantPci"."merchant_id" = "Merchant"."id"'
						)
					),
					array(
						'alias' => 'SaqControlScanUnboarded',
						'table' => 'saq_control_scan_unboardeds',
						'type' => 'LEFT',
						'conditions' => array(
							'"MerchantPci"."merchant_id" = "SaqControlScanUnboarded"."merchant_id"'
						)
					),
					array(
						'alias' => 'TimelineEntry',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							'"MerchantPci"."merchant_id" = "TimelineEntry"."merchant_id"'
						)
					),
					array(
						'alias' => 'TimelineItem',
						'table' => 'timeline_items',
						'type' => 'LEFT',
						'conditions' => array(
							'"TimelineEntry"."timeline_item_id" = "TimelineItem"."id"',
							'TimelineItem.timeline_item_description' => 'Approved',
						)
					),
					array(
						'alias' => 'TimelineEntry2',
						'table' => 'timeline_entries',
						'type' => 'LEFT',
						'conditions' => array(
							'"MerchantPci"."merchant_id" = "TimelineEntry2"."merchant_id"',
						)
					),
					array(
						'alias' => 'TimelineItem2',
						'table' => 'timeline_items',
						'type' => 'LEFT',
						'conditions' => array(
							'"TimelineEntry2"."timeline_item_id" = "TimelineItem2"."id"',
							'TimelineItem2.timeline_item_description' => 'Go-Live date',
						)
					)
				),
				'conditions' => array(
					'MerchantPci.merchant_id_old not in (select regexp_split_to_table(regexp_replace(array_to_string(array_agg(distinct "submids"),\',\'), \',\' , \'\1 \' , \'g\'), E\'\\s+\') AS submids from saq_control_scans where submids is not null)',
					'SaqControlScan.merchant_id IS NULL',
					'MerchantPci.pci_enabled' => true,
					'Merchant.active' => '1',
					'SaqControlScanUnboarded.date_unboarded IS NULL',
					'TimelineEntry2.timeline_date_completed < (NOW() - interval \'7 days\')',
					'MerchantPci.merchant_id' => $merchantId
				)
			)
		);

		if (empty($results)) {
			return false;
		}

		return true;
	}

/**
 * getNextMod10Num()
 * Function uses Luhn Algorithm to generate MOD 10 numbers that could be used as new MID's.
 * The new number(s) generated will be the very next mod 10 number(s) that follow(s) the number that was passed
 * as a first parameter (which is also the next number in the set of all mod 10 numbers).
 * The first parameter does not have to be mod 10.
 * 
 * NOTE: in order to generate AxiaMed MIDs the first parameter must be the the last 7 digits of the last known MID number.
 *
 * @param string $currNumStr a string representation of ANY natural number from which to find the next MOD 10 number(s) that follow it.
 * @param int $amount the anount of MOD 10 numbers to generate.
 * @param string $prefix a prefix to prepend the resulting numbers with.
 * @param boolean $checkAvailability if true the resulting numbers will be checked agains existing merchant MIDs
 * @return array containing string representations of generated MOD 10 numbers greater than the one supplied in the first parameter
 *		   If $checkAvailability is true a two dimension array is returned with arrays at the second dimension containing each MID number and whether it is available
 * @throws InvalidArgumentException
 */
	public function getNextMod10Num($currNumStr, $amount = 1, $prefix = "", $checkAvailability = false) {
		//Convert to number without typecasting and check wether it resuts as a positive natural number
		$curNum = $currNumStr + 0;
		if ($curNum < 0 || !is_int($curNum) || !is_string($currNumStr)) {
			throw new InvalidArgumentException("Function expects parameter 1 to be string representation of a positive natural number but " . gettype($currNumStr) . " $curNum was passed.");
		}
		if ($checkAvailability && strlen($prefix) != 9) {
			throw new InvalidArgumentException("Cannot Check availability of resulting MID numbers without the first 9 digit prefix of the MID number");
		}
		$m10NumStrings = [];
		for ($x = 0; $x < $amount; $x++) {
			//Add one
			$curNum++;
			$currNumStr = strval($curNum);
			do {
				$total = 0;
				//Alternating digits indicator
				$alt = false;
				for ($i = strlen($currNumStr) - 1; $i >= 0; $i--) {
					$curDigit = ($currNumStr[$i]);
					if ($alt) {
						$curDigit *= 2;
						if ($curDigit > 9) {
							$curDigit -= 9;
						}
					}
					$total += $curDigit;
					$alt = !$alt;
				}
				if ($total % 10 !== 0) {
					$curNum++;
					$currNumStr = strval($curNum);
				}

			} while ($total % 10 !== 0);
			if ($checkAvailability) {
				$m10NumStrings[] = [
					'mid' => $prefix . $currNumStr,
					'id' => $this->field('id', ['merchant_mid' => $prefix . $currNumStr]),
				];
			} else {
				$m10NumStrings[] = $currNumStr;
			}
		}
		return $m10NumStrings;
	}
/**
 * extractNonWomplyUsers
 *
 * @param array $mData single dimention array containg users associated with merchant
 * @return array
 */
	public function extractNonWomplyUsers(array $mData) {
		$usersRoles = [];
		if (!empty($mData['user_id']) && $this->User->womplyEnabled($mData['user_id']) === false) {
			$usersRoles[] = User::ROLE_REP;
		}

		if (!empty($mData['partner_id']) && $this->User->womplyEnabled($mData['partner_id']) === false) {
			$usersRoles[] = User::ROLE_PARTNER;
		}

		if (!empty($mData['referer_id']) && $this->User->womplyEnabled($mData['referer_id']) === false) {
			$usersRoles[] = User::ROLE_REFERRER;
		}

		if (!empty($mData['reseller_id']) && $this->User->womplyEnabled($mData['reseller_id']) === false) {
			$usersRoles[] = User::ROLE_RESELLER;
		}
		return $usersRoles;
	}

/**
 * updateUserMerchantsWomply
 * Updates Merchant.womply_merchant_enabled for all merchants associated with User.id
 *
 * @param string $userId a User.id 
 * @param boolean $status the new status for womply_merchant_enabled
 * @return boolean
 */
	public function updateUserMerchantsWomply($userId, $status) {
		if ($status == false) {
			return $this->updateAll(
				['Merchant.womply_merchant_enabled' => 'FALSE'],
				[
					'Merchant.womply_merchant_enabled' => 'TRUE',
					'OR' => [
						'Merchant.user_id' => $userId,
						'Merchant.partner_id' => $userId,
						'Merchant.referer_id' => $userId,
						'Merchant.reseller_id' => $userId,
					]
				]
			);
		} else {
			$conditionsSubQuery = [
				'Merchant2.womply_merchant_enabled = FALSE',
				'OR' => [
					'Merchant2.user_id' => $userId,
					'Merchant2.partner_id' => $userId,
					'Merchant2.referer_id' => $userId,
					'Merchant2.reseller_id' => $userId,
				],
				//User Aliases in these conditions must match the aliases defined in the subquery join array
				'RepUser.womply_user_enabled = TRUE',
				'(CASE WHEN Merchant2.partner_id IS NOT NULL then PartnerUser.womply_user_enabled ELSE TRUE END)',
				'(CASE WHEN Merchant2.referer_id IS NOT NULL then RefUser.womply_user_enabled ELSE TRUE END)',
				'(CASE WHEN Merchant2.reseller_id IS NOT NULL then ResUser.womply_user_enabled ELSE TRUE END)'
			];

			$db = $this->getDataSource();
			$subQuery = $db->buildStatement(
				array(
					'fields' => array('"Merchant2"."id"'),
					'table' => $db->fullTableName($this),
					'alias' => 'Merchant2',
					'joins' => [
						[
							'table' => 'users',
							'alias' => 'RepUser',
							'type' => 'LEFT',
							'conditions' => [
							"Merchant2.user_id = RepUser.id"
							]
						],
						[
							'table' => 'users',
							'alias' => 'PartnerUser',
							'type' => 'LEFT',
							'conditions' => [
							"Merchant2.partner_id = PartnerUser.id"
							]
						],
						[
							'table' => 'users',
							'alias' => 'RefUser',
							'type' => 'LEFT',
							'conditions' => [
							"Merchant2.referer_id = RefUser.id"
							]
						],
						[
							'table' => 'users',
							'alias' => 'ResUser',
							'type' => 'LEFT',
							'conditions' => [
							"Merchant2.reseller_id = ResUser.id"
							]
						],
					],
					'conditions' => $conditionsSubQuery,
				),
				$this //context model is a required param
			);
			$updateAllConditionsSubQuery = 'Merchant.id IN (' . $subQuery . ')';
			return $this->updateAll(
				['Merchant.womply_merchant_enabled' => 'TRUE'],
				[$updateAllConditionsSubQuery]
			);
		}
	}

/**
 * getInstMoCount
 * Calculates the number of months since a merchant's Go-Live date as in a "clock" that starts to count the months based on the following logic:
 *
 *	-- If the Go-Live Date is dated within the 1st - the 15th of the month then the clock starts for that month.
 *	-- If the Go-Live Date is dated after the 15th of the month then the clock starts for next month, but if the next month has not yet started the count will be zero.
 *	-- Clock is zero-based so month zero indicates clock has started to count.
 *	-- Will return 0 if count has not started, in other words if the merchant has not been installed.  
 *  -- All months (compliant with above logic) between start and end month will be counted as a whole regardless of days.
 *
 * @param string $id Merchant.id
 * @return int min 0 max n | n + 1
 */
	public function getInstMoCount($id) {
		$instDate = $this->TimelineEntry->field('timeline_date_completed', [
			'merchant_id' => $id,
			'timeline_item_id' => TimelineItem::GO_LIVE_DATE
		]);

		if (empty($instDate)) {
			return 0;
		}
		$instDate = date_create($instDate);
		$instDay = (int)$instDate->format("d");
		$instY = (int)$instDate->format("Y");
		$instMo = (int)$instDate->format("m");
		$nowY = (int)date('Y');
		$nowMo = (int)date('m');
		$yearDiff = $nowY - $instY;

		$instMoCount = ($nowMo - $instMo) + (12 * ($yearDiff));

		//include Go-Live month if Go-Live day number is within the 1st and - 15th
		if ($instDay <= 15) {
			//count is n + 1
			$instMoCount += 1;
		}
		return $instMoCount;
	}

/**
 * getCommissionPricingMerchants
 * Returns a list of merchants that have a TimelineEntry with a Submitted and Install Commission date within the specified Month/Year and also have the specified product id
 * An install commission date is required for merchants to show up on the Commission Multiple report
 * A Submitted date is required for merchants to show up on the Gross Profit report
 *
 * @param string $productId a product id
 * @param integer $month month number
 * @param integer $year year number
 * @return array
 * @throws InvalidArgumentException
 */
	public function getCommissionPricingMerchants($productId, $month, $year) {
		if (empty($productId) || empty($month) || empty($year)) {
			throw new InvalidArgumentException("function arguments are all requred");
		}
		$range = [
				'month' => $month,
				'year' => $year
			];
		$data = [
			'from_date' => $range,
			'end_date' => $range,
		];
		$fromConditions = $this->TimelineEntry->dateStartConditions($data, 'TimelineEntry');
		$toConditions = $this->TimelineEntry->dateEndConditions($data, 'TimelineEntry');
		$settings = [
			'fields' => ['Merchant.id', 'Merchant.merchant_dba'],
			'joins' => [
				[
					'table' => 'products_and_services',
					'alias' => 'ProductsAndService',
					'type' => 'INNER',
					'conditions' => [
						'ProductsAndService.merchant_id = Merchant.id',
						'ProductsAndService.products_services_type_id' => $productId,
					]
				],
				[
					'table' => 'timeline_entries',
					'alias' => 'TimelineEntry',
					'type' => 'INNER',
					'conditions' => [
						'Merchant.id = TimelineEntry.merchant_id',
						'OR' => [
							['TimelineEntry.timeline_item_id' => TimelineItem::INSTALL_COMMISSIONED],
							['TimelineEntry.timeline_item_id' => TimelineItem::SUBMITTED]
						]
					]
				]
			]
		];

		$settings['conditions'] = array_merge(["Merchant.active" => 1], $fromConditions, $toConditions);
		return $this->find('list', $settings);
	}

}
