<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Axia Invoices', '/' . $this->name . '/' . 'view' . '/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Axia Invoice');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Axia Invoices'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />


<?php
if ($this->Rbac->isPermitted('MerchantCancellations/edit'))
	echo $this->element('Layout/Merchant/merchantAchesAddEdit');
?>

<script type='text/javascript'>activateNav('MerchantAchesView'); </script>
