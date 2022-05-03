<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('System Transactions'); ?> List" />
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
			<th><?php echo $this->Paginator->sort('system_transaction_id'); ?></th>
			<th><?php echo $this->Paginator->sort('transaction_type'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('session_id'); ?></th>
			<th><?php echo $this->Paginator->sort('client_address'); ?></th>
			<th><?php echo $this->Paginator->sort('system_transaction_date'); ?></th>
			<th><?php echo $this->Paginator->sort('system_transaction_time'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_note_id'); ?></th>
			<th><?php echo $this->Paginator->sort('change_id'); ?></th>
			<th><?php echo $this->Paginator->sort('ach_seq_number'); ?></th>
			<th><?php echo $this->Paginator->sort('order_id'); ?></th>
			<th><?php echo $this->Paginator->sort('programming_id'); ?></th>

		</tr>
		<?php foreach ($systemTransactions as $systemTransaction): ?>
			<tr>
				<td><?php echo h($systemTransaction['SystemTransaction']['id']); ?>&nbsp;</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['system_transaction_id']); ?>&nbsp;</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['transaction_type']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['User']['id'], array('controller' => 'users', 'action' => 'view', $systemTransaction['User']['id'])); ?>
				</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $systemTransaction['Merchant']['id'])); ?>
				</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['session_id']); ?>&nbsp;</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['client_address']); ?>&nbsp;</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['system_transaction_date']); ?>&nbsp;</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['system_transaction_time']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['MerchantNote']['id'], array('controller' => 'merchant_notes', 'action' => 'view', $systemTransaction['MerchantNote']['id'])); ?>
				</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['MerchantChange']['id'], array('controller' => 'merchant_changes', 'action' => 'view', $systemTransaction['MerchantChange']['id'])); ?>
				</td>
				<td><?php echo h($systemTransaction['SystemTransaction']['ach_seq_number']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['Orderitems']['id'], array('controller' => 'orderitems', 'action' => 'view', $systemTransaction['Orderitems']['id'])); ?>
				</td>
				<td>
					<?php echo $this->Html->link($systemTransaction['EquipmentProgramming']['id'], array('controller' => 'equipment_programmings', 'action' => 'view', $systemTransaction['EquipmentProgramming']['id'])); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>