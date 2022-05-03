<div class="modal-content">
	<div class="modal-header">
        <button type="button" class="close" onClick="location.reload();" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="strong panel-title" id="myModalLabel">Confirm your user account password</h4>
	</div>
	<div class="modal-body">
		<?php
		echo $this->Form->create('User', array(
				  'inputDefaults' => array(
							'div' => 'form-group',
							'label' => false,
							'wrapInput' => false,
							'class' => 'form-control'
				  ),
				  'class' => 'form-inline',
				  'url' => array('action' => 'checkDecrypPassword')
		));
		?>
		<?php echo $this->Form->input('password', array('type' => 'password', 'placeholder' => 'Password', 'required' => true, 'autofocus', 'autocomplete' => 'off')) ?>
		<?php echo $this->Form->hidden('id') ?>
		<?php echo $this->Form->hidden('redirectController') ?>
		<?php echo $this->Form->hidden('redirectAction') ?>
		<?php echo $this->Form->hidden('redirectActionParams') ?>
	</div>
	<div class="modal-footer">
        <button type="button" class="btn btn-default" onClick="location.reload();" data-dismiss="modal">Close</button>
		<?php
		echo $this->Form->button('Submit', array('type' => 'button', 'class' => "btn btn-success", 'onClick' => 'validatethisForm()'));
		echo $this->form->end();
		?>
	</div>
</div>

<script type="text/javascript">

	CURRENT_MODEL_NAME = "User";
	THIS_FORM_ID = $("[id*=CheckDecrypPasswordForm]").attr('id');
	function validatethisForm() {
		if ($('#' + CURRENT_MODEL_NAME + 'Password').val()) {
			$('#' + CURRENT_MODEL_NAME + 'Password').val(btoa(encodeURI($('#' + CURRENT_MODEL_NAME + 'Password').val())));
			ajaxFormSubmit(document.getElementById(THIS_FORM_ID));
			ajaxRequestCompleteListener();
		}
	}
	$(function() {
		$('#' + THIS_FORM_ID).keydown(function(event) {
			if (event.keyCode === 13) {
				validatethisForm();
				event.preventDefault();

			}
		});
	});
	$('#UserPassword').focus();
</script>


