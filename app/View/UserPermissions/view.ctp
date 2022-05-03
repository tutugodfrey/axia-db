
<input type="hidden" id="thisViewTitle" value="<?php echo __('View User Permission'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit User Permission'), array('action' => 'edit', $userPermission['UserPermission']['id'])); ?> </li>

	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($userPermission['UserPermission']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($userPermission['User']['id'], array('controller' => 'users', 'action' => 'view', $userPermission['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Permission'); ?></dt>
		<dd>
			<?php echo $this->Html->link($userPermission['Permission']['id'], array('controller' => 'permissions', 'action' => 'view', $userPermission['Permission']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>


