<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $this->request->data('CheckGuarantee.id'));
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Products & Services') . " | " . __($this->name)); ?>" />

<?php
echo $this->Form->create('WebBasedAch', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);

if(!empty($this->request->data('WebBasedAch.id'))) {
	echo $this->Form->hidden('id');
}
echo $this->Form->hidden('merchant_id');
?>

<table id='WebBasedACHContent' style='margin-bottom: 0px'>
	<tr>
		<td class="threeColumnGridCell dataCell">			
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Processing Rate</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('vcweb_web_based_rate', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('vcweb_web_based_pi', $numInputOptions); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('vcweb_monthly_fee', $numInputOptions); ?></td></tr>				
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