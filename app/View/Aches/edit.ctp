<?php
/* Drop breadcrumb */
$this->Html->addCrumb($merchant['Merchant']['merchant_dba'], '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Products and Services', '/' . $this->name . '/' . $this->action . '/' . $this->request->data('Ach.id'));
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Products & Services') . " | " . __($this->name);?>" />

<?php
echo $this->Form->create('Ach', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);

$pctLimits = array('min' => '0', 'max' => '100');

if(!empty($this->request->data('Ach.id'))) {
	echo $this->Form->hidden('id');
}
	echo $this->Form->hidden('merchant_id', array('value' => $merchant['Merchant']['id']));
?>

<table>
	<tr>
		<td>
			<div class="col-md-3">

				<span class="contentModuleTitle">ACH Provider</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders"><?php echo $this->Form->label('Ach.ach_provider_id', 'Provider Name'); ?></td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_provider_id', array('wrapInput' => 'col-md-12', 'empty' => __('Please select...'))); ?></td></tr>				
				</table>                        
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="col-md-3">
				<span class="contentModuleTitle">Volume</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Expected Annual Sales</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_expected_annual_sales', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Average Transaction</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_average_transaction', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Estimated Max Transaction</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_estimated_max_transaction', $numInputOptions); ?></td></tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Start-Up Fees/Miscellaneous</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Application Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_application_fee', $numInputOptions); ?></td></tr>
					<tr><td class="dataCell noBorders">Expedite Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_expedite_fee', $numInputOptions); ?></td></tr>
				</table>                        
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="col-md-3">
				<span class="contentModuleTitle">Processing & Recurring Fees</span><br />
				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Rate</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_rate', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Per Item Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_per_item_fee', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Statement Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_statement_fee', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Batch Upload Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_batch_upload_fee', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Reject Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_reject_fee', $numInputOptions); ?></td></tr>
					<tr><td class="dataCell noBorders">Monthly Gateway Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_monthly_gateway_fee', $numInputOptions); ?></td></tr>				
					<tr><td class="dataCell noBorders">Monthly Minimum Fee</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_monthly_minimum_fee', $numInputOptions); ?></td></tr>				
				</table>                        
			</div>		
		</td>
	</tr>
	<tr>
		<td>
			<span class="contentModuleTitle col-md-12">Originator ID Banking</span>
			<div class="col-md-3">
				<span class="contentModuleHeader">Disbursements</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Bank Name</td>
						<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_dsb_bank_name'])) ? h($merchant['Ach']['ach_mi_w_dsb_bank_name']) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Routing #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_dsb_routing_number'); ?></td></tr>				
					<tr><td class="dataCell noBorders">Account #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_dsb_account_number'); ?></td></tr>				
				</table>                        
			</div>
			<div class="col-md-3">
				<span class="contentModuleHeader">Fees</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Bank Name</td>
						<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_fee_bank_name'])) ? h($merchant['Ach']['ach_mi_w_fee_bank_name']) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Routing #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_fee_routing_number'); ?></td></tr>				
					<tr><td class="dataCell noBorders">Account #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_fee_account_number'); ?></td></tr>				
				</table>                        
			</div>
			<div class="col-md-3">
				<span class="contentModuleHeader">Rejects</span><br />

				<table cellpadding="0" cellspacing="0" border="0">
					<tr><td class="dataCell noBorders">Bank Name</td>
						<td class="dataCell noBorders"><?php echo (!empty($merchant['Ach']['ach_mi_w_rej_bank_name'])) ? h($merchant['Ach']['ach_mi_w_rej_bank_name']) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Routing #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_rej_routing_number'); ?></td></tr>				
					<tr><td class="dataCell noBorders">Account #</td>
						<td class="dataCell noBorders"><?php echo $this->Form->input('ach_mi_w_rej_account_number'); ?></td></tr>				
				</table>                        
			</div>
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
echo $this->AssetCompress->script('merchantPandSNav', array('raw' => (bool)Configure::read('debug')));
?>