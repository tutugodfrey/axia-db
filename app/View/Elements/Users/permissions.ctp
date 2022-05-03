<div class="row permissions table-responsive">
	<div class="col-md-12">
		<div class="contentModuleTitle">
			<a id="permissions-section"></a>
			<?php
			$title = __('Permissions');
			echo $this->Html->sectionHeader($title);
			$options = array('title' => $title, 'id' => 'user-profile-role-edit');
			echo $this->Html->editImage($options);
			?>
		</div>
		<div>
			<?php
			$options = array(
				'url' => array(
					'plugin' => false,
					'controller' => 'Users',
					'action' => 'assignRoles',
					Hash::get($user, 'User.id'),
				)
			);
			echo $this->Form->create('Perms', $options);
			?>
			<div id="user-profile-role-links">
				<?php
				echo $this->Html->tag('strong', __('Role')) . ': ';
				$roles = Hash::extract($user, 'UsersRole.{n}.Role');
				$cells = array(); // Role permission cells
				$roleLinks = array();

				foreach ($roles as $role) {
					$roleLinkUrl = array(
						'plugin' => 'rbac',
						'controller' => 'perms',
						'action' => 'security_roles',
						Hash::get($role, 'id'),
					);
					$roleLink = $this->Html->link(h(Hash::get($role, 'name')), $roleLinkUrl);
					$roleLinks[] = $roleLink;

					// fill the allowed permissions by role cells
					$allowedByRoleDescription = Hash::extract($role, 'Permission.{n}.description');
					foreach ($allowedByRoleDescription as $description) {
						$cells[] = array(
							$this->Html->link($description, $roleLinkUrl),
							__('Allowed'),
							''
						);
					}

					// set the current user roles for the input
					$this->request->data['Role']['Role'][] = Hash::get($role, 'id');
				}
				echo implode(', ', $roleLinks);
				?>
			</div>
			<div id="user-profile-role-input" class="row" style="display:none">
				<div class="col-md-6">
					<?php echo $this->Form->input('Role.Role', array('label' => __('Role'), 'options' => $roleList, 'type' => 'select', 'multiple' => 'true', 'empty' => false)); ?>
				</div>
				<div class="col-md-6">
					<?php echo $this->Form->submit(__('Save')); ?>
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div>
			<?php
			$headers = array(__('Description'), __('Allowed by Role'), __('Denied by User Constraint'));
			$deniedByUserDescription = Hash::extract($user, 'PermissionConstraint.{n}.Permission.description');
			$permissionConstraintsUrl = array(
				'plugin' => 'rbac',
				'controller' => 'perms',
				'action' => 'manage_exceptions',
				Hash::get($user, 'User.id'),
			);
			foreach ($deniedByUserDescription as $description) {
				$cells[] = array(
					$this->Html->link($description, $permissionConstraintsUrl),
					'',
					__('Denied')
				);
			}
			echo $this->AxiaHtml->toggleTable($headers, $cells);
			?>
		</div>
	</div>
</div>

<?php
$this->Js->get('#user-profile-role-edit');
$this->Js->event('mouseover', '$("#user-profile-role-edit").css("cursor", "pointer")');
$this->Js->event('click', '$("#user-profile-role-links, #user-profile-role-input").toggle()');
