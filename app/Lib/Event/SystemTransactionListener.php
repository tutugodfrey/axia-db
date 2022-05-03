<?php

App::uses('CakeEventListener', 'Event');
App::uses('AuthComponent', 'Controller/Component');
App::uses('Router', 'Routing');

class SystemTransactionListener extends CakeObject implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Model.afterSave' => 'createTransaction',
		);
	}

/**
 * Utility function to get the user id (mocked in unit tests)
 *
 * @param string $key key to retrieve from current logged in user
 * @return string
 */
	protected function _getCurrentUser($key = null) {
		return AuthComponent::user($key);
	}

/**
 * Utility function to get the client Ip (mocked in unit tests)
 *
 * @return string
 */
	protected function _getClientIp() {
		return Router::getRequest()->clientIp(false);
	}

/**
 * Create a system transaction
 *
 * The $event->subject will be the model that has been updated
 *
 * @param object $event Event
 * @return bool
 * @throws RuntimeException when the transaction can not be saved to the database
 * @throws InvalidArgumentException
 * @todo: implement logic for all transaction types
 */
	public function createTransaction($event) {
		$this->SystemTransaction = ClassRegistry::init('SystemTransaction');

		$user = $this->_getCurrentUser();
		if (empty($user)) {
			throw new InvalidArgumentException(__('There is no user logged in'));
		}

		$transaction = Hash::merge($this->_getTransactionTypeData($event), array(
			'system_transaction_date' => date('Y-m-d'),
			'system_transaction_time' => date('H:i:s'),
			'user_id' => Hash::get($user, 'id'),
			'login_date' => Hash::get($user, 'last_login_date'),
			'client_address' => $this->_getClientIp(),
		));

		$this->SystemTransaction->create();
		if (!$this->SystemTransaction->save(array('SystemTransaction' => $transaction))) {
			$this->log('Error creating system transaction');
			$this->log($transaction);
			$this->log($this->SystemTransaction->validationErrors);
			throw new RuntimeException(__('Unable to create system transaction'));
		}
	}

/**
 * Process the event data to know the transaction type and where to the the related data
 * The logic from this method is based on the data passed from the Model.afterSave event
 *
 * @param object $event CakeEvent
 * @throws InvalidArgumentException when the model or the event name is not valid
 * @return array
 */
	protected function _getTransactionTypeData($event) {
		if ($event->name() !== 'Model.afterSave') {
			throw new InvalidArgumentException(__('Event invalid or not implemented yet'));
		}

		$typeSettings = array(
			'MerchantNote' => array(
				'relatedField' => 'merchant_note_id',
				'transactionType' => TransactionType::MERCHANT_NOTE,
			),
			'Order' => array(
				'relatedField' => 'order_id',
				'transactionType' => TransactionType::EQUIPMENT_ORDER,
			),
			'EquipmentProgramming' => array(
				'relatedField' => 'programming_id',
				'transactionType' => TransactionType::PROGRAMMING_CHANGE,
			),
			'MerchantAch' => array(
				'relatedField' => 'merchant_ach_id',
				'transactionType' => TransactionType::ACH_ENTRY,
			),
		);

		$class = null;
		if ($event->subject instanceof MerchantNote) {
			$class = 'MerchantNote';
		} elseif ($event->subject instanceof MerchantAch) {
			$class = 'MerchantAch';
		} elseif ($event->subject instanceof EquipmentProgramming) {
			$class = 'EquipmentProgramming';
		} elseif ($event->subject instanceof Order) {
			$class = 'Order';
		} else {
			throw new InvalidArgumentException(__('Transaction type invalid or not implemented yet'));
		}

		$typeId = TransactionType::RECORD_UPDATED;
		$created = Hash::get($event->data, '0');
		if ($created) {
			if ($class === 'MerchantNote') {
				$noteType = Hash::get($event->subject->data, "{$event->subject->alias}.note_type_id");
				if ($noteType !== MerchantNote::TYPE_CHANGE_REQUEST) {
					$typeId = Hash::get($typeSettings, "{$class}.transactionType");
				}
			} else {
				$typeId = Hash::get($typeSettings, "{$class}.transactionType");
			}
		}

		return array(
			'transaction_type_id' => $typeId,
			'merchant_id' => Hash::get($event->subject->data, "{$event->subject->alias}.merchant_id"),
			Hash::get($typeSettings, "{$class}.relatedField") => Hash::get($event->subject->data, "{$event->subject->alias}.id"),
		);
	}
}
