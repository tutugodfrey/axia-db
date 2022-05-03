<?php

echo $this->Form->create('User');
echo $this->Form->hidden('User.id');
$salesGoals = $this->request->data('User.SalesGoal');
$calInfo = cal_info(CAL_GREGORIAN);
// used for the saveMany model structure
$index = 0;
$gridInputOptions = array(
		  'label' => false,
);
echo "<table>";
echo $this->element('SalesGoals/grid_table_headers');
foreach ($calInfo['months'] as $monthIndex => $month) {
	echo $this->Form->hidden("SalesGoal.$index.id");
	echo $this->Form->hidden("SalesGoal.$index.goal_month", array('value' => $monthIndex));
	//echo $this->Html->tag('div', );
	echo $this->Html->tableCells(array(
			  $month,
			  $this->Form->input("SalesGoal.$index.goal_accounts", $gridInputOptions),
			  $this->Form->input("SalesGoal.$index.goal_volume", $gridInputOptions),
			  $this->Form->input("SalesGoal.$index.goal_profits", $gridInputOptions),
			  $this->Form->input("SalesGoal.$index.goal_statements", $gridInputOptions),
			  $this->Form->input("SalesGoal.$index.goal_calls", $gridInputOptions),
	));
	$index++;
}
echo "</table>";
echo $this->Form->defaultButtons();
echo $this->Form->end();
