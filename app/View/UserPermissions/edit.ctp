
<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit User Permission'); ?>" />
<?php echo $this->Form->create('UserPermission'); ?>
<fieldset>
	<legend><?php echo __('Edit User Permission'); ?></legend>
	<?php
	echo $this->Form->input('id');
	echo $this->Form->input('user_id');
	echo $this->Form->input('permission_id');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

