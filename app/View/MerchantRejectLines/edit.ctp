
<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit Merchant Reject Line'); ?>" />
<?php echo $this->Form->create('MerchantRejectLine'); ?>
<fieldset>
	<legend><?php echo __('Edit Merchant Reject Line'); ?></legend>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('rejectid');
	echo $this->Form->input('fee');
	echo $this->Form->input('statusid');
	echo $this->Form->input('status_date');
	echo $this->Form->input('notes');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

