<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/' . $this->action . '/' . $merchant[Inflector::singularize($this->name)]['id']);
?>
<input type="hidden" id="thisViewTitle"
	   value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Overview')); ?>"/>
<input type="hidden" id="
" value=1/>
	<div class="col-xs-12 col-md-12">
		<span class="contentModuleTitle">Account Information</span>
		<?php
		$editIsAllowed = $this->Rbac->isPermitted('Merchants/edit');
		if ($editIsAllowed) {
			echo $this->Html->image("editPencil.gif", array("title" => "Edit " . h($merchant['Merchant']['merchant_dba']),
				"class" => "icon", 'url' => array('action' => 'edit', $merchant['Merchant']['id'])));
		}
		if ($this->Rbac->isPermitted('Merchants/delete')) {

			if ($merchant['Merchant']['active']) {
				echo $this->Form->postLink($this->Html->image("green_orb.gif", array('data-toggle' => "tooltip",
					'data-placement' => "right", "title" => "Click to deactivate " . $merchant['Merchant']['merchant_dba'],
					"class" => "icon", 'onmouseover' => 'this.src=\'/img/red_orb.png\'', 'onmouseout' => 'this.src=\'/img/green_orb.gif\'')), array(
					'controller' => 'merchants', 'action' => 'delete', $merchant['Merchant']['id'],
					$merchant['Merchant']['active']), array('escape' => false, 'confirm' => __('Are you sure you want to deactivate %s?', $merchant['Merchant']['merchant_dba'])));
			} else {
				echo $this->Form->postLink($this->Html->image("red_orb.png", array('data-toggle' => "tooltip",
					'data-placement' => "right", "title" => "Click to re-activate " . $merchant['Merchant']['merchant_dba'],
					"class" => "icon", 'onmouseover' => 'this.src=\'/img/green_orb.gif\'', 'onmouseout' => 'this.src=\'/img/red_orb.png\'')), array(
					'controller' => 'merchants', 'action' => 'delete', $merchant['Merchant']['id'],
					$merchant['Merchant']['active']), array('escape' => false, 'confirm' => __('Re-activate %s?', $merchant['Merchant']['merchant_dba'])));
			}
		} else {
			if ($merchant['Merchant']['active']) {
				echo $this->Html->image("green_orb.gif", array("class" => "icon"));
			} else {
				echo $this->Html->image("red_orb.png", array("class" => "icon"));
				echo ' <span class="label label-danger">Inactive</span>';
			}
		}
		if ($editIsAllowed && !empty($merchant['AssociatedExternalRecord']['id'])) { ?>
			<span class="label label-success" style="font-size:100%!Important" data-toggle="tooltip" data-placement="right" title= "Changes made to some fields in this account may be synced with this client's account in Salesfoce automatically.">
			<span class="glyphicon glyphicon-cloud-upload"></span> API enabled</span>
		<?php }

		// Check for open Rejects
		$tmpOpenRej = Hash::extract($merchant, 'MerchantReject.{n}[open=1]');
		if (!empty($tmpOpenRej)) {
			echo $this->Html->tag('span', __('Open Reject'), array('class' => 'open-reject-warning'));
		}
		?>
	</div>
<div class="row">
	<div class="col-md-4">
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('DBA:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<strong>
					<?php echo !empty($merchant['Merchant']['merchant_dba']) ? h($merchant['Merchant']['merchant_dba']) : __("--"); ?>
				</strong>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Address:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['AddressBus']['address_street']) ? h($merchant['AddressBus']['address_street']) : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('City:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo h($merchant['AddressBus']['address_city'] . ", " . $merchant['AddressBus']['address_state'] . " " . $merchant['AddressBus']['address_zip']); ?>
			</div>
		</div>

			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Business phone:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['AddressBus']['address_phone']) ? h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $merchant['AddressBus']['address_phone'])) : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Business fax:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['AddressBus']['address_fax']) ? h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $merchant['AddressBus']['address_fax'])) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Email:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['merchant_email']) ? "<a href='mailto:" . h($merchant['Merchant']['merchant_email']) . "'>" . h($merchant['Merchant']['merchant_email']) . "</a>" : 'Email N/A'; ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Email for chargeback notice:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['chargebacks_email']) ? "<a href='mailto:" . h($merchant['Merchant']['chargebacks_email']) . "'>" . h($merchant['Merchant']['chargebacks_email']) . "</a>" : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Website:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php 
					if (!empty($merchant['Merchant']['merchant_url']) && substr_count($merchant['Merchant']['merchant_url'], "Not Required") == 0) {
						$url = $merchant['Merchant']['merchant_url'];
						if (!preg_match("/^http(s?):\/\//i", $url)) {
							$url = "http://" . $url;
						}
						echo $this->Html->link(h($url), $url, ['target' => '_blank']);
					} else {
						echo __("--");
					}
				?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Ownership Type:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['merchant_ownership_type']) ? h($merchant['Merchant']['merchant_ownership_type']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Contact:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['merchant_contact']) ? h($merchant['Merchant']['merchant_contact']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Position:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['merchant_contact_position']) ? h($merchant['Merchant']['merchant_contact_position']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Client ID:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['client_id']) ? h($merchant['Client']['client_id_global']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Client Name:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['client_id']) ? h($merchant['Client']['client_name_global']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Organization:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Organization']['id']) ? h($merchant['Organization']['name']) : __("--"); ?>
			</div>
		</div>
		<?php if (!empty($merchant['Organization']['id'])): ?>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Region:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Region']['id']) ? h($merchant['Region']['name']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Subregion:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Subregion']['id']) ? h($merchant['Subregion']['name']) : __("--"); ?>
			</div>
		</div>
		<?php endif; 
		if ($merchant['Merchant']['is_acquiring_only'] || $merchant['Merchant']['is_pf_only']) {
		?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Client Type:'); ?></label>
				<div class="col-xs-6 col-md-6">
				<?php if ($merchant['Merchant']['is_acquiring_only'] === true) {
						echo __('- Acquiring client') . '<br/>';
					} 
					if ($merchant['Merchant']['is_pf_only']) {
						echo __('- Payment Fusion client');
					}
					?>
				</div>
			</div>
		<?php }?>

		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ contact email:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['SaqMerchant']['merchant_email']) ? h($merchant['SaqMerchant']['merchant_email']) : __("--"); ?>
			</div>
		</div>
			<?php if (!empty($merchant['Merchant']['reporting_user'])): ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Reporting User ID:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo h($merchant['Merchant']['reporting_user']); ?>
				</div>
			</div>
			<?php endif; ?>

		<?php if (!empty($merchant['SaqControlScan']['saq_type'])) : ?>

			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Type:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo h(__($merchant['SaqControlScan']['saq_type'])); ?>
				</div>
			</div>

		<?php elseif (!empty($merchant['MerchantPci']['saq_type'])) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Type:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo h(__($merchant['MerchantPci']['saq_type'])); ?>
				</div>
			</div>

		<?php else: ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Type:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo __('--'); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($merchant['SaqControlScan']['creation_date'])) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Entity:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo __('Controlscan'); ?>
				</div>
			</div>
		<?php elseif (!$pciQualified) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Entity:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo __(''); ?>
				</div>
			</div>
		<?php elseif (!empty($merchant['MerchantPci']['scanning_company'])) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Entity:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo h(__($merchant['MerchantPci']['scanning_company'])); ?>
				</div>
			</div>
		<?php else: ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Entity:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo __(''); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($merchant['SaqControlScan']['pci_compliance'])) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Status:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo $merchant['SaqControlScan']['pci_compliance'] == 'Yes' ? __('Compliant') : __('Non-Compliant'); ?>
				</div>
			</div>
		<?php elseif (!empty($merchant['MerchantPci']['saq_completed_date'])) : ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Status:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php
						$now = strtotime("now");
						$saqCompDatePlusOneYear = strtotime($merchant['MerchantPci']['saq_completed_date'] . " +1 year");
						echo $now < $saqCompDatePlusOneYear ? __('Compliant') : __('Non-Compliant');
					?>
				</div>
			</div>
		<?php else: ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('SAQ Status:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo __('--'); ?>
				</div>
			</div>
		<?php endif; ?>
			
		<?php 
			if (!empty($nonWomplyUsers)) { ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Womply is Disabled for:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo h(implode(',', $nonWomplyUsers)); ?>
				</div>
			</div>
			
			<?php } else { ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Womply Call Status:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo !empty($merchant['Merchant']['womply_status_id']) ? h($womplyStatus) : __("--"); ?>
			</div>
			</div>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Send to Womply:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo ($merchant['Merchant']['womply_merchant_enabled']) ? __('Enabled') : __("Disabled"); ?>
				</div>
			</div>
		<?php } ?>
		<div class="col-xs-12">
			<div class="panel panel-success">
				<div class="bg-success ">
					<div class="text-center center">
						<?php 
						$allowEditExtRecord = $this->Rbac->isPermitted('AssociatedExternalRecords/upsert');
						if ($allowEditExtRecord && !empty($merchant['AssociatedExternalRecord']['id'])) {
							echo $this->Html->editImageLink('javascript:void(0)', array(
								'class' => "pull-right",
								'onClick' => "enterSFdataEditModeBtn('". $merchant['Merchant']['id'] ."')"
							));
						}
						 ?>
						<strong>Related Salesforce Account Data </strong></div>
                </div>
				<div class="panel-body">
					<div id="sf_data_read_mode">
						<?php if (!empty($merchant['AssociatedExternalRecord']['id'])) : ?>
						
								<table class="table-striped table-hover">
									<?php foreach ($merchant['AssociatedExternalRecord']['ExternalRecordField'] as $externalFields): ?>
									<tr>
										<td class="dataCell noBorders strong"><?php echo $externalFields['field_name']; ?></td>
										<td class="dataCell noBorders"><?php echo $externalFields['value']; ?></td>
									</tr>
									<?php endforeach; ?>
								</table>

						<?php else: ?>
							<div class="text-center text-muted">
							<?php if ($allowEditExtRecord): ?>

								<?php echo  $this->Html->link("Add SF account data manually <span class='glyphicon glyphicon-plus small text-success'></span>"   , "javascript:void(0)", array(
									'onClick' => "enterSFdataEditModeBtn('". $merchant['Merchant']['id'] ."')",
									'class' => 'btn btn-xs btn-default',
									'escape' => false)); ?>

							<?php else: ?>
								<div class="text-center text-muted">--</div>
							<?php endif; ?>
							</div>
							

						<?php endif; ?>
					</div>
					<div id="ext_data_sf">
						<!--Edit mode fields will render here-->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('MID:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['merchant_mid']) ? h($merchant['Merchant']['merchant_mid']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('MID Type:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['MerchantType']['id']) ? h($merchant['MerchantType']['type_description']) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Related Acquiring MID:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['Merchant']['related_acquiring_mid']) ? h($merchant['Merchant']['related_acquiring_mid']) : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Sales Rep Name:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['User']['fullname']) ? h($merchant['User']['fullname']) : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Mgr 1:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['ManagerUser']['fullname']) ? h($merchant['ManagerUser']['fullname']) : __("--"); ?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Mgr 2:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($merchant['SecondManagerUser']['fullname']) ? h($merchant['SecondManagerUser']['fullname']) : __("--"); ?>
			</div>
		</div>
			<?php
			$timeType = array();
			foreach ($merchant['TimelineEntry'] as $timeEntries) {
				($timeEntries['timeline_item_id'] == $timelineItemIds['SUB']) ? $timeType['SUB'] = $this->Time->date($timeEntries['timeline_date_completed']) : '';
				($timeEntries['timeline_item_id'] == $timelineItemIds['INS']) ? $timeType['INS'] = $this->Time->date($timeEntries['timeline_date_completed']) : '';
			}
			$timeType['AGREEMENT_END'] = null;
			if (!empty($agreementEndDate)) {
				$timeType['AGREEMENT_END'] =  $this->AxiaTime->date($agreementEndDate['timeline_date_completed']);
			} elseif (!empty(Hash::get($merchant, 'UwStatusMerchantXref.0.datetime'))) {
					$timeType['AGREEMENT_END'] = $this->Time->date(Hash::get($merchant, 'UwStatusMerchantXref.0.datetime') . "+3 year");
					$timeType['AGREEMENT_END'] .= '<span class="small text-muted bg-warning center-block text-center">(Notice: This date has been automatically set based on approval date +3 years)</span>';
				}
			?>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Submitted Date:') ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($timeType['SUB']) ? $timeType['SUB'] : __("--"); ?>
			</div>
		</div>

			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Approval Date:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty(Hash::get($merchant, 'UwStatusMerchantXref.0.datetime'))? $this->AxiaTime->date(Hash::get($merchant, 'UwStatusMerchantXref.0.datetime')) : __("--"); ?>
			</div>
		</div>

		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Go-Live Date:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($timeType['INS']) ? $timeType['INS'] : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Agreement End:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($timeType['AGREEMENT_END']) ? $timeType['AGREEMENT_END'] : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Last Activity date:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo !empty($lastActivityDate) ? $this->AxiaTime->date($lastActivityDate) : __("--"); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Months Since Go-Live:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php echo $montsSinceInstall; ?>
			</div>
		</div>
		<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Card Types:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php
				if (!empty($merchant['CardType'])) {
					foreach ($merchant['CardType'] as $key => $cardAccepted) {
						echo (!empty($merchant['CardType'][$key + 1])) ? h($cardAccepted['card_type_description']) . ', ' : h($cardAccepted['card_type_description']);
					}
				} else {
					echo __("--");
				}
				?>
				&nbsp;
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Network:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php
				echo (!empty($merchant['Network']['network_description'])) ? h($merchant['Network']['network_description']) : __("--");
				?>
			</div>
		</div>
			<?php if ($this->Rbac->isPermitted('app/actions/Merchants/view/module/ntwrkS', true)): ?>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Back End Network:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo (!empty($merchant['Merchant']['back_end_network_id'])) ? h($merchant['BackEndNetwork']['network_description']) : __("--"); ?>
				</div>
			</div>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('ISO/Acquirer:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo (!empty($merchant['Merchant']['merchant_acquirer_id'])) ? h($merchant['MerchantAcquirer']['acquirer']) : __("--"); ?>
				</div>
			</div>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('BIN:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo (!empty($merchant['MerchantBin']['bin'])) ? h($merchant['MerchantBin']['bin']) : __("--"); ?>
				</div>
			</div>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Originally Signed Through:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo (!empty($merchant['Merchant']['original_acquirer_id'])) ? h($merchant['OriginalAcquirer']['acquirer']) : __("--"); ?>
				</div>
			</div>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Bet Network:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php echo (!empty($merchant['Merchant']['bet_network_id'])) ? h($merchant['BetNetwork']['name']) : __("--"); ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Cancellation Fee:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php
				echo (!empty($merchant['CancellationFee']['cancellation_fee_description'])) ? h($merchant['CancellationFee']['cancellation_fee_description']) : __("--");
				?>
			</div>
		</div>
			<?php if (!empty($merchant['MerchantCancellation']['date_completed'])) : ?>
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Cancellation Date:'); ?></label>
				<div class="col-xs-6 col-md-6">
					<?php
					echo (!empty($merchant['MerchantCancellation']['date_completed'])) ? $this->Time->date($merchant['MerchantCancellation']['date_completed']) : '';
					?>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Partner:'); ?></label>
			<div class="col-xs-6 col-md-6"> <?php
				if (!empty($merchant['Merchant']['partner_id'])) {
					echo h($merchant['Partner']['user_first_name'] . ' ' . $merchant['Partner']['user_last_name']);
					if ($this->Rbac->isPermitted('app/actions/Merchants/view/module/partners', true)) {
						echo '<em><br />';
						echo $merchant['Merchant']['partner_exclude_volume'] ? 'Exclude' : 'Include';
						echo ' volume from non referral total</em>';
					}
				} else {
					echo __("--");
				}
				?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Company:'); ?></label>
			<div class="col-xs-6 col-md-6"><?php
				if (!empty($merchant['Entity']['entity_name'])) {
					echo h($merchant['Entity']['entity_name']);
				} else {
					echo h("--");
				}
				?>
			</div>
		</div>
			<div class="col-xs-12">
			<label class="col-xs-5 col-md-5 control-label"><?php echo __('Brand:'); ?></label>
			<div class="col-xs-6 col-md-6">
				<?php
					if (!empty($merchant['Brand']['name'])) {
						echo h($merchant['Brand']['name']);
					} else {
						echo h("--");
					}
				?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="col-xs-12">

			
				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('MCC:'); ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['MerchantUw']['mcc']) ? h($merchant['MerchantUw']['mcc']) : __("--"); ?></div>
			</div>

			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('General Practice Type:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['Merchant']['general_practice_type']) ? h($merchant['Merchant']['general_practice_type']) : __("--"); ?></div>
			</div>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Specific Practice Type:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['Merchant']['specific_practice_type']) ? h($merchant['Merchant']['specific_practice_type']) : __("--"); ?></div>
			</div>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Business Type:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['Merchant']['merchant_bustype']) ? h($merchant['Merchant']['merchant_bustype']) : __("--"); ?></div>
			</div>

				<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Products/Services Sold:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['Merchant']['merchant_ps_sold']) ? h($merchant['Merchant']['merchant_ps_sold']) : __("--"); ?></div>
			</div>

			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Business Level:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['Merchant']['merchant_buslevel']) ? h($merchant['Merchant']['merchant_buslevel']) : __("--"); ?></div>
			</div>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('V/MC/DS Pricing Structure:') ?></label>
				<div class="col-xs-7 col-md-7"><?php echo !empty($merchant['MerchantPricing']['visa_bet_table_id']) ? h(Hash::get($betRateStructures, $merchant['MerchantPricing']['visa_bet_table_id']))  : __("--"); ?></div>
			</div>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Amex Pricing Structure:') ?></label>
				<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['MerchantPricing']['amex_bet_table_id']) ? h(Hash::get($betRateStructures, $merchant['MerchantPricing']['amex_bet_table_id']))  : __("--"); ?></div>
			</div>
				<?php if ($this->Rbac->isPermitted('app/actions/MerchantAches/view/module/hidenS1', true)): ?>
					<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Processing Rate:') ?></label>
					<div class="col-xs-6 col-md-6"><?php echo !empty($merchant['MerchantPricing']['processing_rate']) ? $this->Number->toPercentage($merchant['MerchantPricing']['processing_rate']) : __("--"); ?></div>
				</div>

					<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Per Item Fee:') ?></label>
					<div class="col-xs-6 col-md-6"><?php
						echo !empty($merchant['MerchantPricing']['mc_vi_auth']) ? $this->Number->currency($merchant['MerchantPricing']['mc_vi_auth'], 'USD', array(
							'after' => false, 'negative' => '-')) : __("--");
						?></div>
					</div>
				<?php endif; ?>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Discount Per Item:') ?></label>
				<div class="col-xs-6 col-md-6"><?php
					echo !empty($merchant['MerchantPricing']['discount_item_fee']) ? $this->Number->currency($merchant['MerchantPricing']['discount_item_fee'], 'USD', array(
						'after' => false, 'negative' => '-')) : __("--");
					?>
				</div>
			</div>
			<div class="col-xs-12">
				<label class="col-xs-5 col-md-5 control-label"><?php echo __('Reseller/Direct:') ?></label>
				<div class="col-xs-6 col-md-6"><?php
					echo !empty($merchant['Merchant']['source_of_sale']) ? $merchant['Merchant']['source_of_sale'] : __("--");
					?>
				</div>
			</div>

				<?php if ($this->Rbac->isPermitted('app/actions/Merchants/view/module/refResSctn', true)): ?>
					<div class="col-xs-12"><br/></div>
					<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Referrer:') ?></label>
					<div class="col-xs-6 col-md-6"><?php echo (!empty($merchant['Referer']['id'])) ? h($merchant['Referer']['user_first_name'] . ' ' . $merchant['Referer']['user_last_name']) : __("--"); ?></div>
				</div>

					<?php if (strpos($merchant['Merchant']['ref_p_type'], 'points') !== false) : ?>
						<?php $tmpTitleStr = ($merchant['Merchant']['ref_p_type'] === 'points') ? 'Subtracted from Gross Profit' : 'Calculate Only';
						?>

						<div class="col-xs-12">
						<label class="col-xs-5 col-md-5 control-label"><?php echo __('Ref Basis Points') ?></label>
						<div class="col-xs-6 col-md-6"><?php
							echo (!empty($merchant['Merchant']['ref_p_value'])) ? $this->Number->toPercentage($merchant['Merchant']['ref_p_value']) : __("--");
							echo " (" . $tmpTitleStr . ")"
							?></div>
						</div>

					<?php else : ?>
						<?php if (strpos($merchant['Merchant']['ref_p_type'], 'percentage') !== false) : ?>
							<?php $tmpTitleStr = $merchant['Merchant']['ref_p_type'] === 'percentage' ? 'Calculate Only' : 'Subtracted from Gross Profit';
							?>
							<div class="col-xs-12">
							<label class="col-xs-5 col-md-5 control-label"><?php echo __('Ref Percentage') ?></label>
							<div class="col-xs-6 col-md-6"><?php
								echo (!empty($merchant['Merchant']['ref_p_value'])) ? $this->Number->toPercentage(strpos($merchant['Merchant']['ref_p_type'], 'percentage') !== false ? $merchant['Merchant']['ref_p_value'] : $merchant['Referer']['RefererOptions']['ref_ref_perc']) : __("--");
								echo " (" . $tmpTitleStr . ")"
								?></div>
							</div>

						<?php endif; ?>
					<?php endif; ?>

					<?php if (!empty($merchant['Merchant']['ref_p_type'])) { ?>
						<div class="col-xs-12">
						<label class="col-xs-5 col-md-5 control-label"><?php echo __('% of GP:') ?></label>
						<div class="col-xs-6 col-md-6"><?php echo ($merchant['Merchant']['ref_p_pct'] > 0) ? $this->Number->toPercentage($merchant['Merchant']['ref_p_pct']) : 'Default'; ?></div>
					</div>
					<?php } ?>

					<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Reseller:') ?></label>
					<div class="col-xs-6 col-md-6"><?php echo (!empty($merchant['Reseller']['id'])) ? h($merchant['Reseller']['user_first_name'] . ' ' . $merchant['Reseller']['user_last_name']) : __("--"); ?></div>
				</div>

					<?php if (strpos($merchant['Merchant']['res_p_type'], 'points') !== false) : ?>
						<?php $tmpTitleStr = ($merchant['Merchant']['res_p_type'] === 'points') ? 'Subtracted from Gross Profit' : 'Calculate Only';
						?>

						<div class="col-xs-12">
						<label class="col-xs-5 col-md-5 control-label"><?php echo __('Res Basis Points') ?></label>
						<div class="col-xs-6 col-md-6"><?php
							echo (!empty($merchant['Merchant']['res_p_value'])) ? $this->Number->toPercentage($merchant['Merchant']['res_p_value']) : __("--");
							echo " (" . $tmpTitleStr . ")"
							?></div>
						</div>

					<?php else: ?>
						<?php if (strpos($merchant['Merchant']['res_p_type'], 'percentage') !== false) : ?>
							<?php $tmpTitleStr = $merchant['Merchant']['res_p_type'] === 'percentage' ? 'Calculate Only' : 'Subtracted from Gross Profit';
							?>
							<div class="col-xs-12">
							<label class="col-xs-5 col-md-5 control-label"><?php echo __('Res Percentage') ?></label>
							<div class="col-xs-6 col-md-6"><?php
								echo (!empty($merchant['Merchant']['res_p_value'])) ? $this->Number->toPercentage((strpos($merchant['Merchant']['res_p_type'], 'percentage') !== false) ? $merchant['Merchant']['res_p_value'] : $merchant['Reseller']['ResellerOptions']['ref_ref_perc']) : __("--");
								echo " (" . $tmpTitleStr . ")"
								?></div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (!empty($merchant['Merchant']['res_p_type'])) { ?>
						<div class="col-xs-12">
						<label class="col-xs-5 col-md-5 control-label"><?php echo __('% of GP:') ?></label>
						<div class="col-xs-6 col-md-6"><?php echo ($merchant['Merchant']['res_p_pct'] > 0) ? $this->Number->toPercentage($merchant['Merchant']['res_p_pct']) : 'Default'; ?></div>
					</div>
					<?php } ?>
				<?php endif; ?>
				<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Expected to Go-Live on:') ?></label>
					<div class="col-xs-6 col-md-6">
						<?php
							echo !empty($expectedInsDate['timeline_date_completed']) ? $this->AxiaTime->date($expectedInsDate['timeline_date_completed']) : __("--");
						?>
					</div>
				</div>
				<div class="col-xs-12">
					<label class="col-xs-5 col-md-5 control-label"><?php echo __('Group:') ?></label>
					<div class="col-xs-6 col-md-6">
						<?php echo !empty($merchant['Group']['group_description']) ? h($merchant['Group']['group_description']) : __("--"); ?></div>
				</div>
		</div>
	</div>
</div>
<hr/>
<div class="row">
	<?php if ($this->Rbac->isPermitted('Merchants/notes')) : ?>
	<div class="col-md-4">
		<ul class="list-group">
			<li class="list-group-item list-group-item-info text-center">
					<?php
					if ($this->Rbac->isPermitted('MerchantNotes/add') && $this->Rbac->isPermitted('app/actions/Merchants/view/module/addGeneralNote', true)) {
						echo $this->MerchantNote->addAjaxNoteButton('Add General Note ', 'addGnrNote', "Add General Note for " . h($merchant['Merchant']['merchant_dba']), ['class' => 'btn btn-success btn-xs']);
					}
					?>
					<?php
					echo $this->Html->link("Open Merchant's Notes " . $this->Html->image("icon_notes.gif", array(
							"title" => "Open Notes for " . h($merchant['Merchant']['merchant_dba']))), "/merchants/notes/" . $merchant['Merchant']['id'], array(
						'class' => 'btn btn-xs btn-info',
						'escape' => false));
					?>
			</li>
			<div id="addNote_frm" style="background: white; margin-bottom: 6px; display:none; z-index:100; " class="panel panel-primary roundEdges ">

			</div>
		<?php if (!empty($recentAndCriticalNotes)): ?>
			
			<?php foreach ($recentAndCriticalNotes as $noteData): ?>
			<li class="list-group-item">
				<div class="col-sm-12 col-md-12 bg-info">
					<span class="contentModuleTitle">
						<?php
						echo $this->Html->link($noteData['note_title'], array(
							'plugin' => false,
							'controller' => 'merchant_notes',
							'action' => 'edit',
							$noteData['id']
						));
						?>
					</span>
					<span class="nowrap pull-right">
						<?php
						if ($noteData['critical']) {
							echo $this->Html->image("icon_critical.png", array("title" => "Critical note!",
								"class" => "icon"));
						}

						if ($noteData['note_sent'] == true) {
							echo $this->Html->image("icon_email.gif", array("title" => "Note emailed to rep",
								"class" => "icon"));
						}
						if (!empty($noteData['flag_image'])) {
							echo $this->Html->image("" . $noteData['flag_image'], array("class" => "icon"));
						}
						if ($this->Rbac->isPermitted('MerchantNotes/edit')) {
							echo $this->Html->image("editPencil.gif", array("title" => "Edit note",
								"class" => "icon", 'url' => array('controller' => 'merchant_notes', 'action' => 'edit',
									$noteData['id'])));
						}
						?>
					</span>
				</div><br/>

					<?php 
					echo "posted by: " . trim(h($noteData['User']['fullname']));
					
					echo " on " . $this->MerchantNote->noteDateTime($noteData['note_date']) . "<br/>";
					echo nl2br(h(trim($noteData['note']))); ?>
			</li>
			<?php endforeach; ?>
		<?php endif; ?>
		</ul>

	</div>
	<?php endif; ?>
	<div class="col-md-4">
            <div class="contentModuleHeader"><?php echo __('Recently Approved Changes'); ?></div>
            <?php
			$filterStatus = MerchantNote::STATUS_COMPLETE;
			echo $this->element('MerchantNote/list', array('data' => Hash::get($merchant, "MerchantNote.{$filterStatus}")));
			$url = $this->MerchantNote->getMerchantNotesUrl($merchant['Merchant']['id'], array(
				'general_status' => $filterStatus,
				'note_type_id' => $changeRequestId
			));
			echo $this->Html->link(__('View entire change history for ') . Hash::get($merchant, 'Merchant.merchant_dba'), $url);
			?>
		
			<div class="contentModuleHeader"><?php echo __('Pending Requests'); ?></div>
            <?php
			$filterStatus = MerchantNote::STATUS_PENDING;
			echo $this->element('MerchantNote/list', array('data' => Hash::get($merchant, "MerchantNote.{$filterStatus}")));
			$url = $this->MerchantNote->getMerchantNotesUrl($merchant['Merchant']['id'], array(
				'general_status' => $filterStatus,
				'note_type_id' => $changeRequestId
			));
			echo $this->Html->link(__('View all pending requests for ') . h(Hash::get($merchant, 'Merchant.merchant_dba')), $url);
			?>
		
			<div class="contentModuleHeader"><?php echo __('Related Information'); ?></div>

			<div class="bullet">
				<a href="/CommissionReports/report">View your commission report for this account</a></div>
			<div class="bullet">
				<a href="/ResidualReports/report">View your residual report for this account</a></div>

	</div>
	<div class="col-md-4">
		<?php echo $this->element('MerchantRejects/merchant_timeline'); ?>
	</div>
</div>
<script type='text/javascript'>
activateNav('MerchantsView');
var merchantId = "<?php echo $merchant['Merchant']['id']; ?>";
$("#addGnrNote").on('click', function(e) {
	e.preventDefault();
	ajaxNote(merchantId, 'General Note','addNote_frm');
	objFader('addNote_frm');
});
	function enterSFdataEditModeBtn(merchantId) {
		$('#sf_data_read_mode').hide();
		$('#ext_data_sf').html('<img src="/img/indicator.gif" class="center-block">');
		renderContentAJAX('', '', '', 'ext_data_sf',  '/AssociatedExternalRecords/upsert/'+merchantId);
	}
</script> 