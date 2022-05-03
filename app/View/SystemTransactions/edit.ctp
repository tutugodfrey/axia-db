
<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit System Transaction'); ?>" />
<?php echo $this->Form->create('SystemTransaction'); ?>
<fieldset>
	<legend><?php echo __('Edit System Transaction'); ?></legend>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('system_transaction_id');
	echo $this->Form->input('transaction_type');
	echo $this->Form->input('user_id');
	echo $this->Form->input('merchant_id');
	echo $this->Form->input('session_id');
	echo $this->Form->input('client_address');
	echo $this->Form->input('system_transaction_date');
	echo $this->Form->input('system_transaction_time');
	echo $this->Form->input('merchant_note_id');
	echo $this->Form->input('change_id');
	echo $this->Form->input('ach_seq_number');
	echo $this->Form->input('order_id');
	echo $this->Form->input('programming_id');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

