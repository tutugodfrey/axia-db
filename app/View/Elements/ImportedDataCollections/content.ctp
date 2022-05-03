<?php
$csvOutput = (!isset($output))? false: true;
$headersCreated = false;
if (!empty($reportData)) {
	foreach ($reportData as $idx => $data) {
		$row = null;
		foreach ($headersMeta as $fieldName => $fieldMeta) {
			if ($csvOutput == false && ($fieldName === 'month' || $fieldName === 'year')) {
				continue;
			} elseif ($csvOutput && $fieldName === 'month') {
				continue;
			}
			if ($headersCreated === false) {
				$sortField = Hash::get($fieldMeta, "sortField");
				$headerTxt = Hash::get($fieldMeta, "text");
				if ($csvOutput) {
					if ($fieldName === 'year') {
						$headerTxt = 'Month/Year';
					}
					$headers[] = $headerTxt;
				} else {
					$headers[] = (!empty($sortField))? $this->Paginator->sort($sortField, $headerTxt): $headerTxt;
				}
			}

			$value = Hash::get($data, $fieldMeta['model_alias'] . ".$fieldName");
			switch ($fieldName) {
				//DateFields
				case 'year':
						$value = $value .'-'.  Hash::get($data, $fieldMeta['model_alias'] . ".month") . '-01';
						$value = $this->Time->format($value, '%b %Y');
						break;
				case 'datetime':
				case 'timeline_date_completed':
				case 'date_completed':
				case 'sf_closed_date':
					if (!$csvOutput) {
						$value = (!empty($value))? $this->Time->date($value) : '--';
					}
					break;
				//Money Fields
				case 'r_profit_amount':
				case 'manager_profit_amount':
				case 'manager_profit_amount_secondary':
				case 'gw_n1_vol':
				case 'gw_n2_vol':
				case 'pf_total_gw_vol':
				case 'pf_recurring_rev':
				case 'pf_recurring_item_rev':
				case 'pf_recurring_device_lic_rev':
				case 'pf_recurring_gw_rev':
				case 'pf_recurring_acct_rev':
				case 'pf_one_time_rev':
				case 'acquiring_one_time_rev':
				case 'pf_one_time_cost':
				case 'acquiring_one_time_cost':
				case 'ach_recurring_rev':
				case 'ach_recurring_gp':
				case 'pf_rev_share':
				case 'sf_projected_acq_vol':
				case 'sf_projected_pf_revenue':
				case 'sf_projected_pf_recurring_ach_revenue':
				case 'sf_projected_pf_recurring_ach_gp':
				case 'mo_gateway_cost':
				case 'profit_loss_amount':
				case 'net_sales':
				case 'gross_sales':
				case 'discount':
				case 'interchng_income':
				case 'interchng_expense':
				case 'other_income':
				case 'other_expense':
				case 'total_income':
				case 'total_expense':
				case 'gross_profit':
				case 'pf_per_item_fee':
				case 'pf_item_fee_total':
				case 'pf_monthly_fees':
				case 'revised_gp':
				case 'total_income_minus_pf':
				case 'card_brand_expenses':
				case 'processor_expenses':
				case 'sponsor_cogs':
				case 'ithree_monthly_cogs':
				case 'pf_actual_mo_vol':
					$value = $this->Number->currency($value);
					break;
				default:
					if (!$csvOutput) {
						$value = h($value);
					}
					break;
			}

			if ($csvOutput) {
				$row[$fieldName] = (!empty($value))? $value : '';
			} else {
				$row[] = $value;
			}
			
		}
		$headersCreated = true;
		$cells[] = $row;
	}

	$recordCount = $idx +1;
	if ($csvOutput) {
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	} else {
		?>
		<div class="contentModule">
			<?php echo __('%s items were included in this report.', $recordCount); ?><br>
		</div>
		<?php
		$this->Html->showPaginator($recordCount);
		?>
		<table class="table table-condensed reportTables">
		<?php
			echo $this->Html->tableHeaders($headers, ['class' => 'nowrap'], ['class' => 'nowrap small']);
			echo $this->Html->tableCells($cells);
		?>
		</table>
		<?php
	}
}