<?php
if (!isset($output)) {
	$output = null;
}
if (!empty($profitReports)):
	$labels = [
		'merchant_mid' => [
			'text' => __('MID'),
			'sortField' => 'Merchant.merchant_mid',
			'sort_options' => [
				'direction' => 'desc'
			]
		],
		'merchant_dba' => [
			'text' => __('DBA'),
			'sortField' => 'Merchant.merchant_dba',
		],
		'client_id_global' => [
			'text' => __('Client ID'),
			'sortField' => 'Client.client_id_global',
		],
		'mo_year' => [
			'text' => __('Month/Year'),
			'sortField' => 'ProfitabilityReport.month',
		],
 		'net_sales_item_count' => [
 			'text' => __('Num of Net Sales'),
 			'sortField' => 'ProfitabilityReport.net_sales_item_count'
 		],
 		'net_sales_vol' => [
 			'text' => __('Amount of Net Sales'),
 			'sortField' => 'ProfitabilityReport.net_sales_vol'
 		],
 		'gross_sales_item_count' => [
 			'text' => __('Num of Gross Sales'),
 			'sortField' => 'ProfitabilityReport.gross_sales_item_count'
 		],
 		'gross_sales_vol' => [
 			'text' => __('Amount of Gross Sales'),
 			'sortField' => 'ProfitabilityReport.gross_sales_vol'
 		],
 		'total_income' => [
 			'text' => __('Total Income'),
 			'sortField' => 'ProfitabilityReport.total_income'
 		],
 		'card_brand_cogs' => [
 			'text' => __('Card Brand COGS'),
 			'sortField' => 'ProfitabilityReport.card_brand_cogs'
 		],
 		'processor_cogs' => [
 			'text' => __('Processor COGS'),
 			'sortField' => 'ProfitabilityReport.processor_cogs'
 		],
 		'sponsor_bank_cogs' => [
 			'text' => __('Sponsor Bank COGS'),
 			'sortField' => 'ProfitabilityReport.sponsor_bank_cogs'
 		],
 		'ithree_monthly_cogs' => [
 			'text' => __('i3 Monthly COGS'),
 			'sortField' => 'ProfitabilityReport.ithree_monthly_cogs'
 		],
 		'axia_net_income' => [
 			'text' => __('Axia Net Income'),
 			'sortField' => 'ProfitabilityReport.axia_net_income'
 		],
 		'cost_of_goods_sold' => [
 			'text' => __('COGS'),
 			'sortField' => 'ProfitabilityReport.cost_of_goods_sold'
 		],
		'pr_total_partner_gp' => [
			'text' => __('Partner GP'),
			'sortField' => null,
		],
 		'axia_gross_profit' => [
 			'text' => __('Axia Gross Profit'),
 			'sortField' => 'ProfitabilityReport.axia_gross_profit'
 		],
		'pr_total_rep_gp' => [
			'text' => __('Rep GP'),
			'sortField' => null,
		],
		'pr_total_sm_gp' => [
			'text' => __('SM GP'),
			'sortField' => null,
		],
		'pr_total_sm2_gp' => [
			'text' => __('SM2 GP'),
			'sortField' => null,
		],
		'pr_total_referrer_gp' => [
			'text' => __('Ref GP'),
			'sortField' => null,
		],
		'pr_total_reseller_gp' => [
			'text' => __('Res GP'),
			'sortField' => null,
		],
 		'total_residual_comp' => [
 			'text' => __('Total Residual Comp'),
 			'sortField' => 'ProfitabilityReport.total_residual_comp'
 		],
 		'axia_net_profit' => [
 			'text' => __('Axia Net Profit'),
 			'sortField' => 'ProfitabilityReport.axia_net_profit'
 		]
	];

	$colGroups = $this->AxiaHtml->getColGroups();
	$headersCreated = false;
	$headers = [];
	$cells = [];
	//Display Totals at the top as well as the bottom
	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($profitReports, 0) as $key => $value) {
		switch ($key) {
			case 'merchant_mid':
				$totalsRow[] = __('Totals');
				break;
			case 'total_income':
			case 'axia_net_income':
			case 'cost_of_goods_sold':
			case 'axia_gross_profit':
			case 'total_residual_comp':
			case 'axia_net_profit':
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$totalsRow[] = $this->Number->currency(Hash::get($totals, $key), 'USD', ['places' => 4]);
				} else {
					$totalsRow[] = CakeNumber::currency(Hash::get($totals, $key)); 
				}
				break;
			default:
				if ($output === null) {
					$totalsVal = '&nbsp;';
				} else {
					$totalsVal = null;
				}
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$totalsRow[] = $totalsVal;
				} else {
					$totalsRow = array_merge($totalsRow, $this->AxiaHtml->getCollapsibleCel($key, $totalsVal));
				}
		}
	}
	$cells[] = $totalsRow;

	$cells[] = [' '];//Spacer
	foreach ($profitReports as $profitReport) {
		$row = [];
		foreach ($profitReport as $key => $value) {
			if ($headersCreated === false) {
				$sortField = Hash::get($labels, "{$key}.sortField");
				if (($output === GUIbuilderComponent::OUTPUT_CSV)) {
					$headers[] = Hash::get($labels, "{$key}.text");
				} else {
					$case = '';
					if (empty($sortField)) {
						$case = Hash::get($labels, "{$key}.text");
					} else {
						$case = $key;
					}
					switch ($case) {
						case 'Partner GP':
						case 'Rep GP':
						case 'SM GP':
						case 'SM2 GP':
						case 'Ref GP':
						case 'Res GP':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['name' => $colGroups['prGpColGroup'], 'class' => 'bg-success hidden']];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'bg-success hidden', 'name' => $colGroups['prGpColGroup']]];
								}
							}
							$headers[] = $header;
							break;
						default:
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = Hash::get($labels, "{$key}.text");
								} else {
									$header = $this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text"), Hash::extract($labels, "{$key}.sort_options"));
								}
							}
							$headers[] = $header;
					}
				}
			}
			//Build rows
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				$row[$key] = $value;
			} else {
				$row = array_merge($row, $this->AxiaHtml->getCollapsibleCel($key, $value));
			}
		}
		$headersCreated = true;
		$cells[] = $row;
	}
	//Display Totals at the bottom as well as the top
	$cells[] = $totalsRow;
	$rowsNumber = $resultCount;
	// Display data based on output
	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
		<div class="contentModule">
			<?php echo __('%s items were included in this report.', $rowsNumber); ?><br>
		</div>
		<?= $this->Html->showPaginator($rowsNumber); ?>
		<table class="table table-condensed">
			<?php
			echo $this->Html->tableHeaders($headers);
			echo $this->Html->tableCells($cells);
			?>
		</table>
		<?= $this->element('pagination'); ?>
	<?php
	endif;
endif;
