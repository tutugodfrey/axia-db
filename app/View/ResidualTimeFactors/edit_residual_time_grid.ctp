<?php
echo $this->extend('/BaseViews/base');

$refererUrl = array(
	'plugin' => false,
	'controller' => 'Users',
	'action' => 'view',
	$userId
);
$breadcrums = $this->Html->setBreadcrumbs($this->params, $this->request->data, __('Users view'), $refererUrl);
$this->start('breadcrumbs');
echo $this->Html->displayCrumbs();
$this->end();

echo $this->Form->create('ResidualTimeFactor', array('inputDefaults' => array('wrapInput' => 'col col-md-12')));
echo $this->Form->hidden('id');
echo $this->Form->hidden('user_compensation_profile_id', array('value' => Hash::get($this->request->data, 'UserCompensationProfile.id')));
echo $this->Form->hidden('UserCompensationProfile.id');
?>
<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle"><?php echo __('Edit residual time factors (Note: Client "Go-Live" date is used for month count)'); ?></span>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table class="table">
			<?php echo $this->ResidualTimeGrid->tierTable('edit'); ?>
		</table>
	</div>
</div><div class="row">
	<div class="col-md-12">
		<?php echo $this->element('ResidualTimeParameters/edit'); ?>
	</div>
</div>

<?php
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
