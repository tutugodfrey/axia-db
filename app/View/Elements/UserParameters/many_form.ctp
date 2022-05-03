<?php

echo $this->Form->create('UserParameter', array(
		'inputDefaults' => array(
				'wrapInput' => 'col col-xs-12 input-sm'
			)
	));
echo $this->UserParameter->edit($userParameterHeaders, $productsServicesTypes);

$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($this->request->data, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
