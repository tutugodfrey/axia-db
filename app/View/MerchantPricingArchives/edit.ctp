<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Products and Services', '/MerchantPricings/products_and_services/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Merchant Pricing Archive');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo htmlspecialchars($merchant['Merchant']['merchant_dba'], ENT_QUOTES) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Edit Archived Pricing'); ?>" />

<?php
echo $this->Form->create('MerchantPricingArchive', array(
	'inputDefaults' => array(
		'wrapInput' => "col-md-12",
		'label' => false),
	'url' => array($this->request->data('MerchantPricingArchive.id'))
	));
$numInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);
?>
<?php
echo $this->Form->hidden('MerchantPricingArchive.id');
echo $this->Form->hidden('merchant_id');
echo $this->Form->hidden('MerchantPricingArchive.user_id');
echo $this->Form->hidden('MerchantPricingArchive.products_services_type_id');
?>
<div class="row">
	<span  class="col col-md-3 contentModuleTitle">
		<?php 
		$archiveDate = date("F Y", mktime(0, 0, 0, (int)$this->request->data("MerchantPricingArchive.month"), 1, (int)$this->request->data("MerchantPricingArchive.year")));
		echo h($this->request->data("ProductsServicesType.products_services_description") . " Residual Pricing for " . $archiveDate); ?>
	</span>
</div>
<br />
<div class="row col-md-3">
	<div class='col col-md-12'>
		<table class='table table-condensed'>
			<tr>
				<td colspan="2" class="text-center contentModuleTitle"><?php echo $merchant['Merchant']['merchant_dba'] . "<br/>Archived Pricing"?></td>	
			</tr>		
			<tr><td class="noBorders">Rate %</td>
				<td class="noBorders"><?php echo $this->Form->input('MerchantPricingArchive.m_rate_pct', $numInputOptions); ?></td></tr>				
			<tr><td class="noBorders">Per Item Fee</td>
				<td class="noBorders"><?php echo $this->Form->input('MerchantPricingArchive.m_per_item_fee', $numInputOptions);?></td></tr>				
			<tr><td class="noBorders">Statement Fee</td>
				<td class="noBorders"><?php echo $this->Form->input('MerchantPricingArchive.m_statement_fee', $numInputOptions); ?></td></tr>
			<tr><td class="noBorders">Discount P/I	</td>
				<td class="noBorders"><?php echo $this->Form->input('MerchantPricingArchive.m_discount_item_fee', $numInputOptions); ?></td></tr>
			<tr><td class="noBorders">Gateway ID</td>
				<td class="noBorders"><?php echo $this->Form->input('MerchantPricingArchive.gateway_mid'); ?></td></tr>
		</table>
	</div>
</div>
<?php foreach ($this->request->data("UserCostsArchive") as $idx => $data): ?>
	<div class="row col-md-3">
		<div class='col col-md-12'>
			<?php 
			$thisRoleName = $this->request->data("UserCostsArchive.$idx.data_for_role");
			if (!empty($this->request->data("UserCostsArchive.$idx.id"))) {
				echo $this->Form->hidden("UserCostsArchive.$idx.id");
				echo $this->Form->hidden("UserCostsArchive.$idx.merchant_pricing_archive_id");
			}
			if (!empty($this->request->data("UserCostsArchive.$idx.user_id"))) {
				echo $this->Form->hidden("UserCostsArchive.$idx.user_id");
			}
			echo $this->Form->hidden("UserCostsArchive.$idx.merchant_id");
			?>
			<table class='table table-condensed'>
				<tr>
					<td colspan="2" class="">
						<?php if (!empty($this->request->data("UserCostsArchive.$idx.User.fullname"))){
								echo "<div class='text-center contentModuleTitle'>" . h($this->request->data("UserCostsArchive.$idx.User.fullname")) . "<br/>Archived Cost </div>" ;
						} else {
								echo "<div class='text-center text-muted'>($thisRoleName)</div>"; 
						}
						 ?>
					</td>	
				</tr>
				<?php if (!empty($thisRoleName)): ?>
					<tr><td class="noBorders"><?php echo h($thisRoleName); ?></td>
									<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.user_id", array(
									'options' => $usersOptions[$thisRoleName], 'empty' => " -- Select $thisRoleName --")); ?></td></tr>
				<?php endif; ?>
				<tr><td class="noBorders">Rate %</td>
					<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.cost_pct", $numInputOptions); ?></td></tr>				
				<tr><td class="noBorders">Per Item Cost</td>
					<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.per_item_cost", $numInputOptions);?></td></tr>				
				<tr><td class="noBorders">Statement Cost</td>
					<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.monthly_statement_cost", $numInputOptions); ?></td></tr>
				<tr><td class="noBorders">Risk Assessment %</td>
					<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.risk_assmnt_pct", $numInputOptions); ?></td></tr>
				<tr><td class="noBorders">Risk Assessment P/I</td>
					<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx.risk_assmnt_per_item", $numInputOptions); ?></td></tr>
				<?php if ($this->request->data("UserCostsArchive.$idx.data_for_role") === $assocRoles['referrer'] || 
							$this->request->data("UserCostsArchive.$idx.data_for_role") === $assocRoles['reseller']) :
							
							if ($thisRoleName === $assocRoles['referrer']) {
								$thisRoleFields[$thisRoleName] = [
											'ref_p_type',
											'ref_p_value',
											'ref_p_pct',
											'refer_profit_pct',
										];
							}
							if ($thisRoleName === $assocRoles['reseller']) {
								$thisRoleFields[$thisRoleName] = [
											'res_p_type',
											'res_p_value',
											'res_p_pct',
											'res_profit_pct',
										];
							}
							$pTypeOptions = array(
								'' => 'BET',
								'percentage' => 'Profit Percentage: Calculate Only',
								'points' => 'Basis Points Subtracted from Gross Profit',
								'percentage-grossprofit' => 'Profit Percentage Subtracted from Gross Profit',
								'points-calculateonly' => 'Basis Points: Calculate Only'
							);

							$pctOptions = array(
								'' => 'Default',
								'80' => '80%',
								'100' => '100%'
							);
				?>
							<tr><td class="noBorders"><?php echo h($thisRoleName) . "<br/>Profit %"; ?></td>
								<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx." . $thisRoleFields[$thisRoleName][3], $numInputOptions); ?></td></tr>
							<tr><td class="noBorders" colspan="2">
								<?php  $attributes = array('legend' => false, 'separator' => "<br/>", 'label' => false, 'class' => "btn-default btn-xs");
										echo $this->Form->radio("UserCostsArchive.$idx." . $thisRoleFields[$thisRoleName][0], $pTypeOptions, $attributes);?>
								</td>
							</tr>
							<tr><td class="noBorders">Value %</td>
								<td class="noBorders"><?php echo $this->Form->input("UserCostsArchive.$idx." . $thisRoleFields[$thisRoleName][1], $numInputOptions); ?></td>
							</tr>
							<tr>
								<td class="noBorders"> % of GP</td>
								<td class="noBorders">
								<?php  $attributes = array('legend' => false, 'separator' => "<br/>", 'label' => false, 'class' => "btn-default btn-xs");
										 $this->Form->radio("UserCostsArchive.$idx." . $thisRoleFields[$thisRoleName][2], $pctOptions, $attributes);
										echo $this->Form->input("UserCostsArchive.$idx." . $thisRoleFields[$thisRoleName][2], $numInputOptions)
									?>
								</td>
							</tr>
				<?php endif?>
			</table>
		</div>
	</div>
	<?php if ($idx === 2)
		echo "<div class='clearfix'></div>";
	 ?>
<?php endforeach; ?>
<?php 
echo $this->Form->end(array("class" => "btn", "div" => array("class" => "submit col-md-12 text-center")));
echo $this->AssetCompress->script('merchantPandSNav', array('raw' => (bool)Configure::read('debug')));
?>
