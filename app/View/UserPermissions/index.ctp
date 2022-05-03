<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('User Permissions'); ?> List" />
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
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
			<th><?php echo $this->Paginator->sort('permission_id'); ?></th>

		</tr>
		<?php foreach ($userPermissions as $userPermission): ?>
			<tr>
				<td><?php echo h($userPermission['UserPermission']['id']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($userPermission['User']['id'], array('controller' => 'users', 'action' => 'view', $userPermission['User']['id'])); ?>
				</td>
				<td>
					<?php echo $this->Html->link($userPermission['Permission']['id'], array('controller' => 'permissions', 'action' => 'view', $userPermission['Permission']['id'])); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>