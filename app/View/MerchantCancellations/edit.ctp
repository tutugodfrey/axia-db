<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Cancellation');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Edit Cancellation')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<div>
<span class="contentModuleTitle">Edit Cancellation Info</span> 
<?php
echo $this->element('MerchantCancellations/editForm');
?>
</div>