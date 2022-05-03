<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/view/' . $merchant[Inflector::singularize($this->name)]['id']);
$this->Html->addCrumb('PCI', '/' . $this->name . '/' . $this->action . '/' . $merchant[Inflector::singularize($this->name)]['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Overview')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<div class="mainContainer">
	<table cellpadding="0" cellspacing="0" border="0" class="containerTable">
		<tbody>
			<tr>
				<td colspan="3">
					<div class="contentModule">
						<span class="contentModuleTitle">
							PCI DSS Compliance
						</span>
						<?php
							if ($this->Rbac->isPermitted('Merchants/pci_edit')) {
								echo $this->Html->image("/img/editPencil.gif", array(
									'title' => 'Edit PCI',
									'class' => 'icon',
									'url' => array(
										'controller' => 'Merchants',
										'action' => 'pci_edit',
										$merchant['Merchant']['id']
									)
								));
							}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td class="threeColumnGridCell">
					<!-- account information -->
					<div class="contentModule">
						<span class="block"><?php echo $pci['Merchant']['merchant_dba']; ?></span>
						<span class="block"><?php echo $pci['AddressBus']['address_street']; ?></span>
						<span class="block"><?php echo $pci['AddressBus']['address_city'] . ' , ' .
							$pci['AddressBus']['address_state'] . $pci['AddressBus']['address_zip']; ?>
						</span>
					</div>

					<div class="contentModule">
						<span class="block">
							<?php echo __('Merchant Name:') . ' ' . h($pci['SaqMerchant']['merchant_name']); ?>
						</span>
						<span class="block">
							<?php echo __('Merchant Email:') . ' ' . h($pci['SaqMerchant']['merchant_email']) ?>
						</span>
						<span class="block">
							<?php 
								echo __('Merchant Password:') . ' ' . h($pci['SaqMerchant']['password']);
							?>
						</span>
					</div>

					<div class="contentModule">
						<span class="contentModuleTitle">Merchant Notes</span>&nbsp;
							<span>
								<?php
									echo $this->Html->link($this->Html->image('newNote.png', array(
											'class' => 'icon',
											'title' => 'Add a note for ' . h($merchant['Merchant']['merchant_dba'])
										)) . __('Add note'),
										'javascript:void(0)',
										array(
											'onclick' => "ajaxNote('" . $merchant['Merchant']['id'] . "',
											'General Note',
											'addNote_frm'); objFader('addNote_frm')",
											'escape' => false
										)
									);
								?>
							</span>
							&nbsp;
							<span>
								<?php echo $this->Html->link(
									$this->Html->image('icon_notes.gif', array(
										'title' => 'Open all Notes for ' . h($merchant['Merchant']['merchant_dba'])
									)) . __(' Notes archive'),
									array(
										'controller' => 'merchants',
										'action' => 'notes',
										$merchant['Merchant']['id']
									),
									array(
										'escape' => false
									));
								?>
							</span>
						</span>
					</div>

					<div id="addNote_frm" style="margin-bottom: 6px; display:none" class="containerStyle roundEdges shadow">
					</div>
				</td>
				<td class="threeColumnGridCell">
					<div class="contentModule">
						<table cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td class="tab"><?php echo __('Compliance Level:'); ?></td>
									<td><?php echo Hash::get($pci, 'MerchantPci.compliance_level')?></td>
								</tr>

								<tr>
									<td class="tab"><?php echo __('Validation Type:'); ?></td>
									<td>
										<?php 
											echo $this->Pci->getValidationType($pci);
										?>
									</td>
								</tr>

								<?php if ($this->Pci->hasCompleteQualification($pci)): ?>
									<tr>
										<td class="tab"><?php echo __('Prequalification Completed Date:'); ?></td>
										<td>
											<?php echo $this->Pci->dateFormat($pci['SaqMerchant']['LastSaqPrequalification']['date_completed']); ?>
										</td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="tab"><?php echo __('Boarded into ControlScan:'); ?></td>
									<td>
										<?php echo ($pci['MerchantPci']['controlscan_boarded']) ? 'Yes' : 'No'; ?>
									</td>
								</tr>
								<?php if ($pci['MerchantPci']['controlscan_boarded']): ?>
									<tr>
										<td class="tab"><?php echo __('MID originally used when boarded:'); ?></td>
										<td>
											<?php echo $pci['MerchantPci']['merchant_id_old']; ?>
										</td>
									</tr>
								<?php endif; ?>
								<?php if ($pci['MerchantPci']['cancelled_controlscan']): ?>
									<tr>
										<td class="tab"><?php echo __('Date Cancelled from ContolScan:'); ?></td>
										<td>
											<?php echo $this->Pci->dateFormat($pci['MerchantPci']['cancelled_controlscan_date']); ?>
										</td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="tab"><?php echo __('SAQ Completed Date:'); ?></td>
									<td>
										<?php echo !empty($pci['MerchantPci']['saq_completed_date']) ? $this->Pci->dateFormat($pci['MerchantPci']['saq_completed_date']) : ''; ?>
									</td>
								</tr>
								<?php if (!empty($pci['SaqControlScan']['pci_compliance'])): ?>
									<tr>
										<td class="tab"><?php echo __('PCI Compliant:'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['pci_compliance']; ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['MerchantPci']['compliance_fee'])): ?>
									<tr>
										<td class="tab"><?php echo __('Non Compliance Fee:'); ?></td>
										<td>
											<?php echo round($pci['MerchantPci']['compliance_fee'], 2); ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['SaqControlScan']['quarterly_scan_fee'])): ?>
									<tr>
										<td class="tab"><?php echo __('Quarterly Scan Fee:'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['quarterly_scan_fee']; ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['SaqControlScan']['host_count'])): ?>
									<tr>
										<td class="tab"><?php echo __('Additional IP Scan Fee:'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['host_count'] * 4.50; ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['SaqControlScan']['host_count']) && $pci['SaqControlScan']['host_count'] > 0): ?>
									<tr>
										<td class="tab"><?php echo __('Quarterly Associated MID Fee:'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['quarterly_scan_fee'] * $pci['SaqControlScan']['host_count']; ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['SaqMerchant']['billing_date'])): ?>
									<tr>
										<td class="tab"><?php echo __('Initial Deadline:'); ?></td>
										<td>
											<?php echo $this->Pci->dateFormat($pci['SaqMerchant']['billing_date']); ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['SaqMerchant']['next_billing_date'])): ?>
									<tr>
										<td class="tab"><?php echo __('Next Deadline:'); ?></td>
										<td>
											<?php echo $this->Pci->dateFormat($pci['SaqMerchant']['next_billing_date']); ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['MerchantPci']['insurance_fee'])): ?>
									<tr>
										<td class="tab"><?php echo __('Data Breach Program Fee:'); ?></td>
										<td>
											<?php echo round($pci['MerchantPci']['insurance_fee'], 2); ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if (!empty($pci['MerchantPci']['last_security_scan'])): ?>
									<tr>
										<td class="tab"><?php echo __('Last Security Scan:'); ?></td>
										<td>
											<?php echo h($pci['MerchantPci']['last_security_scan']); ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php if ($this->Pci->hasControlScanDate($pci) || $this->Pci->hasCompleteSaq($pci)): ?>
									<tr>
										<td class="tab"><?php echo __('Scanning Company:'); ?></td>
										<td>
											<?php echo h($pci['MerchantPci']['scanning_company']); ?>
										</td>
									</tr>
								<?php endif; ?>
 
								<?php if(!empty($pci['SaqControlScanUnboarded'])): ?>
									<tr>
										<td class="tab"><?php echo __('Unboarded From Control Scan:'); ?></td>
										<td>
											<?php echo ''; ?>
										</td>
									</tr>
								<?php endif; ?>

								<?php
									if (!empty($pci['SaqControlScan']['creation_date'])):
								?>
									<tr>
										<td class="tab">
											<strong><br/>Control Scan Data<br/></strong>
										</td>
										<td>&nbsp;</td>
									</tr>
									
									<?php if (!empty($pci['SaqControlScan']['sua'])): ?>
										<tr>
											<td class="tab"><?php echo __('First Login Date:'); ?></td>
											<td>
												<?php echo $this->Pci->dateFormat($pci['SaqControlScan']['sua']); ?>
											</td>
										</tr>
									<?php endif; ?>

									<?php if (!empty($pci['SaqControlScan']['sua'])): ?>
										<tr>
											<td class="tab"><?php echo __('First Login Date:'); ?></td>
											<td>
												<?php echo $this->Pci->dateFormat($pci['SaqControlScan']['sua']); ?>
											</td>
										</tr>
									<?php endif; ?>

									<tr>
										<td class="tab"><?php echo __('ControlScan Scan Date:'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['first_scan_date'] ?: $this->Pci->none(); ?>
										</td>
									</tr>

									<?php if (!empty($pci['SaqControlScan']['scan_status'])): ?>
										<tr>
											<td class="tab"><?php echo __('ControlScan Scan Status:'); ?></td>
											<td>
												<?php echo h($pci['SaqControlScan']['scan_status']); ?>
											</td>
										</tr>
									<?php endif; ?>

									<?php if (!empty($pci['SaqControlScan']['first_questionnaire_date'])): ?>
										<tr>
											<td class="tab"><?php echo __('ControlScan Questionnaire Date:'); ?></td>
											<td>
												<?php echo $this->Pci->dateFormat($pci['SaqControlScan']['first_questionnaire_date']); ?>
											</td>
										</tr>
									<?php endif; ?>

									<tr>
										<td class="tab"><?php echo __('ControlScan Questionnaire Status'); ?></td>
										<td>
											<?php echo $pci['SaqControlScan']['questionnaire_status'] ?: $this->Pci->none() ?>
										</td>
									</tr>

									<?php if (empty($pci['SaqControlScan']['compliant_date'])): ?>
										<tr>
											<td class="tab"><?php echo __('ControlScan Compliance Date:'); ?></td>
											<td>
												<?php echo h($pci['SaqControlScan']['compliant_date']); ?>
											</td>
										</tr>
									<?php endif; ?>

									<?php if (!empty($pci['SaqControlScan']['host_count'])): ?>
										<tr>
											<td class="tab"><?php echo __('Number of IP\'s Scanned:'); ?></td>
											<td>
												<?php echo h($pci['SaqControlScan']['host_count']); ?>
											</td>
										</tr>
									<?php endif; ?>
								<?php 
									endif;
								?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>

			<tr>
				<td class="threeColumnGridCell">
					<p>
						<strong>Prequalification Results</strong>
					</p>

					<table cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<!-- header row -->
								<td class="tab nowrap">
									<span class="header">Date Completed</span>
								</td>
								<td class="tab nowrap">
									<span class="header">Result</span>
								</td>
							</tr>

							<?php if (!empty($pci['SaqMerchant']['LastSaqPrequalification'])): ?>
								<tr onmouseover="highlight(this,true);" onmouseout="highlight(this,false);">
									<td class="tab"><?php echo $this->Pci->dateFormat($pci['SaqMerchant']['LastSaqPrequalification']['date_completed']); ?></td>
									<td><?php echo h($pci['SaqMerchant']['LastSaqPrequalification']['result']); ?></td>
								</tr>
							<?php endif; ?>

							<tr>
								<td class="tab">
									<p><br><strong>Email Timelines</strong></p>
								</td>
							</tr>

							<?php if (!empty($pci['SaqMerchant']['email_sent'])): ?>
								<tr>
									<td class="tab"><?php echo __('Manual Sending'); ?></td>
									<td><?php echo $this->Pci->dateFormat($pci['SaqMerchant']['email_sent']); ?></td>
								</tr>
							<?php endif; ?>

							<?php if (!empty($pci['SaqMerchant']['SaqMerchantPciEmailSent'])): ?>
								<?php foreach ($pci['SaqMerchant']['SaqMerchantPciEmailSent'] as $saqPciEmail): ?>
									<tr>
										<td class="tab"><?php echo $this->Pci->getEmailLabelById($saqPciEmail); ?></td>
										<td><?php echo $this->Pci->dateFormat($saqPciEmail['date_sent']); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>

						</tbody>
					</table>
				</td>
			</tr>

		<!-- pinstripe -->
		<tr>
		<td colspan="3">
			<div class="pinstripe"></div></td>
		</tr>

			<tr>
				<td colspan="3" class="bottomCellBuffer">
					<div class="contentModule">
						<p><strong>SAQ Results</strong></p>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tbody>
								<tr>
									<!-- header row -->
									<td class="tab nowrap">
										<span class="header"><?php echo __('SAQ Survey'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('SAQ Level'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Date Start'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Date Completed'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('IP'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Name'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Title'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Company'); ?></span>
									</td>
									<td class="tab nowrap">
										<span class="header"><?php echo __('Resolution'); ?></span>
									</td>
								</tr>
								<?php if (!empty($pci['SaqMerchant']['SaqMerchantSurveyXref'])): ?>	
									<?php foreach ($pci['SaqMerchant']['SaqMerchantSurveyXref'] as $saqMerchantSurveyXref): ?>
										<tr class="bgGrey" onmouseover="highlight(this, true);" onmouseout="highlight(this, false);">
											<td class="tab"><?php echo h($saqMerchantSurveyXref['saqSurvey']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['saqLevel']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['dateStart']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['dateCompleted']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['ip']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['name']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['title']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['company']); ?></td>
											<td class="tab"><?php echo h($saqMerchantSurveyXref['resolution']); ?></td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="bgBlue">
					<!-- viewing options -->
					<div class="mainBottomUtility pull-right">
						<span class="mainBottomUtilityLink">
							<?php echo $this->Html->image('icon_printer.gif', array(
									'width' => '15',
									'height' => '14',
									'alt' => 'Print',
									'border' => 0
								));

								echo $this->Html->link('Print',
									'javascript:printWindow()',
									array(
										'onmouseover' => "window.status = 'print this page'; return true",
										'onmouseout' => "window.status = ''; return true"
									)
								);
							?>
						</span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type='text/javascript'>activateNav('MerchantsPci'); </script>