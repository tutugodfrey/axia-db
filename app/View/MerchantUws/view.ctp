<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Underwriting', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Underwriting')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/overallReadMode', true)) : ?>
		<span class="contentModuleTitle">Underwriting</span>
		<?php
		if ($this->Rbac->isPermitted('MerchantUws/edit')) {
			if (!empty($merchant['MerchantUw']['id'])) {
				$urlAction = array('controller' => 'MerchantUws', 'action' => 'edit', $merchant['MerchantUw']['id']);
			} else {
				//Pass merchant id as named param to create new record
				$urlAction = Router::url(array('controller' => 'MerchantUws', 'action' => 'edit', 'merchant_id' => $merchant['Merchant']['id']));
			}
			
			echo $this->Html->image("/img/editPencil.gif", array('url' => $urlAction));
		}
		?>
		<div>
			<table>
				<tr>
					<td class="twoColumnGridCell">

						<table style="width: auto" class="table table-condensed table-hover">
							<tr>
								<td class='contentModuleTitle'>Underwriting Status</td>
								<td class='contentModuleTitle'>Date & Time</td>
								<td class='contentModuleTitle'>Notes</td>
							</tr>
							<?php foreach ($assocUwData['uwSatuses'] as $key => $uwSatus): ?>
								<tr><td class="dataCell noBorders"><?php echo $uwSatus['UwStatus']['name']; ?></td>
									<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['UwStatusMerchantXref'][$key]['datetime'])) ? $this->Time->format('F j, Y h:i A', $merchant['Merchant']['UwStatusMerchantXref'][$key]['datetime']) : "--"; ?></td>
									<td class="noBorders"><?php echo (!empty($merchant['Merchant']['UwStatusMerchantXref'][$key]['notes'])) ? h($merchant['Merchant']['UwStatusMerchantXref'][$key]['notes']) : "--"; ?></td>
									<td class="dataCell noBorders">
										<?php
										if (!empty($merchant['Merchant']['UwStatusMerchantXref'][$key]['id'])) {
											echo $this->Form->postLink('<span class="glyphicon glyphicon-send text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Send status update to rep"</span>', array('controller' => 'MerchantUws', 'action' => 'view', $merchant['Merchant']['id'], $merchant['Merchant']['UwStatusMerchantXref'][$key]['id']), array('class'=>'btn-xs', 'escape' => false, 'confirm' => __('Are you sure you want to email %s?', h($merchant['User']['user_first_name'] . ' ' . $merchant['User']['user_last_name']))));
											if ($this->Rbac->isPermitted('UwStatusMerchantXreves/delete')) {
												$confirmMsg = "Remove \"%s\" status entry?\n";
												$confirmMsg .= ($uwSatus['UwStatus']['name'] === 'Approved')? "NOTE: Approval date in this merchant's Timeline will also be removed.\n(This cannot be undone)" : "(This cannot be undone)" ;

												echo $this->Form->postLink('<span class="glyphicon glyphicon-remove text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Remove entry"</span>', array('controller' => 'UwStatusMerchantXreves', 'action' => 'delete', $merchant['Merchant']['UwStatusMerchantXref'][$key]['id']), array('class'=>'btn-xs', 'escape' => false, 'confirm' => __($confirmMsg, $uwSatus['UwStatus']['name'])));												
											}
										}
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</td>
					<td>

						<table style="width: auto" class="table-hover">
							<tr><td colspan="2" class="dataCell noBorders">&nbsp;<!-- SPACER ROW--></td></tr>
							<tr><td class="dataCell noBorders">DBA</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['merchant_dba'])) ? h($merchant['Merchant']['merchant_dba']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">MID</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['merchant_mid'])) ? h($merchant['Merchant']['merchant_mid']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">New or Additional?</td>
			                <td class="dataCell noBorders">
			                <?php
			                    $appQuantities = GUIbuilderComponent::getAppQuantityTypeList();
			                    echo Hash::get($appQuantities, Hash::get($merchant, 'MerchantUw.app_quantity_type'), '--');
			                ?>
                			</td></tr>
							<tr><td class="dataCell noBorders">Expedited</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['expedited']) && $merchant['MerchantUw']['expedited'] === true) ? __("Yes") : __("No"); ?></td></tr>
							<tr><td class="dataCell noBorders">Bank</td>
								<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUw']['sponsor_bank_id']) ? h($merchant['MerchantUw']['SponsorBank']['bank_name']) : '--'; ?></td></tr>
							<tr><td class="dataCell noBorders">Tier Assignment</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['tier_assignment'])) ? h($merchant['MerchantUw']['tier_assignment']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Business Type</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['merchant_bustype'])) ? h($merchant['Merchant']['merchant_bustype']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Business Level</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['merchant_buslevel'])) ? h($merchant['Merchant']['merchant_buslevel']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">MCC</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['mcc'])) ? h($merchant['MerchantUw']['mcc']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Annual Volume</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['mo_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['mo_volume'] * 12, 'USD', array('after' => false, 'negative' => '-')) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Daily/Monthly Discount</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['discount_frequency'])) ? h($merchant['MerchantUwVolume']['discount_frequency']) : __("N/A"); ?></td></tr>
							<tr><td class="dataCell noBorders">Fees Collected Daily</td>
								<td class="dataCell noBorders"><?php echo ($merchant['MerchantUwVolume']['fees_collected_daily'] === true) ? __("Yes") : __("No"); ?></td></tr>
							<tr><td class="dataCell noBorders">Next Day Funding</td>
								<td class="dataCell noBorders"><?php echo ($merchant['MerchantUwVolume']['next_day_funding'] === true) ? __("Yes") : __("No"); ?></td></tr>
							<tr><td class="dataCell noBorders">Funding Delay - Sales</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['funding_delay_sales'])) ? h($merchant['MerchantUw']['funding_delay_sales']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Funding Delay - Credits</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['funding_delay_credits'])) ? h($merchant['MerchantUw']['funding_delay_credits']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Final Status</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['MerchantUwFinalStatus']['name'])) ? h($merchant['MerchantUw']['MerchantUwFinalStatus']['name']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Approved By</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['MerchantUwFinalApproved']['name'])) ? h($merchant['MerchantUw']['MerchantUwFinalApproved']['name']) : "--"; ?></td></tr>
							<tr><td class="dataCell noBorders">Date</td>
								<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['final_date'])) ? $this->Time->format('F j, Y', $merchant['MerchantUw']['final_date']) : "--"; ?></td></tr>
						</table>
					</td>
				</tr>
			</table>
		</div>

		<?php echo $this->element('Layout/Merchant/merchantAchVolumes'); ?>
	<?php endif; //end if permitted to see overallReadMode module ?>
	<?php
	if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/tneSection', true))
		echo $this->element('Layout/Merchant/tAndESection');
	?>
	<?php
	if ($this->Rbac->isPermitted('app/actions/UsersProductsRisks/view/module/readModule', true))
		echo $this->element('Layout/Merchant/riskAssessmentGrid');
	?>
	<?php
	// Continue overallReadMode section persmission
	if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/overallReadMode', true)):
		?>
		<div>
			<table>
				<tr>
					<td class="twoColumnGridCell">

						<table style="width: auto" class="table table-condensed table-hover">
							<tr>
								<td class="dataCell contentModuleTitle"><b>Required Information/Documents</b></td>
								<td class="dataCell contentModuleTitle"><b>Received</b></td>
								<td class="dataCell contentModuleTitle"><b>Notes</b></td>
							</tr>
						<?php foreach ($assocUwData['uwRequiredInfoDocs'] as $rDocs): 
								$r = null;
								foreach($merchant['Merchant']['UwInfodocMerchantXref'] as $idx => $infodocData) {
									if ($rDocs['UwInfodoc']['id'] === $infodocData['uw_infodoc_id']) {
				                        $r = $idx;
				                        break;
				                    }
								}
							?>
								
								<tr><td class="dataCell noBorders"><?php echo $rDocs['UwInfodoc']['name']; ?></td>
									<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['UwInfodocMerchantXref'][$r]['uw_received_id'])) ? h($merchant['Merchant']['UwInfodocMerchantXref'][$r]['UwReceived']['name']) : "--"; ?></td>
									<td class="noBorders"><?php echo (!empty($merchant['Merchant']['UwInfodocMerchantXref'][$r]['notes'])) ? h($merchant['Merchant']['UwInfodocMerchantXref'][$r]['notes']) : "--"; ?></td>
								</tr>
						<?php endforeach; ?>
							<tr>
								<td class="pull-right"><div class="pull-right">Credit %</div></td>
								<td colspan="2"><?php echo (!empty($merchant['MerchantUw']['credit_pct'])) ? $this->Number->toPercentage($merchant['MerchantUw']['credit_pct'], 2) : '--' ?></td>
							</tr>
							<tr>
								<td class="pull-right"><div class="pull-right">Chargeback %</div></td>
								<td colspan="2"><?php echo (!empty($merchant['MerchantUw']['chargeback_pct'])) ? $this->Number->toPercentage($merchant['MerchantUw']['chargeback_pct'], 2) : '--' ?></td>
							</tr>
						</table>
					</td>
					<td class="twoColumnGridCell">

						<table style="width: auto" class="table table-condensed table-hover">
							<tr>
								<td class="dataCell contentModuleTitle"><b>Other Information/Documents</td>
								<td class="dataCell contentModuleTitle"><b>Received</b></td>
								<td class="dataCell contentModuleTitle"><b>Notes</b></td>
							</tr>
							<?php
							foreach ($assocUwData['uwOtherInfoDocs'] as $otherDocs):
								$o = null;
								foreach($merchant['Merchant']['UwInfodocMerchantXref'] as $idx => $infodocData) {
									if ($otherDocs['UwInfodoc']['id'] === $infodocData['uw_infodoc_id']) {
				                        $o = $idx;
				                        break;
				                    }
								}
								?>
								<tr><td class="dataCell noBorders"><?php echo h($otherDocs['UwInfodoc']['name']); ?></td>
									<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['UwInfodocMerchantXref'][$o]['uw_received_id'])) ? h($merchant['Merchant']['UwInfodocMerchantXref'][$o]['UwReceived']['name']) : "--"; ?></td>
									<td class="noBorders"><?php echo (!empty($merchant['Merchant']['UwInfodocMerchantXref'][$o]['notes'])) ? h($merchant['Merchant']['UwInfodocMerchantXref'][$o]['notes']) : "--"; ?></td>
								</tr>
								<?php
							endforeach;
							?>
						</table>
					</td>
				</tr>
			</table>
		</div>
	<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/hiddenS1', true)): ?>
			<div>
				<table>
					<tr>
						<td class="twoColumnGridCell">

							<table style="width: auto" class="table table-condensed table-hover">
								<tr>
									<td class="dataCell contentModuleTitle"><b>Approval Information</b></td>
									<td class="dataCell contentModuleTitle"><b>Verified</b></td>
									<td class="dataCell contentModuleTitle"><b>Notes</b></td>
								</tr>
								<?php foreach ($assocUwData['approvalInfos'] as $n => $approvalInfo): ?>
									<?php
									if ($approvalInfo['UwApprovalinfo']['name'] === 'Acceptable Credit Report/OFAC') {
										$allowedModuleAccess = $this->Rbac->isPermitted('app/actions/MerchantUws/view/module/credScoreSection', true);
									} else {
										$allowedModuleAccess = true;
									}
									?>
									<?php if ($allowedModuleAccess) : ?>
										<tr><td class="dataCell noBorders"><?php echo h($approvalInfo['UwApprovalinfo']['name']); ?></td>
											<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['UwApprovalinfoMerchantXref'][$n]['uw_verified_option_id'])) ? h($merchant['Merchant']['UwApprovalinfoMerchantXref'][$n]['UwVerifiedOption']['name']) : "--"; ?></td>
											<td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['UwApprovalinfoMerchantXref'][$n]['notes'])) ? h($merchant['Merchant']['UwApprovalinfoMerchantXref'][$n]['notes']) : "--"; ?></td>
										</tr>
									<?php endif; ?>
						<?php endforeach; ?>
							</table>
						</td>
		<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/credScoreSection', true)): ?>
							<td class="twoColumnGridCell">
								<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
									<tr><td class="dataCell noBorders"><b>Merchant Credit Score</b></td></tr>
									<tr><td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUw']['credit_score'])) ? h($merchant['MerchantUw']['credit_score']) : '--'; ?></td>
									</tr>
								</table>
							</td>
		<?php endif; ?>
					</tr>
				</table>
			</div>
	<?php endif; ?>
	<?php echo $this->element('modalDialog') ?>
<?php endif; ?>
<script type='text/javascript'>activateNav('MerchantUwsView'); </script>