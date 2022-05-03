<?php
App::uses('AppShell', 'Console/Command');

class BetShell extends AppShell {

	public function main() {
		$Bet = ClassRegistry::init('Bet');
		$Bet->bindBetTableAndCardType();
		$userTotal = $Bet->UserCompensationProfile->User->find('count');

		$counter = 0;
		$increment = 5;

		$this->out($userTotal . ' Users');

		$networkIds = $Bet->Network->find('list', array(
			'fields' => array(
				'id'
			)
		));

		$betTablesCardTypeIds = $Bet->BetTablesCardType->find('list', array(
			'fields' => array(
				'id'
			)
		));

		do {
			$users = $Bet->UserCompensationProfile->User->find('all', array(
				'offset' => $counter,
				'limit' => $increment,
				'fields' => array(
					'User.id'
				)
			));

			$userIds = Set::extract('/User/id', $users);

			$Bet->synchronizeNetworksAndBetTables($userIds, $networkIds, $betTablesCardTypeIds);

			$counter = $counter + $increment;
			$this->out($counter);
		} while ($counter < $userTotal);
	}

}