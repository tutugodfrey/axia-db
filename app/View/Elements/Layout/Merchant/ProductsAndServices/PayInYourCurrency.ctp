
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'MerchantPricing', 'action' => 'edit', $merchant['Merchant']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#PayInYourCurrencyContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>

<table id='PayInYourCurrencyContent' style='margin-bottom: 0px'>
	<tr>
		<td class="threeColumnGridCell dataCell">			
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">PYC Charge to Merchant</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantPricing']['pyc_charge_to_merchant'])) ? $this->Number->currency($merchant['MerchantPricing']['pyc_charge_to_merchant'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Merchant Rebate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantPricing']['pyc_merchant_rebate'])) ? $this->Number->currency($merchant['MerchantPricing']['pyc_merchant_rebate'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>
			</table>                        
		</td>		
	</tr>
</table>