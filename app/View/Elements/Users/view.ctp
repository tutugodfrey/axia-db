<?php
	$editLink = '';
	$actionsMenu = '';
	if ($this->Rbac->isPermitted('Users/edit') || $this->Rbac->isPermitted('app/actions/Users/view/module/apiAccountActions', true)) {
		$editLink = $this->Html->editImageLink(array('action' => 'edit', $user['User']['id']));
		$actionsMenu = '<div class="row pull-right">' . $this->element("Users/actions_menu") . '</div>';
	}
?>
<div class="col-md-12">
	<?php echo $actionsMenu; ?>
	<div class="col-md-3 col-sm-8">
		<div class="panel panel-info">
			<div class="panel-heading contentModuleTitle">
				User Information <?php echo $editLink ?>
			</div>
				<?php
				echo $this->Html->tag('ul', null, array( "class" => "list-group"));
					echo $this->Html->tag('li', "<strong>Name: </strong>" . h($user['User']['fullname']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', "<strong>Username: </strong>" . h($user['User']['username']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', "<strong>Role(s): </strong>" . implode(',', Hash::extract($user['UsersRole'], "{n}.Role.name")), array("class" => "list-group-item"));					
					echo $this->Html->link('<strong>Email: </strong>' . h($user['User']['user_email']), "mailto:{$user['User']['user_email']}", array("escape" => false, "class" => "list-group-item text-info"));
					echo $this->Html->tag('li', '<strong>Entity: </strong>' . h($user['Entity']['entity_name']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', '<strong>Start Date: </strong>' . date_format(date_create($user['User']['date_started']), 'M jS Y'), array("class" => "list-group-item"));
					echo $this->Html->tag('li', $this->Html->checkboxValue($user['User']['active'], 'Status', 'Active', 'Inactive'), array("class" => "list-group-item"));
					echo $this->Html->tag('li', $this->Html->checkboxValue($user['User']['womply_active'], 'Womply User Status', 'Active', 'Inactive'), array("class" => "list-group-item"));
					echo $this->Html->tag('li', $this->Html->checkboxValue($user['User']['womply_user_enabled'], 'Womply', 'Enabled', 'Disabled'), array("class" => "list-group-item"));
					echo $this->Html->tag('li', $this->Html->checkboxValue($user['User']['is_blocked'], 'Blocked', 'Blocked', 'Not Blocked'), array("class" => "list-group-item"));
				echo $this->Html->tag('/ul');
				?>
		</div>
	</div>
	<div class="col-md-4 col-sm-8">
		<div class="panel panel-info">
			<div class="panel-heading contentModuleTitle">
				Contact Information
			</div>
				<?php
				echo $this->Html->tag('ul', null, array( "class" => "list-group"));
					echo $this->Html->tag('li', '<strong>Phone: </strong>' . $this->Html->formattedPhone($user['User']['user_phone']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', '<strong>Fax: </strong>' . $this->Html->formattedPhone($user['User']['user_fax']), array("class" => "list-group-item"));
				echo $this->Html->tag('/ul');
				?>

		</div>
		<?php
		if (!empty($user['User']['account_number'])) : ?>
		<div class="panel panel-info">
			<div class="panel-heading contentModuleTitle">
				Banking Information
			</div>
				<?php
				$re = '/\d{4}$/';
				preg_match($re, $user['User']['account_number'], $matches);
				$tuncAcct = (!empty($matches[0]))?"******{$matches[0]}" : null;
				echo $this->Html->tag('ul', null, array( "class" => "list-group"));
					echo $this->Html->tag('li', '<strong>Bank Name: </strong>' . h($user['User']['bank_name']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', '<strong>Routing Number: </strong>' . h($user['User']['routing_number']), array("class" => "list-group-item"));
					echo $this->Html->tag('li', '<strong>Account Number: </strong>' . $tuncAcct, array("class" => "list-group-item"));
				echo $this->Html->tag('/ul');
				?>
		</div>
	<?php endif; ?>
	</div>
	<?php if ($isApiConsumer) {?>
	<div class="col-md-4 col-sm-8">
		<div class="panel panel-warning">
			<div class="panel-heading contentModuleTitle">
				API Information
			</div>
				<?php
				echo $this->element("Users/access_api_info");
				?>
		</div>
	</div>
	<?php } ?>
</div>
<div class='clearfix'></div><!-- separator-->