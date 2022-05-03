<div class='row'>
<?php
$isFullFormAllowed = $this->Rbac->isPermitted('app/actions/EquipmentProgrammings/view/module/editPrgAllowed', true);
 if ($isFullFormAllowed) {
	echo $this->Form->input('terminal_number', array('label' => 'Term #:')); 
	echo $this->Form->input('network', array('label' => 'Network', 'value' => 'TSYS:')); 
	echo $this->Form->input('hardware_serial', array('label' => 'TID:')); 
	echo $this->Form->input('terminal_type', array('label' => 'Term/POS Type:')); 
	echo $this->Form->input('version', array('label' => 'Version:')); 
	echo $this->Form->input('app_type', array('label' => 'App ID:')); 
	echo $this->Form->input('provider', array('label' => 'Provider:'));
	echo $this->Form->input('serial_number', array('label' => 'Serial #:')); 
	echo $this->Form->input('pin_pad', array('label' => 'Pin Pad:')); 
	echo $this->Form->input('printer', array('label' => 'Printer:')); 
	echo $this->Form->input('agent', array('label' => 'Agent:')); 
	echo $this->Form->input('chain', array('label' => 'Chain:'));
}
echo $this->Form->input('auto_close', array('label' => 'Auto Close:'));
 if ($isFullFormAllowed){
 	echo $this->Form->input('gateway_id', array('label' => 'Gateway:', 'options' => $gateways, 'empty' => '--'));
 	$options = array('PEND' => '&nbsp;<img src="/img/icon_redflag.gif" class="icon"> Pending&nbsp;&nbsp;',
						'COMP' => '&nbsp;<img src="/img/icon_greenflag.gif" class="icon"> Complete ');
	$attributes = array('hiddenField' => false, 'legend' => false, );
	echo "<span class='clearfix'></span>";
	echo "<span class='col-md-12 col-sm-12 contentModuleTitle'>Status:</span>";
	echo "<div class = 'col-md-2 col-sm-3 text-center'>";
	echo $this->Form->radio('status', $options, $attributes);
  	echo "</div>";//close div	
 }
  echo "</div>";//close div
?> 
 <div class="row col-md-12 contentModuleTitle">Programming:</div>
	<?php foreach ($programmingTypes['EquipmentProgrammingTypeXref'] as $key => $progType): ?>
		<div class="row col-md-2">              
			<?php
			if (isset($this->request->data['EquipmentProgrammingTypeXref'][$key]['id'])) {
				echo $this->Form->hidden("EquipmentProgrammingTypeXref.$key.id");
				echo $this->Form->hidden("EquipmentProgrammingTypeXref.$key.equipment_programming_id");
			}
			echo $this->Form->input("EquipmentProgrammingTypeXref.$key.programming_type", array('type' => 'checkbox', 'hiddenField' => false, 'value' => key($progType), 'label' => h($progType[key($progType)])));
			?>
		</div>
	<?php endforeach ?>
