<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Cancelling Merchant Account')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<div>
<span class="contentModuleTitle">Adding new Cancellation </span> 
<?php
echo $this->element('MerchantCancellations/editForm');
?>
</div>