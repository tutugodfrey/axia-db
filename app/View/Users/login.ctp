<div style="width: 350px; margin-left: auto; margin-right: auto">
<?php 
echo $this->Html->tag('div', __('Axia Extranet Database Login'),
	[
		'class' => 'bg-info text-center text-muted'
	]
);
echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => array(
			'class' => 'col col-md-3 control-label'
		),
		'wrapInput' => 'col col-md-9',
		'class' => 'form-control'
	),
	'class' => 'well form-horizontal'
));
echo $this->Form->input('username', array(
	'placeholder' => 'Username or Email',
	'autofocus' => 'autofocus',
	'autocomplete' => 'off'
));
echo $this->Form->input('password', array(
	'placeholder' => 'Password',
	'autocomplete' => 'off'
));
echo $this->Form->input('code', array(
	'placeholder' => '2-Factor Authentication code',
	'autocomplete' => 'off'
));
echo $this->Html->tag('div', $this->Form->submit('Login', array(
	'div' => 'col col-md-9 col-md-offset-3',
	'class' => 'btn btn-default',
	)),
	array('class' => 'form-group')
);
echo $this->Html->tag('div', 
	$this->Html->link('Forgot password', array('action' => 'request_pw_reset'), array('class' => 'text-muted')) . ' | ' .
	$this->Html->link('Renew password', array('action' => 'request_pw_reset', 1), array('class' => 'text-muted')),
	array('class' => 'small pull-right')
	);
echo $this->Form->end();
echo $this->Flash->render('sessionExpiration');
echo $this->Flash->render('loginError');
echo $this->Flash->render('goodbye');
echo $this->Session->flash(); 
?>
</div>
