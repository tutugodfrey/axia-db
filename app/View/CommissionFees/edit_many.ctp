<?php
echo $this->extend('/BaseViews/base');

// Overwrite the default breadcrumbs
$refererUrl = array(
	'plugin' => false,
	'controller' => 'Users',
	'action' => 'view',
	Hash::get($user, 'User.id')
);
$breadcrums = $this->Html->setBreadcrumbs($this->params, $user, __('Users view'), $refererUrl);
$this->start('breadcrumbs');
echo $this->Html->displayCrumbs();
$this->end();

echo $this->Form->create('CommissionFee');
?>

<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle"><?php echo __('Edit commission fees'); ?></span>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php
		echo $this->element('CommissionFees/table', array(
			'commissionFees' => $commissionFees,
			'action' => 'edit'
		));
		?>
	</div>
</div>

<?php
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($user, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
