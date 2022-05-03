<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Business Information', '/Addresses/business_info/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Merchant Owners');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Business Information')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php
echo $this->Form->create('MerchantOwner', array(
	'inputDefaults' => array(
			'div' => 'form-inline',
			'wrapInput' => 'col col-md-12',
			'class' => 'form-control',
			'label' => array('class' => 'col col-md-1 control-label')
		),
		'class' => 'form-horizontal'
	));
?>

<table class="table-condesed dontSpill">
	<tr>
		<td class='twoColumnGridCell'>
			<span class="contentModuleTitle">Ownership Information: </span><br/>
		</td>
	</tr>
	<tr>
		<td class='twoColumnGridCell'>
			<div style="overflow-y: auto; max-width:1600px">
				<table>
					<tr>
						<?php
						echo $this->Form->hidden('Request.isBulkedData', array('value' => true));
						$ownCount = count(Hash::extract($this->request->data, 'MerchantOwner.{n}'));
						for ($x = 0; $x < $ownCount + 1; $x++) : // one additional to allow creating a new owner
							?> 
							<td class="noBorders nowrap">
								<span style="display:block;width:260px" >
									<span class="contentModuleTitle"> Owner/Partner/Officer (<?php echo $x + 1; ?>):</span>
									<table>
										<tr>
											<td class="noBorders"><strong>Name: </strong></td>
											<td class="noBorders">
												<?php echo (!empty($this->request->data['MerchantOwner'][$x]['id']))? $this->Form->hidden("MerchantOwner.$x.id") : ''; ?>
												<?php echo $this->Form->hidden("MerchantOwner.$x.merchant_id", array('value' => $this->request->data['Merchant']['id'])); ?>
												<?php echo $this->Form->input("MerchantOwner.$x.owner_name", array('div' => false, 'label' => false)); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Title: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.owner_title", array('div' => false, 'label' => false)); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Address: </strong></td>
											<td class="noBorders">
												<?php echo (!empty($this->request->data['MerchantOwner'][$x]['Address']['id']))? $this->Form->hidden("MerchantOwner.$x.Address.id"): ''; ?>
												<?php echo $this->Form->hidden("MerchantOwner.$x.Address.merchant_id", array('value' => $this->request->data['Merchant']['id'])); ?>
												<?php echo $this->Form->hidden("MerchantOwner.$x.Address.address_type_id", array('value' => $addressTypes['owner_address'])); ?>
												<?php echo $this->Form->hidden("MerchantOwner.$x.Address.merchant_owner_id"); ?>
												<?php echo $this->Form->input("MerchantOwner.$x.Address.address_street", array('div' => false, 'label' => false)); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>City: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.Address.address_city", array('div' => false, 'label' => false)); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>State: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.Address.address_state", array('div' => false, 'label' => false, 'options' => $usStates, 'empty' => '--')); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Zip: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.Address.address_zip", array('div' => false, 'label' => false)); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Phone: </strong></td>
											<td class="noBorders">
												<?php echo $this->Form->input("MerchantOwner.$x.Address.address_phone", array('wrapInput' => 'col col-md-9', 'div' => false, 'label' => false)); ?>
												<?php echo $this->Form->input("MerchantOwner.$x.Address.address_phone_ext", array('wrapInput' => 'col col-md-2', 'label' => false, 'style' => 'width:50px', 'placeholder' => 'Ext.')); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Phone 2: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.Address.address_phone2", array('wrapInput' => 'col col-md-9', 'div' => false, 'label' => false)); ?>
												<?php echo $this->Form->input("MerchantOwner.$x.Address.address_phone2_ext", array('wrapInput' => 'col col-md-2', 'label' => false, 'style' => 'width:50px', 'placeholder' => 'Ext.')); ?></td>
										</tr>
										<tr>
											<td class="noBorders"><strong>Equity: </strong></td>
											<td class="noBorders"><?php echo $this->Form->input("MerchantOwner.$x.owner_equity", array('div' => false, 'label' => false)); ?></td>
										</tr>   
										<tr>   
											<td class="noBorders"><strong>SSN: </strong></td>
											<td class="noBorders"><?php 
											$editViewSSNHtml = '';
											if (!empty($this->request->data("MerchantOwner.$x.owner_social_sec_no"))) {
												if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
													$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/MerchantOwners/ajaxAddAndEdit';
													$editSSNHtml = $this->Html->tag('span', 
														$this->Html->editImageLink('javascript:void(0)', array("title" => "Edit", "class" => "icon", "data-toggle" => "modal", "data-target" => "#myModal", 
															'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')"), "/img/icon_pencil_small.gif"), 
														array('class' => 'input-group-addon col-md-1'));
												}
												echo '<div class="col col-md-12">';
												echo $this->Html->tag(
													'div',
													'<div class="form-control input-sm"style="width:75%"> ***-**-' . $this->request->data("MerchantOwner.$x.owner_social_sec_no_disp"). '</div>'. 
													$editSSNHtml,
													array('class' => 'input-group col-md-12')
												);
												echo "</div>";
													
											} else {

												if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
													$editSSNHtml = $this->Html->tag('span', 
														$this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", "style" => "margin-left:-7px!important;",
															'onClick' => "toggleShowPwField('MerchantOwner".$x."OwnerSocialSecNo')")), 
														array('class' => 'input-group-addon col-md-1'));
												}
												echo $this->Html->tag(
													'div',
													$this->Form->input("MerchantOwner.$x.owner_social_sec_no", array('wrapInput' => 'row col-md-8', 'div' => false, 'label' => false, 'type' => 'password', 'autocomplete' => 'off')). 
													$editSSNHtml,
													array('class' => 'input-group col-md-12')
												);
											}


										?></td>
										</tr>
									</table>
								</span>
							</td>
						<?php endfor; ?>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td>
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
	echo $this->element('modalDialog') 
?> 	 
<script type='text/javascript'>activateNav('AddressesBusinessInfo'); </script>
