<?php
App::uses('ClassRegistry', 'Utility');
App::uses('UserListener', 'Lib/Event');
App::uses('EmailAlert', 'Lib/Event');
App::uses('SystemTransactionListener', 'Lib/Event');
App::uses('WomplyReportHandler', 'Lib/Event');

// Global events
CakeEventManager::instance()->attach(new UserListener());

/**
 * To attach an event listener to a model, add it to the 'ModelEventListeners' configuration.
 * The AppModel method '_attachListeners' will attach them in the model's constructor.
 *
 * This will make it easy to attach/detach mocked listeners for unit tests
 */
Configure::write('ModelEventListeners', [
	'User' => [
		'EmailAlert' => new EmailAlert(),
	],
	'MerchantChange' => [
		'WomplyReportHandler' => new WomplyReportHandler(),
	],
	'Womply' => [
		'EmailAlert' => new EmailAlert(),
	],
	'MerchantUw' => [
		'EmailAlert' => new EmailAlert(),
	],
	'UserCompensationProfile' => [
		'EmailAlert' => new EmailAlert(),
	],
	'MerchantPricingArchive' => [
		'EmailAlert' => new EmailAlert(),
	],
	'ResidualReport' => [
		'EmailAlert' => new EmailAlert(),
	],
	'ProfitabilityReport' => [
		'EmailAlert' => new EmailAlert(),
	],
	'CommissionReport' => [
		'EmailAlert' => new EmailAlert(),
	],
	'MerchantAch' => [
		'SystemTransactionListener' => new SystemTransactionListener(),
	],
	'EquipmentProgramming' => [
		'SystemTransactionListener' => new SystemTransactionListener(),
	],
	'Order' => [
		'SystemTransactionListener' => new SystemTransactionListener(),
	],
	'MerchantNote' => [
		'EmailAlert' => new EmailAlert(),
		'SystemTransactionListener' => new SystemTransactionListener(),
	],
]);
