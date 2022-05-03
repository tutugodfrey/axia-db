
<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Products and Services', '/MerchantPricings/products_and_services/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Gateway1');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Products & Services') . " | " . __($this->name); ?>" />

<?php
echo $this->Form->create('Gateway1', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);
?>
<?php
if(!empty($this->request->data('Gateway1.id'))) {
	echo $this->Form->hidden('id');
}
echo $this->Form->hidden('merchant_id');
?>
<div class="row">
		<?php //************************************Merchant Pricing & Settings *************************************    ?>
		<span  class="col col-md-3 contentModuleTitle">Edit Gateway 1</span>
	</div>
<div class="row panel">
	<div class='col col-md-3'>
		<table>			
			<tr><td class="dataCell noBorders">Gateway</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gateway_id', array("style" => "width:150px")); ?></td></tr>				
			<tr><td class="dataCell noBorders">Gateway ID</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_mid');?></td></tr>
				<tr><td class="dataCell noBorders">Merchant Gateway Processing Rate</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_rate', $numInputOptions);?></td></tr>				
			<tr><td class="dataCell noBorders">Merchant Gateway Per Item Fee</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_per_item', $numInputOptions); ?></td></tr>				
			<tr><td class="dataCell noBorders">Merchant Gateway Monthly Fee</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_statement', $numInputOptions); ?></td></tr>				
			<tr><td class="dataCell noBorders">Monthly Volume</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_monthly_volume', $numInputOptions); ?></td></tr>				
			<tr><td class="dataCell noBorders">Monthly Item Count</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('gw1_monthly_num_items', $numInputOptions); ?></td></tr>
			<tr><td class="dataCell noBorders">Additional Rep Monthly Cost</td>
				<td class="dataCell noBorders"><?php echo $this->Form->input('addl_rep_statement_cost', 
				array_merge($numInputOptions,
							array(
								"data-toggle" => "tooltip", "data-placement" => "bottom", "data-original-title" => "Note: This addt'l monthtly cost will be added to any Gateway Monthly cost defined in this Rep's UCP"
							))); ?></td></tr>
			<tr>
				<td class="noBorders" colspan="2">
					<div class="panel panel-default">
						<div class="panel-heading contentModuleTitle">Features:</div>
						<?php echo $this->Form->textarea('gw1_rep_features', array('class' => 'col col-md-12 roundEdges', 'rows' => '5', 'style' => "max-width: 500px")); ?>
					</div>
				</td>
			</tr>
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

