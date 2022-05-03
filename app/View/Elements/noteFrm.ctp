<?php echo $this->Form->create('MerchantNote'); ?>


<div class="contrTitle roundEdges">New Note</div>
<?php
echo $this->Form->hidden('merchant_note_id', array('value' => 159000));
echo $this->Form->hidden('note_type', array('value' => $noteType));
//echo $this->Form->input('user_id');
echo $this->Form->hidden('merchant_id', array('value' => $merchant['Merchant']['id']));
//echo $this->Form->hidden('note_date');
echo $this->Form->input('note');
//echo $this->Form->input('note_title');
//echo $this->Form->input('general_status');
//echo $this->Form->input('date_changed');
//echo $this->Form->input('critical');
//echo $this->Form->input('note_sent');
?>

<?php echo $this->Form->end(__('Submit')); ?>
