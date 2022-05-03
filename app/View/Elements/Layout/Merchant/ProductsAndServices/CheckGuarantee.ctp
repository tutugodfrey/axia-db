
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'CheckGuarantees', 'action' => 'edit', $merchant['CheckGuarantee']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#ChkGuaranteeContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>

<table id='ChkGuaranteeContent' style='margin-bottom: 0px'>
	<tr>
		<td class="threeColumnGridCell dataCell">

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Check Guarantee MID</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_mid'])) ? h($merchant['CheckGuarantee']['cg_mid']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Station Number</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_station_number'])) ? h($merchant['CheckGuarantee']['cg_station_number']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Account Number</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_account_number'])) ? h($merchant['CheckGuarantee']['cg_account_number']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Check Guarantee Provider</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['CheckGuaranteeProvider'])) ? h($merchant['CheckGuarantee']['CheckGuaranteeProvider']['provider_name']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Service Type</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['CheckGuaranteeServiceType'])) ? h($merchant['CheckGuarantee']['CheckGuaranteeServiceType']['service_type']) : h("--"); ?></td></tr>				
			</table>                        
		</td>
		<td class="threeColumnGridCell dataCell">
			<span class="contentModuleHeader">Processing & Recurring Fees</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Processing Rate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_transaction_rate'])) ? $this->Number->toPercentage($merchant['CheckGuarantee']['cg_transaction_rate']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_per_item_fee'])) ? $this->Number->currency($merchant['CheckGuarantee']['cg_per_item_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_monthly_fee'])) ? $this->Number->currency($merchant['CheckGuarantee']['cg_monthly_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Minimum Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['cg_monthly_minimum_fee'])) ? $this->Number->currency($merchant['CheckGuarantee']['cg_monthly_minimum_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
			</table>                        
		</td>
		<td class="threeColumnGridCell dataCell">
			<span class="contentModuleHeader">Rep Processing Costs</span><br />

			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Processing Cost % </td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['rep_processing_rate_pct'])) ? $this->Number->toPercentage($merchant['CheckGuarantee']['rep_processing_rate_pct']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Per Item Cost</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['rep_per_item_cost'])) ? $this->Number->currency($merchant['CheckGuarantee']['rep_per_item_cost'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Cost</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['CheckGuarantee']['rep_monthly_cost'])) ? $this->Number->currency($merchant['CheckGuarantee']['rep_monthly_cost'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			</table>                        
		</td>
	</tr>
</table>