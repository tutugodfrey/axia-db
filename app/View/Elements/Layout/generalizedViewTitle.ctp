<?php $thisHumanName = Inflector::humanize(inflector::underscore($this->name)) ?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($user['User']['user_first_name'] . " " . $user['User']['user_last_name']) . " | " . __($thisHumanName); ?>" />
<?php
$userIdParam = (!empty($partnerUserId))? $partnerUserId :$userId;
$refererUrl = array(
	'plugin' => false,
	'controller' => 'Users',
	'action' => 'view',
	$userIdParam
);
$breadcrumbs = $this->Html->setBreadcrumbs($this->params, $user, __('Users view'), $refererUrl);
$this->start('breadcrumbs');
echo $this->Html->displayCrumbs();
$this->end();
?>
<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle">
			<?php echo __("Edit $thisHumanName", true); ?>
		</span>
	</div>
</div>