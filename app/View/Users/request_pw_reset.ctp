<div style="width: 350px; margin-left: auto; margin-right: auto">
<?php
echo $this->Html->tag('div', __('Reset My Password'),
	[
		'class' => 'bg-primary text-center text-muted'
	]
);
echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => false,
		'wrapInput' => 'col col-md-12',
		'class' => 'form-control'
	),
	'class' => 'well form-horizontal'
));
echo $this->Form->input('username', array(
	'wrapInput' => 'col col-md-12 input-group',
	'beforeInput' => '<span class ="input-group-addon glyphicon glyphicon-user"></span>',
	'placeholder' => 'Username',
	'autofocus' => 'autofocus',
	'autocomplete' => 'off'
));

echo $this->Html->tag('div', $this->Html->link('Cancel', array('action' => 'login'), array(
	'class' => 'btn btn-danger',
	)) . $this->Form->submit('Send', array(
	'div' => 'pull-right',
	'class' => 'btn btn-primary ',
	)),
	array('class' => 'form-group')
);
echo $this->Form->end();
echo $this->Flash->render('loginError');
echo $this->Session->flash(); 
?>
</div>
