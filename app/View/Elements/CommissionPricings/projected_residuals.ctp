<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($commissionMultiples)):
	if (!empty(Hash::get($this->request->params, 'named.direction'))) {
		$sortVector = (Hash::get($this->request->params, 'named.direction') === 'asc')? 'desc' : 'asc';
	} else {
		$sortVector = 'asc';
	}
	$labels = [
		'mid' => [
			'text' => __('MID'),
			'sortField' => 'Merchant.merchant_mid',
			'sort_options' => [
				'direction' => $sortVector
			]
		],
		'dba' => [
			'text' => __('DBA'),
			'sortField' => 'Merchant.merchant_dba',
			'sort_options' => [
				'direction' => $sortVector
			]
		],
		'bet' => [
			'text' => __('Bet'),
		],
		'product' => [
			'text' => __('Product'),
		],
		'install_date' => [
			'text' => __('Go-Live Date'),
		],
		'com_month' => [
			'text' => __('Com. Month'),
		],
		'avg_tkt' => [
			'text' => __('Avg Tkt'),
		],
		'items' => [
			'text' => __('Items'),
		],
		'volume' => [
			'text' => __('Volume'),
		],
		'original_merch_rate' => [
			'text' => __('Merch Rate'),
		],
		'merch_rate' => [
			'text' => __('Merch Rate + Bet %'),
		],
		'merch_p_i' => [
			'text' => __('Merch P/I'),
		],
		'merch_stmt' => [
			'text' => __('Merch Stmt'),
		],
		'rep' => [
			'text' => __('Rep'),
		],
		'rep_rate' => [
			'text' => __('Rep Rate'),
		],
		'rep_p_i' => [
			'text' => __('Rep P/I'),
		],
		'rep_stmt' => [
			'text' => __('Rep Stmt/Gtwy'),
		],
		'rep_gross_profit' => [
			'text' => __('Rep Gross Profit'),
		],
		'partner_profit_amount' => [
			'text' => __('Partner Profit'),
		],
		'rep_residual_gp' => [
			'text' => __('Rep Res Gross Profit'),
		],
		'rep_product_profit_pct' => [
			'text' => __('Rep Profit %'),
		],
		'partner_name' => [
			'text' => __('Partner'),
		],
		'rep_pct_of_gross' => [
			'text' => __('Rep % of Gross'),
		],
		'multiple_amount' => [
			'text' => __('Multiple Amount'),
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

	$headers = [];
	$cells = [];
	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($commissionMultiples, '0') as $key => $value) {
		switch ($key) {
			case 'mid':
				$totalsRow[] = __('Totals');
				break;
			case 'items':
				$totalsRow[] = Hash::get($commissionMultipleTotals, "$key");
				break;
			case 'avg_tkt':	
			case 'volume':
			case 'multiple_amount':
			case 'resid_gross_profit':
			case 'rep_gross_profit':
			case 'rep_residual_gp':
				$totalsRow[] = (!is_null(Hash::get($commissionMultipleTotals, "$key")))? CakeNumber::currency(Hash::get($commissionMultipleTotals, "$key")) : null;
				break;

			default:
			if (isset($labels[$key])) {
				if ($output === null) {
					$totalsRow[] = '&nbsp;';
				} else {
					$totalsRow[] = null;
				}				
			}
		}
	}
	//Display totals at the top as well as bottom
	$cells[] = $totalsRow;

	foreach ($commissionMultiples as $gpaProjection) {
		// Create the headers the first time
		if (empty($headers)) {
			foreach ($gpaProjection as $key => $value) {
				if (isset($labels[$key])) {
					$sortField = Hash::get($labels, "{$key}.sortField");
					if (($output === GUIbuilderComponent::OUTPUT_CSV) || empty($sortField)) {
						$headers[] = Hash::get($labels, "{$key}.text");
					} else {
						$headers[] = $this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text"), Hash::extract($labels, "{$key}.sort_options"));
					}
				}
			}
		}

		$row = [];
		foreach ($gpaProjection as $key => $value) {
			//add only the values that have labels
			if (isset($labels[$key])) {
				switch ($key) {
					case 'items':
						$row[$key] = ($value !== null) ? CakeNumber::precision($value, 1) : null;
					break;
					case 'install_date':
					case 'com_month':
						$row[$key] = ($this->Time->format($value, '%m-%d-%Y'))? : $value;
						break;

					case 'volume':
					case 'partner_profit_amount':
					case 'rep_residual_gp':
					case 'avg_tkt':
					case 'merch_p_i':
					case 'merch_stmt':
					case 'multiple_amount':
					case 'rep_p_i':
					case 'rep_stmt':
					case 'rep_gross_profit':
						$row[$key] = is_numeric($value)? CakeNumber::currency($value, null, [
							'zero' => '0']) : $value;
						break;

					case 'merch_rate':
					case 'original_merch_rate':
					case 'rep_rate':					
					case 'rep_product_profit_pct':
					case 'rep_profit_pct':
					case 'rep_pct_of_gross':
						$row[$key] = ($value !== null) ? CakeNumber::toPercentage($value) : null;
						break;
					default:
						if ($output === GUIbuilderComponent::OUTPUT_CSV) {
							$row[$key] = $value;
						} else {
							$row[$key] = h($value);
						}
				}
			}
		}
		$cells[] = $row;
	}

	//Display totals at the bottom as well
	$cells[] = $totalsRow;

	// Display data based on output
	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>

		<div class="contentModule text-center bg-primary">
			<?php 
				$startDate = strtotime($this->request->data('CommissionPricing.from_date.year') . "-" . $this->request->data('CommissionPricing.from_date.month'). "-01");
				$startDate = date('M Y',$startDate)
				?>
			<strong>Projected Residuals</strong> | <?php echo __('%s | Residual Data: %s month(s)', $startDate, $this->request->data('CommissionPricing.res_months')); ?>
			<?php echo __('| %s items were included in this report.', count($commissionMultiples)); ?>
		</div>
		<div>
			<table>
				<?php
				echo $this->Html->tableHeaders($headers);
				echo $this->Html->tableCells($cells);
				?>
			</table>
		</div>
	<?php
	endif;
endif;
?>