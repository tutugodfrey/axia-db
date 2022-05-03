<?php

//echo checkboxes
if (!empty($ucpList)) {
	echo $this->Form->select("UserCompensationProfile.id", $ucpList, ['multiple' => 'checkbox', 'class' => 'col-md-12 col-sm-12 col-xs-12 col-lg-12']);
} else {
	echo '<div class="text-center text-muted"><i>(No UCPs to copy)</i>';
}