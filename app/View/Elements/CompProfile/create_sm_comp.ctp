<li class="divider"></li>
<?php foreach($managerRoles as $roleId => $roleName): ?>
	<li class="small">
		<?php
		echo $this->Html->link("Create new $roleName Comp Profile", "javascript:void(0)", array('onClick' => "requestNewCompProfile('$roleId', '$roleName','$userId','$isManager')"));
		?>
	</li>
<?php endforeach;?>