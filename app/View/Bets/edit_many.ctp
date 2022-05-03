<div class="row">
	<div class="col-md-12">
		<span class="contentModuleTitle">
			<?php
			$userFullName = Hash::get($user, 'User.user_first_name') . ' ' . Hash::get($user, 'User.user_last_name');
			echo h(__("Edit %s's bets for card type %s and bet table %s", $userFullName, Hash::get($cardType, 'CardType.card_type_description'), Hash::get($betTable, 'BetTable.name')));
			?>
		</span>
	</div>
</div>
<?php
echo $this->element('Bets/many_form');
