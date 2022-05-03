<?php
$headersCreated = false;
$TitleAndHeadersAdded = false;
$subTotalsAdded = false;
$subTotalsRow = [];
$gTotalsRow = [];
$titleRow = [];
$titleColspan = 0;
if (!empty($reportData)) {
	echo '<div class="contentModule">';
	echo __('%s items were included in this report.', $resultCount) . '<br>'; 
	echo '</div>';
	echo '<div id="GrandTotalsTop">';

	echo '</div>';

	foreach ($subsetIndices as $sIdx) {
		if ($sIdx !== '<END>') {
			foreach ($headersMeta as $fieldName => $fieldMeta) {
				if ($fieldName === 'month' || $fieldMeta['model_alias'] == 'Merchant') {
					continue;
				}

				if ($headersCreated === false) {
					$titleColspan += 1;
					$headerTxt = Hash::get($fieldMeta, "text");
					if ($fieldName === 'year') {
						$headerTxt = 'Month/Year';
					}
						$headers[] = $headerTxt;
				}

				$value = Hash::get($reportData, $sIdx . '.' . $fieldMeta['model_alias'] . ".$fieldName");
				switch ($fieldName) {
					//DateFields
					case 'year':
						$value = $value .'-'.  Hash::get($reportData, $sIdx . '.' . $fieldMeta['model_alias'] . ".month") . '-01';
						$value = $this->Time->format($value, '%b %Y');
						$subTotalsRow[$fieldName] = 'Sub Totals';
						break;
					case 'datetime':
					case 'timeline_date_completed':
					case 'date_completed':
					case 'sf_closed_date':
						$subTotalsRow[$fieldName] = '';
						$value = (!empty($value))? $this->Time->date($value) : '--';
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
						$subTotalsRow[$fieldName] = Hash::get($subTotalsRow, $fieldName) + $value;
						$gTotalsRow[$fieldName] = Hash::get($gTotalsRow, $fieldName) + $value;
						$value = $this->Number->currency($value);
						break;
					default:
						$subTotalsRow[$fieldName] = '';
						$value = h($value);
						break;
				}
				$row[] = $value;
			}
			if (empty($titleRow)) {
				$titleRow = [Hash::get($reportData, "$sIdx.Merchant.merchant_mid") .' - '. Hash::get($reportData, "$sIdx.Merchant.merchant_dba")];
			}
			$headersCreated = true;
			$cells[] = $row;
			$row = [];
		} else {
			echo '<table class="table-condesed table-bordered reportTables" style="margin-bottom:1px">';
			echo $this->Html->tableHeaders($titleRow, null, ['class' => 'bg-info small', 'colspan' => $titleColspan]);
			echo $this->Html->tableHeaders($headers, ['class' => 'nowrap'], ['class' => 'nowrap small']);
			echo $this->Html->tableCells($cells);
			echo $this->Html->tableHeaders($subTotalsRow, ['class' => 'bg-success small'], null);
			echo '</table>';
			//reset vars
			$titleRow = [];
			$subTotalsRow = [];
			$row = [];
			$cells = [];
		}

	}
	$totalsHTML = '<table class="table-striped table-condensed text-center" style="max-width: max-content;margin-bottom:2px">';
	$count = 0;
	$totalsHTML .='<tr><th class="bg-success text-center" colspan="100">Grand Totals</th></tr>';
	foreach ($gTotalsRow as $key => $val) {
		if (($count % 7) === 0) {
			$totalsHTML .= '<tr>';
		}

		$totalsHTML .= '<td class="text-left"><strong>' . $headersMeta[$key]['text'] . ':</strong></td>';
		$totalsHTML .= '<td class="text-right">' . $this->Number->currency($val) . '</td>';

		if ($count > 0 && ( ($count +1) % 7) === 0) {
			$totalsHTML .= '</tr>';
		}
		$count +=1;
	}
	//if count mod 7 != 0 we still need to close the row
	if (($count % 7) !== 0) {
		$totalsHTML .= '</tr>';
	}
	$totalsHTML .= '</table>';
	echo $totalsHTML;
	echo "<script>$('#GrandTotalsTop').html('$totalsHTML')</script>";
}
