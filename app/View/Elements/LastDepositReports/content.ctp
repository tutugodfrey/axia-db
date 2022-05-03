<?php
if (!isset($output)) {
	$output = null;
}
?>
<?php
if (!empty($lastDepositReports)) :
	$fields = [
		//Model field => header
		'Merchant.merchant_mid' => __('MID'),
		'Merchant.merchant_dba' => __('DBA'),
		'Client.client_id_global' => __('Client ID'),
		'LastDepositReport.last_deposit_date' => __('Last Activity Date'),
		'ApprovedUwStatus.datetime' => __('Approval Date'),
		'InstalledTimeLineEntry.timeline_date_completed' => __('Go-Live Date'),
		'LastDepositReport.sales_num' => __('Last Activity Sales'),
		'LastDepositReport.monthly_volume' => __('Last Activity Volume'),
		'Merchant.User.initials' => 'Rep Initials',
		'Merchant.Organization.name' => 'Organization',
		'Merchant.Region.name' => 'Region',
		'Merchant.Subregion.name' => 'Subregion',
		'Merchant.AddressBus.address_street' => 'Location',
	];

	$cells = [];
	$headers = [];
	$headersCreated = false;
	foreach ($lastDepositReports as $lastDepositReport) {
		$row = [];
		//extract array data using $fields array
		foreach ($fields as $key => $val) {
			// Create the headers only once
			if (!$headersCreated) {
				if ($output === GUIbuilderComponent::OUTPUT_CSV) {
					$headers[] = $val;
				} else {
					//Second and Third level deep associations cannot be sorted.
					//Level depth can be infered by the number of dots in the dot notation
					if (substr_count($key, '.') >= 2) {
						$headers[] = h($val);
					} else {
						$headers[] = $this->Paginator->sort($key, $val);
					}
				}				
			}
			$dataValue = Hash::get($lastDepositReport, $key);
			if ($key === 'LastDepositReport.last_deposit_date' || $key === 'ApprovedUwStatus.datetime' || $key === 'InstalledTimeLineEntry.timeline_date_completed') {
				$dataValue = $this->Time->date($dataValue);
			} elseif ($key === 'LastDepositReport.monthly_volume') {
				$dataValue = $this->Number->currency($dataValue);
			}
			if ($output === GUIbuilderComponent::OUTPUT_CSV) {
				//Useing key as Hash path
				$row[] = $dataValue;
			} else {
				//@TODO Echo HTML CELL WITH LINKS WHENEVER NEEDED
				if ($key === 'Merchant.merchant_mid') {
					$row[] = $this->Html->link($dataValue, array('controller' => 'Merchants', 'action' => 'view', Hash::get($lastDepositReport, 'Merchant.id')));
				} elseif ($key === 'Merchant.User.initials') {
					$row[] = $this->Html->link($dataValue, array('controller' => 'Users', 'action' => 'view', Hash::get($lastDepositReport, 'Merchant.User.id')));
				} else {
					$row[] = h($dataValue);
				}
			}
			
		}
		$headersCreated = true;
		$cells[] = $row;
	}

	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
		<br/>
		<table class = "table table-condesed table-hover table-striped">
			<?php
			echo $this->Html->tableHeaders($headers);
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	endif;
endif;
