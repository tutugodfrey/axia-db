<div class="row">
	<div class="col-md-4">
		<span class="contentModuleTitle">
			Personal Information:
		</span>
		<span class="contentModule">
			<?php
			echo $this->Form->input('id');
			echo $this->Form->input('username');
			echo $this->Form->input('user_first_name', array('label' => 'First Name'));
			echo $this->Form->input('user_last_name', array('label' => 'Last Name', 'placeholder' => '(optional - leave blank if no last name)'));
			echo $this->Form->input('user_email');
			?>

			<?php
			$roleSettings = array(
				'label' => __('Role' . '<span style="color: #e32;" >*</span>'),
				'empty' => __('Please select...'),
				'required' => true,
			);
			if (!empty($this->validationErrors['User']['Role'])) {
				$roleSettings['after'] = "<div class='error text-right'><strong>" . Hash::get($this->validationErrors, 'User.Role.0') . "</strong></div>";
			}
			if ($this->Session->read('Auth.User.id') === $this->request->data('User.id') &&
				$this->Rbac->isPermitted('app/actions/Users/view/module/changeRoleSelfModule', true) === false) {
				$roleSettings['disabled'] = 'disabled';
				$roleSettings['class'] = 'form-control disabled';


				$roles = Hash::extract($this->request->data, 'UsersRole.{n}.Role.name');
				$roleSettings['value'] = (!empty($roles))? implode(',', $roles): null;
				echo $this->Form->input('RoleReadOnly', $roleSettings);
			} else {
				echo $this->Form->input('Role', $roleSettings);
			}
			echo $this->Form->input('entity_id', array(
					'empty' => __('Please select...'),
				)
			);
			echo $this->Form->input('initials');
			echo $this->Form->input('date_started', array(
				'type' => 'date',
			));

			if (!empty($newUser) && $newUser == true) {
				echo $this->Form->input('active', array(
						'type' => 'checkbox',
						'label' => __('Active User'),
						'checked' => 'true'
					)
				);
			} else {
				echo $this->Form->input('active', array(
						'type' => 'checkbox',
						'label' => array('text' => __('Active User'), 'class' => 'col col-md-12'),
					)
				);
			}

			echo $this->Form->input('is_blocked', array(
					'type' => 'checkbox',
					'label' =>array('text' => __('Blocked User'), 'class' => 'col col-md-12'),
				)
			);
			echo $this->Form->input('womply_active', array(
					'type' => 'checkbox',
					'label' => array('text' => __('Womply User Active'), 'class' => 'col col-md-12'),
				)
			);
			echo $this->Form->input('womply_user_enabled', array(
					'type' => 'checkbox',
					'label' => array('text' => __('Womply Enabled'), 'class' => 'col col-md-12'),
				)
			);
			?>
		</span>
	</div>
	<div class="col-md-4">
		<span class="contentModule">
			<span class="contentModuleTitle">
				Contact Information
			</span>
		</span>
			<?php
			echo $this->Form->input('user_phone', array('label' => __('Phone') . ':'));
			echo $this->Form->input('user_fax', array('label' => __('Fax') . ':'));
			?>
		<span class="contentModule">
			<span class="contentModuleTitle">
				Banking Information
			</span>
		</span><span class="small text-muted"> (optional)</span>
		<?php
			echo $this->Form->input('bank_name');
			echo $this->Form->input('routing_number');
			echo $this->Form->input('account_number', array('type' => 'password', 'value' => $this->request->data('User.account_number'), 'wrapInput' => 'col-md-5', 'after' => '<a id="bkAcctMaskToggler" href="javascript:void(0)" class="btn btn-default text-muted"><span class="glyphicon glyphicon-eye-open"></span></a>'));
		?>
		
	</div>
	<div class="col-md-4">
		<span class="contentModule">
			<span class="contentModuleTitle">
				Add or Change Password
			</span><br />
			<em>
				When editing User, leave password field blank to leave password unchanged.
			</em>
			<?php
			echo $this->Form->input('password', array('type' => 'password'));
			echo $this->Form->input('repeat_password', array('type' => 'password'));
			?>
		</span>
	</div>
</div>
<script> 
$('#bkAcctMaskToggler').on('mousedown', function(){
	$('#UserAccountNumber').attr('type', 'text');
});
$('#bkAcctMaskToggler').on('mouseup mouseleave', function(){
	$('#UserAccountNumber').attr('type', 'password');
});
</script>