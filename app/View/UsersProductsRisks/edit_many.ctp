<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Underwriting', '/MerchantUws/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->action)), '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Edit Associates Risk Assessments')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<div class="well well-sm">
<?php
echo $this->Form->create('UsersProductsRisks', array('inputDefaults'=> array('label' => array(
			'class' => 'col col-sm-1 control-label'
			))));
?>
<table class="table table-condensed table-hover table-striped">
		<tr>
			<th class="text-center">Product Name:</th>
			<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">
				<?php echo (!empty($merchant['Merchant']['partner_id']))?"Partner Rep":"Rep";?>
			</th>
			<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Manager <?php echo '' . (empty($merchant['Merchant']['sm_user_id']))? '- (NONE)': ''; ?></th>
			<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Manager2 <?php echo '' . (empty($merchant['Merchant']['sm2_user_id']))? '- (NONE)': ''; ?></th>
			<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Partner <?php echo '' . (empty($merchant['Merchant']['partner_id']))? '- (NONE)': ''; ?></th>
			<th class="text-center" style="border-right-style:ridge;border-right-color:white" colspan="2">Referrer <?php echo '' . (empty($merchant['Merchant']['referer_id']))? '- (NONE)': ''; ?></th>
			<th class="text-center" colspan="2">Reseller <?php echo '' . (empty($merchant['Merchant']['reseller_id']))? '- (NONE)': ''; ?></th>
		</tr>
		<tr>
			<th><!-- SPACER --></th>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
			<td class="text-center">Rate:</td>
			<td class="text-center" style="border-right-style:ridge;border-right-color:white">Per Item:</td>
		</tr>
		<?php
		foreach($riskData as $pName => $dat):
		?>
		<tr>
			<td>
				<?php echo h($pName)?>
			</td>
				<?php foreach($dat as $idx => $userRisk):
					$options = array();
					if (empty($dat[$idx]['user_id'])) {
						$options = array('disabled' => true);
					}
				?>
				<td>
					<?php
					echo $this->Form->hidden("UsersProductsRisk.$idx.id");
					echo $this->Form->hidden("UsersProductsRisk.$idx.merchant_id");
					echo $this->Form->hidden("UsersProductsRisk.$idx.user_id");
					echo $this->Form->hidden("UsersProductsRisk.$idx.products_services_type_id");
					echo $this->Form->input("UsersProductsRisk.$idx.risk_assmnt_pct", array_merge(array('type' => 'text','label' => "%"), $options));
					?>
				</td>
				<td style="border-right-style:ridge;border-right-color:white">
					<?php echo $this->Form->input("UsersProductsRisk.$idx.risk_assmnt_per_item", array_merge(array('type' => 'text','label' => "$"), $options)); ?>
				</td>
				<?php  endforeach;?>
		</tr>
	<?php endforeach; ?>
</table>
<?php
echo $this->Form->defaultButtons();
echo $this->Form->end();
?>
</div>
<script type='text/javascript'>activateNav('MerchantUwsView'); </script>