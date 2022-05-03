<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/view/' . $merchant[Inflector::singularize($this->name)]['id']);
$this->Html->addCrumb('PCI DSS Compliance', '/' . $this->name . '/' . 'pci' . '/' . $merchant[Inflector::singularize($this->name)]['id']);
$this->Html->addCrumb('Edit PCI');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant PCI DSS Compliance')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<!-- containment div for main content -->
	<?php
		echo $this->Form->create('Merchant', array(
			'inputDefaults' => array(
				'wrapInput' => 'col col-md-6',
				'class' => 'form-control',
				'label' => false
			)));
		echo $this->Form->hidden('Merchant.id');
		echo $this->Form->hidden('Merchant.user_id');
		echo (!empty($this->request->data('Merchant.SaqMerchant.id')))? $this->Form->hidden('Merchant.SaqMerchant.id') : "";
		echo (!empty($this->request->data('Merchant.MerchantPci.id')))? $this->Form->hidden('Merchant.MerchantPci.id') : "";


		echo $this->Form->hidden('Merchant.SaqMerchant.merchant_id', array('value' => $merchant['Merchant']['id']));
		echo $this->Form->hidden('Merchant.MerchantPci.merchant_id', array('value' => $merchant['Merchant']['id']));
	?>

<div class="col-md-2 contentModuleTitle">
	<?php echo __('Edit PCI DSS Compliance'); ?>
	<br />
</div>


			<!-- account information -->
<div class="col-md-12 contentModuleTitle nowrap panel">
	<br />
	<?php echo $pci['Merchant']['merchant_dba']; ?><br>
	<?php echo $pci['AddressBus']['address_street']; ?><br>
	<?php echo h($pci['AddressBus']['address_city']), ' , ', h($pci['AddressBus']['address_state']), ' ', h($pci['AddressBus']['address_zip']); ?>
</div>
<div class="col-md-6" >			
	<table>
		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.SaqMerchant.merchant_name', __('Merchant name:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.SaqMerchant.merchant_name')
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.SaqMerchant.merchant_email', __('Merchant Email:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.SaqMerchant.merchant_email')
				?>
			</td>
		</tr>
	</table>
</div>			

<div class="col-md-6">
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.compliance_level', __('Compliance Level:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.compliance_level', array('options' => $compilanceLevels))
				?>
			</td>
		</tr>

		<tr>
			<?php
				if (empty($pci['SaqControlScan']['saq_type'])) {
					echo "<td>";
						echo $this->Form->label('Merchant.MerchantPci.saq_type', __('Validation Type:'));
					echo "</td>";

					echo "<td>";
						echo $this->Form->input('Merchant.MerchantPci.saq_type', array(
							'options' => $validationTypes,
							'selected' => $this->Pci->getValidationType($pci) ? $this->Pci->getValidationType($pci) : null,
							'empty' => true
						));
					echo "</td>";
				} else {
					echo "<td>";
						echo $this->Form->label(__('Validation Type:'));
					echo "</td>";

					echo "<td>";
						echo $this->Pci->getValidationType($pci);
					echo "</td>";
				}
			?>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.SaqMerchant.SaqPrequalification.0.date_completed', __('Prequalification Completed Date:')); ?>
			</td>
			<td>
				<?php 
				if (!empty($this->request->data('Merchant.SaqMerchant.LastSaqPrequalification.date_completed'))) {
					echo $this->AxiaTime->date($this->request->data('Merchant.SaqMerchant.LastSaqPrequalification.date_completed'));
				} else {
					echo '--';
				}
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.saq_completed_date', __('SAQ Completed Date:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.saq_completed_date', array(
					'type' => 'date',
					'style' => "font-size:8pt",
					'empty' => true,
					'value' => $this->Pci->getSAQCompletedDate($this->request->data)))
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.compliance_fee', __('Compliance Fee:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.compliance_fee')
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.insurance_fee', __('Data Breach Program Fee:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.insurance_fee')
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.last_security_scan', __('Last Security Scan:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.last_security_scan', array(
					'style' => "font-size:8pt",
					'type' => 'date',
					'empty' => true))
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $this->Form->label('Merchant.MerchantPci.scanning_company', __('Scanning Company:')); ?>
			</td>
			<td>
				<?php echo $this->Form->input('Merchant.MerchantPci.scanning_company')
				?>
			</td>
		</tr>
	</table>
</div>
		
<table class="table table-condensed">
    <tr>
        <td>
            <?php
				if ($isEditLog) {
					echo $this->Form->hidden('MerchantNote.0.id');
				}
				echo $this->element('Layout/Merchant/merchantNoteForChanges');
				echo $this->element('Layout/Merchant/mNotesDefaultBttns');
				echo $this->Form->end();
			?>
        </td>
    </tr>
</table>
<script type='text/javascript'>activateNav('MerchantsPci'); </script>