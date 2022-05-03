<?php
if (!isset($output)) {
	$output = null;
}
if (!empty($reportData)) {
	$totalsRow = [];
	$headers = [];
	$cells = [];
	$grandTotal = Hash::get($reportData, 'totals.grand_total');
	unset($reportData['totals']);
	foreach ($reportData as $idx => $report) {
		$row = [];
		foreach ($report as $key => $value) {
			// *** $labels variable is defined in the model
			//Create headers and totals row once with the first array entry
			if ($idx === 0) {
				if ($key === 'id' || $key === 'merchant_id') {
					continue;
				}
				$totalsCellText = null;
				if ($key === 'total_ach') {
					$totalsCellText = ($output === GUIbuilderComponent::OUTPUT_CSV)? $grandTotal : ['Grand Total:<br/>' . $this->Number->currency($grandTotal, 'USD2dec'), ['class' => 'strong text-success bg-success roundEdges nowrap']];
				}

				$sortField = Hash::get($labels, "{$key}.sortField");
				if (($output === GUIbuilderComponent::OUTPUT_CSV) || empty($sortField)) {
					$header = Hash::get($labels, "{$key}.text");
				} else{
					$header = $this->Paginator->sort($sortField, Hash::get($labels, "{$key}.text"), Hash::extract($labels, "{$key}.sort_options"));
				}
				$totalsRow[] = $totalsCellText;
				$headers[] = $header;

			}//done headers

			switch ($key) {
				case 'id':
				case 'merchant_id':
					break;
				case 'merchant_mid':
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$row[$key] = Hash::get($report, 'merchant_mid');
				} else {
					$row[$key] = $this->Html->link(Hash::get($report, 'merchant_mid'), ['controller' => 'Merchants', 'action' => 'view', Hash::get($report, 'merchant_id')]);
				}
					break;
				case 'accounting_mo_yr':
					$row[$key] = (!empty(Hash::get($report, $key)))? $this->Time->format('M Y', Hash::get($report, $key)) : null;
					break;
				case 'date_completed':
					$row[$key] = $this->AxiaTime->date(Hash::get($report, $key));
					break;
				case 'ach_setups':
				case 'app_fees':
				case 'equip_repair_fees':
				case 'equip_sales':
				case 'replace_fees':
				case 'software_setup_fees':
				case 'licence_setup_fees':
				case 'client_implement_fees':
				case 'termination_fees':
				case 'shipping_fees':
				case 'reject_fees':
				case 'rental_fees':
				case 'misc_fees':
				case 'tax':
				case 'total_ach':
				case 'tax_amount_state':
				case 'tax_amount_county':
				case 'tax_amount_city':
				case 'tax_amount_district':
				case 'non_taxable_ach_amount':
				case 'ach_amount':
				case 'subtotal':
				case 'expedite_fees':
				case 'account_setup_fees':
				case 'replacement_shipping_fees':
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$row[$key] = Hash::get($report, $key);
					} else {
						$row[$key] = $this->Number->currency(Hash::get($report, $key), 'USD2dec');
					}
					break;
				case 'status':
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$row[$key] = (Hash::get($report, $key) === GUIbuilderComponent::STATUS_COMPLETED)? 'Complete': 'Pending';
					} else {
						$settings = ['class' => 'center-block'];
						if (Hash::get($report, $key) === GUIbuilderComponent::STATUS_COMPLETED) {
							$statusImg = '/img/icon_greenflag.gif';
						} else {
							$statusImg = '/img/icon_redflag.gif';
							$settings['name'] = 'pending-invoice';
							$settings['data-pend-inv-id'] = Hash::get($report, 'id');
						}

						$row[$key] = $this->Html->link($this->Html->image($statusImg, $settings), 'javascript:void(0)',
							[
							'data-toggle' => 'tooltip',
							'data-placement'=> 'right',
							'data-original-title' => (Hash::get($settings, "name"))? 'Mark as Complete': 'Revert to Pending',
							'escape' => false,
							'onClick' => "updateStatus('". (int)(Hash::get($settings, "name") == 'pending-invoice') . "', '" . Hash::get($report, 'id') .  "')",
							'class' => 'btn btn-sm btn-default'
						]);
					}
					break;
				default:
					$row[$key] = h(Hash::get($report, $key));
					if ($output === GUIbuilderComponent::OUTPUT_CSV) {
						$row[$key] = Hash::get($report, $key);
					}
					break;
			}
		}
		$cells[] = $row;
	}
	if ($output === GUIbuilderComponent::OUTPUT_CSV) {
		echo $this->Csv->row($headers);
		echo $this->Csv->row($totalsRow);
		echo $this->Csv->rows($cells);
		echo $this->Csv->row($totalsRow);
	} else { ?>
			<span class='list-group-item list-group-item-info'>
				<strong class="text-primary">Show/Hide columns:</strong>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="12">ACH Set Ups</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="13">Application Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="14">Equipment Repair & Maintenance</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="15">Equipment Sales</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="16">Replacement Income</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="17">Expedite Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="18">Gateway Setup Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="19">License Setup Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="20">Account Setup Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="21">Client Implementation Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="22">Termination Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="23">Shipping</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="24">Replacement Shipping</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="25">Reject Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="26">Rental Fees</a>
				<a class="toggle-vis btn-xs btn-success" href="javascript:void(0)" data-column="27">Miscellaneous Fees</a>
				<br>
				<?php echo __('%s items were included in this report.', $resultCount); ?>
			</span><br>
		<table class="table table-condensed reportTables" id='accountingReportTable'>
			<thead>
				<?php
				echo $this->Html->tableHeaders($headers, [], ['class' => 'small']);
				?>
			</thead>
			<?php
			echo $this->Html->tableCells($totalsRow);
			echo $this->Html->tableCells($cells);
			echo $this->Html->tableCells($totalsRow);
			?>
		</table>
	<?php
	}
}