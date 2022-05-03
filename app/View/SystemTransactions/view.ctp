
<input type="hidden" id="thisViewTitle" value="<?php echo __('View System Transaction'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit System Transaction'), array('action' => 'edit', $systemTransaction['SystemTransaction']['id'])); ?> </li>

	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('System Transaction Id'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['system_transaction_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Transaction Type'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['transaction_type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['User']['id'], array('controller' => 'users', 'action' => 'view', $systemTransaction['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $systemTransaction['Merchant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Session Id'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['session_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client Address'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['client_address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('System Transaction Date'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['system_transaction_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('System Transaction Time'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['system_transaction_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant Note'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['MerchantNote']['id'], array('controller' => 'merchant_notes', 'action' => 'view', $systemTransaction['MerchantNote']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant Change'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['MerchantChange']['id'], array('controller' => 'merchant_changes', 'action' => 'view', $systemTransaction['MerchantChange']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ach Seq Number'); ?></dt>
		<dd>
			<?php echo h($systemTransaction['SystemTransaction']['ach_seq_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Orderitems'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['Orderitems']['id'], array('controller' => 'orderitems', 'action' => 'view', $systemTransaction['Orderitems']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Equipment Programming'); ?></dt>
		<dd>
			<?php echo $this->Html->link($systemTransaction['EquipmentProgramming']['id'], array('controller' => 'equipment_programmings', 'action' => 'view', $systemTransaction['EquipmentProgramming']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>


