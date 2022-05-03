<input type="hidden" id="thisViewTitle" value="<?php echo __('Master Equipment List'); ?>" />


	<div class='well well-sm'>	
	<?php
	if (!empty($equipmentItems) && $this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)) {
		$icon = $this->Csv->icon(null, ['title' => __('Export all equipment data'), 'class' => 'icon']);
		$exportLinks[] = $this->Html->link($icon, "#", ['onClick' => "exportTableToCSV('master_equipment_list.csv', 'equip-list-content')", 'escape' => false]);
		echo $this->Html->tag('span',
			"<strong>Export Data: <br></strong>" . $this->element('Layout/exportCsvUi', array('exportLinks' => $exportLinks)),
			array('class' => 'pull-left')
		);
	}
	if ($this->Rbac->isPermitted('EquipmentItems/add')) {
		echo $this->Form->create('EquipmentItem', 
			array(
				'url' => array('action' => 'add'),
				'inputDefaults' => array(
						'wrapInput' => 'col col-md-8'
					),
				'class' => 'form-inline'
			)
		);
		echo $this->Element('EquipmentItems/FormFields');
		echo $this->Form->end(array('label' => 'Add New Equipment', 'class'=> 'btn btn-sm btn-success', 'div' => array('class'=> 'form-group')));
	}
?>
	</div>

<div class="text-center">
<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
</div>
	<table class="table table-condensed table-hover" id="equip-list-content">
		<tr>
			<th><?php echo $this->Paginator->sort('equipment_item_description', 'Item'); ?></th>
			<?php
			/* Only admins can see this column */
			if ($this->Rbac->isPermitted('app/actions/EquipmentItems/view/module/trueCost', true)): ?>
				<th><?php echo $this->Paginator->sort('equipment_item_true_price', 'True Cost'); ?></th>
			<?php endif; ?>
			<?php if ($this->Rbac->isPermitted('app/actions/EquipmentItems/view/module/repCost', true)): ?>
				<th colspan="2"><?php echo $this->Paginator->sort('equipment_item_rep_price', 'Rep Cost'); ?></th>
			<?php endif; ?>
		</tr>
		<?php foreach ($equipmentItems as $equipmentItem): ?>
		<tr>
			<td><?php echo $this->Html->link(h($equipmentItem['EquipmentItem']['equipment_item_description']), array(
		'controller' => 'EquipmentItems', 'action' => 'edit', $equipmentItem['EquipmentItem']['id']));
		?>&nbsp;</td>
			<?php
			/* Only admins can see this column */
			if ($this->Rbac->isPermitted('app/actions/EquipmentItems/view/module/trueCost', true)): ?>
			<td>
				<?php echo $this->Number->currency(h($equipmentItem['EquipmentItem']['equipment_item_true_price']), 'USD'); ?>&nbsp;
			</td>
			<?php endif; ?>
			<?php if ($this->Rbac->isPermitted('app/actions/EquipmentItems/view/module/repCost', true)): ?>
				<td><?php echo $this->Number->currency(h($equipmentItem['EquipmentItem']['equipment_item_rep_price']), 'USD'); ?>&nbsp;</td>
			<?php endif; ?>
			<?php if ($this->Rbac->isPermitted('EquipmentItems/delete') && $this->Rbac->isPermitted('EquipmentItems/edit')): ?>
				<td class="text-right">
					<?php
					echo $this->Html->image("/img/editPencil.gif", array("title" => "Edit Equipment",
						"class" => "btn btn-xs btn-default", 'url' => array('controller' => 'EquipmentItems', 'action' => 'edit', $equipmentItem['EquipmentItem']['id'])));
					echo $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', array('controller' => 'EquipmentItems', 'action' => 'delete',
						$equipmentItem['EquipmentItem']['id']), array('class' => 'btn btn-xs btn-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete %s?', $equipmentItem['EquipmentItem']['equipment_item_description'])));
					?>
				</td>
		<?php endif; ?>
		</tr>
		<?php endforeach; ?>
	</table>
<?php
echo $this->AssetCompress->script('reports', [
		'raw' => (bool)Configure::read('debug')
	]);