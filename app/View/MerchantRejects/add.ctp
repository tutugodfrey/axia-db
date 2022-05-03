
<input type="hidden" id="thisViewTitle" value="<?php echo __('Add Merchant Reject'); ?>" />
<?php echo $this->Form->create('MerchantReject'); ?>
<fieldset>
	<legend><?php echo __('Add Merchant Reject'); ?></legend>
	<?php
	echo $this->Form->input('merchant_id');
	echo $this->Form->input('trace');
	echo $this->Form->input('reject_date');
	echo $this->Form->input('typeid');
	echo $this->Form->input('code');
	echo $this->Form->input('amount');
	echo $this->Form->input('recurrance_id');
	echo $this->Form->input('open');
	echo $this->Form->input('loss_axia');
	echo $this->Form->input('loss_mgr1');
	echo $this->Form->input('loss_mgr2');
	echo $this->Form->input('loss_rep');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

