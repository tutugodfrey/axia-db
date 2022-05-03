
<input type="hidden" id="thisViewTitle" value="<?php echo __('Add User Compensation Profile'); ?>" />
<?php echo $this->Form->create('UserCompensationProfile'); ?>
	<fieldset>
		<legend><?php echo __('Add User Compensation Profile'); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('partner_user_id');
		echo $this->Form->input('is_partner_rep');
		echo $this->Form->input('is_default');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

