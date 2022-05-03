<?php
$userId = Hash::get($user, 'User.id');
$compensationId = Hash::get($userCompensation, 'UserCompensationProfile.id');
$betTableId = Hash::get($betTable, 'BetTable.id');
$cardTypeId = Hash::get($cardType, 'CardType.id');

echo $this->Form->create('Bet', array('url' => array('action' => "editMany/{$cardTypeId}/{$betTableId}/{$userId}/{$compensationId}/{$partnerUserId}")));
?>
<table class="table">
	<?php
	echo $this->element('Bets/table_headers');
	echo $this->element('Bets/many_form_fields', [
		'compensationId' => $compensationId,
		'betTableId' => $betTableId,
		'cardTypeId' => $cardTypeId,
	]);
	?>
</table>
<?php
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($user, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
