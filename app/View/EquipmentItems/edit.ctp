<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit Equipment Item'); ?>" />
<?php 
echo $this->Form->create('EquipmentItem', 
		array(
			'inputDefaults' => array(
					'wrapInput' => 'col col-md-8'
				),
			'class' => 'form-inline'
		)
	); 
echo $this->Form->hidden('id');
echo $this->Element('EquipmentItems/FormFields');
echo $this->Form->defaultButtons(null, ['action' => 'index']);
echo $this->Form->end();
?>

