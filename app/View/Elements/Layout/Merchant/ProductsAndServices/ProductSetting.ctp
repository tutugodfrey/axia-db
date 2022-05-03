
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
		$content = $this->Html->editImageLink(array(
			'controller' => 'ProductSettings', 
			'action' => 'edit',
			Hash::get($productSetting, "id"))
		);
$panelBodyHtmlId .= $panelBodyHtmlId . "Content";
?>

	<script >
		/*this script will display the edit and activate menu buttons on this elements title*/
		$(function() {
			appendHTMLContent($("#<?php echo $panelBodyHtmlId; ?>").parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});

	</script>
<?php endif ?>

<table id="<?php echo $panelBodyHtmlId; ?>">
	<tr>
		<td>
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'generic_product_mid'))? h($customLabels['generic_product_mid']) : __('Product ID #:'); ?>
					</td>
					<td class="noBorders"><?php echo (!empty($productSetting['generic_product_mid'])) ? h($productSetting['generic_product_mid']) : h("--"); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'product_feature'))? h($customLabels['product_feature']) : __('Feature'); ?>
					</td>
					<td class="noBorders"><?php echo (!empty($productSetting['product_feature_id'])) ? h($merchant['ProductFeature'][$productSetting['products_services_type_id']][$productSetting['product_feature_id']]) : h("--"); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'other_features'))? h($customLabels['other_features']) : __('Other Features'); ?>
					</td>
					<td class="noBorders"><?php echo (!empty($productSetting['other_features'])) ? h($productSetting['other_features']) : h("--"); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'monthly_fee'))? h($customLabels['monthly_fee']) : __('License Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Number->currency($productSetting['monthly_fee'], 'USD2dec'); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'rate'))? h($customLabels['rate']) : __('Rate'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Number->toPercentage($productSetting['rate'], 3); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'per_item_fee'))? h($customLabels['per_item_fee']) : __('Per Item Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Number->currency($productSetting['per_item_fee'], 'USD3dec'); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'gral_fee_multiplier'))? h($customLabels['gral_fee_multiplier']) : __('General Fee Multiplier'); ?>
					</td>
					<td class="noBorders"><?php echo (integer)$productSetting['gral_fee_multiplier']; ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'gral_fee'))? h($customLabels['gral_fee']) : __('General Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Number->currency($productSetting['gral_fee'], 'USD2dec'); ?></td>
				</tr>
				<tr>
					<td class="noBorders">
						<?php echo (Hash::get($customLabels, 'monthly_total'))? h($customLabels['monthly_total']) : __('Total Monthly Fee'); ?>
					</td>
					<td class="noBorders"><?php echo $this->Number->currency($productSetting['monthly_total'], 'USD2dec'); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>