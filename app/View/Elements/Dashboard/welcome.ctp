<div class="alert alert-success"><span class="glyphicon glyphicon-ok pull-left" style="font-size:15pt; margin-right:15px"></span>
	<strong style="font-size:10pt;">Welcome <?php echo h($this->session->read('Auth.User.user_first_name')); ?>!<br /></strong>
	<?php
	
	if (!empty($currentUserLastLGN)) {
		$tranDateTime = strtotime($currentUserLastLGN['SystemTransaction']['system_transaction_date'] . ' ' . $currentUserLastLGN['SystemTransaction']['system_transaction_time']);
		echo 'Your last login was ' . $this->AxiaTime->relativeTime($tranDateTime);
	}
	?>
</div>