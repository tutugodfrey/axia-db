<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($income)):
	if ($output === GUIbuilderComponent::OUTPUT_CSV) {
		$headers = [
			__('Income')
		];
	} else {
		$headers = [
			[__('Income') => ['colspan' => 2]],
		];
	}

	$labels = [
		'rep_residuals' => __('Rep/PartnerRep Residuals'),
		'sm_residuals' => __('SM Residuals'),
		'sm2_residuals' => __('SM2 Residuals'),
		'partner_residuals' => __('Partner Residuals'),
		'commission' => __('Commission'),
		'multiple_amount' => __('Commission Multiple'),
		'manager_multiple_amount' => __('SM Commission Multiple'),
		'manager2_multiple_amount' => __('SM2 Commission Multiple'),
		'partner_commission' => __('Partner Commission'),
		'gross_income' => __('Gross Income'),
	];
	//Preserve the order above
 	$labels = array_intersect_key($labels, $income);
 	$income = array_merge($labels, $income);
	$cells = [];
	foreach ($income as $key => $value) {
		$labelOptions = ['class' => 'small-label nowrap'];
		$valueOptions = [];
		switch ($key) {
			case 'rep_residuals':
				$currencyOptions['zero'] = '-';
				break;

			case 'gross_income':
				$labelOptions['class'] = 'small-label strong';
				$valueOptions['class'] = 'strong';
				break;
		}

		$label = Hash::get($labels, $key);
		$value = CakeNumber::currency($value, null, $currencyOptions);
		if ($output === GUIbuilderComponent::OUTPUT_CSV) {
			$cells[] = [$label, $value];
		} else {
			$cells[] = [
				[$label, $labelOptions],
				[$value, $valueOptions],
			];
		}
	}

	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	else: ?>
		<table>
			<?php echo $this->Html->tableHeaders($headers); ?>
			<?php echo $this->Html->tableCells($cells); ?>
		</table>
	<?php
	endif;
endif;
