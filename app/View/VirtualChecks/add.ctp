
<input type="hidden" id="thisViewTitle" value="<?php echo __('Add Virtual Check'); ?>" />
<?php echo $this->Form->create('VirtualCheck'); ?>
<fieldset>
	<legend><?php echo __('Add Virtual Check'); ?></legend>
	<?php
	echo $this->Form->input('merchant_id');
	echo $this->Form->input('vc_mid');
	echo $this->Form->input('vc_web_based_rate');
	echo $this->Form->input('vc_web_based_pi');
	echo $this->Form->input('vc_monthly_fee');
	echo $this->Form->input('vc_gateway_fee');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

