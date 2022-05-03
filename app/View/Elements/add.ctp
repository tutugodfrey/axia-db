
<input type="hidden" id="thisViewTitle" value="<?php echo __('Add Partner'); ?>" />
<?php echo $this->Form->create('NewPartner', array('action' => 'add', 'url' => array('controller' => 'partners', 'action' => 'add'))); ?>
<fieldset>
	<legend><?php echo __('Add Partner'); ?></legend>
	<?php
	echo $this->Form->input('partner_id');
	echo $this->Form->input('partner_name');
	echo $this->Form->input('active');
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

