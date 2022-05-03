<?php
if (!isset($output)) {
	$output = null;
}

if (!empty($netIncome)):
	$cells = [];

	$label = __('Net Income After Adjustments');
	$value = CakeNumber::currency(Hash::get($netIncome, 'netIncome'));

	if ($output === GUIbuilderComponent::OUTPUT_CSV):
		echo $this->Csv->row([$label, $value]);
	else: ?>
		<table>
			<?php
			$cells[] = [
				[$label, ['class' => 'large-label strong']],
				[$value, ['class' => 'strong']],
			];
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	endif;
endif;
