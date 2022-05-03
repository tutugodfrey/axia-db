<?php

// Provides an organization of the app permissions
$modules = array(
	'Dashboard' => array(
		'actions' => array(
			// title =>
			'View Dashboard' => 'app/actions/Dashboards/home',
			'Retrieve Chart Data' => 'app/actions/Dashboards/getChartData'
		),
		// mandatory modules definition
		'modules' => array(
			'Dashboards sysCsvExport' => 'app/actions/Dashboards/view/module/sysCsvExport',
			'Dashboards corpModule' => 'app/actions/Dashboards/view/module/corpModule',
			'Dashboards homeAdminModule' => 'app/actions/Dashboards/view/module/homeAdminModule',
			'Dashboards Reports Top Nav' => 'app/actions/Dashboards/view/module/reportsTopNav',
		)
	),
	'Partner' => array(
		'actions' => array(
			// title =>
			'Partners index' => 'app/actions/Partners/index'
		),
		// mandatory modules definition
		'modules' => array()
	),
	'User' => array(
		'actions' => array(
			// title =>
			'Users index' => 'app/actions/Users/index',
			'Users Users list export' => 'app/actions/Users/exportUsers',
			'Users view' => 'app/actions/Users/view',
			'Users edit' => 'app/actions/Users/edit',
			'Users add' => 'app/actions/Users/add',
			'Users Toggle Active/Inactive' => 'app/actions/Users/toggleActive',
			'Users block' => 'app/actions/Users/block',
			'Users unblock' => 'app/actions/Users/unblock',
			'Users associated edit' => 'app/actions/Users/editAssociated',
			'Users associated ajaxAssociatedView' => 'app/actions/Users/ajaxAssociatedView',
			'Users associated remove' => 'app/actions/Users/removeAssociated',
			'Users get by role' => 'app/actions/Users/getUsersByRole',
			'Users get permission' => 'app/actions/Users/getPermissionLevelByRole',
			'Users checkDecrypPassword' => 'app/actions/Users/checkDecrypPassword',
			'Users assignRoles' => 'app/actions/Users/assignRoles',
			'Users Set Main/Default Associated User' => 'app/actions/Users/setMainAssociated',
			'Users Create API Access Token' => 'app/actions/Users/create_api_token',
			'Users Create API Password' => 'app/actions/Users/create_api_pw',
			'API GET Partners' => 'app/actions/Users/api_get_partners',
			'API GET Reps' => 'app/actions/Users/api_get_reps',
			'Users secret' => 'app/actions/Users/secret',
			'Users turn off 2FA' => 'app/actions/Users/turn_off_2FA',
		),
		// mandatory modules definition
		'modules' => array(
			'Report Filter Default Entity' => 'app/actions/Users/view/module/defaultValueUserFilterModule',
			'Change RoleSelf' => 'app/actions/Users/view/module/changeRoleSelfModule',
			'API Actions' => 'app/actions/Users/view/module/apiAccountActions',
		)
	),
	'UsersProductsRisk' => array(
		'actions' => array(
			'UsersProductsRisks editMany' => 'app/actions/UsersProductsRisks/editMany',
		),
		'modules' => array(
			'UsersProductsRisks readModule' => 'app/actions/UsersProductsRisks/view/module/readModule',
		)
	),
	'CommissionFee' => array(
		'actions' => array(
			'CommissionFees editMany' => 'app/actions/CommissionFees/editMany',
			'CommissionFees ajaxView' => 'app/actions/CommissionFees/ajaxView',
		),
		'modules' => array(
			'CommissionFees readModule' => 'app/actions/CommissionFees/view/module/readModule',
		)
	),
	'MerchantPricing' => array(
		'actions' => array(
			'MerchantPricing products_and_services' => 'app/actions/MerchantPricings/products_and_services',
			'MerchantPricing edit' => 'app/actions/MerchantPricings/edit',
		),
		'modules' => array(
			'MerchantPricings Product Activation Pane' => 'app/actions/MerchantPricings/view/module/activatorPanel',
			'MerchantPricings psModule1' => 'app/actions/MerchantPricings/view/module/psModule1'
		)
	),
	'MerchantPricingArchive' => array(
		'actions' => array(
			'MerchantPricingArchives Index' => 'app/actions/MerchantPricingArchives/index',
			'MerchantPricingArchives Edit' => 'app/actions/MerchantPricingArchives/edit',
			'MerchantPricingArchives Delete Many Selected' => 'app/actions/MerchantPricingArchives/deleteMany',
			'MerchantPricing ajaxShowArchiveEditMenu' => 'app/actions/MerchantPricingArchives/ajaxShowArchiveEditMenu',
			'MerchantPricing Create Many By Merchant' => 'app/actions/MerchantPricingArchives/createManyByMerchant',
			'MerchantPricing Ajax Display Create Many Menu' => 'app/actions/MerchantPricingArchives/createManyMenu',
		),
		'modules' => array(
			'MerchantPricingArchives archivePanel' => 'app/actions/MerchantPricingArchives/view/module/archivePanel',
		)
	),
	'MerchantCancellation' => array(
		'actions' => array(
			'MerchantCancellations index' => 'app/actions/MerchantCancellations/index',
			'MerchantCancellations view' => 'app/actions/MerchantCancellations/view',
			'MerchantCancellations add' => 'app/actions/MerchantCancellations/add',
			'MerchantCancellations edit' => 'app/actions/MerchantCancellations/edit',
		),
		'modules' => array()
	),
	'UserCompensationProfile' => array(
		'actions' => array(
			'Users update profile options' => 'app/actions/UserCompensationProfiles/updateProfileOptions',
			'UserCompensationProfile add Comp. Profile' => 'app/actions/UserCompensationProfiles/addCompProfile',
			'UserCompensationProfile copy Comp. Profiles' => 'app/actions/UserCompensationProfiles/copyMany',
			'UserCompensationProfile get updated UCP list' => 'app/actions/UserCompensationProfiles/getUpdatedUcpList',
			'UserCompensationProfile Delete Comp. Profile' => 'app/actions/UserCompensationProfiles/delete',
		),
		'modules' => array(
			'UserCompensationProfile Primary Managers Compensation Module' => 'app/actions/UserCompensationProfiles/view/module/SMCompModule',
			'UserCompensationProfile Secondary Managers Compensation Module' => 'app/actions/UserCompensationProfiles/view/module/SM2CompModule',
			'UserCompensationProfile RepCostStructureModule' => 'app/actions/UserCompensationProfiles/view/module/RepCostStructureModule',
			'UserCompensationProfile ProductSettingModule' => 'app/actions/UserCompensationProfiles/view/module/ProductSettingModule',
			'UserCompensationProfile CompensatonTabs' => 'app/actions/UserCompensationProfiles/view/module/CompensatonTabs',
		)
	),
	'MerchantNotes' => array(
		'actions' => array(
			'MerchantNotes index' => 'app/actions/MerchantNotes/index',
			'MerchantNotes add' => 'app/actions/MerchantNotes/add',
			'MerchantNotes edit' => 'app/actions/MerchantNotes/edit',
			'MerchantNotes delete' => 'app/actions/MerchantNotes/delete',
		),
		'modules' => array('MerchantNotes addPrgmNote' => 'app/actions/MerchantNotes/view/module/addPrgmNote')
	),
	'TimelineEntries' => array(
		'actions' => array(
			'TimelineEntries index' => 'app/actions/TimelineEntries/index',
			'TimelineEntries add' => 'app/actions/TimelineEntries/add',
			'TimelineEntries edit' => 'app/actions/TimelineEntries/edit',
			'TimelineEntries timeline' => 'app/actions/TimelineEntries/timeline',
		),
		'modules' => array('TimelineEntries addInstallNote' => 'app/actions/TimelineEntries/view/module/addInstallNote',
			'TimelineEntries timelineList' => 'app/actions/TimelineEntries/view/module/timelineList',
		)
	),
	'MerchantUws' => array(
		'actions' => array(
			'MerchantUws index' => 'app/actions/MerchantUws/index',
			'MerchantUws view' => 'app/actions/MerchantUws/view',
			'MerchantUws add' => 'app/actions/MerchantUws/add',
			'MerchantUws edit' => 'app/actions/MerchantUws/edit',
			'MerchantUws delete' => 'app/actions/MerchantUws/delete',
		),
		'modules' => array(
			'MerchantUws activitySectionEdit' => 'app/actions/MerchantUws/view/module/activitySectionEdit',
			'MerchantUws hiddenS1' => 'app/actions/MerchantUws/view/module/hiddenS1',
			'MerchantUws tneSection' => 'app/actions/MerchantUws/view/module/tneSection',
			'MerchantUws credScoreSection' => 'app/actions/MerchantUws/view/module/credScoreSection',
			'MerchantUws indexRepCol' => 'app/actions/MerchantUws/view/module/indexRepCol',
			'MerchantUws indexPartnerCol' => 'app/actions/MerchantUws/view/module/indexPartnerCol',
			'MerchantUws overallReadMode' => 'app/actions/MerchantUws/view/module/overallReadMode'
		)
	),
	'MerchantOwners' => array(
		'actions' => array(
			'MerchantOwners add' => 'app/actions/MerchantOwners/add',
			'MerchantOwners edit' => 'app/actions/MerchantOwners/edit',
			'MerchantOwners Delete' => 'app/actions/MerchantOwners/delete',
			'MerchantOwners ajaxDisplayDecryptedVal' => 'app/actions/MerchantOwners/ajaxDisplayDecryptedVal',
			'MerchantOwners ajaxAddAndEdit' => 'app/actions/MerchantOwners/ajaxAddAndEdit',
		),
		'modules' => array()
	),
	'MerchantBanks' => array(
		'actions' => array(
			'MerchantBanks add' => 'app/actions/MerchantBanks/add',
			'MerchantBanks edit' => 'app/actions/MerchantBanks/edit',
			'MerchantBanks ajaxDisplayDecryptedVal' => 'app/actions/MerchantBanks/ajaxDisplayDecryptedVal',
			'MerchantBanks ajaxAddAndEdit' => 'app/actions/MerchantBanks/ajaxAddAndEdit',
		),
		'modules' => array()
	),
	'MerchantUwVolumes' => array(
		'actions' => array(
			'MerchantUwVolumes index' => 'app/actions/MerchantUwVolumes/index',
			'MerchantUwVolumes add' => 'app/actions/MerchantUwVolumes/add',
			'MerchantUwVolumes edit' => 'app/actions/MerchantUwVolumes/edit',
			'MerchantUwVolumes delete' => 'app/actions/MerchantUwVolumes/delete',
			'MerchantUwVolumes ajaxAddAndEdit' => 'app/actions/MerchantUwVolumes/ajaxAddAndEdit',
			'MerchantUwVolumes ajaxDisplayDecryptedVal' => 'app/actions/MerchantUwVolumes/ajaxDisplayDecryptedVal',
		),
		'modules' => array(
		)
	),
	'UwStatusMerchantXreves' => array(
		'actions' => array(
			'UwStatusMerchantXreves delete' => 'app/actions/UwStatusMerchantXreves/delete',
		),
		'modules' => array(
		)
	),
	'MerchantAches' => array(
		'actions' => array(
			'MerchantAches index' => 'app/actions/MerchantAches/index',
			'MerchantAches view' => 'app/actions/MerchantAches/view',
			'MerchantAches add' => 'app/actions/MerchantAches/add',
			'MerchantAches edit' => 'app/actions/MerchantAches/edit',
			'MerchantAches delete' => 'app/actions/MerchantAches/delete',
			'MerchantAches getTaxRateAPI' => 'app/actions/MerchantAches/getTaxRateAPI',
			'MerchantAches Add Invoice Items' => 'app/actions/MerchantAches/renderNewItem',
			'MerchantAches Accounting Report' => 'app/actions/MerchantAches/accounting_report',
			'MerchantAches Update Statuses' => 'app/actions/MerchantAches/updateStatus',
		),
		'modules' => array(
			'MerchantAches hidenS1' => 'app/actions/MerchantAches/view/module/hidenS1'
		)
	),
	'Address' => array(
		'actions' => array(
			'Addresses business_info' => 'app/actions/Addresses/business_info',
			'Addresses business_info_edit' => 'app/actions/Addresses/business_info_edit',
			'Addresses Get Locations' => 'app/actions/Addresses/getLocations',
		),
		'modules' => array(
			'Addresses ssnAndBankEdit' => 'app/actions/Addresses/view/module/ssnAndBankEdit',
		)
	),
	'Merchant' => array(
		'actions' => array(
			// title =>
			'Merchants validate client id' => 'app/actions/Merchants/validate_client_id',
			'Merchants edit' => 'app/actions/Merchants/edit',
			'Merchants upload' => 'app/actions/Merchants/upload',
			'Merchants index' => 'app/actions/Merchants/index',
			'Merchants find' => 'app/actions/Merchants/find',
			'Merchants view' => 'app/actions/Merchants/view',
			'Merchants header' => 'app/actions/Merchants/header',
			'Merchants notes' => 'app/actions/Merchants/notes',
			'Merchants equipment' => 'app/actions/Merchants/equipment',
			'Merchants invoices' => 'app/actions/Merchants/invoices',
			'Merchants add' => 'app/actions/Merchants/add',
			'Merchants delete' => 'app/actions/Merchants/delete',
			'Merchants merchantQuickOpen' => 'app/actions/Merchants/merchantQuickOpen',
			'Merchants autoCompleteSuggestions' => 'app/actions/Merchants/autoCompleteSuggestions',
			'Merchants ajaxDisplayDecryptedVal' => 'app/actions/Merchants/ajaxDisplayDecryptedVal',
			'Merchants ajaxAddAndEdit' => 'app/actions/Merchants/ajaxAddAndEdit',
			'Merchants pci' => 'app/actions/Merchants/pci',
			'Merchants pci_edit' => 'app/actions/Merchants/pci_edit',
			'Merchants rejects' => 'app/actions/Merchants/rejects',
			'API ADD Merchant' => 'app/actions/Merchants/api_add',
			'API GET Merchant' => 'app/actions/Merchants/api_get_merchant',
			'Generate MID numbers' => 'app/actions/Merchants/mid_generator',
		),
		// mandatory modules definition
		'modules' => array(
			'view/partners' => 'app/actions/Merchants/view/module/partners',
			'Merchants refResSctn' => 'app/actions/Merchants/view/module/refResSctn',
			'Merchants ntwrkS' => 'app/actions/Merchants/view/module/ntwrkS',
			'Merchants hidenS1' => 'app/actions/Merchants/view/module/hidenS1',
			'Merchants addGeneralNote' => 'app/actions/Merchants/view/module/addGeneralNote',
			'Merchants Quick Search' => 'app/actions/Merchants/view/module/quickSearch'
		),
	),
	'EquipmentProgramming' => array(
		'actions' => array(
			'Equipment Programming add' => 'app/actions/EquipmentProgrammings/add',
			'Equipment Programming edit' => 'app/actions/EquipmentProgrammings/edit',
		),
		'modules' => array('Equipment Programming editPrgAllowed' => 'app/actions/EquipmentProgrammings/view/module/editPrgAllowed',
			'Equipment Programming readIsAllowedS1' => 'app/actions/EquipmentProgrammings/view/module/readIsAllowedS1')
	),
	'ProductsServicesType' => array(
		'actions' => array(
			'Products Services Type index' => 'app/actions/ProductsServicesTypes/index',
			'Products Services Type view' => 'app/actions/ProductsServicesTypes/view',
			'Products Services Type edit' => 'app/actions/ProductsServicesTypes/edit',
		),
		'modules' => array()
	),
	'Order' => array(
		'actions' => array(
			'Orders merchant_equipment_assigned' => 'app/actions/Orders/merchant_equipment_assigned',
			'Orders index' => 'app/actions/Orders/index',
			'Orders mark_as_paid_order' => 'app/actions/Orders/mark_as_paid_order',
			'Orders delete' => 'app/actions/Orders/delete',
			'Orders add_equipment_invoice' => 'app/actions/Orders/add_equipment_invoice',
			'Orders equipment_invoice' => 'app/actions/Orders/equipment_invoice',
			'Orders edit_equipment_invoice' => 'app/actions/Orders/edit_equipment_invoice'
		),
		'modules' => array(
			'Orders orderDate' => 'app/actions/Orders/view/module/orderDate', //date ordered section hidden for some users
			'Orders repCost' => 'app/actions/Orders/view/module/repCost', //date ordered section hidden for some users
			'Orders trueCost' => 'app/actions/Orders/view/module/trueCost', //date ordered section hidden for some users
			'Orders shipCost' => 'app/actions/Orders/view/module/shipCost', //date ordered section hidden for some users
			'Orders invoices' => 'app/actions/Orders/view/module/invoices' //date ordered section hidden for some users
		)
	),
	'Orderitem' => array(
		'actions' => array(
			'Orderitems edit' => 'app/actions/Orderitems/edit',
			'Orderitems delete' => 'app/actions/Orderitems/delete',
		),
		'modules' => array()
	),
	'RbacTest' => array(
		'actions' => array(
			// title =>
			'RbacTests first, admin and representative' => 'app/actions/RbacTests/first',
			'RbacTests second, admin and referrer' => 'app/actions/RbacTests/second',
			'RbacTests third, admin, manager and reseller' => 'app/actions/RbacTests/third',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'Rbac' => array(
		'actions' => array(
			'Rbac Home' => 'app/actions/Rbac/Perms/index',
			'Security Roles' => 'app/actions/Rbac/Perms/security_roles',
			'Assign Roles' => 'app/actions/Rbac/Perms/assign_roles',
			'Manage Exceptions' => 'app/actions/Rbac/Perms/manage_exceptions',
		),
		'modules' => array()
	),
	'SalesGoal' => array(
		'actions' => array(
			// title =>
			'SalesGoals editMany' => 'app/actions/SalesGoals/editMany',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'UserParameter' => array(
		'actions' => array(
			// title =>
			'UserParameters editMany' => 'app/actions/UserParameters/editMany',
			'UserParameters ajaxView' => 'app/actions/UserParameters/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'UserParameters readModule' => 'app/actions/UserParameters/view/module/readModule',
		)
	),
	'CommissionReport' => array(
		'actions' => array(
			'CommissionReports build' => 'app/actions/CommissionReports/build',
			'CommissionReports report' => 'app/actions/CommissionReports/report',
			'CommissionReports export commission report' => 'app/actions/CommissionReports/exportCommissionReport',
			'CommissionReports export commission multiple' => 'app/actions/CommissionReports/exportCommissionMultiple',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'CommissionPricing' => array(
		'actions' => array(
			'CommissionPricings gross profit report' => 'app/actions/CommissionPricings/grossProfitReport',
			'CommissionPricings gross profit analysis' => 'app/actions/CommissionPricings/gp_analysis',
			'CommissionPricings Commission Multiple analysis' => 'app/actions/CommissionPricings/commission_multiple_analysis',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ResidualReport' => array(
		'actions' => array(
			'ResidualReports upload' => 'app/actions/ResidualReports/upload',
			'ResidualReports report' => 'app/actions/ResidualReports/report',
			'ResidualReports delete' => 'app/actions/ResidualReports/delete',
			'ResidualReports Delete Many Selected' => 'app/actions/ResidualReports/deleteMany',
			'ResidualReports toggleStatus' => 'app/actions/ResidualReports/toggleStatus'
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ResidualVolumeTier' => array(
		'actions' => array(
			'ResidualVolumeTiers editResidualGrid' => 'app/actions/ResidualVolumeTiers/editResidualGrid',
			'ResidualVolumeTiers ajaxView' => 'app/actions/ResidualVolumeTiers/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'ResidualVolumeTiers readModule' => 'app/actions/ResidualVolumeTiers/view/module/readModule',
		)
	),
	'ResidualTimeFactor' => array(
		'actions' => array(
			'ResidualTimeFactors editResidualTimeGrid' => 'app/actions/ResidualTimeFactors/editResidualTimeGrid',
			'ResidualTimeFactors ajaxView' => 'app/actions/ResidualTimeFactors/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'ResidualTimeFactors readModule' => 'app/actions/ResidualTimeFactors/view/module/readModule',
		)
	),
	'AppStatus' => array(
		'actions' => array(
			'AppStatuses editMany' => 'app/actions/AppStatuses/editMany',
			'AppStatuses ajaxView' => 'app/actions/AppStatuses/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'AppStatuses adminModules' => 'app/actions/AppStatuses/view/module/adminModules',
			'AppStatuses readModule' => 'app/actions/AppStatuses/view/module/readModule',
		)
	),
	'AttritionRatio' => array(
		'actions' => array(
			'AttritionRatios editMany' => 'app/actions/AttritionRatios/editMany',
			'AttritionRatios ajaxView' => 'app/actions/AttritionRatios/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'AttritionRatios readModule' => 'app/actions/AttritionRatios/view/module/readModule'
		)
	),
	'GrossProfitReport' => array(
		'actions' => array(
			'GrossProfitReports gross_profit_report' => 'app/actions/GrossProfitReports/gross_profit_report',
			),
		// mandatory modules definition
		'modules' => array()
	),
	'Bet' => array(
		'actions' => array(
			'Bets view' => 'app/actions/Bets/view',
			'Bets editMany' => 'app/actions/Bets/editMany',
			'Bets mass-update users bets' => 'app/actions/Bets/mass_update',
			'Bets Get List Grouped By User (ajax)' => 'app/actions/Bets/getListGroupedByUser'
		),
		// mandatory modules definition
		'modules' => array(
			'Bets readModule' => 'app/actions/Bets/view/module/readModule',
		)
	),
	'EquipmentCost' => array(
		'actions' => array(
			'EquipmentCost editMany' => 'app/actions/EquipmentCosts/editMany',
			'EquipmentCost ajaxView' => 'app/actions/EquipmentCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array(
			'EquipmentCost trueCost' => 'app/actions/EquipmentCosts/view/module/trueCost',
			'EquipmentCost readModule' => 'app/actions/EquipmentCosts/view/module/readModule',
		)
	),
	'EquipmentItem' => array(
		'actions' => array(
			'EquipmentItem add' => 'app/actions/EquipmentItems/add',
			'EquipmentItem edit' => 'app/actions/EquipmentItems/edit',
			'EquipmentItem delete' => 'app/actions/EquipmentItems/delete',
			'EquipmentItem index' => 'app/actions/EquipmentItems/index',
		),
		// mandatory modules definition
		'modules' => array(
			'EquipmentItem trueCost' => 'app/actions/EquipmentItems/view/module/trueCost',
			'EquipmentItem repCost' => 'app/actions/EquipmentItems/view/module/repCost',
		)
	),
	'LastDepositReport' => array(
		'actions' => array(
			'LastDepositReport Upload' => 'app/actions/LastDepositReports/upload',
			'LastDepositReport Report' => 'app/actions/LastDepositReports/index',
			'LastDepositReport Export Report' => 'app/actions/LastDepositReports/exportToCsv',
		),
		'modules' => array(
		)
	),
	'MerchantReject' => array(
		'actions' => array(
			'MerchantReject Upload' => 'app/actions/MerchantRejects/import',
			'MerchantReject Index' => 'app/actions/MerchantRejects/index',
			'MerchantReject Add' => 'app/actions/MerchantRejects/add',
			'MerchantReject Report' => 'app/actions/MerchantRejects/report',
			'MerchantReject Edit' => 'app/actions/MerchantRejects/edit',
			'MerchantReject Edit Row' => 'app/actions/MerchantRejects/editRow',
			'MerchantReject Delete' => 'app/actions/MerchantRejects/delete',
			'MerchantReject Cancel Row' => 'app/actions/MerchantRejects/cancelRow',
		),
		'modules' => array(
			'MerchantReject axLoss' => 'app/actions/MerchantRejects/view/module/axLoss',
			'MerchantReject smLoss' => 'app/actions/MerchantRejects/view/module/smLoss',
			'MerchantReject sm2Loss' => 'app/actions/MerchantRejects/view/module/sm2Loss'
		)
	),
	'MerchantRejectLine' => array(
		'actions' => array(
			'MerchantRejectLine Add' => 'app/actions/MerchantRejectLines/add',
			'MerchantRejectLine Edit Row' => 'app/actions/MerchantRejectLines/editRow',
			'MerchantRejectLine Delete' => 'app/actions/MerchantRejectLines/delete',
			'MerchantRejectLine Cancel Row' => 'app/actions/MerchantRejectLines/cancelRow',
		),
		'modules' => array(
		)
	),
	'AchRepCost' => array(
		'actions' => array(
			'AchRepCosts editMany' => 'app/actions/AchRepCosts/editMany',
			'AchRepCosts ajaxView' => 'app/actions/AchRepCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'RepMonthlyCost' => array(
		'actions' => array(
			'RepMonthlyCosts editMany' => 'app/actions/RepMonthlyCosts/editMany',
			'RepMonthlyCosts ajaxView' => 'app/actions/RepMonthlyCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'WebAchRepCost' => array(
		'actions' => array(
			'WebAchRepCosts edit' => 'app/actions/WebAchRepCosts/edit',
			'WebAchRepCosts ajaxView' => 'app/actions/WebAchRepCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'PaymentFusionRepCost' => array(
		'actions' => array(
			'PaymentFusionRepCosts edit' => 'app/actions/PaymentFusionRepCosts/edit',
			'PaymentFusionRepCosts ajaxView' => 'app/actions/PaymentFusionRepCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'PaymentFusion' => array(
		'actions' => array(
			'PaymentFusions edit' => 'app/actions/PaymentFusions/edit',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'AddlAmexRepCost' => array(
		'actions' => array(
			'AddlAmexRepCosts edit' => 'app/actions/AddlAmexRepCosts/edit',
			'AddlAmexRepCosts ajaxView' => 'app/actions/AddlAmexRepCosts/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'GatewayCostStructure' => array(
		'actions' => array(
			'GatewayCostStructures edit' => 'app/actions/GatewayCostStructures/editMany',
			'GatewayCostStructures ajaxView' => 'app/actions/GatewayCostStructures/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'SystemTransaction' => array(
		'actions' => array(
			'SystemTransactions userActivity' => 'app/actions/SystemTransactions/userActivity',
		),
		'modules' => array(
		),
	),
	'Gateway1' => array(
		'actions' => array(
			'Gateway1s edit' => 'app/actions/Gateway1s/edit',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'WebBasedAch' => array(
		'actions' => array(
			'WebBasedAches edit' => 'app/actions/WebBasedAches/edit',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ProductSetting' => array(
		'actions' => array(
			'ProductSettings edit' => 'app/actions/ProductSettings/edit',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'RepProductSetting' => array(
		'actions' => array(
			'RepProductSettings edit' => 'app/actions/RepProductSettings/editMany',
			'RepProductSettings ajaxView' => 'app/actions/RepProductSettings/ajaxView',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'MultipurposeReport' => array(
		'actions' => array(
			'MultipurposeReports report' => 'app/actions/MultipurposeReports/report'
		),
		// mandatory modules definition
		'modules' => array()
	),
	'MaintenanceDashboard' => array(
		'actions' => array(
			'MaintenanceDashboards Main Menu' => 'app/actions/MaintenanceDashboards/main_menu',
			'MaintenanceDashboards edit' => 'app/actions/MaintenanceDashboards/edit',
			'MaintenanceDashboards content' => 'app/actions/MaintenanceDashboards/content',
			'MaintenanceDashboards delete' => 'app/actions/MaintenanceDashboards/delete',
			'MaintenanceDashboards undoDelete' => 'app/actions/MaintenanceDashboards/undoDelete',
			'MaintenanceDashboards generateApiDoc' => 'app/actions/MaintenanceDashboards/generateApiDoc',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'DataBreachBillingReport' => array(
		'actions' => array(
			'DataBreachBillingReports report' => 'app/actions/DataBreachBillingReports/report',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'Adjustment' => array(
		'actions' => array(
			'Adjustments index' => 'app/actions/Adjustments/index',
			'Adjustments add' => 'app/actions/Adjustments/add',
			'Adjustments edit' => 'app/actions/Adjustments/edit',
			'Adjustments delete' => 'app/actions/Adjustments/delete',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ProductsAndServices' => array(
		'actions' => array(
			'ProductsAndServices Merchant Products Report' => 'app/actions/ProductsAndServices/merchant_products_report',
			'ProductsAndServices Add One Product' => 'app/actions/ProductsAndServices/add',
			'ProductsAndServices Add Many Products' => 'app/actions/ProductsAndServices/addMany',
			'ProductsAndServices Delete One Product' => 'app/actions/ProductsAndServices/delete',
			'ProductsAndServices Delete Many Products' => 'app/actions/ProductsAndServices/deleteMany',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'Documentation' => array(
		'actions' => array(
			'Documentations Help' => 'app/actions/Documentations/help',
		),
		// mandatory modules definition
		'modules' => array(
			'Documentations Admin Only Help Module' => 'app/actions/Documentations/view/module/adminOnlyModule'
		)
	),
	'Organization' => array(
		'actions' => array(
			'Organizations get regions' => 'app/actions/Organizations/getRegions',
			'Organizations Get Sub Regions' => 'app/actions/Organizations/getSubregionsByRegion',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ProfitabilityReport' => array(
		'actions' => array(
			'Import new data' => 'app/actions/ProfitabilityReports/import',
			'Delete report data' => 'app/actions/ProfitabilityReports/delete',
			'View report' => 'app/actions/ProfitabilityReports/report'
		),
		// mandatory modules definition
		'modules' => array()
	),
	'BackgroundJob' => array(
		'actions' => array(
			'Update StatusList Ajax Functionality' => 'app/actions/BackgroundJobs/updateList',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ProfitProjection' => array(
		'actions' => array(
			'Update Profit Projections' => 'app/actions/ProfitProjections/updateProjections',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'InvoiceItems' => array(
		'actions' => array(
			'Delete Invoice Item' => 'app/actions/InvoiceItems/delete',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'Ach' => array(
		'actions' => array(
			'Update ACH Product' => 'app/actions/Aches/edit',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ApiConfiguration' => array(
		'actions' => array(
			'View all Api Connections' => 'app/actions/ApiConfigurations/index',
			'Edit Api Connections' => 'app/actions/ApiConfigurations/edit',
			'Add Api Connections' => 'app/actions/ApiConfigurations/add',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'ImportedDataCollection' => array(
		'actions' => array(
			'Upload data' => 'app/actions/ImportedDataCollections/upload',
			'View Report' => 'app/actions/ImportedDataCollections/report',
			'Ajax render report' => 'app/actions/ImportedDataCollections/reportAjaxRederer',
			'Ajax Check if data exists' => 'app/actions/ImportedDataCollections/checkDataExists',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'Client' => array(
		'actions' => array(
			'Ajax search similar' => 'app/actions/Clients/searchSimilar',
		),
		// mandatory modules definition
		'modules' => array()
	),
	'AssociatedExternalRecord' => array(
		'actions' => array(
			'Update or create record' => 'app/actions/AssociatedExternalRecords/upsert',
			'Delete All records' => 'app/actions/AssociatedExternalRecords/deleteAll',
		),
		// mandatory modules definition
		'modules' => array()
	),
);

return $modules;
