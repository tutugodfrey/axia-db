<?php
/* Drop breadcrumb */
$this->Html->addCrumb($merchant['Merchant']['merchant_dba'], '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Business Information', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Merchant Business Information'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<table>
    <tr>
        <td class="twoColumnGridCell">
            <span class="contentModuleTitle">Locale Information
				<?php
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit')) {
					//Using one of the available address id's to access the edit view
					$addsId = (empty($businessAddresses['Corporate_Address']['id']))? $businessAddresses['Business_Address']['id'] : $businessAddresses['Corporate_Address']['id'];
					echo $this->Html->editImageLink(array('action' => 'business_info_edit', $addsId));
				}
				?></span><br /><br />
            <span style="display:inline-block; width:250px">
                <span class="contentModuleTitle">Corporate Address:</span><br />
				<?php if (!empty($businessAddresses['Corporate_Address']['id'])) : ?>
					<?php echo h($businessAddresses['Corporate_Address']['address_title']) ?><br />
					<?php echo h($businessAddresses['Corporate_Address']['address_street']) ?><br />
					<?php echo h($businessAddresses['Corporate_Address']['address_city']) . " " . h($businessAddresses['Corporate_Address']['address_state']) . ", " . h($businessAddresses['Corporate_Address']['address_zip']) ?>
				<?php else: ?>
					<br /><em>-- None provided --</em> <br /><br />
				<?php endif; ?>
            </span><br />

            <span style="display:inline-block; width:250px" >
                <br /><span class="contentModuleTitle">Business Address:</span><br />
				<?php if (!empty($businessAddresses['Business_Address']['id'])) : ?>
					<?php echo h($businessAddresses['Business_Address']['address_title']) ?><br />
					<?php echo h($businessAddresses['Business_Address']['address_street']) ?><br />
					<?php echo h($businessAddresses['Business_Address']['address_city']) . " " . h($businessAddresses['Business_Address']['address_state']) . ", " . h($businessAddresses['Business_Address']['address_zip']) ?>
				<?php else: ?>
					<br /><em>-- None provided --</em> <br /><br />
				<?php endif; ?>
            </span><br />   

            <span style="display:inline-block; width:250px" >
                <br /><span class="contentModuleTitle">Mail Address: </span><br />
				<?php if (!empty($businessAddresses['Mail_Address']['id'])) : ?>
					<?php echo h($businessAddresses['Mail_Address']['address_title']) ?><br />
					<?php echo h($businessAddresses['Mail_Address']['address_street']) ?><br />
					<?php echo h($businessAddresses['Mail_Address']['address_city']) . " " . h($businessAddresses['Mail_Address']['address_state']) . ", " . h($businessAddresses['Mail_Address']['address_zip']) ?>
				<?php else: ?>
					<br /><em>-- None provided --</em> <br /><br />
				<?php endif; ?>
            </span><br />

		</td>
		<td class="twoColumnGridCell">
			<span style="display:inline-block; width:250px" >
                <br /><span class="contentModuleTitle">Contact Information&nbsp;&nbsp;</span><br />
                <strong>Corporate Phone: </strong>
				<?php
				echo!empty($businessAddresses['Corporate_Address']['address_phone']) ?
						h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $businessAddresses['Corporate_Address']['address_phone'])) : '--';
				?><br />
                <strong>Corporate Fax: </strong>
				<?php
				echo!empty($businessAddresses['Corporate_Address']['address_fax']) ?
						h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $businessAddresses['Corporate_Address']['address_fax'])) : '--';
				?>                
                <br /><br />
                <strong>Business Phone: </strong>                
				<?php
				echo!empty($businessAddresses['Business_Address']['address_phone']) ?
						h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $businessAddresses['Business_Address']['address_phone'])) : '--';
				?>                
                <br />                
                <strong>Business Fax: </strong>
				<?php
				echo!empty($businessAddresses['Business_Address']['address_fax']) ?
						h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $businessAddresses['Business_Address']['address_fax'])) : '--';
				?>                 
                <br /><br />                
                <strong>Contact: </strong>
				<?php
				echo!empty($businessData['Merchant']['merchant_contact']) ?
						h($businessData['Merchant']['merchant_contact']) : '--';
				echo!empty($businessData['Merchant']['merchant_email']) ?
						'<br /> <a href="mailto:' . h($businessData['Merchant']['merchant_email']) . '">' . h($businessData['Merchant']['merchant_email']) . '</a>' : '';
				if (!empty($businessData['Merchant']['chargebacks_email'])) {
						echo '<br /><strong>Email for chargeback notice: </strong><br /> <a href="mailto:' . h($businessData['Merchant']['chargebacks_email']) . '">' . h($businessData['Merchant']['chargebacks_email']) . '</a>';
				}
				echo!empty($businessData['Merchant']['merchant_url']) ?
						'<br /> <a href="http://' . h($businessData['Merchant']['merchant_url']) . '" target="_blank">' . h($businessData['Merchant']['merchant_url']) . '</a>' : '';
				?>                 
                <br /><br />
                <strong>Mail Contact: </strong>
				<?php
				echo!empty($businessData['Merchant']['merchant_mail_contact']) ?
						h($businessData['Merchant']['merchant_mail_contact']) . '<br />' : '--';
				?>                
                <br />                        
            </span><br />
		</td>
    </tr>
  </table>
  <table>
    <tr >
        <td colspan="2">
            <span class="contentModuleTitle">Ownership Information
				<?php
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					$urlAction = array('controller' => 'MerchantOwners', 'action' => 'edit');
					if (!empty($merchantOwners['MerchantOwner'][0]['id'])) {
						$urlAction[] = $merchantOwners['MerchantOwner'][0]['id'];
					} else {
						//Pass merchant id as named param to create new record
						$urlAction['merchant_id'] = $merchant['Merchant']['id'];
					}
					echo $this->Html->editImageLink($urlAction);
				}
				?></span><br /><br />

			<span style="display:inline-block; max-width: 250px" >
				<strong>Ownership Type: </strong>

				<?php
				if (!empty($businessData['Merchant']['merchant_ownership_type']))
					echo h($businessData['Merchant']['merchant_ownership_type']);
				?>
				<br />
				<strong>Federal Tax ID: </strong>
				<?php
				if (!empty($businessData['Merchant']['merchant_tin_disp'])) {
					echo 'xx-xxx' . h($businessData['Merchant']['merchant_tin_disp']);
					echo '&nbsp;';
					if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/Merchants/ajaxDisplayDecryptedVal/merchant_tin';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
				}
				echo '&nbsp';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit')) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/Merchants/ajaxAddAndEdit';
					echo $this->Html->editImageLink('javascript:void(0)', array("data-toggle" => "modal", "data-target" => "#myModal", 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')"));
				}
				?>                        
				<br />
				<strong>D&B: </strong>
				<?php
				if (!empty($businessData['Merchant']['merchant_d_and_b_disp'])) {
					echo 'xx-xxx' . h($businessData['Merchant']['merchant_d_and_b_disp']);
					echo '&nbsp;';
					if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/Merchants/ajaxDisplayDecryptedVal/merchant_d_and_b';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
				}
				echo '&nbsp';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit')) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/Merchants/ajaxAddAndEdit';					
					echo $this->Html->editImageLink('javascript:void(0)', array("data-toggle" => "modal", "data-target" => "#myModal", 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')"));
				}
				?>
			</span><br />
			<div>
                <table cellpadding="0" cellspacing="0" >
                    <tr>
						<td style="border-bottom:0px"><span style="display:block;" >
						<?php
						$x = 1;
						$ownersCount = count(Hash::get($merchantOwners, "MerchantOwner", array()));

						foreach (Hash::extract($merchantOwners, "MerchantOwner.{n}.Address") as $idx => $ownersAddress) :
							?> 
							<div class="col col-sm-6 col-md-6 list-group-item">
								<?php if ($ownersCount > 1 && $this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
									echo $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', 
										[
											'controller' => 'MerchantOwners',
											'action' => 'delete',
											$merchantOwners['MerchantOwner'][$idx]['id']
										],
										[
											'class' => 'pull-right btn btn-xs btn-danger',
											'data-original-title' => "Completely deletes everything about this Owner/Officer.",
											'data-placement' => "top",
											'data-toggle' => "tooltip",
											'escape' => false,
											'confirm' => "Everything (including address information) about {$merchantOwners['MerchantOwner'][$idx]['owner_name']} will be deleted!\n Are you sure?"
										]);
								}
								?>
									<br /><span class="contentModuleTitle"> Owner/Partner/Officer (<?php echo $x; ?>):</span>
									<table class="table-condensed table-hover">
										<tr>
											<td style="border-bottom:0px; width:50%" class="nowrap">
												<span style="display:inline-block;vertical-align: top;">
													<?php echo !empty($merchantOwners['MerchantOwner'][$idx]['owner_name']) ? h($merchantOwners['MerchantOwner'][$idx]['owner_name']) : ''; ?><br />
													<?php echo !empty($merchantOwners['MerchantOwner'][$idx]['owner_title']) ? h($merchantOwners['MerchantOwner'][$idx]['owner_title']) : ''; ?><br />
													<?php
													echo (!empty($ownersAddress['address_street']) ? h($ownersAddress['address_street']) . ' <br />' : '' ) .
													(!empty($ownersAddress['address_city']) ? h($ownersAddress['address_city']) . ' ' : '' ) .
													(!empty($ownersAddress['address_state']) ? h($ownersAddress['address_state']) . ', ' : '' ) .
													(!empty($ownersAddress['address_zip']) ? h($ownersAddress['address_zip']) : '' )
													?>                                    
												</span>
											</td>
											<td style="border-bottom:0px">
												<span style="white-space: nowrap">
													<strong>Equity:</strong>
													<?php echo!empty($merchantOwners['MerchantOwner'][$idx]['owner_equity']) ? h($merchantOwners['MerchantOwner'][$idx]['owner_equity']) . '%' : '--'; ?>
													<br />
													<strong>SSN:</strong>
													<?php
													if (!empty($merchantOwners['MerchantOwner'][$idx]['owner_social_sec_no_disp'])) {
														echo 'xxx-xx-' . h($merchantOwners['MerchantOwner'][$idx]['owner_social_sec_no_disp']);
														echo '&nbsp;';
														if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
															$ajaxUrl = '/Users/checkDecrypPassword/' . $merchantOwners['MerchantOwner'][$idx]['id'] . '/MerchantOwners/ajaxDisplayDecryptedVal/owner_social_sec_no';
															echo $this->Html->modalDecryptIcon($ajaxUrl);
														}
													} else
														echo '--';

													echo '&nbsp';
													if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
														$ajaxUrl = '/Users/checkDecrypPassword/' . $merchant['Merchant']['id'] . '/MerchantOwners/ajaxAddAndEdit';
														echo $this->Html->editImageLink('javascript:void(0)', array("title" => "Edit", "class" => "icon", "data-toggle" => "modal", "data-target" => "#myModal", 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')"), "/img/icon_pencil_small.gif");
													}
													?>
												</span>
											</td>
										</tr>
									</table>
								</span>
							</div>
							<?php
								// if ($x%2 ===0) {
								// 	echo "<span class='clearfix'></span>";
								// }
							?>
							<?php
							$x += 1;
						endforeach;
						?>
						</td>
                    </tr>
                </table>
			</div>
        </td>
    </tr>
</table>
<table>
    <tr>
        <td class="twoColumnGridCell">
            <span class="contentModuleTitle">Electronic Debit / Credit Authorization
				<?php
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					echo $this->Html->editImageLink(array('controller' => 'MerchantBanks', 'action' => 'edit', $merchantBank['MerchantBank']['id']));
				}
				?></span><br /><br />
			<span style="display:inline-block; width:30em" >
				<strong>Bank Information:</strong><br />
				<?php
				echo (!empty($merchantBank['MerchantBank']['bank_name']) ? h($merchantBank['MerchantBank']['bank_name']) . '<br />' : '<br />');
				echo (!empty($bankAddresses['address_street']) ? h($bankAddresses['address_street']) . ' <br />' : '' ) .
				(!empty($bankAddresses['address_city']) ? h($bankAddresses['address_city']) . ' ' : '' ) .
				(!empty($bankAddresses['address_state']) ? h($bankAddresses['address_state']) . ', ' : '' ) .
				(!empty($bankAddresses['address_zip']) ? h($bankAddresses['address_zip']) : '' )
				?>
				<?php echo $this->element('Layout/Merchant/merchantBanking') ?>
			</span>                

        </td>
        <td class="twoColumnGridCell">
            <div class="contentModuleTitle">Trade References:</div><br />
				<?php if (!empty($businessData['MerchantReference'])) : ?>
				<div class="row">
						<?php foreach ($businessData['MerchantReference'] as $key => $bref): ?>
						<div class="col-xs-3">
							<span class="contentModuleHeader">Reference <?php echo $key + 1 ?></span><br />
							<?php echo h($bref['business_name']); ?> <br />
						<?php echo h($bref['person_name']); ?> <br />
						<?php echo h($bref['phone']); ?> 
						</div>
				<?php endforeach; ?>
				</div>
<?php endif; ?>
        </td>
    </tr>
</table>
<?php echo $this->element('modalDialog') ?>  
<script type='text/javascript'>activateNav('AddressesBusinessInfo'); </script>