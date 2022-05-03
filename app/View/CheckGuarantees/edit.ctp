<?php
/* Drop breadcrumb */
$this->Html->addCrumb($merchant['Merchant']['merchant_dba'], '/Merchants/view/' . $this->request->data('CheckGuarantee.id'));
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Products & Services') . " | " . __($this->name); ?>" />

<?php
echo $this->Form->create('CheckGuarantee', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);

if(!empty($this->request->data('CheckGuarantee.id'))) {
	echo $this->Form->hidden('id');
}
	echo $this->Form->hidden('merchant_id');
?>

<div class="row">
	<div class='col-md-3'>
		<br />
			<table cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Check Guarantee MID</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_mid'); ?></td></tr>				
				<tr><td class="dataCell noBorders">Station Number</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_station_number'); ?></td></tr>				
				<tr><td class="dataCell noBorders">Account Number</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_account_number'); ?></td></tr>
				<tr><td class="dataCell noBorders">Check Guarantee Provider</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('check_guarantee_provider_id', array('empty' => __('Please select...'))); ?></td></tr>
				<tr><td class="dataCell noBorders">Service Type</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('check_guarantee_service_type_id', array('empty' => __('Please select...'))); ?></td></tr>				
			</table>                        
	</div>
	<div class='col-md-3'>
			<span class="contentModuleHeader">Processing & Recurring Fees</span><br />
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Processing Rate</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_transaction_rate', $numInputOptions); ?></td></tr>
				<tr><td class="dataCell noBorders">Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_per_item_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_monthly_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Minimum Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('cg_monthly_minimum_fee', $numInputOptions); ?></td></tr>				
			</table>                        
	</div>
	<div class='col-md-3'>
			<span class="contentModuleHeader">Rep Processing Costs</span><br />
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="dataCell noBorders">Processing Cost % </td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('rep_processing_rate_pct', $numInputOptions); ?></td></tr>
				<tr><td class="dataCell noBorders">Per Item Cost</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('rep_per_item_cost', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Cost</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('rep_monthly_cost', $numInputOptions); ?></td></tr>
			</table>
	</div>
</div>
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
