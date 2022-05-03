<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Axia Invoices', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba']) . " / " . h($merchant['Merchant']['merchant_mid']) . " / " . h($merchant['User']['user_first_name']) . " " . h($merchant['User']['user_last_name']) . " | " . __('Axia Invoices'); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php
	$achTopMenuHtml = '';
if(!empty(Hash::get($merchantAches, '0.MerchantAch'))) {
	$achTopMenuHtml .= $this->Html->link(
			'Show items',
			'javascript:void(0)', 
			array('escape' => false,
			'class' => 'btn btn-sm btn-default',
			'onClick' => "$('[id^=itemCollection]').fadeIn(500)",
			'data-toggle' =>'tooltip', 'data-placement' => 'top', 'data-original-title' => 'Show all Invoice Items'
		));
	$achTopMenuHtml .= $this->Html->link(
			'Hide items',
			'javascript:void(0)', 
			array('escape' => false,
			'class' => 'btn btn-sm btn-default',
			'onClick' => "$('[id^=itemCollection]').fadeOut(500)",
			'data-toggle' =>'tooltip', 'data-placement' => 'top', 'data-original-title' => 'Hide all Invoice Items'
		));
}
if ($this->Rbac->isPermitted('MerchantAches/add')) {
	$achTopMenuHtml .= ' ';
	$achTopMenuHtml .= $this->Html->link('<span class="glyphicon glyphicon-plus"></span> New Invoice', ['controller' => 'MerchantAches', 'action' => 'add', $merchant['Merchant']['id']], ["escape" => false, "class" => "btn btn-sm btn-success"]);
	echo $this->Html->tag('div', $achTopMenuHtml, ["class" => "text-center"]);
}
?>

<?php echo $this->element('Layout/Merchant/merchantAchesList') ?>
<script type='text/javascript'>activateNav('MerchantAchesView'); </script>
