<div class="col-md-12">
	<div class="contentModuleTitle panel-heading bg-success">
		<?php 
		echo $this->Html->sectionHeader(__('Permission Levels'));
		if($this->Rbac->isPermitted('Users/editAssociated')){
			$editUrl = array(
					'action' => 'editAssociated', $assocUsers['User']['id'], $assocUsers['UserCompensationProfile']['id'], $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon(),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('Users', 'ajaxAssociatedView', [$compensationId, $partnerUserId], 'assocViewMainContainer');
		}
		?>
	</div>
	<div class="contentModule">
		<table cellpadding="0" cellspacing="0" class="reportTables">
			<tr>
				<th>Role</th>
				<th>Name</th>
				<th>Permission Level</th>
			</tr>
			<?php if(!empty($assocUsers['UserCompensationAssociation'])):
					foreach ($assocUsers['UserCompensationAssociation'] as $associatedUser) : ?>
				<tr>
					<td><?php echo h($associatedUser['role']); ?>
					</td>
					<td>
						<?php echo h(Hash::get($associatedUser, 'UserAssociated.fullname')); ?>
					</td>
					<td>
						<?php echo h($associatedUser['permission_level']); ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
		</table>
	</div>
</div>

