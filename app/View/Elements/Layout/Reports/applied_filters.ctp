<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($appliedFilters)) {
	$labels = array(
		'user_id' => __('Entity/Rep'),
		'merchant_dba' => __('Merchant'),
		'from_date' => __('From Date'),
		'end_date' => __('End Date'),
		'res_months' => __('Residual Data'),
	);

	$headers = [];
	$cells = [];
	$row = [];
	foreach ($appliedFilters as $key => $value) {
		$headers[] = Hash::get($labels, $key);
		switch ($key) {
			case 'from_date':
			case 'end_date':
				$row[$key] = $this->Time->format($value, '%B %Y');
				break;

			default:
				$row[$key] = $value;
		}
	}

	$cells[] = $row;

	// Display data based on output
	if ($output === GUIbuilderComponent::OUTPUT_CSV) {
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	}
}
