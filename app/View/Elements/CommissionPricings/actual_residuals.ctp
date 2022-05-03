<?php
if (!isset($output)) {
	$output = null;
}
/*
 In order for column sorting to work on the report view, the paginator requires virtual fields to be set
before paginate() method is called in the controller. 
The sortField names are defined as the keys in the array returned by the ResidualReport->getReportVirtualFields()
and those exact sortfield names must be used here for sorting to work.
*/
if (!empty($actualResiduals)):
	if (!empty(Hash::get($this->request->params, 'named.direction'))) {
		$sortVector = (Hash::get($this->request->params, 'named.direction') === 'asc')? 'desc' : 'asc';
	} else {
		$sortVector = 'asc';
	}
	$labels = [
		'merchant_mid' => [
			'text' => __('MID'),
			'sortField' => 'Merchant.merchant_mid',
			'sort_options' => [
				'direction' => $sortVector 
			]
		],
		'merchant_dba' => [
			'text' => __('DBA'),
			'sortField' => 'Merchant.merchant_dba',
			'sort_options' => [
				'direction' => $sortVector 
			]
		],
		'RepFullname' => [
			'text' => __('Rep'),
		],
		'products_services_description' => [
			'text' => __('Product'),
		],
		'r_avg_ticket' => [
			'text' => __('Avg Tkt'),
		],
		'r_items' => [
			'text' => __('Total Items'),
		],
		'r_volume' => [
			'text' => __('Total Volume'),
		],
		'avg_volume' => [
			'text' => __('Avg Volume'),
		],
		'rep_gross_profit' => [
			'text' => __('Total Rep Gross Profit'),
		],
		'avg_partner_profit_amount' => [
			'text' => __('Avg Partner Profit'),
		],
		'rep_avg_gross_profit' => [
			'text' => __('Avg Gross Profit'),
		],
		'rep_total_residual_gp' => [
			'text' => __('Total Rep Residual GP'),
		],
		'rep_avg_residual_gp' => [
			'text' => __('Avg Residual GP'),
		],
		'rep_projected_res_gp' => [
			'text' => __('Projected Rep Res GP'),
		],
		'gp_amount_diff' => [
			'text' => __('Diff $'),
		],
		'gp_pct_diff' => [
			'text' => __('Diff %'),
		],
		'PartnerFullname' => [
			'text' => __('Partner'),
		],
		'actual_multiple' => [
			'text' => __('Actual Multiple'),
		],
		'organization' => [
			'text' => __('Organization'),
		],
		'region' => [
			'text' => __('Region'),
		],
		'subregion' => [
			'text' => __('Subregion'),
		],
		'location' => [
			'text' => __('Location'),
		],
	];

	if ($this->action === 'gp_analysis') {
		unset($labels['r_volume'],$labels['rep_projected_res_gp'], $labels['rep_gross_profit'], $labels['rep_total_residual_gp'], $labels['actual_multiple']);
	} elseif ($this->action === 'commission_multiple_analysis') {
		unset($labels['avg_partner_profit_amount']);
	}

	$colGroups = $this->AxiaHtml->getColGroups();
	$headersCreated = false;
	$headers = [];
	$cells = [];
	//Display Totals at the top as well as the bottom
	// Add totals
	$totalsRow = [];
	// foreach ((array)Hash::get($actualResiduals, '0') as $key => $value) {
	foreach ($labels as $key => $value) {
		switch ($key) {
			case 'active_merchant':
				break;
			case 'merchant_mid':
				$totalsRow[] = __('Totals');
				break;
			case 'r_items':
				$totalsRow[] = Hash::get($totals, "$key");
				break;
			case 'r_volume':
			case 'r_avg_ticket':
			case 'avg_volume':
			case 'rep_gross_profit':
			case 'avg_partner_profit_amount':
			case 'rep_avg_gross_profit':
			case 'rep_total_residual_gp':
			case 'rep_avg_residual_gp':
			case 'rep_projected_res_gp':
			case 'gp_amount_diff':
			case 'actual_multiple':
				if ($key === 'actual_multiple') {
					$value = $this->Number->currency(Hash::get($totals, "$key"), 'USD3dec');
					
					if (empty($output)) {
						$value .= "<br>Diff: " . $this->Number->currency(Hash::get($totals, "multiple_diff"), 'USD3dec');
					} else {
						$value .= " (Diff: " . $this->Number->currency(Hash::get($totals, "multiple_diff"), 'USD3dec') . ")";						
					}
					$totalsRow[] = $value;
				} else {
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$totalsRow[] = $this->Number->currency(Hash::get($totals, "$key"), 'USD', ['places' => 4]);
					} else {
						$totalsRow[] = $this->Number->currency(Hash::get($totals, "$key"), 'USD', ['places' => 2]);
					}
				}				
				break;
			case 'gp_pct_diff':
				$totalsRow[] = CakeNumber::toPercentage(Hash::get($totals, "$key"), 2);
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
					$totalsRow[] = $totalsVal;
				}
		}
	}

	$cells[] = $totalsRow;
	foreach ($actualResiduals as $actuals) {
		$row = [];
		foreach ($labels as $key => $value) {
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
						case 'rep_gross_profit':
						case 'Rep Gross Profit':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['name' => $colGroups['repColGroup'], 'class' => 'info hidden']];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['repColGroup']]];
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
				$row[] = Hash::get($actuals, $key);
			} else {
				$cssClasss = '';
				$cellContent = Hash::get($actuals, $key);
				if ($this->action === 'commission_multiple_analysis' && $key === 'products_services_description') {
					$cellContent .= Hash::get($actuals, 'commissionable')? ' **': '';
				}
				if ($actuals['active_merchant'] == 0) {
					$cssClasss = 'text-muted';
					$cellContent = "<i>$cellContent</i>";
					if ($key == 'merchant_mid' || $key == 'merchant_dba') {
						$cssClasss = 'text-danger';
					}
				}
				$row[] = [h($cellContent), ['class' => $cssClasss]];
			}
		}
		$headersCreated = true;
		$cells[] = $row;
	}
	//Display Totals at the bottom as well as the top
	$cells[] = $totalsRow;
	$rowsNumber = count($actualResiduals);
	// Display data based on output
	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
		<div class="contentModule text-center bg-primary">
			<span class="pull-left small"><i><?php echo ($this->action === 'commission_multiple_analysis')? "Commissionable Product **" : "(Showing data for all products)"?></i></span>
			<?php
				$startDate = strtotime($this->request->data('CommissionPricing.from_date.year') . "-" . $this->request->data('CommissionPricing.from_date.month'). "-01");
				$startDate = date('M Y',$startDate)
				?>
			<strong>Actual Residuals</strong> | <?php echo __('%s | Residual Data: %s month(s)', $startDate, $this->request->data('CommissionPricing.res_months')); ?>
			<?php echo __('| %s items were included in this report.', $rowsNumber); ?>
		</div>
		<table class="table table-condensed">
			<?php
			echo $this->Html->tableHeaders($headers, ['class' => 'nowrap'], ['class' => 'nowrap small']);
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	endif;
endif;
