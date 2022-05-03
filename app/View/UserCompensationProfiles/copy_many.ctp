<div class="panel panel-primary">
	<div class="panel-heading">
		<strong>
			Copy Compensation Profiles
			<?php echo (!empty($targetUserName))? " - Creating Copy For $targetUserName":''; ?>
		</strong>
	</div>
	<div class="panel-body">
			<?php
				echo $this->Form->create('UserCompensationProfile',
					[
						'inputDefaults' => [
							'div' => 'form-group',
							'label' => ['class' => 'col col-xs-12 col-md-4'],
							'wrapInput' => 'col col-xs-12 col-md-12',
							'class' => 'form-control'
						],
						'class' => 'form-horizontal'
					]
				);
				echo $this->Form->hidden('target_user_id', ['value' => $targetUserId]);
				if (!empty($newPartnerIdForNewCopy)) {
					echo $this->Form->hidden('new_partner_id_for_new_copy', ['value' => $newPartnerIdForNewCopy]);
				}
				echo $this->Form->input('user_id', [
					'label' => 'Select User to Copy From:',
					'empty' => 'Select user from which to copy comp profile(s)',
				]);
				echo $this->Form->label('ucp_list', "Selected User's Compensation Profiles (UCP) available to copy from:");
				?>
				<ul class="list-group">
					<li class="list-group-item well well-sm col-md-12 col-sm-12 col-xs-12 col-lg-12" id="ucpListContainer"></li>
				</ul>
				<?php
				echo $this->Html->tag('div', 
					$this->Html->link(__('Cancel'), '#', ['class' => 'btn btn-sm btn-danger', 'data-dismiss' => 'modal']) .
					$this->Form->submit(__('Submit'), ['div' => false, 'class' => 'btn btn-sm btn-primary', 'onClick' => "$(this).attr('value', 'Processing...');$(this).attr('disabled', 'disabled');$('#UserCompensationProfileCopyManyForm').submit();"]),
					['class' => 'text-center']);
				echo $this->Form->end();
			?>
	</div>
</div>
<script>
var emptyUcpListHtml = '<div class="text-center text-muted"><i>(No UCPs to copy)</i>'
$(document).ready(function() {
	if ($('#ucpListContainer').html() == '') {
		$('#ucpListContainer').html(emptyUcpListHtml);
	}
});
$("#UserCompensationProfileUserId").change(function() {
	if ($(this).val() !== '') {
		renderContentAJAX('', '', '', 'ucpListContainer', '/UserCompensationProfiles/getUpdatedUcpList/' + $(this).val() + '/' + $('#UserCompensationProfileTargetUserId').val());
	} else {
		$('#ucpListContainer').html(emptyUcpListHtml);
	}
});
</script>