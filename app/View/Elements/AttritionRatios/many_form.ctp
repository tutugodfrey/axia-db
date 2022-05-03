<?php
echo $this->extend('/BaseViews/base');

$refererUrl = array(
	'plugin' => false,
	'controller' => 'Users',
	'action' => 'view',
	$user['User']['id']
);
$breadcrumbs = $this->Html->setBreadcrumbs($this->params, $user, __('Users view'), $refererUrl);
$this->start('breadcrumbs');
echo $this->Html->displayCrumbs();
$this->end();
?>
<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle">
			<?php echo __('Edit Attrition Ratios', true); ?>
		</span>
	</div>
</div>
<?php
echo $this->Form->create('AttritionRatio');
$attritionRatios = Hash::extract($this->request->data, '{n}.AttritionRatio');
$index = 0;
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	'min' => 0,
	'max' => 100,
);
$tableHeader = $this->element('AttritionRatios/grid_table_headers');
$tableRows = array();

foreach ($attritionRatios as $attritionRatio) {
	$tableRow = '';
	if (!empty($this->request->data("$index.AttritionRatio.id"))) {
		$tableRow = $this->Form->hidden("$index.AttritionRatio.id");
	}
	$tableRow .= $this->Form->hidden("$index.AttritionRatio.user_compensation_profile_id", array('value' => $compensationId));
	$tableRow .= $this->Form->hidden("$index.AttritionRatio.associated_user_id");
	$tableRow .= $this->Html->tableCells(array(
		CakeText::insert(':role<br>(:name)', array_map('h', $associatedUsers[$attritionRatio['associated_user_id']])),
		$this->Form->input("$index.AttritionRatio.percentage", $gridInputOptions),
	));
	
	$tableRows[] = $tableRow;
	$index++;
}
$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table', $tableHeader . $tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($user, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
