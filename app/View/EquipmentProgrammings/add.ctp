<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Programming', '/merchants/equipment/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Add Equipment Programming');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Add Merchant Equipment Programming'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php
echo $this->Form->create('EquipmentProgramming', array(
    'inputDefaults' => array(
        'div' => 'form-group col-md-2',
        'label' => array('class' => 'contentModuleTitle nowrap col-md-12 control-label'),
        'wrapInput' => 'col-md-12',
        'class' => 'form-control',
        'style' => 'max-width:100px'
    ),
        'class' => 'well well-sm form-inline'));

 echo $this->Form->hidden('merchant_id', array('value' => $merchant['Merchant']['id']));
 echo $this->element('Layout/Merchant/equipmentProgrammingForm');
 echo $this->Form->submit('Save Changes', array('class' => 'btn btn-success btn-sm'));
 echo $this->Form->end();
 ?>
<script type='text/javascript'>activateNav('MerchantsEquipment'); </script>