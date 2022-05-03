<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/view/' . $merchant[Inflector::singularize($this->name)]['id']);
$this->Html->addCrumb('Notes', '/' . $this->name . '/' . $this->action . '/' . $merchant[Inflector::singularize($this->name)]['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Notes')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<div id="merchant-note-filter" class="contentModule text-center">
	<?php
	echo $this->Form->create('Merchant', array(
		'inputDefaults' => array(
			'div' => array('class' => 'form-group'),
			'label' => array('class' => 'nowrap'),
			'wrapInput' => false,
			'class' => 'form-control'
		),
		'class' => 'well well-sm form-inline',
		'url' => array(
			'action' => 'notes',
			Hash::get($merchant, 'Merchant.id')
		)
	));
	/* Results Filter */
	echo $this->Form->input('MerchantNote.note_type_id', array('label' => 'Note Type: ', 'options' => $optnsNoteTypes));
	echo $this->Form->input('MerchantNote.general_status', array(
		'label' => __('Status'),
		'empty' => 'All',
		'options' => $statusList
	)); ?>
	<div class="form-group">
		<?php
		echo $this->Form->input('MerchantNote.begin_m', array('div' => false, 'label' => 'Begin: ', 'options' => $optnsMonths));
		echo $this->Form->input('MerchantNote.begin_y', array('div' => false, 'label' => false, 'options' => $optnsYears)) . '&nbsp;';
		?>
	</div>
	<div class="form-group">
		<?php
		echo $this->Form->input('MerchantNote.end_m', array('div' => false, 'label' => 'End: ', 'options' => $optnsMonths));
		echo $this->Form->input('MerchantNote.end_y', array('div' => false, 'label' => false, 'options' => $optnsYears));
		?>
	</div>

	<?php echo $this->Form->submit(('Generate'), array('div' => false));
	echo $this->Form->end(); ?>

    <div class="pager text-center" style="margin:5px 0px 5px 0px">
		<?php
		if (!empty($notes)) {
			echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
			echo $this->Paginator->numbers(array('separator' => ''));
			echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		}
		?>
        &nbsp;&nbsp;
		<?php
		if ($this->Rbac->isPermitted('MerchantNotes/add') && $this->Rbac->isPermitted('app/actions/Merchants/view/module/addGeneralNote', true)): ?>
			<?php
			echo $this->MerchantNote->addAjaxNoteButton('Add General Note ', 'addGnrNote', "Add General Note for " . h($merchant['Merchant']['merchant_dba']), ['class' => 'btn-success btn-sm']);
			?>
		<?php
		endif;
		?>
    </div>
</div>
<div id="addNote_frm" style="background: white; position: fixed;top: 25%; left: 40%; width: 450px; z-index: 10; display: none; overflow:hidden; " class="panel panel-primary shadow"></div>

<div class="reportTables">
	<?php if (!empty($notes['0'])): ?>
		<table class="table table-condensed dontSpill">
			<tr>
				<th><?php echo $this->Paginator->sort('note_date', 'Date Submitted'); ?></th>
				<th><?php echo __('Author'); ?></th>
				<th><?php echo $this->Paginator->sort('note_title', 'Title'); ?></th>
				<th><?php echo __('Note'); ?></th>
				<th><!--spacer--></th>
			</tr>
			<?php foreach ($notes as $merchantNote): ?>
				<tr>
					<td><?php echo $this->MerchantNote->noteDateTime($merchantNote['MerchantNote']['note_date']); ?></td>
					<td><?php echo h($merchantNote['User']['user_first_name'] . ' ' . $merchantNote['User']['user_last_name']); ?></td>
					<td><?php echo nl2br(h($merchantNote['MerchantNote']['note_title'])); ?></td>
					<td><?php echo nl2br(h($merchantNote['MerchantNote']['note'])); ?></td>
					<td class="nowrap">
						<span class="pull-right">
						<?php
						if ($merchantNote['MerchantNote']['critical']) {
							echo $this->Html->image("icon_critical.png", array("title" => "Critical note!", "class" => "icon"));
						}
						if ($merchantNote['MerchantNote']['note_sent']) {
							echo $this->Html->image("icon_email.gif", array("title" => "Note emailed to rep", "class" => "icon"));
						}
						echo $this->Html->showStatus(Hash::get($merchantNote, 'MerchantNote.general_status'), false);
						if ($this->Rbac->isPermitted('MerchantNotes/edit')) {
							echo $this->Html->image("editPencil.gif", array(
								"title" => __('Edit note'),
								"class" => "icon",
								'url' => array(
									'controller' => 'merchant_notes',
									'action' => 'edit',
									$merchantNote['MerchantNote']['id']
								)
							));
						}
						if ($this->Rbac->isPermitted('MerchantNotes/delete')) {
							$url = array(
									'controller' => 'MerchantNotes',
									'action' => 'delete',
									$merchantNote['MerchantNote']['id']
								);
							$options = array(
									'class' => "btn btn-xs btn-danger",
									'data-original-title' => "Delete",
									'data-placement' => "top",
									'data-toggle' => "tooltip",
									'escape' => false,
									'confirm' => 'Are you sure you wish to delete this note?'
								);
							if ($merchantNote['MerchantNote']['note_type_id'] === NoteType::CHANGE_REQUEST_ID || !empty($merchantNote['MerchantNote']['loggable_log_id'])) {
								$url = '#';
								$options['class'] = "btn btn-xs btn-default disabled";
								$options['data-original-title'] = 'Merchant change notes cannot be deleted';
							}

							echo $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
						}
						?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
<?php else: 
		echo "<span class='list-group-item text-muted text-center'>- No notes were found. -</span>";
	endif; ?>
</div>
<div class="pager text-center" style="margin:5px 0px 5px 0px">
	<?php
	if (!empty($notes)) {
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	}
	?>
</div>
<script type='text/javascript'>
activateNav('MerchantsNotes'); 
var merchantId = "<?php echo $merchant['Merchant']['id']; ?>";
$("#addGnrNote").on('click', function(e) {
	e.preventDefault();
	ajaxNote(merchantId, 'General Note','addNote_frm');
	objFader('addNote_frm');
});
</script>