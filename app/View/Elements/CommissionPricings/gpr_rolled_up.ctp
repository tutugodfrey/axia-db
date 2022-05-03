<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($reportRollUpData)):
	$grandTotalHeaders = [];
	$grandTotal = [];
	$headers = [];//subsets headers -- created once and reused for all subsets
	//Vars to track when content is created and avoid duplication or omission of DOM/CSV content
	$gTotalSet = false;
	$sTotalSet = false;
	$subTotal = null;
	$headersCreated = false;
	$gTotalsTableShown = false;

	$gTotalsTable = null;

	foreach($reportRollUpData as $mid => $groupedData) {
		//reset vars for every subset of merchant data
		$row = [];
		$cells = [];
		$mid = h($mid);
		foreach ($groupedData as $categoryName => $grossProfitReports) {
			$subTotal = [];
			$dba = h(Hash::get($reportRollUpData, "$mid.$categoryName.grossProfitEstimates.0.dba"));
			$rep = h(Hash::get($reportRollUpData, "$mid.$categoryName.grossProfitEstimates.0.rep"));
			foreach ($grossProfitReports['grossProfitEstimates'] as $gpReport) {
				foreach ($gpReport as $key => $value) {
					if ($output !== GUIbuilderComponent::OUTPUT_CSV && ($key === 'mid' || $key === 'dba' || $key === 'rep' || $key === 'bus_level')) {
						continue;
					}
					//Create report subtotal for each category and grand totals rows once
					if (!$sTotalSet) {
						switch ($key) {
							case ($key === 'mid' && $output === GUIbuilderComponent::OUTPUT_CSV):
								$subTotal[] = $mid;
								break;
							case ($key === 'dba' && $output === GUIbuilderComponent::OUTPUT_CSV):
								$subTotal[] = $dba;
								break;
							case 'bet':
								if (!$gTotalSet) {
									$grandTotal[] = __('Totals');
									$grandTotalHeaders[] = null;
								}
								
								if ($output === GUIbuilderComponent::OUTPUT_CSV) {
									$subTotal[] = "$categoryName Totals";
								} else {
									$subTotalLabel = ($categoryName === 'Credit')? "$mid $dba<br>$categoryName" : h($categoryName);
									$subTotal[] = [__("$subTotalLabel Totals"), ['colspan' => 5, 'class' => 'bg-success strong']];
								}
								break;

							case 'product':
							case 'sub_date':
							case 'days_to_approved':
							case 'app_date':
								//Skipping web UI ouput for these columns to make use of colspan for the subtotal label
								if ($output === GUIbuilderComponent::OUTPUT_CSV) {
									$subTotal[] = null;
								}
								break;
							case 'volume':
							case 'multiple_amount':
							case 'rep_gross_profit':
								if (!$gTotalSet) {
									$grandTotalHeaders[] = Hash::get($labels, "$key.text");
									$grandTotal[] = CakeNumber::currency(Hash::get($totals, $key));
								}
								if ($output === GUIbuilderComponent::OUTPUT_CSV) {
									$subTotal[] = CakeNumber::currency(Hash::get($grossProfitReports, "grossProfitEstimateTotals.$key"));
								} else {
									$subTotal[] = [CakeNumber::currency(Hash::get($grossProfitReports, "grossProfitEstimateTotals.$key")), ['class' => 'bg-success strong']];
								}
								break;
							default:
								if ($output === null) {
									$subTotal[] = ['', ['class' => 'bg-success']];
								} else {
									$subTotal[] = null;
								}
						}
					}
					//Create subset Headers once and reuse it for all subsets
					if ($headersCreated === false) {
						$headers[] = Hash::get($labels, "{$key}.text");
					}
					//Build rows
					switch ($key) {
						case 'items':
							$row[$key] = ($value !== null) ? CakeNumber::precision($value, 1) : null;
						break;
						case 'product':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$row[$key] = $value;
							} else {
								$row[$key] = [$value, ['class' => 'nowrap']];
							}
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
				//Subtotal done for current category
				$sTotalSet = true;
				//Headers done
				$headersCreated = true;
				//Grand totals all done
				$gTotalSet = true;

				if (empty($gTotalsTable)) {
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$gTotalsTable = $this->Csv->row($grandTotalHeaders);
						$gTotalsTable .= $this->Csv->rows([$grandTotal]);
					} else {
						$gTotalsTable = $this->Html->tag('table',
							$this->Html->tableHeaders($grandTotalHeaders) .
							$this->Html->tableCells([$grandTotal]),
							['class' => 'table table-condensed table-hover table-striped center-block', 'style' => "max-width: fit-content;"]);
					}
				}
			}
			//Current product category total
			//Reset subtotal tracker
			$sTotalSet = false;
			//Add last row of current category subset which is the subtotals
			$cells[] = $subTotal;
			if (!isset($spacerColspan)) {
				$spacerColspan = count(Hash::get($reportRollUpData, "$mid.$categoryName.grossProfitEstimates.0"));
				$spacerColspan -= 2; //subtract the mid and dba columns since we are skipping them
			}
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				$cells[] = [''];
			} else {
				//Pacer between product categories
				$cells[] = [['', ['colspan' => $spacerColspan, 'style' => 'border-top:2px ridge gray']]];
				
			}
			
			$busLvl = "Buisness Level: " . Hash::get($reportRollUpData, "$mid.$categoryName.grossProfitEstimates.0.bus_level");
		}
		//Print totals table at top once
		if ($gTotalsTableShown === false) {
			$counterMsg =  __('%s items were included in this report.', $totalRecords);
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				echo $this->Csv->row([$counterMsg]);
			} else {
				echo $this->Html->tag('div', $counterMsg, ['class' => 'text-center bg-info']);
			}
			$gTotalsTableShown = true;
			echo $gTotalsTable;
		}
		//Here we echo each and every data subset
		if ($output === GUIbuilderComponent::OUTPUT_CSV) {
			if (!isset($csvHeadersShownOnce)) {
				echo $this->Csv->row($headers);
				$csvHeadersShownOnce = true;
			}
			echo $this->Csv->rows($cells);
		} else {
			echo $this->Html->tag('div', null, ['class' => 'panel panel-primary']);
			echo $this->Html->tag('div', $this->Html->tag('span', "$mid - $dba | $busLvl | $rep", ['style' => 'position:absolute;left:25px;z-index:2;', 'name' => 'merchPanelTitle']) . "&nbsp;", 
				['class' => 'bg-primary strong']);
			echo $this->Html->tag('table',
					$this->Html->tableHeaders($headers, [], ['class' => 'small']) .
					$this->Html->tableCells($cells),
					['class' => 'table table-condensed table-hover table-striped']
				);
			echo $this->Html->tag('/div');
		}
	}
	//Print totals table at bottom once more at the end of it all
	echo $gTotalsTable;

	// Display data based on output
	if ($output === null): ?>
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