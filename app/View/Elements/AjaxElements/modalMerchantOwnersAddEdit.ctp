<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" onClick="location.reload();
				"data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="myModalLabel"><u>Owners/Officers Social Security Numbers</u></h4>
	</div>
	<div class="modal-body well well-sm">                        

		<?php
		echo $this->Form->create('MerchantOwner', array(
			'url' => $urlAction,
				'inputDefaults' => array(
							'div' => 'form-group',
							'label' => array(
									  'class' => 'col col-sm-5 control-label'
							),
							'wrapInput' => 'col col-sm-6',
							'class' => 'form-control '
				  ),
		));
		?>
		<?php 
		echo $this->Form->hidden('Request.is_modal', array("value" => true)); 
		echo $this->Form->hidden('Request.isBulkedData', array('value' => true));
		echo $this->element('/Layout/Merchant/merchantOwnersAddEdit'); 
		if ($isEditLog) {
			echo $this->Form->hidden('MerchantNote.0.id');
		}
		echo $this->element('Layout/Merchant/merchantNoteForChanges');
		?>

		<div class="col-md-offset-3" id="frmBtnsWrapper">
			<?php
			echo $this->element('Layout/Merchant/mNotesDefaultBttns');
			echo $this->Form->end(); 
			?>
		</div>
	</div>
	<div class="modal-footer footer"> 
		<?php echo $this->element('/Layout/footer'); ?>        
	</div>
</div>
<script type="text/javascript">
<?php $cancelBtn = $this->Form->button("Cancel", array("type" => "button", "class" => "btn btn-danger", "onClick" => "location.reload();", "data-dismiss" => "modal"));?>
//append a cancel button at the innermost/last div where the rest of the buttons are
$("#frmBtnsWrapper div:last").append('<?php echo $cancelBtn; ?>');
</script>