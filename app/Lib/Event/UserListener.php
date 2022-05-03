<?php

App::uses('CakeEventListener', 'Event');

class UserListener extends CakeObject implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Auth.afterIdentify' => 'updateLastLogin',
			'Auth.afterIdentify' => 'createTransaction',
		);
	}

/**
 * Update user and session last login date
 *
 * Required values from $event->data:
 *	- string 'user.id': User id
 *
 * @param object $event Event
 * @throws InvalidArgumentException
 * @return boolean
 */
	public function updateLastLogin($event) {
		$this->User = ClassRegistry::init('User');
		$userData = Hash::get($event->data, 'user');
		if (empty($userData['id'])) {
			throw new InvalidArgumentException(__('Invalid user id'));
		}

		$userData['last_login_date'] = date('Y-m-d H:i:s');
		if ($this->User->save($userData, true, array('id', 'last_login_date'))) {
			// Update session user data
			$event->subject->Session->write(AuthComponent::$sessionKey, $userData);
			return true;
		}
		return false;
	}

/**
 * Create a system transaction
 *
 * The $event->subject will be the model that has been updated
 *
 * @param object $event Event
 * @return void
 * @throws RuntimeException when the transaction can not be saved to the database
 * @throws InvalidArgumentException
 * @todo: implement logic for all transaction types
 */
	public function createTransaction($event) {
		$this->SystemTransaction = ClassRegistry::init('SystemTransaction');
		$userId = Hash::get($event->data, 'user.id');
		if (empty($userId)) {
			throw new InvalidArgumentException(__('Invalid user id'));
		}
		$transaction = array(
			'transaction_type_id' => TransactionType::USER_LOGIN,
			'system_transaction_date' => date('Y-m-d'),
			'system_transaction_time' => date('H:i:s'),
			'user_id' => $userId,
			'login_date' => date('Y-m-d H:i:s'),
			'client_address' => Router::getRequest()->clientIp()
		);

		$this->SystemTransaction->create();
		if (!$this->SystemTransaction->save(array('SystemTransaction' => $transaction))) {
			$this->log('Error creating system transaction');
			$this->log($transaction);
			$this->log($this->SystemTransaction->validationErrors);
			throw new RuntimeException(__('Unable to create system transaction'));
		}
	}
}
