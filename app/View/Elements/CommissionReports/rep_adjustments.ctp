<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($repAdjustments)):
	if ($output === GUIbuilderComponent::OUTPUT_CSV) {
		$headers = [
			__('Rep Adjustments')
		];
	} else {
		$headers = [
			[__('Rep Adjustments') => ['colspan' => 2, 'class' => 'bg-info']],
		];
	}

	$cells = [];
	foreach ((array)Hash::get($repAdjustments, 'adjustments') as $adjustment) {
		$adjDesc = ($output === GUIbuilderComponent::OUTPUT_CSV)? Hash::get($adjustment, 'adj_description') : h(Hash::get($adjustment, 'adj_description'));
		$cells[] = [
			$adjDesc,
			CakeNumber::currency(Hash::get($adjustment, 'adj_amount')),
		];
	}
	$grossAdjustment = Hash::get($repAdjustments, 'gross_adjustments');
	if ($grossAdjustment !== null) {
		$label = __('Gross Adjustments');
		$value = CakeNumber::currency($grossAdjustment);

		if ($output === GUIbuilderComponent::OUTPUT_CSV) {
			$cells[] = [$label, $value];
		} else {
			$cells[] = [
				[$label, ['class' => 'strong bg-success']],
				[$value, ['class' => 'strong bg-success']],
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
