<?php
echo $this->extend('/BaseViews/base');
?>
<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle">
			<?php echo __('Edit Associated Users', true); ?>
		</span>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table class="table table-condensed table-striped table-bordered table-hover">
			<tr>
				<th><?php echo __('Role', true);?></th>
				<th><?php echo __('Name', true);?></th>
				<th><?php echo __('Permission Level', true);?></th>
				<th><?php echo __('Actions', true);?></th>
			</tr>
			<?php
			foreach ($this->data['UserCompensationAssociation'] as $associatedUser) {?>
				<?php echo ($associatedUser['main_association'])? '<tr class="info strong">': '<tr>' ?>
					<td><?php echo h($associatedUser['role']); ?>
					</td>
					<td>
						<?php 
						echo h($associatedUser['UserAssociated']['fullname']);
						?>
					</td>
					<td>
						<?php echo h($associatedUser['permission_level']);
						if ($associatedUser['main_association']) {
							echo ' ' . $this->Html->tag('div', null, array(
								'class' => 'glyphicon glyphicon-star-empty btn-xs btn-warning roundEdges',
								'data-toggle' => "tooltip",
								'data-placement' => "right", "title" => "This is the default " . h($associatedUser['role'])))
							 . $this->Html->tag('/div');
						}
						?>
					</td>
					<td>
						<?php 
						if ($associatedUser['permission_level'] === User::ROLE_SM && $associatedUser['main_association'] === false) {
							echo $this->Form->postLink('<span class="glyphicon glyphicon-star"></span>', 
								array('controller' => 'Users', 'action' => 'setMainAssociated', $associatedUser['id'], $this->request->data['UserCompensationProfile']['id']),
								array(
									'data-toggle' => "tooltip",
									'data-placement' => "top", 
									"title" => "Make this the prevalent or default " . h($associatedUser['role']),
									'class' => 'btn btn-xs btn-info',
									'escape' => false,
									'confirm' => __("Make %s the prevalent or default Manager?\n\n(By making this change, this user will be set as the manager in future new merchant accounts)", $associatedUser['UserAssociated']['fullname'])
								)
							);
						}
						echo $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', array('controller' => 'Users', 'action' => 'removeAssociated', $associatedUser['id'], $this->request->data['UserCompensationProfile']['id']), array('class' => 'btn btn-xs btn-danger', 'escape' => false, 'confirm' => __("WARNING:\nRemoving %s will also delete any and all of his/her compensation data from %s's compensation profile.\nThis won't affect %s. \nProceed?", $associatedUser['UserAssociated']['fullname'], $this->request->data('User.fullname'), $this->request->data('User.fullname'))));
						?>
					</td>
				</tr>
			<?php
			}?>
		</table>
	</div>
</div>
<hr>
<?php
echo $this->Form->create('AssociatedUser');
?>
<div class="row">
	<div class="col-md-4">
		<span class="contentModuleTitle">
			<?php
				echo h(__("Assign a user to {$this->request->data['User']['fullname']} with special permissions:"));
			?>
		</span>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php
		echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $this->data['User']['id']));
		echo $this->Form->input('user_compensation_profile_id', array('type' => 'hidden', 'value' => $this->data['UserCompensationProfile']['id']));
		?>
		<div class="col-md-4">
			<?php
			echo $this->Form->input('role', array('options' => $roleNames, 'empty' => __('Select Role', true)));
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $this->Form->input('associated_user_id');
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $this->Form->input('permission_level', array('type' => 'select'));
			?>
		</div>
	</div>
	<div class="col-md-10">
		<div class="col-md-6 actions">
			<?php
			echo $this->Html->link(__('Cancel', true), array('controller' => 'users', 'action' => 'view', $this->data['User']['id']), array('class' => 'btn'));
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $this->Form->submit(__('Add', true), array('type' => 'submit', 'class' => 'btn'));
			?>
		</div>
	</div>
</div>
<?php
echo $this->Form->end();
$this->Js->get('#AssociatedUserRole, #AssociatedUserUserId');
$this->Js->event('change', $this->Js->request(
				array(
				  'controller' => 'users',
				  'action' => 'getUsersByRole'
				), array(
				  'update' => '#AssociatedUserAssociatedUserId',
				  'async' => true,
				  'method' => 'post',
				  'dataExpression' => true,
				  'data' => $this->Js->serializeForm(
						  array(
									'isForm' => true,
									'inline' => true
						  )
				  )
				)
		)
);
$this->Js->event('change', $this->Js->request(
				array(
				  'controller' => 'users',
				  'action' => 'getPermissionLevelByRole'
				), array(
				  'update' => '#AssociatedUserPermissionLevel',
				  'async' => true,
				  'method' => 'post',
				  'dataExpression' => true,
				  'data' => $this->Js->serializeForm(
						  array(
									'isForm' => true,
									'inline' => true
						  )
				  )
				)
		)
	);
?>
<script>
$(document).ajaxComplete(function(event, xhr, settings) {
	if (xhr.status === 200) {
		if (settings.url.indexOf("getUsersByRole") > 0) {
			//Extract options returned
			optnsHtml = $($.parseHTML(xhr.responseText)).filter('OPTION');
			if ($(optnsHtml).length == 1 && optnsHtml[0].value == '') {
				$('#AssociatedUserAssociatedUserId').attr("disabled", "disabled");
				$('#AssociatedUserAssociatedUserId')
				    .find('option')
				    .remove()
				    .end()
				    .append('<option>(No users found with selected Role)</option>');
			} else {
				$('#AssociatedUserAssociatedUserId').removeAttr("disabled");
			}
		}
	}
    });
</script>