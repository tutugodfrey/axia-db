<?php
	$assocPath = "MerchantUwVolume";
	//check if MerchantUwVolume model is set as child Association in request->data
	if (array_key_exists("MerchantUw", $this->request->data)) {
		debug("deadcode");
		$assocPath = "MerchantUw.MerchantUwVolume";
	}
	
	echo $this->Form->input("$assocPath.te_amex_number", array(
			'type' => 'password',
			'value' => $this->request->data("$assocPath.te_amex_number"),
			'placeholder' => 'Amex #',
			'label' => false,
			'wrapInput' => false,
			// 'div' => 'col col-md-2',
			'beforeInput' => '<span class="contentModuleTitle">American Express:<br /></span><div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
										'onClick' => "toggleShowPwField('MerchantUwVolumeTeAmexNumber')")) .'</span>',
			'afterInput' => '</div>'
	));

	echo $this->Form->input("$assocPath.te_diners_club_number", array(
			'type' => 'password',
			'value' => $this->request->data("$assocPath.te_diners_club_number"),
			'placeholder' => 'Diners #',
			'label' => false,
			'wrapInput' => false,
			// 'div' => 'col col-md-2',
			'beforeInput' => '<span class="contentModuleTitle">Diners:<br /></span><div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
										'onClick' => "toggleShowPwField('MerchantUwVolumeTeDinersClubNumber')")) .'</span>',
			'afterInput' => '</div>'
	));


	echo $this->Form->input("$assocPath.te_discover_number", array(
			'type' => 'password',
			'value' => $this->request->data("$assocPath.te_discover_number"),
			'placeholder' => 'Discover #',
			'label' => false,
			'wrapInput' => false,
			// 'div' => 'col col-md-2',
			'beforeInput' => '<span class="contentModuleTitle">Discover:<br /></span><div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
										'onClick' => "toggleShowPwField('MerchantUwVolumeTeDiscoverNumber')")) .'</span>',
			'afterInput' => '</div>'
	));

	echo $this->Form->input("$assocPath.te_jcb_number", array(
			'type' => 'password',
			'value' => $this->request->data("$assocPath.te_jcb_number"),
			'placeholder' => 'JCB #',
			'label' => false,
			'wrapInput' => false,
			// 'div' => 'col col-md-2',
			'beforeInput' => '<span class="contentModuleTitle">JCB:<br /></span><div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
										'onClick' => "toggleShowPwField('MerchantUwVolumeTeJcbNumber')")) .'</span>',
			'afterInput' => '</div>'
	));


	echo $this->Form->hidden("$assocPath.encrypt_fields");
	if (!empty($this->request->data("$assocPath.id"))) {
		// the hidden field for "MerchantUwVolume.merchant_id is placed in the parent view where this element is being rendered
		echo $this->Form->hidden("$assocPath.id"); 
	}
?> 
		    
		