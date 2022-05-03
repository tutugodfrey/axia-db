<?php
	/* Drop breadcrumb */
	$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
	$this->Html->addCrumb('Notes', '/Merchants/notes/' . $merchant['Merchant']['id']);
	$this->Html->addCrumb('Edit Merchant Note');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Edit Merchant Note')); ?>" />

<div class="contentModuleTitle"><?php echo __('Edit Merchant Note'); ?></div>

<?php
echo $this->Form->create('MerchantNote', array(
	'novalidate' => true,
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => array('class' => 'control-label'),
		'wrapInput' => false,
	),
	'class' => 'well form-horizontal'
));

echo $this->Form->hidden('id');
echo $this->Form->hidden('note_type_id');
echo $this->Form->hidden('merchant_id');
echo $this->Form->hidden('user_id');
echo $this->Form->hidden('loggable_log_id');
if ($needToApproveChanges) {
	echo $this->Form->hidden('general_status');
}

$noteSendInput = $this->MerchantNote->checkbox('note_sent', array(
	'checked' => false,
	'label' => __('Emal note changes to rep'),
	'class' => 'merchant-note-checkbox'
));
?>

<div class="row">
	<div class="col-xs-6">
		<?php
		echo $this->Form->input('note_title', array(
			'label' => __('Title:')
		));
		echo $this->Form->input('MerchantNote.note', array(
			'label' => __('Comments'),
			'required' => true,
			'rows' => "8",
			'cols' => "80"
		));

		if (!$needToApproveChanges) {
			if ($isGeneralNote) {
				echo $this->MerchantNote->statusInput('general_status', array(
					'class' => 'form-control merchant-note-options'
				));
				echo $this->MerchantNote->checkbox('critical', array('label' => _('Critical')));
			}

			echo $noteSendInput;
		}

		echo $this->Form->submit(__('Save Note Changes'), array(
			'class' => 'btn btn-primary',
			'name' => 'save-note-submit',
			'div' => array('class' => 'form-group submit')
		));
		?>
	</div>

	<?php
	if (Hash::get($this->request->data, 'NoteType.id') === NoteType::CHANGE_REQUEST_ID): ?>
		<div class="col-xs-5 col-xs-offset-1">
			<div class="form-group">
				<span class="contentModuleHeader">
					<?php echo h($this->request->data('NoteType.note_type_description')); ?>
				</span>
				<br><br>
				<?php echo __('Account Information Change'); ?><br>
					<?php
					if (!empty($controllerName) && !empty($loggedForeignModel)) {
						if ($isModal) {
							$ajaxUrl = '/Users/checkDecrypPassword/' . Hash::get($loggedForeignModel, 'LoggableLog.foreign_key') . '/' . $controllerName . '/ajaxAddAndEdit/' . Hash::get($this->request->data, 'MerchantNote.id'); 
							echo $this->Html->link(
							 __('View change'),
							'javascript:void(0)',
							array("data-toggle" => "modal", "data-target" => "#myModal", 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')")
							);
						} else { 
							$editChangesUrl = Router::url(array(
								'plugin' => false,
								'controller' => $controllerName,
								'action' => $actionName,
								Hash::get($loggedForeignModel, 'LoggableLog.foreign_key'),
								Hash::get($this->request->data, 'MerchantNote.id'),
								'merchant_id' => $merchant['Merchant']['id']
							)); 
							echo $this->Html->link(
							 __('View change'),
							$editChangesUrl
							);
						} 
					} else {
						echo '<div class="list-group col-xs-5">
								<a href="#" class="list-group-item list-group-item-warning">
									<span class="glyphicon glyphicon-exclamation-sign"></span>
									' . __("Can't view change (Logged data not found).") . '
								</a>
							</div>';
					}
				?>	
			</div>

			<div class="form-group">
				<span class="contentModuleHeader">
					<?php echo __('Status'); ?>
				</span>
				<br />
				<?php
					$status = $this->request->data('MerchantNote.general_status');
					echo $this->Html->showStatus($status);
				?>
				<br />
			</div>

			<div class="form-group">
				<?php 
					$noteDate = $this->MerchantNote->noteDateTime($this->request->data['MerchantNote']['note_date']);
					if (preg_match('/[am|pm]/', $noteDate) !== 1){
						$noteDate .= " <span class='list-group-item-warning'>(time not available.)</span>";
					}

					echo __('Originally posted:') . ' ' . $noteDate; ?> <br>
				<?php echo __('by') . ' ' . h($this->request->data('User.user_first_name') . ' ' . $this->request->data('User.user_last_name')); ?>
			</div>
				<?php
					if (!empty($this->request->data('MerchantNote.resolved_date'))) {
						$resolvedDateTime = strtotime($this->request->data['MerchantNote']['resolved_date'] . " " . $this->request->data['MerchantNote']['resolved_time']);
						$resolvedDateTime = $this->AxiaTime->relativeTime($resolvedDateTime);
						$noteSummaryHtml = '';
						if ($this->request->data('MerchantNote.general_status') === MerchantNote::STATUS_REJECTED) {
							$noteSummaryHtml .= '<div class="form-group alert alert-warning col-md-7 col-sm-7 col-xs-7 strong">';
							$noteSummaryHtml .= __('Rejected ') . $resolvedDateTime . '</div>';
						} else {
							$noteSummaryHtml .= '<div class="form-group alert alert-success col-md-8 col-sm-8 col-xs-8 strong">';
							$noteSummaryHtml .= __('Approved ') . $resolvedDateTime . ' ';
							$noteSummaryHtml .=  __('by') . ' ' . h($this->request->data('ApprovingUser.user_first_name')) . ' ' .  h($this->request->data('ApprovingUser.user_last_name'));
							$noteSummaryHtml .= '</div>';
						}
						echo $noteSummaryHtml;
					}						
					?> 
			<?php
			if ($needToApproveChanges) {
				echo $noteSendInput; ?>
				<div class="form-group">
					<?php
					echo $this->Form->hidden("MerchantNote.dataForModel", array("value" => $loggedForeignModel['LoggableLog']['model']));
					if ($userCanApproveChanges) {
						echo $this->Form->submit(__('Approve Change'), array(
							'class' => 'btn btn-primary',
							'name' => 'approve-change-submit',
							'div' => false,
						));
					}
					echo $this->Form->submit(__('Reject Change'), array(
						'class' => 'btn btn-default',
						'name' => 'reject-change-submit',
						'div' => false,
					));
					?>
				</div>
			<?php
			}
			?>
			</div>
		</div>
	<?php
	endif;
	?>
</div>

<?php
echo $this->Form->end(); ?>
<?php echo $this->element('modalDialog') ?>  
<script type='text/javascript'>activateNav('MerchantsNotes'); </script>
