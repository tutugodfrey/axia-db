<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Users'); ?> List" />

<?php
if (!empty($users)) {
		$icon = $this->Csv->icon(null, [
		'title' => __('Export DB Users List'),
		'class' => 'icon'
	]);
	$exportLink = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'Users',
		'action' => 'exportUsers',
		'?' => $this->request->query
	]);
	echo $this->Html->tag('span', "<strong>Export Data:</strong><br>" . $exportLink,['class' => 'pull-left well-sm']);
}
echo $this->Form->create('User', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => false,
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'class' => 'well well-sm form-inline'
));  echo $this->Form->input('search', array(
		'placeholder' => 'Username',
		'div' => 'col col-md-1',
	)
); 
echo $this->Form->input('active',
	array(
		'div' => 'checkbox col col-md-1 col-md-offset-1',
		'class' => false,
		'label' => 'Active Users',
		'default' => 1,
		'type' => 'checkbox',
		'wrapInput' => false,
	)
); 
echo $this->Form->submit(__('Search'),
	array(
		'div' => 'form-group',
		'class' => 'btn btn-default'
	)
); ?>

<?php
	//Check permission to add user
	if($this->Rbac->isPermitted('app/actions/Users/add')){
		echo $this->Html->link(
			$this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-user')) . ' Add New User', 
			array('controller' => 'Users', 'action' => 'add'),
			array(
				'type'=>'button',
				'class'=> 'btn btn-success',
				'confirm' => 'Are you sure you wish create a new user?',
				'escape' => false)
		);
	}
?>

<?php echo $this->Form->end(); ?>
<div>
	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
</div>

<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>
</p>
<table class="table table-condensed table-hover">
	<tr>            
		<th><?php echo $this->Paginator->sort('user_last_name', 'Last Name'); ?></th>
		<th><?php echo $this->Paginator->sort('user_first_name', 'First Name'); ?></th>
		<th><?php echo $this->Paginator->sort('Role.name', 'Role(s)'); ?></th>
		<th><?php echo $this->Paginator->sort('username'); ?></th>
		<th><?php echo $this->Paginator->sort('initials'); ?></th>
		<th><?php echo $this->Paginator->sort('user_email', 'Email'); ?></th>	
		<th><?php echo $this->Paginator->sort('entity'); ?></th>
		<th><?php echo $this->Paginator->sort('active'); ?></th>
		<th><?php echo 'block/unblock'; ?></th>
	</tr>

	<?php foreach ($users as $user): ?>
		<tr>    

			<td><?php echo $this->Html->link($user['User']['user_last_name'], array('controller' => 'Users', 'action' => 'view', $user['User']['id'])); ?>&nbsp;</td>
			<td><?php echo $this->Html->link($user['User']['user_first_name'], array('controller' => 'Users', 'action' => 'view', $user['User']['id'])); ?>&nbsp;</td>
			<td><?php echo h($user['Role']['name']); ?>&nbsp;</td>
			<td><?php echo h($user['User']['username']); ?>&nbsp;</td>
			<td><?php echo h($user['User']['initials']); ?>&nbsp;</td>
			<td><?php echo h($user['User']['user_email']); ?>&nbsp;</td>	
			<td><?php echo h($user['Entity']['entity_name']); ?>&nbsp;</td>
			<td><?php
			if ($user['User']['active']) {/* binary boolean values */
					echo $this->Html->image('green_orb.gif', array('title' => 'Active'));
			} else {
					echo $this->Html->image('red_orb.png', array('title' => 'Inactive'));
			}
			?>&nbsp;</td>

			<td>
				<?php
					if ($user['User']['is_blocked'] == true) {
						echo $this->Html->link('unblock',
							array(
								'controller' => 'Users',
								'action' => 'unblock',
								$user['User']['id']
							),
							array(
								'confirm' => 'Are you sure you wish to unblock this user?'
							)
						);
					} else {
						echo $this->Html->link('block',
							array(
								'controller' => 'Users',
								'action' => 'block',
								$user['User']['id'],
							),
							array(
								'confirm' => 'Are you sure you wish to block this user?'
							)
						);
					}
				?>
			</td>

		</tr>
	<?php endforeach; ?>
</table>
