<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" onClick="location.reload();
                "data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><u>Travel & Entertainment Merchant Numbers</u></h4>
    </div>
    <div class="modal-body well well-sm">

		<?php
        echo $this->Form->create('MerchantUwVolume', array(
            'inputDefaults' => array(
                    'div' => 'col col-md-6'
                ),
            'url' => $urlAction,
            'class' => 'form-inline'
            )
        ); ?>
		<?php echo $this->Form->hidden("MerchantUwVolume.merchant_id"); ?>

        <?php echo $this->Form->hidden('Request.is_modal', array("value" => true)); ?>
		<?php echo $this->element('/Layout/Merchant/tAndESectionAddEdit'); ?>
		<?php 
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