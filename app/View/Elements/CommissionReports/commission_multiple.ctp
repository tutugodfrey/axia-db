<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($commissionMultiples)):
	$labels = [
		'mid' => __('MID'),
		'dba' => __('DBA'),
		'bet' => __('Bet'),
		'bus_level' => __('Bus Level'),
		'product' => __('Product'),
		'sub_date' => __('Sub. Date'),
		'app_date' => __('App. Date'),
		'install_date' => __('Go-Live Date'),
		'recd_install_sheet' => __("Rec'd Install Sheet"),
		'com_month' => __('Com. Month'),
		'volume' => __('Volume'),
		'avg_tkt' => __('Avg Tkt'),
		'items' => __('Items'),
		'original_merch_rate' => __('Merch Rate'),
		'bet_extra_pct' => __('Bet Extra %'),
		'merch_rate' => __('Merch Rate + Bet %'),
		'merch_p_i' => __('Merch P/I'),
		'merch_stmt' => __('Merch Stmt'),
		'rep' => __('Rep'),
		'rep_rate' => __('Rep Rate'),
		'rep_p_i' => __('Rep P/I'),
		'rep_stmt' => __('Rep Stmt/Gtwy'),
		'rep_gross_profit' => __('Rep Gross Profit'),
		'rep_pct_of_gross' => __('Rep % of Gross'),
		'multiple' => __('Rep Multiple %'),
		'multiple_amount' => __('Rep Multiple Amount'),
		'manager_multiple' => __('SM Multiple %'),
		'manager_multiple_amount' => __('SM Multiple Amount'),
		'manager2_multiple' => __('SM2 Multiple %'),
		'manager2_multiple_amount' => __('SM2 Multiple Amount'),
		'ent_name' => __('Company'),
		'partner_name' => __('Partner'),
		'partner_pct_of_gross' => __('Partner % of Gross'),
		'partner_profit_pct' => __('Partner Profit %'),
	];

	$headers = [];
	$cells = [];
	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($commissionMultiples, 0) as $key => $value) {
		switch ($key) {
			case 'mid':
				$totalsRow[] = __('Totals');
				break;

			case 'volume':
			case 'rep_gross_profit':
			case 'multiple_amount':
			case 'manager_multiple_amount':
			case 'manager2_multiple_amount':
				$totalsRow[] = CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($commissionMultipleTotals, $key)));
				break;

			default:
				if ($output === null) {
					$totalsRow[] = '&nbsp;';
				} else {
					$totalsRow[] = null;
				}
		}
	}
	//Add totals row at the top
	$cells[] = $totalsRow;
	foreach ($commissionMultiples as $commissionMultiple) {
		// Create the headers the first time
		if (empty($headers)) {
			foreach ($commissionMultiple as $key => $value) {
				$headers[] = Hash::get($labels, $key);
			}
		}

		$row = [];
		foreach ($commissionMultiple as $key => $value) {
			switch ($key) {
				case 'items':
					$row[$key] = ($value !== null) ? CakeNumber::precision($value, 1) : null;
				break;
				case 'sub_date':
				case 'app_date':
				case 'install_date':
				case 'recd_install_sheet':
					$row[$key] = ($this->Time->date($value))? : $value;
					break;

				case 'com_month':
					$row[$key] = $this->Time->format($value, '%B %Y');
					break;

				case 'volume':
				case 'avg_tkt':
				case 'merch_p_i':
				case 'merch_stmt':
				case 'multiple_amount':
				case 'manager_multiple_amount':
				case 'manager2_multiple_amount':
				case 'rep_p_i':
				case 'rep_stmt':
				case 'rep_gross_profit':

					$row[$key] = is_numeric($value)? $this->Number->currency($value, 'USD3dec', [
						'zero' => '-']) : $value;
					break;

				case 'original_merch_rate':
				case 'bet_extra_pct':
				case 'merch_rate':
				case 'multiple':
				case 'manager_multiple':
				case 'manager2_multiple':
				case 'rep_rate':
				case 'rep_pct_of_gross':
					$row[$key] = ($value !== null) ? CakeNumber::toPercentage($value, 3) : null;
					break;

				default:
					$row[$key] = h($value);
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$row[$key] = $value;
					}
			}
		}
		$cells[] = $row;
	}
	//Add totals row at the bottom as well
	$cells[] = $totalsRow;

	// Display data based on output
	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
		<div class="contentModule">
			<?php echo __('Detail on New Account Multiples.'); ?><br>
			<?php echo __('%s items were included in this report.', count($commissionMultiples)); ?>
		</div>
		<table>
			<?php
			echo $this->Html->tableHeaders($headers);
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	endif;
endif;
