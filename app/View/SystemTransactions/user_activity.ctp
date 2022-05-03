<?php
$this->Html->addCrumb(__('User activity'), array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));
?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('User activity'); ?>" />

<?php echo $this->element('SystemTransactions/filter_form'); ?>

<div class="row">
	<div class="col-xs-12">
		<?php
		$headers = array(
			$this->Paginator->sort('user_fullname', __('Name')),
			$this->Paginator->sort('User.username', __('Username')),
			$this->Paginator->sort('SystemTransaction.login_date', __('Login date')),
			$this->Paginator->sort('SystemTransaction.system_transaction_date', __('Transaction date')),
			$this->Paginator->sort('SystemTransaction.client_address', __('Logged in from')),
			__('Changes & Notes / DBA'),
		);

		$cells = array();
		foreach ($systemTransactions as $transaction) {
			$descriptionCellContent = null;
			$description = h(Hash::get($transaction, 'TransactionType.transaction_type_description'));
			if (!empty($description)) {
				$url = null;
				switch (Hash::get($transaction, 'SystemTransaction.transaction_type_id')) {
					case TransactionType::MERCHANT_NOTE:
						$url = array(
							'plugin' => false,
							'controller' => 'merchant_notes',
							'action' => 'edit',
							Hash::get($transaction, 'SystemTransaction.merchant_note_id'),
						);
						break;

					case TransactionType::CHANGE_REQUEST:
						$url = array(
							'plugin' => false,
							'controller' => 'merchant_notes',
							'action' => 'edit',
							Hash::get($transaction, 'MerchantChange.merchant_note_id'),
						);
						break;

					case TransactionType::ACH_ENTRY:
						$url = array(
							'plugin' => false,
							'controller' => 'merchant_aches',
							'action' => 'edit',
							Hash::get($transaction, 'SystemTransaction.merchant_ach_id'),
						);
						$description = __('Axia Invoice: %s', $description);
						break;

					case TransactionType::EQUIPMENT_ORDER:
						$url = array(
							'plugin' => false,
							'controller' => 'orders',
							'action' => 'equipment_invoice',
							Hash::get($transaction, 'SystemTransaction.order_id'),
						);
						$description = __('Invoice #%s', $description);
						break;

					case TransactionType::PROGRAMMING_CHANGE:
						$url = array(
							'plugin' => false,
							'controller' => 'equipment_programmings',
							'action' => 'edit',
							Hash::get($transaction, 'SystemTransaction.programming_id'),
						);
						$description = __('Programming Term #%s', $description);
						break;
				}
				$descriptionCellContent = empty($url) ? $description : $this->Html->link($description, $url, ['escape' => false]);
			}

			if (!empty($transaction['SystemTransaction']['merchant_id'])) {
				$descriptionCellContent .= '&nbsp;/&nbsp;' . $this->Html->link(Hash::get($transaction, 'Merchant.merchant_dba'), array(
					'plugin' => false,
					'controller' => 'merchants',
					'action' => 'view',
					Hash::get($transaction, 'SystemTransaction.merchant_id'),
				));
			}

			$cells[] = array(
				$this->Html->link(Hash::get($transaction, 'SystemTransaction.user_fullname'), array(
					'plugin' => false,
					'controller' => 'users',
					'action' => 'view',
					Hash::get($transaction, 'SystemTransaction.user_id'),
				)),
				h(Hash::get($transaction, 'User.username')),
				$this->Time->datetime(Hash::get($transaction, 'SystemTransaction.login_date')),
				$this->Time->date(Hash::get($transaction, 'SystemTransaction.system_transaction_date')) . ' ' . $this->Time->time(Hash::get($transaction, 'SystemTransaction.system_transaction_time')),
				Hash::get($transaction, 'SystemTransaction.client_address'),
				array($descriptionCellContent, array('class' => 'transaction-description')),
			);
		}
		?>

		<?php echo $this->element('pagination'); ?>
		<table class="table table-hover table-striped table-condensed"  id="table-user-activity">
			<?php echo $this->Html->tableHeaders($headers); ?>
			<?php echo $this->Html->tableCells($cells); ?>
		</table>
		<?php echo $this->Html->showPaginator(count($systemTransactions)); ?>
	</div>
</div>
