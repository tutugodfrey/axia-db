<?php
	$blockingUrl = $activationUrl = ['controller' => 'Users', 'action' => '', $user['User']['id']];
	$blockSettings = $actSettings = ['escape' => false, 'class' => 'list-group-item strong btn-sm', 'confirm' => ''];
	$activationUrl['action'] = 'toggleActive';
	if (($user['User']['active'] == 1)) {
		$activeClass = 'glyphicon glyphicon-minus-sign text-danger';
		$activeLabel = 'Deactivate User';
	} else {
		$activeClass = 'glyphicon glyphicon-plus-sign text-success';
		$activeLabel = 'Activate User';
	}
	$actSettings['confirm'] = __("Are you sure you want to $activeLabel?");
	if (($user['User']['is_blocked'] == false)) {
		$blockedClass = 'glyphicon glyphicon-ban-circle text-danger';
		$blockedLabel = 'Block User';
		$blockingUrl['action'] = 'block';
	} else {
		$blockedClass = 'glyphicon glyphicon-ok-circle text-success';
		$blockedLabel = 'Unblock User';
		$blockingUrl['action'] = 'unblock';
	}
	$blockSettings['confirm'] = __("Are you sure you want to $blockedLabel?");

	$resetPwUrl = null;
	$resetPwSettings = ['escape' => false, 'class' => 'list-group-item strong btn-sm', 'confirm' => __("User's password will be changed to a temporary one and an email will be sent to the user to update it.\nContinue?")];
	if ($user['User']['active'] == 1 && $user['User']['is_blocked'] == false) {
		$resetPwUrl = ['controller' => 'Users', 'action' => 'request_pw_reset', true, $user['User']['id'], true];
	} else {
		unset($resetPwSettings['confirm']);
		$resetPwSettings['class'] .= " disabled";
		$resetPwSettings = array_merge($resetPwSettings, ['data-toggle' => "tooltip", 'data-placement' => "left", "title" => "You must activate and/or unblock user first."]);
	}
?>
<div class="panel panel-primary">
	<div class="panel-heading contentModuleTitle">User Actions: </div>
		<div class="list-group">
			<?php
			if ($this->Rbac->isPermitted('Users/edit')) {
				if (!empty($user['User']['secret'])) {
					echo $this->Form->postLink("<span class='glyphicon glyphicon-off text-danger'></span> Disable 2-Factior Auth",
						['action' => 'turn_off_2FA', $user['User']['id']],
						['class' => 'list-group-item strong btn-sm', 'escape' => false, 'confirm' => __("Are you sure you want to disable two factor authentication for this user?")]);
				}
				echo $this->Form->postLink("<span class='$activeClass'></span> $activeLabel", $activationUrl, $actSettings);
				echo $this->Form->postLink("<span class='$blockedClass'></span> $blockedLabel", $blockingUrl, $blockSettings);
				echo $this->Form->postLink("<span class='glyphicon glyphicon-refresh text-success'></span> Reset Password", $resetPwUrl, $resetPwSettings);
			}
			$resetAPIPwUrl = null;
			$apiActionsAttr = ['escape' => false, 'class' => 'list-group-item disabled'];
			if ($user['User']['active'] == 1 && $user['User']['is_blocked'] == false && $isApiConsumer && $this->Rbac->isPermitted('app/actions/Users/view/module/apiAccountActions', true)) {
				$apiActionsAttr['class'] = 'list-group-item strong btn-sm';
				$apiActionsAttr['confirm'] = __("This will replace any current API token. Continue?");
				$ApiUrlAction = ['controller' => 'Users', 'action' => 'create_api_token', $user['User']['id']];
				echo $this->Form->postLink("<span class='glyphicon glyphicon-console text-success'></span> Create API Token", $ApiUrlAction, $apiActionsAttr);
				//Only the owner of this user profile can create an API password
				if (Hash::get($user, 'User.id') === $this->Session->read('Auth.User.id')) {	
					$apiActionsAttr['confirm'] = __("This will replace any current API password. Continue?");
					$ApiUrlAction = ['controller' => 'Users', 'action' => 'view', $user['User']['id'], 0, true];
					echo $this->Form->postLink("<span class='glyphicon glyphicon-lock text-success'></span> Create API Password", $ApiUrlAction, $apiActionsAttr);
				}
			}
			?>
		</div>
</div>
