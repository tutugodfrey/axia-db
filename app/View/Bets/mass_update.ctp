<input type="hidden" id="thisViewTitle" value="User BETs Mass Update"/>

<?php
echo $this->element('Bets/mass_edit_form', [
	'betTable' => h($betTable),
	'betNetworks' => h($betNetworks)
	]);