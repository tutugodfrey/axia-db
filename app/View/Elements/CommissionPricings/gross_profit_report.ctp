<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($grossProfitEstimates)):
	$headers = [];
	$cells = [];
	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($grossProfitEstimates, 0) as $key => $value) {
		switch ($key) {
			case 'mid':
				$totalsRow[] = __('Totals');
				break;

			case 'volume':
			case 'multiple_amount':
			case 'resid_gross_profit':
			case 'rep_gross_profit':
			case 'rep_residual_gross_profit':
			case 'rep_profit':
			case 'partner_rep_gross_profit':
			case 'partner_rep_residual_gross_profit':
			case 'partner_rep_profit':
				$totalsRow[] = CakeNumber::currency(Hash::get($grossProfitEstimateTotals, $key));
				break;

			default:
				if ($output === null) {
					$totalsRow[] = '&nbsp;';
				} else {
					$totalsRow[] = null;
				}
		}
	}
	//Display totals at the top as well as bottom
	$cells[] = $totalsRow;

	foreach ($grossProfitEstimates as $grossProfitEstimate) {
		// Create the headers the first time
		if (empty($headers)) {
			foreach ($grossProfitEstimate as $key => $value) {
				$sortField = Hash::get($labels, "{$key}.sortField");
				if (($output === GUIbuilderComponent::OUTPUT_CSV) || empty($sortField)) {
					$headers[] = Hash::get($labels, "{$key}.text");
				} else {
					$headers[] = $this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text"));
				}
			}
		}

		$row = [];
		foreach ($grossProfitEstimate as $key => $value) {
			switch ($key) {
				case 'items':
					$row[$key] = ($value !== null) ? CakeNumber::precision($value, 1) : null;
				break;
				case 'sub_date':
				case 'app_date':
				case 'install_date':
				case 'recd_install_sheet':
				case 'com_month':
					$row[$key] = ($this->Time->format($value, '%m-%d-%Y'))? : $value;
					break;

				case 'volume':
				case 'avg_tkt':
				case 'merch_p_i':
				case 'merch_stmt':
				case 'multiple_amount':
				case 'resid_gross_profit':
				case 'rep_p_i':
				case 'rep_stmt':
				case 'rep_gross_profit':
				case 'rep_residual_gross_profit':
				case 'rep_profit':
				case 'partner_rep_p_i':
				case 'partner_rep_stmt':
				case 'partner_rep_gross_profit':
				case 'partner_rep_residual_gross_profit':
				case 'partner_rep_profit':
					$row[$key] = is_numeric($value)? CakeNumber::currency($value, null, [
						'zero' => '-']) : $value;
					break;

				case 'merch_rate':
				case 'bet_extra_pct':
				case 'original_merch_rate':
				case 'multiple':
				case 'rep_rate':
				case 'rep_pct_of_gross':
				case 'rep_profit_pct':
				case 'partner_rep_rate':
				case 'partner_rep_pct_of_gross':
				case 'partner_rep_profit_pct':
					$row[$key] = ($value !== null) ? CakeNumber::toPercentage($value) : null;
					break;

				default:
					$row[$key] = $value;
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

		<div class="contentModule">
			<?php echo __('%s items were included in this report.', count($grossProfitEstimates)); ?>
		</div>
		<div>
			<table>
				<?php
				echo $this->Html->tableHeaders($headers);
				echo $this->Html->tableCells($cells);
				?>
			</table>
		</div>
		<div class="contentModule">
			<?php echo __('The multiple amount paid to affiliates is based on the projected residual gross profit for each individual account signed.'); ?><br>
			<?php echo __('The projected gross profit is determined by calculating the profit margin, volume and average ticket documented on the merchant application.'); ?><br>
			<?php echo __('Any referral payments are subtracted to reach the projected residual gross profit.'); ?><br>
			<?php echo __('It is important to recognize that Axia reserves the right to correct for discrepancies on these payments as the actual gross profit becomes available.'); ?><br>
			<?php echo __('This correction will be based on the difference between actual residual gross profit and projected residual gross profit over the first seven (7) months each merchant has been processing with Axia.'); ?><br>
		</div>
	<?php
	endif;
endif;
?>