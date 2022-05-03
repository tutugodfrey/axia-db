
<?php echo $this->Form->create($this->name); ?>
<fieldset>
	<legend><?php echo __('Add Equipmentssss Item'); ?></legend>
	<?php
	echo $this->Form->hidden('equipment_item');
	echo $this->Form->hidden('equipment_type');
	echo $this->Form->input('equipment_item_description', array('label' => 'Description'));
	echo $this->Form->input('equipment_item_true_price', array('label' => 'True Cost'));
	echo $this->Form->input('equipment_item_rep_price', array('label' => 'Rep Cost'));
	/* 'active' field defaults to 1 at the database level */
	/* 'warranty' field is not being used */
	?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
