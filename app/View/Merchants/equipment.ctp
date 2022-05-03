<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/view/' . $merchant[Inflector::singularize($this->name)]['id']);
$this->Html->addCrumb('Programming', '/' . $this->name . '/' . $this->action . '/' . $merchant[Inflector::singularize($this->name)]['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Equipment Programming')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

	<?php if ($this->Rbac->isPermitted('EquipmentProgrammings/add')): ?>      
		<div class="center-block text-center">
			<?php echo $this->Html->link(
				"<span class='glyphicon glyphicon-plus text-success'><span> " . $this->Html->image('cc-term.png', array('style' => 'height:30px')), 
				array('controller' => 'equipment_programmings', 'action' => 'add', $merchant['Merchant']['id']), 
				array('data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => 'Add Terminal/Programming', 'escape' => false, 'class' => 'btn-default btn btn-xs')); ?>&nbsp;
		</div>  
	<?php endif; ?>
<table class="table table-condensed table-hover dontSpill">
	<tr>		
		<th><?php echo __('Terminal #'); ?></th>
		<th><?php echo __('Network'); ?></th>                
		<th><?php echo __('TID'); ?></th>
		<th><?php echo __('Terminal Type'); ?></th>
		<th><?php echo __('Version'); ?></th>
		<th><?php echo __('Gateway'); ?></th>
		<th><?php echo __('App ID'); ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/EquipmentProgrammings/view/module/readIsAllowedS1', true)): ?>
			<th><?php echo __('Provider'); ?></th>
			<th><?php echo __('Serial #'); ?></th>
			<th><?php echo __('Pin Pad'); ?></th>			
			<th><?php echo __('Agent'); ?></th>
			<th><?php echo __('Chain'); ?></th>
		<?php endif; ?>
		<th><?php echo __('Printer'); ?></th>
		<th><?php echo __('Auto Close'); ?></th>
		<th><?php echo __('Programming'); ?></th>	
		<th><!--spacer--></th>		
	</tr>

	<?php
	if (!empty($equipment)):
		foreach ($equipment as $equipmentProgramming):
			?>
			<tr>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['terminal_number']); ?></td>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['network']); ?></td>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['hardware_serial']); ?></td>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['terminal_type']); ?></td>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['version']); ?></td>
				<td><?php echo !empty($equipmentProgramming['EquipmentProgramming']['gateway_id']) ? h($gateways[$equipmentProgramming['EquipmentProgramming']['gateway_id']]) : ''; ?></td>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['app_type']); ?></td>	
				<?php if ($this->Rbac->isPermitted('app/actions/EquipmentProgrammings/view/module/readIsAllowedS1', true)): ?>
					<td><?php echo h($equipmentProgramming['EquipmentProgramming']['provider']); ?></td>
					<td><?php echo h($equipmentProgramming['EquipmentProgramming']['serial_number']); ?></td>
					<td><?php echo h($equipmentProgramming['EquipmentProgramming']['pin_pad']); ?></td>
					<td><?php echo h($equipmentProgramming['EquipmentProgramming']['agent']); ?></td>
					<td><?php echo h($equipmentProgramming['EquipmentProgramming']['chain']); ?></td>
				<?php endif; ?>
				<td><?php echo h($equipmentProgramming['EquipmentProgramming']['printer']); ?></td>
				<td ><?php echo h($equipmentProgramming['EquipmentProgramming']['auto_close']); ?></td>
				<td ><?php
					$prgTypes = Hash::extract($equipmentProgramming['EquipmentProgrammingTypeXref'], '{n}.programming_type');
					$prgTypesDescriptions = array();
					if (!empty($prgTypes)) {
						foreach ($prgTypes as $prgType) {
							array_push($prgTypesDescriptions, $prgDescriptions[$prgType]);
						}
					}
					echo h(implode(",", $prgTypesDescriptions));
					?>
				</td>

				<td class="nowrap">
					<?php echo $this->Html->image("" . $FlagStatusLogic->getThisStatusFlag($equipmentProgramming['EquipmentProgramming']['status']), array("class" => "icon")); ?>   
					<?php
					if ($this->Rbac->isPermitted('Merchants/delete')) {
						echo $this->Form->postLink($this->Html->image("redx.png", array("title" => "Delete Terminal", "class" => "icon")), array('controller' => 'equipment_programmings', 'action' => 'delete', $equipmentProgramming['EquipmentProgramming']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete %s?', $equipmentProgramming['EquipmentProgramming']['terminal_type'] . " #" . h($equipmentProgramming['EquipmentProgramming']['terminal_number']))));
					}
					if ($this->Rbac->isPermitted('EquipmentProgrammings/edit')) {
						echo $this->Html->image("editPencil.gif", array("title" => "Edit Terminal", "class" => "icon", 'url' => array('controller' => 'EquipmentProgrammings', 'action' => 'edit', $equipmentProgramming['EquipmentProgramming']['id'])));
					}
					?>				
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>         
</table>
<?php
	if (empty($equipment)) {
		echo '<span style="margin:20px 0 20px 0"class="list-group-item text-center text-muted">- Merchant has no Terminals/Programming -</span>';
	}
?>
<div id="NewMerchantNoteErrorDialog" title="<?php echo __('Error'); ?>" style="display: none;"></div>

<div style="width: 30%">
	<div class="panel-heading bg-primary">
		<span class="contentModuleTitle">Programming Notes</span>
		<?php
		if ($this->Rbac->isPermitted('MerchantNotes/add') && $this->Rbac->isPermitted('app/actions/MerchantNotes/view/module/addPrgmNote', true)) {
			echo $this->MerchantNote->addAjaxNoteButton(null, 'addPrgNote', "Add Programming note for " . $merchant['Merchant']['merchant_dba'], ['class' => 'pull-right btn-success btn-xs']);
		}
		?>      
	</div>                 
	<div class="well well-sm">
		<div id="addNote_frm" style="margin-bottom: 6px; display:none ">

		</div>
		<?php if (!empty($prgNotes['MerchantNote'])): ?>
			<?php foreach ($prgNotes['MerchantNote'] as $noteData): ?>                

				<div class="panel panel-info">
					<div class="panel-heading contentModuleTitle">
						<?php if ($this->Rbac->isPermitted('MerchantNotes/edit')): ?>
							<?php echo $this->Html->image("editPencil.gif", array("title" => "Edit this note.", "class" => "icon pull-right contrTitle roundEdges", 'url' => array('controller' => 'merchant_notes', 'action' => 'edit', $noteData['id']))); ?>
						<?php endif; ?>
						<span class="contentModuleTitle">Posted on <?php echo date_format(date_create($noteData['note_date']), 'M jS Y'); ?>:</span><br />
					</div>                            
					<div class='panel-body'>
						<?php echo nl2br(trim(h($noteData['note']))); ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<span id="noteNothingPrg" class='list-group-item text-muted text-center'>- No Programming Notes -</span>                        
		<?php endif; ?>
	</div>
</div>
<script type='text/javascript'>
activateNav('MerchantsEquipment');
var merchantId = "<?php echo $merchant['Merchant']['id']; ?>";
$("#addPrgNote").on('click', function(e) {
	e.preventDefault();
	ajaxNote(merchantId, 'Programming Note','addNote_frm');
	objFader('addNote_frm'); objFader('noteNothingPrg');
});
</script>