<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Business Information', '/Addresses/business_info/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Merchant Banking');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Equipment, Peripherals & Other Accessories'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<span class="contentModuleTitle">Electronic Debit / Credit Authorization</span>
<div class="row">
    <div class="col-sm-6 col-md-3">
		<?php
		if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
			$ajaxEditUrl = '/Users/checkDecrypPassword/' . $this->request->data('MerchantBank.id') . '/MerchantBanks/ajaxAddAndEdit';
			echo '<div class="form-group text-right"><strong> Update Accounts <span class="glyphicon glyphicon-arrow-right"></span></strong>' . $this->Html->editImage(array("data-toggle" => "modal", "data-target" => "#myModal", 'url' => 'javascript:void(0)', 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxEditUrl . "')")) .'</div>'; 
		}

		echo $this->Form->create('MerchantBank', array(
		'inputDefaults' => array(
			'wrapInput' => 'col col-md-6 col-sm-6',
			'label' => array('class' => 'col col-md-5 col-sm-6 control-label')
		),
		));
		?>	
		<?php
		/* Merchant hasMany Addresses but for this form we only need one at the first array element of reques->data */
		echo $this->Form->hidden('Request.isBulkedData', array('value' => true));
		echo $this->Form->hidden('Merchant.id', array('value' => $merchant['Merchant']['id']));
		echo $this->Form->hidden('Address.id');
		echo $this->Form->hidden('Address.merchant_id');
		echo $this->Form->hidden('Address.address_type_id');
		echo $this->Form->input('Address.address_title', array('label' => __('Bank Name')));
		echo $this->Form->input('Address.address_phone', array('label' => __('Phone Number')));
		echo $this->Form->input('Address.address_street', array('label' => __('Address')));
		echo $this->Form->input('Address.address_city', array('label' => __('City')));
		echo $this->Form->input('Address.address_state', array('label' => __('State')));
		echo $this->Form->input('Address.address_zip', array('label' => __('Zip')));

		
		if ($isEditLog) {
			echo $this->Form->hidden('MerchantNote.0.id');
		}
		echo $this->element('Layout/Merchant/merchantNoteForChanges');
		echo $this->element('Layout/Merchant/mNotesDefaultBttns');
		echo $this->Form->end();  ?> 
    </div>                   
</div>                  
<?php echo $this->element('modalDialog') ?>  
<script type='text/javascript'>activateNav('AddressesBusinessInfo'); </script>
