<h1><?php echo __('Reject Time-line'); ?></h1>

<table>
	<thead>
		<tr>
			<th><?php echo __('Trace'); ?></th>
			<th><?php echo __('Date'); ?></th>
			<th><?php echo __('Status'); ?></th>
			<th><?php echo __('Notes'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($merchant['MerchantReject'] as $merchantReject): ?>
		<tr>
			<td><?php echo h(Hash::get($merchantReject, 'trace')); ?></td>
			<td colspan="3">
				<table>
					<tbody>
					<?php foreach ($merchantReject['MerchantRejectLine'] as $merchantRejectLine): ?>
						<tr>
							<td><?php echo $this->Time->format('M jS, Y', Hash::get($merchantRejectLine, 'status_date')); ?></td>
							<td><?php echo h(Hash::get($merchantRejectLine, 'MerchantRejectStatus.name')); ?></td>
							<td><?php echo h(Hash::get($merchantRejectLine, 'notes')); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

