<?php
if (!isset($output)) {
	$output = null;
}
if (!empty($residualRollUpData)){
	// *** $labels variable is defined in the model
	$colGroups = (is_null($output))? $this->AxiaHtml->getColGroups(): null;
	$grandTotalHeaders = [];
	$grandTotal = [];
	$grandTotalReferrals = [];
	$grandTotalNonReferrals = [];
	$headers = [];//subsets headers -- created once and reused for all subsets

	//Vars to track when content is created and avoid duplication or omission of DOM/CSV content
	$gTotalSet = false;
	$sTotalSet = false;
	$subTotal = null;
	$headersCreated = false;
	$gTotalsTableShown = false;

	$gTotalsTable = null;
	
	foreach ($residualRollUpData as $mid => $groupedData) {
		//reset vars for every subset of merchant data
		$row = [];
		$cells = [];
		foreach ($groupedData as $categoryName => $residualReports) {
			$subTotal = [];
			foreach ($residualReports['residualReports'] as $residualReport) {
				foreach ($residualReport as $key => $value) {
					if ($output !== GUIbuilderComponent::OUTPUT_CSV && ($key === 'merchant_mid' || $key === 'merchant_dba')) {
						continue;
					}
					//Create report subtotal for each category and grand totals rows once
					if (!$sTotalSet) {
						switch ($key) {
							case 'bet_name':
								if (!$gTotalSet) {
									$grandTotal[] = __('Totals');
									$grandTotalHeaders[] = null;
									$grandTotalReferrals[] = 'Referral partner total';
									$grandTotalNonReferrals[] = 'Non referral partner total';
								}
								$subTotal[] = __("$categoryName Totals");
								break;
							case 'r_volume':
							case 'total_profit':
							case 'r_profit_amount':
							case 'refer_profit_amount':
							case 'res_profit_amount':
							case 'manager_profit_amount':
							case 'manager_profit_amount_secondary':
							case 'partner_profit_amount':
								$grandTotalHeaders[] = Hash::get($labels, "$key.text");
								if ($output === GUIbuilderComponent::OUTPUT_CSV) {
									$subTotal[] = $this->Number->currency(Hash::get($residualReports, "totals.$key"), 'USD', ['places' => 4, 'after' => false]);
									if (!$gTotalSet) {
										$grandTotal[] = $this->Number->currency(Hash::get($totals, $key), 'USD', ['places' => 4, 'after' => false]);
										$grandTotalReferrals[] = ($key === 'r_volume')? $this->Number->currency(Hash::get($totalsReferrals, $key), 'USD', ['places' => 4, 'after' => false]) : '';
										$grandTotalNonReferrals[] = ($key === 'r_volume')? $this->Number->currency(Hash::get($totalsNonReferrals, $key), 'USD', ['places' => 4, 'after' => false]) : '';
									}
								} else {
									$subTotal[] = CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($residualReports, "totals.$key")));
									if (!$gTotalSet) {
										$grandTotal[] = CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($totals, $key)));
										$grandTotalReferrals[] = ($key === 'r_volume')? CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($totalsReferrals, $key))) : '';
										$grandTotalNonReferrals[] = ($key === 'r_volume')? CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($totalsNonReferrals, $key))) : '';
									}
								}
								break;
							case 'r_items':
								$subTotal[] = Hash::get($residualReports, "totals.$key");
								if (!$gTotalSet) {
									$grandTotal[] = Hash::get($totals, $key);
									$grandTotalHeaders[] = Hash::get($labels, "$key.text");
									$grandTotalReferrals[] = '';
									$grandTotalNonReferrals[] = '';
								}
								break;
							default:
								if ($output === null) {
									$totalsVal = '&nbsp;';
								} else {
									$totalsVal = null;
								}
								if ($output === GUIbuilderComponent::OUTPUT_CSV) {
									$subTotal[] = $totalsVal;
								} else {
									$subTotal = array_merge($subTotal, $this->AxiaHtml->getCollapsibleCel($key, $totalsVal));
								}
						}
					}
					//Create subset Headers once and reuse it for all subsets
					if ($headersCreated === false) {
						if (($output === GUIbuilderComponent::OUTPUT_CSV)) {
							$headers[] = Hash::get($labels, "{$key}.text");
						} else {
							switch ($key) {
								case 'r_profit_amount':
								case 'manager_profit_amount':
								case 'manager_profit_amount_secondary':
									$headers[] = $this->Paginator->sort(Hash::get($labels, "{$key}.sortField"), "<span class='small'>" . Hash::get($labels, "{$key}.text") . "</span>", ['name' => 'sortableCol', 'escape' => false, 'class' => 'btn btn-xs btn-default']);
									break;
								case 'r_per_item_fee':
								case 'r_statement_fee':
								case 'rep_gross_profit':
								case 'rep_pct_of_gross':
								case 'r_profit_pct':
								case 'r_rate_pct':
									$headers[] = [Hash::get($labels, "{$key}.text") => ['name' => $colGroups['repColGroup'], 'class' => 'info hidden']];
									break;
								case 'manager_rate':
								case 'manager_per_item_fee':
								case 'manager_statement_fee':
								case 'manager_gross_profit':
								case 'manager_profit_pct':
								case 'manager_pct_of_gross':
									$headers[] = [Hash::get($labels, "{$key}.text") => ['class' => 'info hidden', 'name' => $colGroups['smColGroup']]];
									break;
								case 'manager2_rate':
								case 'manager2_per_item_fee':
								case 'manager2_statement_fee':
								case 'manager2_gross_profit':
								case 'manager_profit_pct_secondary':
								case 'manager2_pct_of_gross':
									$headers[] = [Hash::get($labels, "{$key}.text") => ['class' => 'info hidden', 'name' => $colGroups['sm2ColGroup']]];
									break;
								case 'partner_rate';
								case 'partner_per_item_fee';
								case 'partner_statement_fee';
								case 'partner_gross_profit';
								case 'partner_pct_of_gross';
								case 'partner_profit_pct';
									$headers[] = [Hash::get($labels, "{$key}.text") => ['class' => 'info hidden', 'name' => $colGroups['prtnrColGroup']]];;
									break;
								case 'referrer_rate':
								case 'referrer_per_item_fee':
								case 'referrer_statement_fee':
								case 'referrer_gross_profit':
								case 'refer_profit_pct':
								case 'referer_pct_of_gross':
									$headers[] = [Hash::get($labels, "{$key}.text") => ['class' => 'info hidden', 'name' => $colGroups['refColGroup']]];
									break;
								case 'reseller_rate':
								case 'reseller_per_item_fee':
								case 'reseller_statement_fee':
								case 'reseller_gross_profit':
								case 'res_profit_pct':
								case 'reseller_pct_of_gross':
									$headers[] = [Hash::get($labels, "{$key}.text") => ['class' => 'info hidden', 'name' => $colGroups['resColGroup']]];
									break;
								default:
									$headers[] = Hash::get($labels, "{$key}.text");
							}
						}
					}
					//Build rows
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$row[$key] = $value;
					} else {
						$row = array_merge($row, $this->AxiaHtml->getCollapsibleCel($key, h($value)));
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
						$gTotalsTable .= $this->Csv->rows([$grandTotal, $grandTotalNonReferrals, $grandTotalReferrals]);
					} else {
						$gTotalsTable = $this->Html->tag('table',
							$this->Html->tableHeaders($grandTotalHeaders) .
							$this->Html->tableCells([$grandTotal, $grandTotalNonReferrals, $grandTotalReferrals]),
							['class' => 'table table-condensed table-hover table-striped', 'style' => "max-width: fit-content;"]);
					}
				}
			}
			//Current product category total
			//Reset subtotal tracker
			$sTotalSet = false;
			//Add last row of current category subset which is the subtotals
			$cells[] = $subTotal;
			if (!isset($spacerColspan)) {
				$spacerColspan = count(Hash::get($residualRollUpData, "$mid.$categoryName.residualReports.0"));
				$spacerColspan -= 2; //subtract the mid and dba columns since we are skipping them
			}
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				$cells[] = [''];
			} else {
				//Pacer between product categories
				$cells[] = [['', ['colspan' => $spacerColspan, 'style' => 'border-top:2px ridge gray']]];
				
			}
			$dba = Hash::get($residualRollUpData, "$mid.$categoryName.residualReports.0.merchant_dba");
		}
		//Print totals table at top once
		if ($gTotalsTableShown === false) {
			$counterMsg =  __('%s items were included in this report.', $resultCount);
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				echo $this->Csv->row([$counterMsg]);

			} else {
				echo $this->Html->tag('div', $counterMsg, ['class' => 'text-center']);
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
			echo $this->Html->tag('div', $this->Html->tag('span', h("$mid - $dba"), ['style' => 'position:absolute;left:25px;z-index:2;', 'name' => 'merchPanelTitle']) . "&nbsp;", 
				['class' => 'bg-primary strong']);
			echo $this->Html->tag('table',
					$this->Html->tableHeaders($headers, [], ['class' => 'nowrap small']) .
					$this->Html->tableCells($cells),
					['class' => 'table table-condensed table-hover table-striped']
				);
			echo $this->Html->tag('/div');
		}

	}
		//Print totals table at bottom once more at the end of it all
		echo $gTotalsTable;
}