<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchant Aches'); ?> List" />
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('ach_seq_number'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('date_submitted'); ?></th>
			<th><?php echo $this->Paginator->sort('date_completed'); ?></th>
			<th><?php echo $this->Paginator->sort('reason'); ?></th>
			<th><?php echo $this->Paginator->sort('credit_amount'); ?></th>
			<th><?php echo $this->Paginator->sort('debit_amount'); ?></th>
			<th><?php echo $this->Paginator->sort('reason_other'); ?></th>
			<th><?php echo $this->Paginator->sort('status'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('invoice_number'); ?></th>
			<th><?php echo $this->Paginator->sort('app_status_id'); ?></th>
			<th><?php echo $this->Paginator->sort('billing_option_id'); ?></th>
			<th><?php echo $this->Paginator->sort('commission_month'); ?></th>
			<th><?php echo $this->Paginator->sort('tax'); ?></th>

		</tr>
		<?php foreach ($merchantAches as $merchantAch): ?>
			<tr>
				<td><?php echo h($merchantAch['MerchantAch']['id']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['ach_seq_number']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($merchantAch['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantAch['Merchant']['id'])); ?>
				</td>
				<td><?php echo h($merchantAch['MerchantAch']['date_submitted']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['date_completed']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['reason']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['credit_amount']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['debit_amount']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['reason_other']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['status']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($merchantAch['User']['id'], array('controller' => 'users', 'action' => 'view', $merchantAch['User']['id'])); ?>
				</td>
				<td><?php echo h($merchantAch['MerchantAch']['invoice_number']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($merchantAch['MerchantAchAppStatus']['id'], array('controller' => 'merchant_ach_app_statuses', 'action' => 'view', $merchantAch['MerchantAchAppStatus']['id'])); ?>
				</td>
				<td>
					<?php echo $this->Html->link($merchantAch['MerchantAchBillingOption']['id'], array('controller' => 'merchant_ach_billing_options', 'action' => 'view', $merchantAch['MerchantAchBillingOption']['id'])); ?>
				</td>
				<td><?php echo h($merchantAch['MerchantAch']['commission_month']); ?>&nbsp;</td>
				<td><?php echo h($merchantAch['MerchantAch']['tax']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>