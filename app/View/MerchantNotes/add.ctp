<input type="hidden" id="thisViewTitle" value="<?php echo __('Add Merchant Note'); ?>" />

<div>
	<div class="contrTitle">
		<?php
			echo __('New Note');
			echo $this->Html->image('close_window.gif', array(
				'class' => 'pull-right close-button',
				'alt' => 'close'
			));
		?>
	</div>
	<?php
		echo $this->Form->create('MerchantNote', array(
			'novalidate' => true,
			'inputDefaults' => array(
				'div' => 'form-group',
				'label' => array('class' => 'control-label'),
				'wrapInput' => false,
			)
		));
	?>
	<div class="well">

		<div class="row">
			<div class="col-md-12">
				<?php
					echo $this->Form->hidden('note_type_id');
					echo $this->Form->hidden('merchant_id');
					echo $this->Form->hidden('user_id');
					if ($noteType !== MerchantNote::TYPE_GENERAL) {
						$this->Form->hidden('general_status');
					} else {
						echo $this->Form->input('note_title', array('class' => 'form-control'));
					}

					echo $this->Form->input('MerchantNote.note', array(
						'label' => __('Comments'),
						'required' => true,
						'rows' => "8",
					));
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php
					if ($noteType == MerchantNote::TYPE_GENERAL) {
						echo $this->MerchantNote->statusInput('general_status', array(
							'class' => 'form-control merchant-note-options'
						));
					} else {
						if ($noteType == MerchantNote::TYPE_GNRSIMPLE) {
							echo $this->Form->hidden('note_type', array('value' => MerchantNote::TYPE_GNR));
						} else {
							echo $this->Form->hidden('note_type', array('value' => $noteType));
						}

						if ($noteType == MerchantNote::TYPE_GNR) {
							echo $this->Form->input('general_status', array(
								'options' => $gnrOptions,
								'type' => 'radio',
								'legend' => false,
								'class' => 'form-control merchant-note-options'
							),
							array(
								'escape' => false,
								'separator' => ' '
							));

							echo $this->Form->input('critical');
							echo $this->Form->input('note_title');
						}
					}
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php
					if ($noteType == MerchantNote::TYPE_GENERAL) {
						echo $this->Form->input('critical', array(
							'type' => 'checkbox',
							'legend' => false,
							'label' => array('text' => __('Critical')),
							'wrapInput' => false,
							'class' => 'merchant-note-checkbox'
						));

						echo $this->Form->input('note_sent', array(
							'type' => 'checkbox',
							'legend' => false,
							'label' => array('text' => __('Email note changes to rep')),
							'wrapInput' => false,
							'class' => 'merchant-note-checkbox'
						));
					}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class='pull-right'>
					<?php
						echo $this->Html->link(__('Cancel'),
							'#addNote_frm',
							array(
								'class' => 'btn btn-danger',
								'onClick' => "objSlider('addNote_frm', 200)"
							)
						);
						echo $this->Form->button(__('Save'), array(
							'type' => 'button',
							'class' => 'btn btn-primary',
							'onClick' => 'ajaxRequestCompleteListener();ajaxFormSubmit(document.getElementById(\'MerchantNoteAddForm\'));'
						));
					?>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
</div>

<?php
	$this->AssetCompress->autoInclude = false;
	echo $this->Html->script('views/merchants/view');
	$this->start('script');
	$this->end();
	echo $this->AssetCompress->script('merchantNotes', array('raw' => (bool)Configure::read('debug')));

if ($noteType == 'General Note'):
	echo $this->AssetCompress->script('resizableNote', array('raw' => (bool)Configure::read('debug'))); ?>
	<style>
		.ui-resizable-helper { border: 1px dotted black; }
	</style>
<?php endif; ?>
