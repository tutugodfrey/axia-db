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
if (!empty($residualReports)):
	$colGroups = $this->AxiaHtml->getColGroups();
	$headersCreated = false;
	$headers = [];
	$cells = [];
	//Display Totals at the top as well as the bottom
	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($residualReports, 0) as $key => $value) {
		switch ($key) {
			case 'merchant_mid':
				$totalsRow[] = __('Totals');
				break;
			case 'r_volume':
			case 'r_profit_amount':
			case 'refer_profit_amount':
			case 'res_profit_amount':
			case 'manager_profit_amount':
			case 'manager_profit_amount_secondary':
			case 'partner_profit_amount':
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$totalsRow[] = $this->Number->currency(Hash::get($totals, $key), 'USD', ['places' => 4]);
				} else {
					$totalsRow[] = CakeNumber::currency(GUIbuilderComponent::setDecimalsNoRounding(Hash::get($totals, $key))); 
				}
				
				break;
			case 'r_items':
				$totalsRow[] = Hash::get($totals, $key);
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

	// Add totalsNonReferral
	$totalsNonReferral = [];
	foreach ((array)Hash::get($residualReports, 0) as $key => $value) {
		switch ($key) {
			case 'merchant_mid':
				$totalsNonReferral[] = __('Non referral partner total');
				break;

			case 'r_volume':
				$totalsNonReferral[] = CakeNumber::currency(Hash::get($totalsNonReferrals, $key));
				break;

			default:
				if ($output === null) {
					$totalsNonRefVal = '&nbsp;';
				} else {
					$totalsNonRefVal = null;
				}
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$totalsNonReferral[] = $totalsNonRefVal;
				} else {
					$totalsNonReferral = array_merge($totalsNonReferral, $this->AxiaHtml->getCollapsibleCel($key, $totalsNonRefVal));
				}
		}
	}
	$cells[] = $totalsNonReferral;

	// Add totals
	$totalsReferral = [];
	foreach ((array)Hash::get($residualReports, 0) as $key => $value) {
		switch ($key) {
			case 'merchant_mid':
				$totalsReferral[] = __('Referral partner total');
				break;
			case 'r_volume':
				$totalsReferral[] = CakeNumber::currency(Hash::get($totalsReferrals, $key));
				break;

			default:
				if ($output === null) {
					$totalsRefVal = '&nbsp;';
				} else {
					$totalsRefVal = null;
				}
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$totalsReferral[] = Hash::get($totalsReferrals, $key);
				} else {
					$totalsReferral = array_merge($totalsReferral, $this->AxiaHtml->getCollapsibleCel($key, $totalsRefVal));
				}
		}
	}
	$cells[] = $totalsReferral;
	$cells[] = [' '];//Spacer
	foreach ($residualReports as $residualReport) {
		$row = [];
		foreach ($residualReport as $key => $value) {
			if ($headersCreated === false) {
				// *** $labels variable is defined in the model
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
						case 'r_per_item_fee':
						case 'r_statement_fee':
						case 'rep_gross_profit':
						case 'rep_pct_of_gross':
						case 'r_profit_pct':
						case 'r_rate_pct':
						case 'Rep Gross Profit':
						case 'Rep % of Gross':
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
						case 'manager_rate':
						case 'manager_per_item_fee':
						case 'manager_statement_fee':
						case 'manager_gross_profit':
						case 'manager_profit_pct':
						case 'manager_pct_of_gross':
						case 'SM Rate':
						case 'SM P/I':
						case 'SM Stmt/Gtwy':
						case 'SM Gross Profit':
						case 'SM % of Gross':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['class' => 'info hidden', 'name' => $colGroups['smColGroup']]];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['smColGroup']]];
								}
							}
							$headers[] = $header;
							break;
						case 'manager2_rate':
						case 'manager2_per_item_fee':
						case 'manager2_statement_fee':
						case 'manager2_gross_profit':
						case 'manager_profit_pct_secondary':
						case 'manager2_pct_of_gross':
						case 'SM2 Rate':
						case 'SM2 P/I':
						case 'SM2 Stmt/Gtwy':
						case 'SM2 Gross Profit':
						case 'SM2 % of Gross':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['class' => 'info hidden', 'name' => $colGroups['sm2ColGroup']]];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['sm2ColGroup']]];
								}
							}
							$headers[] = $header;
							break;
						case 'partner_rate';
						case 'partner_per_item_fee';
						case 'partner_statement_fee';
						case 'partner_gross_profit';
						case 'partner_pct_of_gross';
						case 'partner_profit_pct';
						case 'Partner Rate';
						case 'Partner P/I';
						case 'Partner Stmt/Gtwy';
						case 'Partner Gross Profit';
						case 'Partner % of Gross';
						case 'Partner Profit %';
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['class' => 'info hidden', 'name' => $colGroups['prtnrColGroup']]];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['prtnrColGroup']]];
								}
							}
							$headers[] = $header;
							break;
						case 'referrer_rate':
						case 'referrer_per_item_fee':
						case 'referrer_statement_fee':
						case 'referrer_gross_profit':
						case 'refer_profit_pct':
						case 'referer_pct_of_gross':
						case 'Ref Rate':
						case 'Ref P/I':
						case 'Ref Stmt/Gtwy':
						case 'Ref Gross Profit':
						case 'Ref %':
						case 'Ref % of Gross':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['class' => 'info hidden', 'name' => $colGroups['refColGroup']]];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['refColGroup']]];
								}
							}
							$headers[] = $header;
							break;
						case 'reseller_rate':
						case 'reseller_per_item_fee':
						case 'reseller_statement_fee':
						case 'reseller_gross_profit':
						case 'res_profit_pct':
						case 'reseller_pct_of_gross':
						case 'Res Rate':
						case 'Res P/I':
						case 'Res Stmt/Gtwy':
						case 'Res Gross Profit':
						case 'Res %':
						case 'Res % of Gross':
							if ($output === GUIbuilderComponent::OUTPUT_CSV) {
								$header = $case;
							} else {
								if (empty($sortField)) {
									$header = [$case => ['class' => 'info hidden', 'name' => $colGroups['resColGroup']]];
								} else {
									$header = [$this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text")) => ['class' => 'info hidden', 'name' => $colGroups['resColGroup']]];
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
				$row = array_merge($row, $this->AxiaHtml->getCollapsibleCel($key, h($value)));
			}
		}
		$headersCreated = true;
		$cells[] = $row;
	}
	//Display Totals at the bottom as well as the top
	$cells[] = $totalsRow;
	$cells[] = $totalsNonReferral;
	$cells[] = $totalsReferral;
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
		<table class="table table-condensed reportTables">
			<?php
			echo $this->Html->tableHeaders($headers, ['class' => 'nowrap'], ['class' => 'nowrap small']);
			echo $this->Html->tableCells($cells);
			?>
		</table>
		<?= $this->element('pagination'); ?>
	<?php
	endif;
endif;
