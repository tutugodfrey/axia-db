<?php
$inputIndex = 0;
	$tableCells = array();
	foreach ($betNetworks as $networkId => $networkName) {
		$networkBet = array();
		//get the data from the request if there is a validation error
		if ($this->request->is('post') || $this->request->is('put')) {
			$networkBet = Hash::extract($this->request->data, "Bet.{n}[bet_network_id={$networkId}]");
		} elseif($this->action !== 'mass_update') {
			$networkBet = Hash::extract($bets, "{n}.Bet[bet_network_id={$networkId}]");
		}

		$row = array();
		//first column with the name and hidden fields
		$cellContent = h($networkName);
		//update if there is a previously saved bet
		$betId = Hash::get($networkBet, '0.id');
		if (!empty($betId)) {
			$options = array(
				'type' => 'hidden',
				'value' => $betId,
			);
			$cellContent .= $this->Form->input("{$inputIndex}.id", $options);
		}

		$options = array(
			'type' => 'hidden',
			'value' => $networkId,
		);
		$cellContent .= $this->Form->input("{$inputIndex}.bet_network_id", $options);
		//These fields are not needed when performing a mass update action
		if ($this->action !== 'mass_update') {
			$options = array(
				'type' => 'hidden',
				'value' => $betTableId,
			);
			$cellContent .= $this->Form->input("{$inputIndex}.bet_table_id", $options);

			$options = array(
				'type' => 'hidden',
				'value' => $cardTypeId,
			);
			$cellContent .= $this->Form->input("{$inputIndex}.card_type_id", $options);

			$options = array(
				'type' => 'hidden',
				'value' => $compensationId,
			);
			$cellContent .= $this->Form->input("{$inputIndex}.user_compensation_profile_id", $options);
		}
		$row[] = $cellContent;

		//rest of the columns with the field inputs to edit
		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.pct_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.pct_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.pi_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.pi_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.additional_pct'),
		);
		$row[] = $this->Form->input("{$inputIndex}.additional_pct", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.sales_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.sales_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.dial_sales_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.dial_sales_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.non_dial_sales_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.non_dial_sales_cost", $options);


		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.auth_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.auth_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.dial_auth_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.dial_auth_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.non_dial_auth_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.non_dial_auth_cost", $options);

		$options = array(
			'label' => false,
			'value' => Hash::get($networkBet, '0.settlement_cost'),
		);
		$row[] = $this->Form->input("{$inputIndex}.settlement_cost", $options);

		$tableCells[] = $row;
		$inputIndex++;
	}
	echo $this->Html->tableCells($tableCells);
