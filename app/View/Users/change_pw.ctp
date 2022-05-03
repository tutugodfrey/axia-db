<div style="width: 350px; margin-left: auto; margin-right: auto">
<?php
$actionStr = ($renewingPw)? 'Update' : 'Reset';
echo $this->Html->tag('div', __("$actionStr Password"),
	[
		'class' => 'bg-primary text-center text-muted'
	]
);
echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => false,
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well form-horizontal'
));

if ($renewingPw) {
	echo $this->Form->input('cur_password', array('type' => 'password','placeholder' => 'Enter current password', 'autocomplete' => 'off'));
}

echo $this->Form->hidden('id');
echo $this->Form->input('password', array('type' => 'password', 'placeholder' => 'Enter new password', 'autocomplete' => 'off'));
echo $this->Form->input('repeat_password', array('type' => 'password', 'placeholder' => 'Confirm new password', 'autocomplete' => 'off'));

echo $this->Html->tag('div', $this->Html->link('Cancel', array('action' => 'login'), array(
	'class' => 'btn btn-danger',
	)) . $this->Form->submit($actionStr, array(
	'div' => 'pull-right',
	'class' => 'btn btn-primary',
	)),
	array('class' => 'form-group ')
);
echo $this->Form->end();
echo $this->Session->flash(); 
?>
</div>
