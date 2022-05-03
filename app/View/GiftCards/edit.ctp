<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $this->request->data('CheckGuarantee.id'));
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Products & Services') . " | " . __($this->name); ?>" />

<?php
echo $this->Form->create('GiftCard', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);

if(!empty($this->request->data('GiftCard.id'))) {
	echo $this->Form->hidden('id');
}
echo $this->Form->hidden('merchant_id');
?>

<table style='margin-bottom: 0px'>
		<tr>
			<td class="dataCell">			
				<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
					<tr><td class="noBorders">Gift Card MID</td>
						<td class="noBorders"><?php echo $this->Form->input('gc_mid', array("style" => "width:150px")); ?></td></tr>				
					<tr><td class="noBorders">Gift Card Provider</td>
						<td class="noBorders"><?php echo $this->Form->input('gift_card_provider_id', array("style" => "width:150px")); ?></td></tr>				
					<tr><td class="noBorders">Plan Type</td>
						<td class="noBorders"><?php 
							$options = array('one_rate' => 'One Rate Plan', 'per_item' => 'Per Item Fee Plan', '' => 'None');
							$attributes = array('legend' => false, 'separator' => "<br/>", 'label' => 'Plan Type:', 'class' => "btn-default btn-xs");
							echo $this->Form->radio('gc_plan', $options, $attributes);
						?></td></tr>								
				</table>                        
			</td>		
		</tr>
</table>
<table style='margin-bottom: 0px'>
	<tr>
		<td class="twoColumnGridCell dataCell">	
			<span class="contentModuleHeader">Processing & Recurring Fees</span><br />
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Statement Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_statement_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Gift Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_gift_item_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Loyalty Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_loyalty_item_fee', $numInputOptions); ?></td></tr>								
				<tr><td class="dataCell noBorders">One Rate Monthly Fee</td>
					<td class="dataCell noBorders"><?php  echo $this->Form->input('gc_chip_card_one_rate_monthly', $numInputOptions); ?></td></tr>								
				<tr><td class="dataCell noBorders">Loyalty Management Database</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_loyalty_mgmt_database', $numInputOptions); ?></td></tr>								
			</table>                        
		</td>		
		<td class="twoColumnGridCell dataCell">	
			<span class="contentModuleHeader">Set-up & Artwork Fees/Miscellaneous</span><br />
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Application Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_application_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Artwork Set Up Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_merch_prov_art_setup_fee', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Training Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_training_fee', $numInputOptions); ?></td></tr>								
				<tr><td class="dataCell noBorders">Card Re-order Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('gc_card_reorder_fee', $numInputOptions); ?></td></tr>								
			</table>                        
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