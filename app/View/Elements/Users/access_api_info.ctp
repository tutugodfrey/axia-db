<?php
echo $this->Html->tag('ul', null, array( "class" => "list-group"));
$tokenHTML = (Hash::get($user, 'User.access_token'))?: "<i class='text-muted small'>(Not yet created)</i>";
if (!empty(Hash::get($user, 'User.access_token'))) {
	$tokenHTML = $this->Html->link(
		"<span class='glyphicon glyphicon-eye-close text-small text-danger'></span>",
		"javascript:void(0)",
		array('onClick' => "objFader('tokenStrDisplay')", 'id' => 'toggleTokenBtn', 'escape' => false, 'class' => 'btn btn-xs btn-default', 'data-toggle' => "tooltip", 'data-placement' => "top", "title" => "Show/Hide")
	) . ' <kbd class="nowrap" id="tokenStrDisplay" style="display:none">' . $tokenHTML . '</kbd>';
}
echo $this->Html->tag('li', "<strong>Access Token: </strong> " . $tokenHTML, array("class" => "list-group-item", 'escape' => false));
//Only the owner of this user profile can copy the API password
if (Hash::get($user, 'User.id') === $this->Session->read('Auth.User.id')) {
	if (isset($oneTimeCopyPasswordStr)) {
		echo '<li class="list-group-item">';
		echo $this->Form->input("User.tmp_api_pw_disp", array(
				'type' => 'password',
				'value' => $oneTimeCopyPasswordStr,
				'label' => 'API Password',
				'class' => 'form-control',
				'readonly' => 'readonly',
				'beforeInput' => '<div class="input-group col-md-6"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
											'onClick' => "toggleShowPwField('UserTmpApiPwDisp')")) .'</span>',
				'afterInput' => '</div>'
		));
		echo '</li>';
 } else {
		echo $this->Html->tag('li', "<i class='text-muted'>Lost API password?<br>Create a new one using the menu on the right.</i>", array("class" => "list-group-item"));
	}
}
echo $this->Html->tag('/ul');
?>
