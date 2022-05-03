<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"></link>
<?php
/*Data structure example of $content variable (some keys may be absent):
 $content = [
	'job_name' => 'Residual Report',
	'result' => false,
	'recordsAdded' => false,
	'start_time' => '2020-01-30 10:30:05',
	'end_time' => '2020-01-30 12:30:05',
	'log' => [
		'products' => ['Visa','MasterCard', 'Discover'],
		'errors' => ['Error A: Something went caput', 'Error A: Something else went caput'],
		'optional_msgs' => [(array)],
	]
];
*/
if (Hash::get($content, 'result')) {
	$status = 'Completed (see details below)';
	$className = "bg-success";
} else {
	$status = 'FALURE! (see details below)';
	$className = "bg-danger";
}
?>
<h3 class="text-info"><?php echo Hash::get($content, 'job_name');?> Process Completion Log.</h3>
<h4 class="<?php echo $className; ?>">Status: <?php echo $status; ?></h4>
<table class="table table-bordered table-striped" border="1">
	<tr>
		<th>Start Time: <?php echo $this->Time->format(Hash::get($content, 'start_time'), '%b %e, %Y %H:%M:%S %p', '(not available)'); ?></th>
		<th>
			End Time: <?php echo $this->Time->format(Hash::get($content, 'end_time'), '%b %e, %Y %H:%M:%S %p', '(not available)'); ?>
		</th>
	</tr>
	<tr class="<?php echo $className; ?>">
		<th>Result:</th>
		<td>
			<?php
				if (Hash::get($content, 'result')) {
					echo __('Server has finished the process successfully.');
					if (array_key_exists('recordsAdded', $content)) {
						echo '<br/><br/><strong>Important Notes:</strong><br/>';
						if ($content['recordsAdded']) {
							echo __('Data generated successfully.');
						} else {
							echo '<span class="bg-danger">' . __('There was no report data generated for the time frame and/or products selected.') . '</span>';
						}
					}
				} else {
					echo __('Process FAILED: Error occurred during the process!');
				}
			?>
		</td>
	</tr>
	<tr>
		<th>Errors:</th>
		<td>
			<?php
				if (empty(Hash::get($content, 'log.errors'))) {
					echo __('(none)');
				} else {
					echo "<strong class='text-danger'>";
					echo implode('<br/><br/>', Hash::get($content, 'log.errors'));
					echo "</strong>";
				}
			?>
		</td>
	</tr>
		<th style="vertical-align:top">Additional info:</th>
		<td>
			<?php
				$otherInfo = null;
				if (!empty(Hash::get($content, 'log.optional_msgs'))) {
					$otherInfo .= implode('<br/>', Hash::get($content, 'log.optional_msgs'));
					$otherInfo .= "<br/>";
				}
				if (!empty(Hash::get($content, 'log.products'))) {
					$otherInfo .= "<strong>Selected Product(s):</strong><br><br/>";
					$otherInfo .= implode('<br/>', Hash::get($content, 'log.products'));
				}

				if(!empty($otherInfo)) {
					echo $otherInfo;
				} else {
					echo __('(none)');
				}
			?>
		</td>
	</tr>
</table>
<br />
<?php
	echo $this->element('GenericEmailSignature');
?>
