<?php 
/* Drop breadcrumb */
$this->Html->addCrumb(Inflector::humanize($this->name), ["controller" => $this->name, 'action' => 'content']);
if ($modelName === 'Client' && empty($this->request->data('Client.id'))) {
	echo '<div class="alert alert-warning alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong>Notice:</strong> Client IDs that were created in SalesForce may be added here. However it is not possible to create new Clients from here, this can only be done from SalesForce.</div>';
}
echo $this->Form->create('MaintenanceDashboard', 
		[
			'inputDefaults' => [
				'div' => 'form-group',
				'label' => ['class' => 'col col-xs-12 control-label'],
				'wrapInput' => false,
				'class' => 'form-control'
			],
			'class' => 'well well-sm form-inline'
		]
	);
	echo $this->Html->tag('h6', __("Add/Edit $modelName content:"), ["class" => "contentModuleTitle"]);
	if ($modelName === 'Client') {
		echo $this->element('MaintenanceDashboards/Clients/add_edit_form');
	} else {
		echo $this->element('MaintenanceDashboards/MultiModelForm/add_edit_many_models');
	}
	echo $this->Form->hidden('MaintenanceDashboard.modelName');
	echo $this->Form->hidden('MaintenanceDashboard.modelName');
echo $this->Form->end(['label' => 'Save', 'class' => "btn btn-success", 'id' => 'MaintenanceDashboardFormSubmitBtn', 'div' => ["class" => "form-group"]]);
?>