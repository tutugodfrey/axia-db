<div class="col-md-12">
	<?php
	if($this->Rbac->isPermitted('Bets/editMany')){
		$editUrl = Router::url(array(
			'plugin' => false,
			'controller' => 'bets',
			'action' => 'editMany',
			h(Hash::get($cardType, 'CardType.id')),
			h(Hash::get($betTable, 'BetTable.id')),
			h(Hash::get($user, 'User.id')),
			h(Hash::get($userCompensation, 'UserCompensationProfile.id')),
			$partnerUserId
		));
		echo $this->Html->link(
				'<strong>Edit ' . h(Hash::get($betTable, 'BetTable.name')) . ' </strong>' . $this->Html->editIcon('', array('title' => __('Edit this BET'))),
				$editUrl,
				array('target' => '_blank', 'escape' => false)
			);
	}
	?>
</div>
<table class="table">
	<?php
	echo $this->element('Bets/table_headers');
	$tableCells = array();
	foreach ($betNetworks as $networkId => $networkName) {
		$networkBet = Hash::extract($bets, "{n}.Bet[bet_network_id={$networkId}]");
		$row = array(
			h($networkName),
			$this->Html->formatToPercentage(Hash::get($networkBet, '0.pct_cost'), 4),
			$this->Number->currency(Hash::get($networkBet, '0.pi_cost'), 'USD3dec'),
			$this->Html->formatToPercentage(Hash::get($networkBet, '0.additional_pct'), 4),
			$this->Number->currency(Hash::get($networkBet, '0.sales_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.dial_sales_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.non_dial_sales_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.auth_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.dial_auth_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.non_dial_auth_cost'), 'USD3dec'),
			$this->Number->currency(Hash::get($networkBet, '0.settlement_cost'), 'USD3dec'),
		);
		$tableCells[] = $row;
	}
	echo $this->Html->tableCells($tableCells);
	?>
</table>
