<?php
if (!isset($output)) {
	$output = null;
}
if ($output === null) {
	echo $this->AssetCompress->css('custom-bootstrap', array(
		'raw' => (bool)Configure::read('debug')
	));
}

if (!empty($commissionReports)):

	$labels = [
		'mid' => $this->Paginator->sort('Merchant.merchant_mid', __('MID')),
		'dba' => $this->Paginator->sort('Merchant.merchant_dba', __('DBA')),
		'client_id_global' => __('Client ID'),
		'axia_invoice' => __('Axia Inv'),
		'rep' => $this->Paginator->sort('User.fullname', __('Rep')),
		'description' => $this->Paginator->sort('CommissionReport.description', __('Description')),
		'retail' => __('Retail'),
		'rep_cost' => __('Rep Cost'),
		'partner_cost' => __('Partner Cost'),
		'shipping_type' => __('Shipping Type'),
		'shipping_cost' => $this->Paginator->sort('CommissionReport.c_shipping', __('Shipping Cost')),
		'expedited' => $this->Paginator->sort('MerchantUws.expedited', __('Expedited')),
		'rep_profit' => __('Rep Profit'),
		'partner_profit' => __('Partner Profit'),
		'axia_profit' => __('Axia Profit'),
		'tax_amount' => __('Tax'),
		'state' =>  __('State'),
		'ent_name' => __('Company'),
		'organization' => _('Organization'),
		'region' => _('Region'),
		'subregion' => _('Subregion'),
		'location' => _('Location'),
		'partner_name' => __('Partner'),
	];

	$cells = [];
	foreach ($commissionReports as $commissionReport) {
		// Create the headers the first time (for export, CSV headers are created in the controller)
		if (!isset($headers)) {
			foreach ($commissionReport as $key => $value) {
				$headers[] = [Hash::get($labels, $key) => ['class' => 'nowrap']];
			}
		}

		$row = [];
		foreach ($commissionReport as $key => $value) {
			switch ($key) {
				case 'order_items': 
					//This key is handled diferently below
					break;
				case 'description':
					if ($value === 'Equipment Order') {
						$value = "(Equipment order items listed below)";
					}
					$row[] = ($output === GUIbuilderComponent::OUTPUT_CSV)? $value: h($value);
					break;
				case 'shipping_type':
					$row[] = ($output === GUIbuilderComponent::OUTPUT_CSV)? Hash::get($shippingTypes, $value): h(Hash::get($shippingTypes, $value));
					break;
				case 'retail':
				case 'rep_cost':
				case 'partner_cost':
				case 'shipping_cost':
				case 'rep_profit':
				case 'partner_profit':
				case 'axia_profit':
				case 'tax_amount':
					$row[] = CakeNumber::currency($value, null, [
						'zero' => '-',
					]);
					break;
				default:
					$row[] = ($output === GUIbuilderComponent::OUTPUT_CSV)? $value: h($value);
			}
		}
		$cells[] = $row;
	}

	// Add totals
	$totalsRow = [];
	foreach ((array)Hash::get($commissionReports, 0) as $key => $value) {
		switch ($key) {
			case 'mid':
				$totalsRow[] = __('Totals');
				break;

			case 'rep_profit':
			case 'partner_profit':
			case 'axia_profit':
			case 'tax_amount':
				$totalsRow[] = CakeNumber::currency(Hash::get($commissionReportTotals, $key));
				break;

			default:
				if ($output === null) {
					$totalsRow[] = '&nbsp;';
				} else {
					$totalsRow[] = null;
				}
		}
	}
	$cells[] = $totalsRow;


	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
<br/>
	<div class="text-center bg-info">
		<?php 
		echo $this->Paginator->counter(array(
			'format' => __('Showing {:current} records out of {:count} total.')
		));
		?>
	</div>
		<table class = "table table-condesed">
			<?php
			echo $this->Html->tableHeaders($headers);
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	endif;
endif;
