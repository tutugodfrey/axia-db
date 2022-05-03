<?php
/* Drop breadcrumb */ 
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
$permitProdAction = $this->Rbac->isPermitted('app/actions/MerchantPricings/view/module/activatorPanel', true);

?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Products & Services')); ?>" />
<div>
	<div class="col-sm-6 col-xs-6 col-md-4">
		<div class="panel panel-primary" style="position: absolute;width: 100%;z-index: 100;">
			<div class="contrTitle" style="height:35px;">
				<span class="panel-title">
					<a href="javascript:void(0)">
							<span onclick="objSlider('disabledProductsList', 500); rotateThis(this, 180, 500)" class="glyphicon glyphicon-chevron-down"></span></a> Products List:
							<?php 
								$tipMsg = ($permitProdAction)? "Clicking active/inactive products will remove/add them from this merchant" : "List of products that this merchant has and does not have";
							?>
							<?php if($permitProdAction)
								echo '<a href="/Documentations/help#addingProductsM" target="_blank">';
							?>
								<span class="glyphicon glyphicon-question-sign pull-right" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $tipMsg; ?>"></span>
							<?php if($permitProdAction)
								echo '</a>';
							?>
				</span>
			</div>
			<div class="row col-xs-9 col-md-9" style="position: absolute; z-index: 101; display: none;" id="productAlertWrapper">
				<div class="alert alert-danger alert-dismissible shadow" id="productAlert">
					<button type="button" class="close" onclick="objSlider('productAlertWrapper', 400); $('#productAlertMsg').html('');" aria-label="Close"><span aria-hidden="true">×</span></button>
					<span id="productAlertMsg">Alert</span>
				</div>

			</div>
			<div class="row" id="disabledProductsList" style="height: 400px; margin: 0 0.15em 0.15em 0px; display: none; overflow-y: auto;">
				<ul class="list-group">
					<?php
					$onProdsTitleHTML = "<strong>Merchant's Products</strong>";
					$offProdsTitleHTML = "<strong>Inactive Products</strong>";

					if ($permitProdAction) {
						$onProdsTitleHTML .= $this->Form->button('Deactivate Selected', array(
							'type' => 'button',
							'id' => 'productsOffBtn',
							'merchant-id' => $merchant['Merchant']['id'],
							'class' => 'pull-right btn btn-xs btn-danger disabled',
							'disabled' => 'disabled'));
						$offProdsTitleHTML.= $this->Form->button('Activate Selected', array(
							'type' => 'button',
							'id' => 'productsOnBtn',
							'merchant-id' => $merchant['Merchant']['id'],
							'class' => 'pull-right btn btn-xs btn-success disabled',
							'disabled' => 'disabled'));
					}
						echo $this->Html->tag('li', $onProdsTitleHTML,
								array('class' => 'list-group-item list-group-item-success')
							);
						if (empty($enabledProds)) {
							echo $this->Html->tag('li', 'Merchant has no products.',
										array('class' => 'list-group-item text-muted')
									);
						} else {
							foreach ($enabledProds as $productsAndServicesId => $name) {
								if ($permitProdAction) {
									echo $this->Html->tag('div',
										$this->Form->checkbox($productsAndServicesId, array(
											'name' => 'disablePrdsCheck',
											'hiddenField' => false, 
											'class' => 'pull-right', 
											'style' => 'top:7px;position:relative;z-index:2;')) .
										$this->Form->postLink("$name" . $this->Html->image("green_orb.gif", array(
													"data-toggle" => "tooltip", "data-placement" => "left", "data-original-title" => "Deactivate " . h($name),
													"class" => "icon pull-right", "onmouseover" => "this.src='/img/red_orb.png'",
													"onmouseout" => "this.src='/img/green_orb.gif'")), array('controller' => 'ProductsAndServices',
											'action' => 'delete', $productsAndServicesId, $merchant['Merchant']['id']), array("class" => "list-group-item",
											'escape' => false, 'confirm' => __('Remove %s from %s?', h($name), $merchant['Merchant']['merchant_dba'])))
									);
								} else {
									echo $this->Html->tag('li', h($name) . $this->Html->image('green_orb.gif', array('class' => 'icon pull-right')),
											array('class' => 'list-group-item')
										);
								}
							}
						}
						echo $this->Html->tag('li', $offProdsTitleHTML, array('class' => 'list-group-item list-group-item-danger'));
						foreach ($disabledProducts as $id => $name) {
							if ($permitProdAction) {
								echo $this->Html->tag('div',
										$this->Form->checkbox($id, array(
											'name' => 'enablePrdsCheck',
											'hiddenField' => false,
											'class' => 'pull-right',
											'style' => 'top:7px;position:relative;z-index:2;')) .
										$this->Form->postLink(h($name) . $this->Html->image("red_orb.png", array(
											"data-toggle" => "tooltip", "data-placement" => "left", "data-original-title" => "Activate " . h($name),
											"class" => "icon pull-right", "onmouseover" => "this.src='/img/green_orb.gif'",
											"onmouseout" => "this.src='/img/red_orb.png'")), array('controller' => 'ProductsAndServices',
									'action' => 'add', $merchant['Merchant']['id'], $id), array("class" => "list-group-item",
									'escape' => false, 'confirm' => __('Add %s to %s?', h($name), $merchant['Merchant']['merchant_dba'])))
								);
							} else {
								echo $this->Html->tag('li', h($name) . $this->Html->image('red_orb.png', array('class' => 'icon pull-right')),
										array('class' => 'list-group-item')
									);
							}
						}
					?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-8">
		<ul class="list-group">
			<li class="list-group-item">
				<span class="badge"><?php echo (!empty($merchant['MerchantCardType'])) ? count($merchant['MerchantCardType']) : 'None'; ?></span>
				<strong style="font-size:9pt">Cards Accepted:&nbsp;&nbsp;</strong>
					<?php foreach ($merchant['MerchantCardType'] as $cardDetail): ?>
						<span class="label label-primary" style="font-size:100%"><?php echo h($cardDetail['CardType']['card_type_description']); ?></span>
					<?php endforeach; ?>
			</li>
		</ul>
	</div>
<?php 
if (!empty($merchant['MerchantPricing']['id'])) : ?>
	<div class="clearfix"></div>
	<?php
	if ($this->Rbac->isPermitted('app/actions/MerchantPricings/view/module/psModule1', true)) {
		echo $this->Html->tag('div', 'Underwriting Information:', ['class' => 'contentModuleTitle']);
		echo $this->element('Layout/Merchant/merchantAchVolumes');
	}
	?>

	<?php
		if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/tneSection', true)) {
			echo $this->element('Layout/Merchant/tAndESection');
		}
	?>
<?php if ($this->Rbac->isPermitted('app/actions/MerchantPricings/view/module/psModule1', true)): ?>
	<hr/>
	<div class="contentModuleTitle">
		<?php
			$editUrl = array(
					  'controller' => 'MerchantPricings',
					  'action' => 'edit', Hash::get($merchant, 'MerchantPricing.id'));
			echo $this->Html->sectionHeader(__('Products & Services: '));
			if ($this->Rbac->isPermitted('MerchantPricings/edit')){
				echo $this->Html->editImage(array('title' => h('Products & Services'), 'url' => $editUrl));
			}
		?>
	</div>
	<div>
		<?php //************************************Merchant Pricing & Settings *************************************    ?>
		<span class="contentModuleTitle">Merchant Pricing & Settings</span>
	</div>
	<?php
	$vMcRate = (float)$merchant['MerchantPricing']['processing_rate'];
	$dsRate = (float)$merchant['MerchantPricing']['ds_processing_rate'];
	$McViDsRatesAreEqual = $vMcRate === $dsRate;
	$vMcAuthFee = (float)$merchant['MerchantPricing']['mc_vi_auth'];
	$dsAuthFee = (float)$merchant['MerchantPricing']['ds_auth_fee'];
	$wirelessAuthFee = (float)$merchant['MerchantPricing']['wireless_auth_fee'];
	$McViDsAuthAreEqual = $vMcAuthFee === $dsAuthFee;
	?>

			<div class="col-md-3">
				<table class="col-md-12">
					<tr>
						<!-- data row -->
						<td class="noBorders">Visa <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['VisaBetTable']['name'])) ? h($merchant['MerchantPricing']['VisaBetTable']['name']) : '--'; ?></td>
					</tr>
					<tr>
						<!-- data row -->
						<td class="noBorders">MasterCard <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['McBetTable']['name'])) ? h($merchant['MerchantPricing']['McBetTable']['name']) : '--'; ?></td>
					</tr>
					<tr>
						<td class="noBorders">Discover <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['DiscoverBetTable']['name']) ? h($merchant['MerchantPricing']['DiscoverBetTable']['name']) : '--'; ?></td>
					</tr>
					<tr>
						<td class="noBorders">American Express <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['AmexBetTable']['name']) ? h($merchant['MerchantPricing']['AmexBetTable']['name']) : '--'; ?></td>
					</tr>
						<td class="noBorders">V/MC<?php echo ($McViDsRatesAreEqual) ? "/DS Discount Processing Rates" : " Discount Processing Rate"; ?></td>
						<td class="noBorders"><?php echo !empty($vMcRate) ? $this->Number->toPercentage($vMcRate) : "--"; ?></td>
					</tr>
					</tr>
						<td class="noBorders">American Express Processing Rate</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['amex_processing_rate']) ? $this->Number->toPercentage($merchant['MerchantPricing']['amex_processing_rate']) : "--"; ?>
						</td>
					</tr>
					<?php if ($McViDsRatesAreEqual === FALSE): ?>
						<tr>
							<td class="noBorders">Discover Discount Processing Rate</td>
							<td class="noBorders"><?php echo !empty($dsRate) ? $this->Number->toPercentage($dsRate) : "--"; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($this->Rbac->isPermitted('MerchantPricings/edit')): ?>
						<tr>
							<td class="noBorders">Rep/Mgr <strong>NOT</strong> paid on<br />Discover Discount/Settled Items</td>
							<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['ds_user_not_paid']) ? 'NOT Paid' : 'Paid'; ?></td>
						</tr>
					<?php endif; ?>
				</table>
			

			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Authorization Fees</span><br />

				<table class="col-md-12">
					<tr><td class="noBorders">V/MC<?php echo ($McViDsAuthAreEqual) ? "/DS Auth Fee" : " Auth Fee"; ?></td>
						<td class="noBorders"><?php echo !empty($vMcAuthFee) ? $this->Number->currency($vMcAuthFee, 'USD3dec', array(
								'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">Amex Auth Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['amex_auth_fee']) ? $this->Number->currency($merchant['MerchantPricing']['amex_auth_fee'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
						<?php if ($McViDsAuthAreEqual === FALSE): ?>
					<tr><td class="noBorders">Discover Auth Fee</td>
							<td class="noBorders">
								<?php echo !empty($dsAuthFee) ? $this->Number->currency($dsAuthFee, 'USD3dec', array('after' => false, 'negative' => '-')) : "--"; ?>
							</td>
						</tr>
						<?php endif; ?>
					<tr><td class="noBorders">Discount Item Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['discount_item_fee']) ? $this->Number->currency($merchant['MerchantPricing']['discount_item_fee'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">ARU/Voice Auth Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['aru_voice_auth_fee']) ? $this->Number->currency($merchant['MerchantPricing']['aru_voice_auth_fee'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">Wireless Auth Fee</td>
						<td class="noBorders"><?php echo !empty($wirelessAuthFee) ? $this->Number->currency($wirelessAuthFee, 'USD3dec', array(
			'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Monthly Fees</span><br />
				<table class="col-md-12">
					<tr><td class="noBorders">Statement Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['statement_fee']) ? $this->Number->currency($merchant['MerchantPricing']['statement_fee'], 'USD', array(
			'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">Monthly Minimum Fee</td>
						<td class="noBorders"><?php
							echo !empty($merchant['MerchantPricing']['min_month_process_fee']) ? $this->Number->currency($merchant['MerchantPricing']['min_month_process_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--";
							?></td></tr>
					<tr><td class="noBorders">Debit Access Fee</td>
						<td class="noBorders"><?php
							echo !empty($merchant['MerchantPricing']['debit_access_fee']) ? $this->Number->currency($merchant['MerchantPricing']['debit_access_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--";
							?></td></tr>
					<tr><td class="noBorders">EBT Access Fee</td>
						<td class="noBorders"><?php
							echo !empty($merchant['MerchantPricing']['ebt_access_fee']) ? $this->Number->currency($merchant['MerchantPricing']['ebt_access_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--";
							?></td></tr>
					<tr><td class="noBorders">Gateway</td>
						<td class="noBorders"><?php
							echo (__(Hash::get($merchant, 'MerchantPricing.Gateway.name')))? : '--';
							?></td></tr>
					<tr><td class="noBorders">Gateway Access Fee</td>
						<td class="noBorders"><?php
							echo !empty($merchant['MerchantPricing']['gateway_access_fee']) ? $this->Number->currency($merchant['MerchantPricing']['gateway_access_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--";
							?></td></tr>
					<tr><td class="noBorders">Wireless Access Fee</td>
						<td class="noBorders"><?php
							echo !empty($merchant['MerchantPricing']['wireless_access_fee']) ? $this->Number->currency($merchant['MerchantPricing']['wireless_access_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--";
							?></td></tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Miscellaneous Fees</span><br />
				<table class="col-md-12">
					<tr><td class="noBorders">Annual Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['annual_fee']) ? $this->Number->currency($merchant['MerchantPricing']['annual_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">Annual Fee Billing Month</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['annual_fee_bill_month']) ? date("M", mktime(0, 0, 0, 8, $merchant['MerchantPricing']['annual_fee_bill_month'], 10)) : "--"; ?></td></tr>
					<tr><td class="noBorders">Chargeback Fee
									<?php echo !empty($merchant['Merchant']['chargebacks_email'])? "<div class='small text-muted nowrap' style='position: absolute;'>(Email chargeback notice: ". h($merchant['Merchant']['chargebacks_email']).")</div>" : null?>
						</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['chargeback_fee']) ? $this->Number->currency($merchant['MerchantPricing']['chargeback_fee'], 'USD', array(
										'after' => false, 'negative' => '-')) : "--"; ?></td></tr>
					<tr><td class="noBorders">Data Breach Program Fee</td>
						<td class="noBorders"><?php
						echo !empty($merchant['MerchantPci']['insurance_fee']) ? $this->Number->currency($merchant['MerchantPci']['insurance_fee'], 'USD', array(
									'after' => false, 'negative' => '-')) : "--";?>
						</td>
					</tr>
					</tr>
						<td class="noBorders">MasterCard Acquirer Fee</td>
						<td class="noBorders"><?php echo $this->Number->currency($merchant['MerchantPricing']['mc_acquirer_fee'], 'USD3dec') ?></td>
					</tr>
				</table>
			</div>
					<?php
					$VMcBillAuthFee = $merchant['MerchantPricing']['billing_mc_vi_auth'];
					$dsBillAuthFee = $merchant['MerchantPricing']['billing_discover_auth'];
					$VMcDsBillAuthEqual = $VMcBillAuthFee === $dsBillAuthFee;
					?>

			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-3">
				<span class="contentModuleTitle">Billing Auth Fees</span><br />
				<table class="col-md-12">
					<tr><td class="noBorders">Billing V/MC<?php echo ($VMcDsBillAuthEqual) ? '/DS Auth Fees' : ' Auth Fee'; ?></td>
						<td class="noBorders"><?php echo (!empty($VMcBillAuthFee)) ? $this->Number->currency($VMcBillAuthFee, 'USD3dec', array(
								'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
				<?php if ($VMcDsBillAuthEqual === FALSE): ?>
						<tr><td class="noBorders">Billing Discover Auth Fee</td>
							<td class="noBorders"><?php echo (!empty($dsBillAuthFee)) ? $this->Number->currency($dsBillAuthFee, 'USD3dec', array(
				'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
				<?php endif; ?>
					<tr><td class="noBorders">Billing Amex Auth Fee</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['billing_amex_auth'])) ? $this->Number->currency($merchant['MerchantPricing']['billing_amex_auth'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
					<tr><td class="noBorders">Billing Debit Auth Fee</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['billing_debit_auth'])) ? $this->Number->currency($merchant['MerchantPricing']['billing_debit_auth'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : '--'; ?>
						</td>
					</tr>
					<tr>
						<td class="noBorders">Billing EBT Auth Fee</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['billing_ebt_auth'])) ? $this->Number->currency($merchant['MerchantPricing']['billing_ebt_auth'], 'USD3dec', array(
			'after' => false, 'negative' => '-')) : '--'; ?>
						</td>
					</tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Wireless Pricing</span><br />
				<table class="col-md-12">
					<tr><td class="noBorders">Wireless Per Item Cost</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['wireless_auth_cost'])) ? $this->Number->currency($merchant['MerchantPricing']['wireless_auth_cost'], 'USD', array(
			'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
					<tr><td class="noBorders">Number of Wireless Terminals</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['num_wireless_term'])) ? $merchant['MerchantPricing']['num_wireless_term'] : '--'; ?></td></tr>
					<tr><td class="noBorders">Per Wireless Terminal Cost</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['per_wireless_term_cost'])) ? $this->Number->currency($merchant['MerchantPricing']['per_wireless_term_cost'], 'USD', array(
			'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
					<tr><td class="noBorders">Total Monthly Wireless Cost</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['total_wireless_term_cost'])) ? $this->Number->currency($merchant['MerchantPricing']['total_wireless_term_cost'], 'USD', array(
			'after' => false, 'negative' => '-')) : '--'; ?></td></tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Debit & EBT Pricing</span><br />

				<table class="col-md-12">
					<tr>
						<td class="noBorders">Debit <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['DebitBetTable']['name']) ? h($merchant['MerchantPricing']['DebitBetTable']['name']) : '--'; ?>
						</td>
						<td class="noBorders">EBT <abbr title="Billing Elements Table" class="initialism">BET</abbr></td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['EbtBetTable']['name']) ? h($merchant['MerchantPricing']['EbtBetTable']['name']) : '--'; ?>
						</td>
					</tr>					
					<tr><td class="noBorders">Pin Debit Authorization</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['debit_auth_fee'])) ? $this->Number->currency($merchant['MerchantPricing']['debit_auth_fee'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
						</td>
						<td class="noBorders">EBT Authorization</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['ebt_auth_fee'])) ? $this->Number->currency($merchant['MerchantPricing']['ebt_auth_fee'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
						</td>
					</tr>
					<tr><td class="noBorders">Pin Debit Discount %</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['debit_processing_rate']) ? $this->Number->toPercentage($merchant['MerchantPricing']['debit_processing_rate']) : "--"; ?>
						</td>
						<td class="noBorders">EBT Discount %</td>
						<td class="noBorders"><?php echo !empty($merchant['MerchantPricing']['ebt_processing_rate']) ? $this->Number->toPercentage($merchant['MerchantPricing']['ebt_processing_rate']) : "--"; ?>
						</td>
					</tr>
					<tr><td class="noBorders">Debit Discount P/I</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['debit_discount_item_fee'])) ? $this->Number->currency($merchant['MerchantPricing']['debit_discount_item_fee'], 'USD', array('after' => false, 'negative' => '-')) : "--"; ?>
						</td>
						<td class="noBorders">EBT Discount P/I</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['ebt_discount_item_fee'])) ? $this->Number->currency($merchant['MerchantPricing']['ebt_discount_item_fee'], 'USD', array('after' => false, 'negative' => '-')) : "--"; ?>
						</td>
					</tr>	
					<tr>
						<td class="noBorders">Debit Acquirer</td>
						<td class="noBorders">
							<?php
							echo (!empty($merchant['MerchantPricing']['debit_acquirer_id'])) ? h($merchant['MerchantPricing']['DebitAcquirer']['debit_acquirers']) : "--";
							?>
						</td>
						<td class="noBorders">EBT Acquirer</td>
						<td class="noBorders"><?php echo (!empty($merchant['MerchantPricing']['ebt_acquirer_id'])) ? h($merchant['MerchantPricing']['EbtAcquirer']['debit_acquirers']) : "--"; ?>
						</td>
					</tr>
				</table>
			</div>
			<div class="col-md-3">
				<span class="contentModuleTitle">Additional Fees ACH'd Separately</span><br />
				<table class="col-md-12">
					<tr><td class="noBorders">MerchantLink Per Item Fee</td>
						<td class="noBorders"><?php echo (empty($merchant['Gateway1']['has_m_link_pi']))? 'N/A': 'Yes: IP = $.04, Dial = $.06'; ?></td></tr>
					<tr><td class="noBorders">Additional Gateway Processing Rate</td>
						<td class="noBorders"><?php echo !empty($merchant['Gateway1']['gw1_rate']) ? $this->Number->toPercentage($merchant['Gateway1']['gw1_rate']) : "--"; ?></td></tr>
					<tr><td class="noBorders">Additional Gateway Per Item Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['Gateway1']['gw1_per_item']) ? $this->Number->currency($merchant['Gateway1']['gw1_per_item'], 'USD3dec') : "--"; ?></td></tr>
					<tr><td class="noBorders">Additional Gateway Monthly Fee</td>
						<td class="noBorders"><?php echo !empty($merchant['Gateway1']['gw1_statement']) ? $this->Number->currency($merchant['Gateway1']['gw1_statement'], 'USD3dec') : "--"; ?></td>
						</tr>
				</table>
			</div>
	<br/>
<?php endif;  //RBAC permissions check app/actions/MerchantPricings/view/module/psModule1?>
<?php endif; //if merhcnat has MerchantPricing?>
<div class="clearfix"></div>
<?php if ($this->Rbac->isPermitted('app/actions/MerchantPricings/view/module/psModule1', true)): ?>
	<?php
	/*****************************Render Products Elements******************************/
	if (!empty($merchant['ProductsAndService'])) {
		echo $this->Html->renderProductElements($merchant);
	}
	if ($this->Rbac->isPermitted('app/actions/MerchantPricingArchives/view/module/archivePanel', true)) {
		echo $this->element('Layout/Merchant/ProductsAndServices/archivePanel');
	}
	?>
	<!--Bootstrap element breakpoint-->
	<div class='clearfix visible-xs-block'></div>
	<?php echo $this->element('modalDialog'); 
endif; 
echo $this->AssetCompress->script('merchantPandSNav', array('raw' => (bool)Configure::read('debug')));
echo $this->AssetCompress->script('merchantPricing2', array('raw' => (bool)Configure::read('debug')));
?>
</div>