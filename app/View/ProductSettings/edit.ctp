<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $this->request->data('ProductSetting.merchant_id'));
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Products & Services') . " | " . __($this->request->data['ProductsServicesType']['products_services_description'])); ?>" />

<?php
echo $this->Form->create('ProductSetting', array('inputDefaults' => array('label' => false)));
$numInputOptions = array(
	'type' => 'number',
	"step" => "0.001",
);

if(!empty($this->request->data('ProductSetting.id'))) {
	echo $this->Form->hidden('id');
}
echo $this->Form->hidden('merchant_id');
echo $this->Form->hidden('products_services_type_id');
?>
<div class="contentModuleTitle"><?php echo h("Edit {$productData['ProductsServicesType']['products_services_description']}:"); ?></div>
<table>
	<tr>
		<td>
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'generic_product_mid'))? h($customLabels['generic_product_mid']) : __('Product ID #:'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('generic_product_mid'); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'product_feature'))? h($customLabels['product_feature']) : __('Feature'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('product_feature_id', ['options' => $productFeatures[$productData['ProductsServicesType']['id']], 'empty' => '--']); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'other_features'))? h($customLabels['other_features']) : __('Other Features'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('other_features', ['style' => 'font-size:9pt']); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'monthly_fee'))? h($customLabels['monthly_fee']) : __('License Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('monthly_fee', $numInputOptions); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'rate'))? h($customLabels['rate']) : __('Rate'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('rate', $numInputOptions); ?></td>
				</tr>
				
			</table>
		</td>
		<td>
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'per_item_fee'))? h($customLabels['per_item_fee']) : __('Per Item Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('per_item_fee', $numInputOptions); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'gral_fee_multiplier'))? h($customLabels['gral_fee_multiplier']) : __('General Fee Multiplier'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('gral_fee_multiplier', ["step" => "1"]); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'gral_fee'))? h($customLabels['gral_fee']) : __('General Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Form->input('gral_fee', $numInputOptions); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'monthly_total'))? h($customLabels['monthly_total']) : __('Total Monthly Fee'); ?>
					</td>
					<td class="noBorders"><?php 
					$toolTip = ["readonly", "data-toggle" => "tooltip", "data-placement" => "right", "data-original-title" => "(Read only) Calculates automatically."];
					echo $this->Form->input('monthly_total', array_merge($numInputOptions, $toolTip)); ?></td>
				</tr>
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