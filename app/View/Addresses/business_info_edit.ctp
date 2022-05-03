<?php
/* Drop breadcrumb */
$this->Html->addCrumb($merchant['Merchant']['merchant_dba'], '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Business Information', '/' . $this->name . '/' . 'business_info' . '/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Business Addresses');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Merchant Business Information'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php
echo $this->Form->create('Address', array(
	'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => 'col col-md-3',
			'class' => 'form-control',
			'label' => array('class' => 'col col-md-2 control-label')
		),
		'class' => 'form-horizontal'
	));
?>
<table><tr>
        <td class="twoColumnGridCell">
			<span class="contentModuleHeader">Corporate Address</span>
			<?php
			/* Merchant hasMany Addresses CORP_ADDRESS is first */
			echo $this->Form->hidden('Request.isBulkedData', array('value' => true));
			echo $this->Form->hidden('Address.0.id');
			echo $this->Form->hidden('Address.0.merchant_id');
			echo $this->Form->hidden('Address.0.address_type_id');
			echo $this->Form->input('Address.0.address_title', array('label' => 'Name'));
			echo $this->Form->input('Address.0.address_street', array('label' => 'Address'));
			echo $this->Form->input('Address.0.address_city', array('label' => 'City'));
			echo $this->Form->input('Address.0.address_state', array('label' => 'State', 'options' => $usStates, 'empty' => '--'));
			echo $this->Form->input('Address.0.address_zip', array('label' => 'Zip'));
			?>    
		</td>
		<td class="twoColumnGridCell">
			<span class="contentModuleHeader">Contact Information</span>
			<?php
			echo $this->Form->input('Address.0.address_phone', array('label' => 'Corporate Phone:'));
			echo $this->Form->input('Address.0.address_phone2', array('label' => 'Corporate Phone 2:'));
			echo $this->Form->input('Address.0.address_fax', array('label' => 'Corporate Fax:'));
			?>
			<br />
		</td>                   
    </tr>                   
</table>

<table><tr>
		<td class="twoColumnGridCell">
			<span class="contentModuleHeader">Site Address</span>
			<?php
			/* Merchant hasMany Addresses BUSINESS_ADDRESS */
			echo $this->Form->hidden('Address.1.id');
			echo $this->Form->hidden('Address.1.merchant_id');
			echo $this->Form->hidden('Address.1.address_type_id');
			echo $this->Form->input('Address.1.address_title', array('label' => 'Name:'));
			echo $this->Form->input('Address.1.address_street', array('label' => 'Address:'));
			echo $this->Form->input('Address.1.address_city', array('label' => 'City:'));
			echo $this->Form->input('Address.1.address_state', array('label' => 'State:', 'options' => $usStates, 'empty' => '--'));
			echo $this->Form->input('Address.1.address_zip', array('label' => 'Zip:'));
			?>
		</td>                   
		<td class="twoColumnGridCell">
			<span class="contentModuleHeader">Contact Information</span>
			<table style="width:300px">
				<tr>
					<td  class="noBorders">
						<?php echo $this->Form->input('Address.1.address_phone', array('wrapInput' => 'col col-md-7', 'label' => array('class' => 'col col-md-5 control-label', 'text' => 'Business Phone:'))); ?>
					</td>
					<td  class="noBorders">
						<?php echo $this->Form->input('Address.1.address_phone_ext', array('label' => array('class' => 'col col-md-1 col-md-pull-1', 'text' => 'x:'), 'wrapInput' => 'col col-md-7 col-md-pull-3', 'placeholder' => 'Ext.')); ?>
					</td>
				</tr>
				<tr>
					<td  class="noBorders">
						<?php echo $this->Form->input('Address.1.address_phone2', array('wrapInput' => 'col col-md-7', 'label' => array('class' => 'col col-md-5 control-label', 'text' => 'Business Phone 2:'))); ?>
					</td>
					<td  class="noBorders">
						<?php echo $this->Form->input('Address.1.address_phone2_ext', array('label' => array('class' => 'col col-md-1 col-md-pull-1', 'text' => 'x:'), 'wrapInput' => 'col col-md-7 col-md-pull-3', 'placeholder' => 'Ext.')); ?>
					</td>
				</tr>
				<tr>              
					<td  class="noBorders">
						<?php echo $this->Form->input('Address.1.address_fax', array('wrapInput' => 'col col-md-7', 'label' => array('class' => 'col col-md-5 control-label', 'text' => 'Business fax:'))); ?>
					</td>
				</tr>
			</table>
			<br />
		</td>                   
    </tr>                   
</table>
<table><tr>
		<td class="twoColumnGridCell">
			<span class="contentModuleHeader">Mailing Address</span>
			<?php
			/* Merchant hasMany Addresses MAIL_ADDRESS */
			if (!empty($this->request->data('Address.2.id'))) {
				echo $this->Form->hidden('Address.2.id');
			}
			echo $this->Form->hidden('Address.2.merchant_id', ['value' => $merchant['Merchant']['id']]);
			echo $this->Form->hidden('Address.2.address_type_id', ['value' => AddressType::MAIL_ADDRESS]);
			echo $this->Form->input('Address.2.address_title', array('label' => 'Name:'));
			echo $this->Form->input('Address.2.address_street', array('label' => 'Address:'));
			echo $this->Form->input('Address.2.address_city', array('label' => 'City:'));
			echo $this->Form->input('Address.2.address_state', array('label' => 'State:', 'options' => $usStates, 'empty' => '--'));
			echo $this->Form->input('Address.2.address_zip', array('label' => 'Zip:'));
			?>
		</td>                   
		<td class="twoColumnGridCell">
			<span class="contentModuleHeader">Contact Information</span>
			<?php
			echo $this->Form->input('Merchant.id', array('label' => 'Email:'));
			echo $this->Form->input('Merchant.merchant_email', array('label' => 'Email:'));
			echo $this->Form->input('Merchant.merchant_url', array('label' => 'Website:'));
			echo $this->Form->input('Merchant.merchant_contact', array('label' => 'Contact:'));
			echo $this->Form->input('Merchant.merchant_mail_contact', array('label' => 'Mail Contact:'));
			echo $this->Form->input('Merchant.chargebacks_email', array('label' => 'Email for chargeback notice:'));
			?>
			<br />
		</td>                   
    </tr>
</table>
<table>
	<tr>
		<td class="threeColumnGridCell" colspan="3">
			<?php
			if ($isEditLog) {
				echo $this->Form->hidden('MerchantNote.0.id');
			}
			echo $this->element('Layout/Merchant/merchantNoteForChanges');
			?>
		</td>
	</tr>
</table>
<?php 
echo $this->element('Layout/Merchant/mNotesDefaultBttns');
echo $this->Form->end();
?>
<script type='text/javascript'>activateNav('AddressesBusinessInfo'); </script>