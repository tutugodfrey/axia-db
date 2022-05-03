<?php
echo $this->extend('/BaseViews/base');
echo $this->Form->create('User');
?>
<div class="row">
	<div class="col-md-4">
		<span class="contentModuleTitle">
			Edit User Profile
		</span>
	</div>
</div>

<?php echo $this->element('Users/user_form'); ?>

<?php
echo $this->Form->defaultButtons();
echo $this->Form->end();