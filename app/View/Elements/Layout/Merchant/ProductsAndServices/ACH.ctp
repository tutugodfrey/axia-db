
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
		$content = $this->Html->editImageLink(array(
			'controller' => 'Aches', 
			'action' => 'edit',
			Hash::get($merchant, "Ach.id"))
		);
	?>
	<script >
		/*this script will display the edit and activate menu buttons on this elements title*/
		$(function() {
			appendHTMLContent($('#AchContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});

	</script>
<?php endif ?>

<table id='AchContent'>
	<tr>
		<td class="threeColumnGridCell dataCell">

			<span class="contentModuleTitle">ACH Provider</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Provider Name</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['AchProvider']['id'])) ? h($merchant['Ach']['AchProvider']['provider_name']) : h("--"); ?></td></tr>				
			</table>                        
		</td>
	</tr>
</table>
<table>
	<tr>
		<td class="twoColumnGridCell dataCell">
			<span class="contentModuleTitle">Volume</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Expected Annual Sales</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_expected_annual_sales'])) ? $this->Number->currency($merchant['Ach']['ach_expected_annual_sales']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Average Transaction</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_average_transaction'])) ? $this->Number->currency($merchant['Ach']['ach_average_transaction']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Estimated Max Transaction</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_estimated_max_transaction'])) ? $this->Number->currency($merchant['Ach']['ach_estimated_max_transaction']) : h("--"); ?></td></tr>
			</table>
		</td>
		<td class="twoColumnGridCell dataCell">
			<span class="contentModuleTitle">Start-Up Fees/Miscellaneous</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Application Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_application_fee'])) ? $this->Number->currency($merchant['Ach']['ach_application_fee']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Expedite Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_expedite_fee'])) ? $this->Number->currency($merchant['Ach']['ach_expedite_fee']) : h("--"); ?></td></tr>
			</table>                        
		</td>
	</tr>
</table>
<table>
	<tr>
		<td class="dataCell">
			<span class="contentModuleTitle">Processing & Recurring Fees</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Rate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_rate'])) ? $this->Number->toPercentage($merchant['Ach']['ach_rate']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_per_item_fee'])) ? $this->Number->currency($merchant['Ach']['ach_per_item_fee'], 'USD3dec') : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Statement Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_statement_fee'])) ? $this->Number->currency($merchant['Ach']['ach_statement_fee']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Batch Upload Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_batch_upload_fee'])) ? $this->Number->currency($merchant['Ach']['ach_batch_upload_fee'], 'USD3dec') : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Reject Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_reject_fee'])) ? $this->Number->currency($merchant['Ach']['ach_reject_fee'], 'USD3dec') : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Gateway Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_monthly_gateway_fee'])) ? $this->Number->currency($merchant['Ach']['ach_monthly_gateway_fee']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Monthly Minimum Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_monthly_minimum_fee'])) ? $this->Number->currency($merchant['Ach']['ach_monthly_minimum_fee']) : h("--"); ?></td></tr>
			</table>                        
		</td>		
	</tr>
</table>

<span class="contentModuleTitle">Originator ID Banking</span>
<table style="margin-bottom: 0px;">
	<tr>
		<td class="threeColumnGridCell dataCell">
			<span class="contentModuleHeader">Disbursements</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Bank Name</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_dsb_bank_name'])) ? h($merchant['Ach']['ach_mi_w_dsb_bank_name']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Routing #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_dsb_routing_number'])) ? h($merchant['Ach']['ach_mi_w_dsb_routing_number']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Account #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_dsb_account_number'])) ? h($merchant['Ach']['ach_mi_w_dsb_account_number']) : h("--"); ?></td></tr>				
			</table>                        
		</td>
		<td class="threeColumnGridCell dataCell">
			<span class="contentModuleHeader">Fees</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Bank Name</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_fee_bank_name'])) ? ($merchant['Ach']['ach_mi_w_fee_bank_name']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Routing #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_fee_routing_number'])) ? ($merchant['Ach']['ach_mi_w_fee_routing_number']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Account #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_fee_account_number'])) ? ($merchant['Ach']['ach_mi_w_fee_account_number']) : h("--"); ?></td></tr>				
			</table>                        
		</td>
		<td class="threeColumnGridCell dataCell">
			<span class="contentModuleHeader">Rejects</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Bank Name</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_rej_bank_name'])) ? h($merchant['Ach']['ach_mi_w_rej_bank_name']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Routing #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_rej_routing_number'])) ? h($merchant['Ach']['ach_mi_w_rej_routing_number']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Account #</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_rej_account_number'])) ? h($merchant['Ach']['ach_mi_w_rej_account_number']) : h("--"); ?></td></tr>				
			</table>                        
		</td>
	</tr>
</table>