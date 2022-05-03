
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'GiftCards', 'action' => 'edit', $merchant['GiftCard'][0]['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#GiftLoyaltyContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>
<?php foreach ($merchant['GiftCard'] as $giftCard): ?>
	<table id='GiftLoyaltyContent' style='margin-bottom: 0px'>
		<tr>
			<td class="dataCell">			
				<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
					<tr><td class="dataCell noBorders">Gift Card MID</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_mid'])) ? h($giftCard['gc_mid']) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Gift Card Provider</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gift_card_provider_id'])) ? ($giftCard['GiftCardProvider']['provider_name']) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Plan Type</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_plan'])) ? h(Inflector::humanize($giftCard['gc_plan'])) : h("--"); ?></td></tr>								
				</table>                        
			</td>		
		</tr>
	</table>
	<table id='GiftLoyaltyContent' style='margin-bottom: 0px'>
		<tr>
			<td class="twoColumnGridCell dataCell">	
				<span class="contentModuleHeader">Processing & Recurring Fees</span><br />
				<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
					<tr><td class="dataCell noBorders">Statement Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_statement_fee'])) ? $this->Number->currency($giftCard['gc_statement_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Gift Per Item Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_gift_item_fee'])) ? $this->Number->currency($giftCard['gc_gift_item_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Loyalty Per Item Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_loyalty_item_fee'])) ? $this->Number->currency($giftCard['gc_loyalty_item_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>								
					<tr><td class="dataCell noBorders">One Rate Monthly Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_chip_card_one_rate_monthly'])) ? $this->Number->currency($giftCard['gc_chip_card_one_rate_monthly'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>								
					<tr><td class="dataCell noBorders">Loyalty Management Database</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_loyalty_mgmt_database'])) ? $this->Number->currency($giftCard['gc_loyalty_mgmt_database'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>								
				</table>                        
			</td>		
			<td class="twoColumnGridCell dataCell">	
				<span class="contentModuleHeader">Set-up & Artwork Fees/Miscellaneous</span><br />
				<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
					<tr><td class="dataCell noBorders">Application Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_application_fee'])) ? $this->Number->currency($giftCard['gc_application_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Artwork Set Up Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_merch_prov_art_setup_fee'])) ? $this->Number->currency($giftCard['gc_merch_prov_art_setup_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
					<tr><td class="dataCell noBorders">Training Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_training_fee'])) ? $this->Number->currency($giftCard['gc_training_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>								
					<tr><td class="dataCell noBorders">Card Re-order Fee</td>
						<td class="dataCell noBorders"><?php echo (!empty($giftCard['gc_card_reorder_fee'])) ? $this->Number->currency($giftCard['gc_card_reorder_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>								
				</table>                        
			</td>		
		</tr>
	</table>
<?php endforeach; ?>