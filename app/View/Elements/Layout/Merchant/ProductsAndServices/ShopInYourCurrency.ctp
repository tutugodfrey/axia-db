
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'MerchantPricing', 'action' => 'edit', $merchant['Merchant']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#ShopInYourCurrencyContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>

<table id='ShopInYourCurrencyContent' style='margin-bottom: 0px'>
	<tr>
		<td class="threeColumnGridCell dataCell">			
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">SYC Charge to Merchant</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['MerchantPricing']['syc_charge_to_merchant'])) ? $this->Number->currency($merchant['MerchantPricing']['syc_charge_to_merchant'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
			</table>                        
		</td>		
	</tr>
</table>