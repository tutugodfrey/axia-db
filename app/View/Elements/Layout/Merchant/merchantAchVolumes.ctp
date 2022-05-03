<div class="row">
	<div class="col-md-3 col-sm-3">
		<span class="col-md-12 col-sm-12 contentModuleTitle">Activity</span>
		<table style="width: auto" class="table-hover">
			<tr><td class="dataCell noBorders">Monthly Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['mo_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['mo_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Average Ticket</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['average_ticket'])) ? $this->Number->currency($merchant['MerchantUwVolume']['average_ticket'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Max Trans. Amount</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['max_transaction_amount'])) ? $this->Number->currency($merchant['MerchantUwVolume']['max_transaction_amount'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>                            
			<tr><td class="dataCell noBorders">Sales</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['sales']) ? $this->Number->precision($merchant['MerchantUwVolume']['sales'], 0) : h("--"); ?></td></tr>
		</table>                        
	</div>
	<div class="col-md-5 col-sm-5">
			<?php $showAllProjected = $this->Rbac->isPermitted('app/actions/MerchantUws/view/module/hiddenS1', true); ?>
		<span class="col-md-12 col-sm-12 contentModuleTitle">Projected Profits</span>
		<table class="table-hover table-condensed" style="width:auto">
			<tr>
				<th class="dataCell">
					<ul class="nav navbar-nav navbar-left" >
						<li class="dropdown">
							<a href="#" id="projectionsDrop1" role="button" class="dropdown-toggle" data-toggle="dropdown" style="padding: 0"><span class="glyphicon glyphicon-option-vertical"></span>Options</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="projectionsDrop1">
								<?php
									if ($this->Rbac->isPermitted('ProfitProjections/updateProjections')) {
										echo $this->Form->postLink("Update Projections<span class='pull-right glyphicon glyphicon-refresh'</span>",
											array('controller' => 'ProfitProjections', 'action' => 'updateProjections', $merchant['Merchant']['id']),
											array('class' => 'small nowrap bg-success', 'escape' => false, 'confirm' => __('Current Profit Projections will be replaced, are you sure?')));
									}
								?>
								<a href="javascript:void(0)" 'role'="menuitem" class="small" onClick="$('td[name=creditProjRows]').fadeToggle('slow')">Expand/Collapse <span class="pull-right small glyphicon glyphicon-arrow-down"></span><span class="pull-right small glyphicon glyphicon-arrow-up"></span></a>
							</ul>
						</li>
					</ul>
				</th>
				<?php if ($showAllProjected): ?>
				<th class="dataCell">Rep Gross Pofit</th>
				<?php endif; ?>
				<th class="dataCell">Rep Profit</th>
				<?php if ($showAllProjected): ?>
				<th class="dataCell">Axia Profit</th>
				<?php endif; ?>
			</tr>
			<?php if (empty($profitProjections)): ?>
			<tr> 
				<td colspan='1000'>
				<div class="text-center">
					<span class="list-group-item text-center text-muted">- None. -
						<?php echo ($this->Rbac->isPermitted('ProfitProjections/updateProjections'))? '<br><span class="small">(Use options above to update this list)</span>' : null; ?>
					</span>
				</div>
				</td>
			</tr>
			<?php else:
				$creditProjections = Hash::get($profitProjections, 'Credit');
				/*Extract credit data to always place it at the top of the list first*/
				if (!empty($creditProjections)) {
					unset($profitProjections['Credit']);
					$totalGp = 0;
					$totalRepPofit = 0;
					$totalAxProfit = 0;
					$rows = [];
					foreach ($creditProjections as $productName => $projData) {
						$cells = [];
						$cells[] = [$productName, ['class' => 'bg-success', 'name' => 'creditProjRows', 'style' => 'display:none']];
						if ($showAllProjected) {
							$cells[] = [$this->Number->currency(Hash::get($projData, 'ProfitProjection.rep_gross_profit'), 'USD3dec'), ['class' => 'bg-success', 'name' => 'creditProjRows', 'style' => 'display:none']];
							$totalGp += Hash::get($projData, 'ProfitProjection.rep_gross_profit');
						}
						$cells[] = [$this->Number->currency(Hash::get($projData, 'ProfitProjection.rep_profit_amount'), 'USD3dec'), ['class' => 'bg-success', 'name' => 'creditProjRows', 'style' => 'display:none']];
						$totalRepPofit += Hash::get($projData, 'ProfitProjection.rep_profit_amount');
						if ($showAllProjected) {
							$cells[] = [$this->Number->currency(Hash::get($projData, 'ProfitProjection.axia_profit_amount'), 'USD3dec'), ['class' => 'bg-success', 'name' => 'creditProjRows', 'style' => 'display:none']];
							$totalAxProfit += Hash::get($projData, 'ProfitProjection.axia_profit_amount');
						}
						$rows[] = $cells;
					}
					$credTotals[] = ['<div data-toggle="tooltip" data-placement="right" data-original-title="To see a credit breakdown click expand in the options menu."><strong>Credit Totals</strong> <span class="glyphicon glyphicon-info-sign"></span></div> ', ['class' => 'nowrap bg-success']];
					if ($showAllProjected) {
						$credTotals[] = [$this->Number->currency($totalGp, 'USD3dec'), ['class' => 'bg-success']];
					}
					$credTotals[] = [$this->Number->currency($totalRepPofit, 'USD3dec'), ['class' => 'bg-success']];
					if ($showAllProjected) {
						$credTotals[] = [$this->Number->currency($totalAxProfit, 'USD3dec'), ['class' => 'bg-success']];
					}
					$rows[] = $credTotals;
				}
				if (!empty($profitProjections)) {
					foreach (Hash::extract($profitProjections, '{s}.{s}') as $projData) {
						$cells = [];
						$cells[] = h($projData['ProductsServicesType']['products_services_description']);
						if ($showAllProjected) {
							$cells[] = $this->Number->currency(Hash::get($projData, 'ProfitProjection.rep_gross_profit'), 'USD3dec');
						}
						$cells[] = $this->Number->currency(Hash::get($projData, 'ProfitProjection.rep_profit_amount'), 'USD3dec');
						if ($showAllProjected) {
							$cells[] = $this->Number->currency(Hash::get($projData, 'ProfitProjection.axia_profit_amount'), 'USD3dec');
						}
						$rows[] = $cells;
					}

				}
				echo $this->Html->tableCells($rows);
			?>
			<?php endif; ?>
		</table>
	</div>
	<div class="col-md-4 col-sm-4">
		<span class="contentModuleTitle">Volume Breakdown</span><br />

		<table style="width: auto" class="table-hover">
			<tr><td class="dataCell noBorders">Visa Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['visa_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['visa_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">MasterCard Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['mc_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['mc_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Discover Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['ds_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['ds_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Pin Debit Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['pin_debit_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['pin_debit_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Pin Debit Avg Ticket</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['pin_debit_avg_ticket'])) ? $this->Number->currency($merchant['MerchantUwVolume']['pin_debit_avg_ticket'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">American Express<br>Volume</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['amex_volume'])) ? $this->Number->currency($merchant['MerchantUwVolume']['amex_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">American Express<br>Average Ticket</td>
				<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantUwVolume']['amex_avg_ticket'])) ? $this->Number->currency($merchant['MerchantUwVolume']['amex_avg_ticket'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>                            
		</table>                        
	</div>
	<div class="clearfix"></div>
	<div class="col-md-6 col-sm-6">
		<span class="contentModuleTitle">Method of Sales</span><br />

		<table style="width: auto" class="table-hover">
			<tr><td class="dataCell noBorders">Card present swipe</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_present_swiped']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_present_swiped'], 0) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Card present imprint</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_present_imprint']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_present_imprint'], 0) : h("--"); ?></td></tr>
			<?php if (!empty($merchant) && ($merchant['Merchant']['merchant_buslevel'] == 'EFTSecure Internet' && empty($merchant['MerchantUwVolume']['card_not_present_internet']))) { ?>

				<tr><td class="dataCell noBorders">Card not present Keyed</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_keyed']) ? $this->Number->toPercentage(($merchant['MerchantUwVolume']['card_not_present_keyed'] / 2), 0) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Card not present Internet</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_internet']) ? $this->Number->toPercentage(($merchant['MerchantUwVolume']['card_not_present_keyed'] / 2), 0) : h("--"); ?></td></tr>

			<?php } elseif (!empty($merchant['MerchantUwVolume']['card_not_present_internet'])) { ?>

				<tr><td class="dataCell noBorders">Card not present Keyed</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_keyed']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_keyed'], 0) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Card not present Internet</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_internet']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_internet'], 0) : h("--"); ?></td></tr>

			<?php } elseif (!empty($merchant) && ($merchant['Merchant']['merchant_buslevel'] == 'MOTO' && empty($merchant['MerchantUwVolume']['card_not_present_internet']))) { ?>

				<tr><td class="dataCell noBorders">Card not present Keyed</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_keyed']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_keyed'], 0) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Card not present Internet</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_internet']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_internet'], 0) : h("--"); ?></td></tr>

			<?php } else { ?>

				<tr><td class="dataCell noBorders">Card not present Keyed</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_keyed']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_keyed'], 0) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Card not present Internet</td>
					<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['card_not_present_internet']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['card_not_present_internet'], 0) : h("--"); ?></td></tr>

			<?php } ?>                            
		</table>                        
	</div>
	<div class="col-md-6 col-sm-6">
		<span class="contentModuleTitle">Percentage of Products Sold</span><br />

		<table style="width: auto" class="table-hover">
			<tr><td class="dataCell noBorders">Direct to consumer</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['direct_to_consumer']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['direct_to_consumer'], 0) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Business to business</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['direct_to_business']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['direct_to_business'], 0) : h("--"); ?></td></tr>
			<tr><td class="dataCell noBorders">Government</td>
				<td class="dataCell noBorders"><?php echo!empty($merchant['MerchantUwVolume']['direct_to_government']) ? $this->Number->toPercentage($merchant['MerchantUwVolume']['direct_to_government'], 0) : h("--"); ?></td></tr>
		</table>                        
	</div>
</div>