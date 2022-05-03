<input type="hidden" id="thisViewTitle" value="<?php  echo __('User Compensation Profiles'); ?> List" />
<div class="reportTables">
        
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
        <p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
        <table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('partner_user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('is_partner_rep'); ?></th>
			<th><?php echo $this->Paginator->sort('is_default'); ?></th>
			
	</tr>
	<?php
	foreach ($userCompensationProfiles as $userCompensationProfile): ?>
	<tr>
		<td><?php echo h($userCompensationProfile['UserCompensationProfile']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($userCompensationProfile['User']['fullname'], array('controller' => 'users', 'action' => 'view', $userCompensationProfile['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($userCompensationProfile['PartnerUser']['fullname'], array('controller' => 'users', 'action' => 'view', $userCompensationProfile['PartnerUser']['id'])); ?>
		</td>
		<td><?php echo h($userCompensationProfile['UserCompensationProfile']['is_partner_rep']); ?>&nbsp;</td>
		<td><?php echo h($userCompensationProfile['UserCompensationProfile']['is_default']); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>	
</div>