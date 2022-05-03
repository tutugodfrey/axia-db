
<input type="hidden" id="thisViewTitle" value="<?php echo __('Add Merchant Reference'); ?>" />
<?php echo $this->Form->create('MerchantReference'); ?>
<fieldset>
	<legend><?php echo __('Add Merchant Reference'); ?></legend>
	<?php
	echo $this->Form->input('merchant_ref_seq_number');
	echo $this->Form->input('merchant_id');
	echo $this->Form->input('merchant_ref_type');
	echo $this->Form->input('bank_name');
	echo $this->Form->input('person_name');
	echo $this->Form->input('phone');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

