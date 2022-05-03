
<?php echo $this->Form->create('NewPartner', array('action' => 'merchantQuickOpen', 'url' => array('controller' => 'merchants', 'action' => 'merchantQuickOpen'))); ?>

<?php
echo $this->Form->input('merchant_mid');
?>

<?php echo $this->Form->end(__('Jump')); ?>
</div>

