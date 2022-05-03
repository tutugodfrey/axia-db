
<?php 
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'PaymentFusions', 'action' => 'edit', $merchant['PaymentFusion']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#PaymentFusionContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif;
	if (Hash::get($merchant, 'PaymentFusion.is_hw_as_srvc')) {
		echo $this->Html->tag('div', $this->element('Layout/flagHaaS'), array('class' => 'text-center'));
	}
?>
<table id='PaymentFusionContent' style='margin-bottom: 0px'>
	<tr class="dataCell noBorders">
		<td class="dataCell noBorders">
			<table cellpadding="0" cellspacing="0" border="0" class="table-striped table-hover">
				<tr><td class="dataCell noBorders strong">Payment Fusion ID</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['generic_product_mid'])) ? h($merchant['PaymentFusion']['generic_product_mid']) : h("--"); ?></td></tr>
				<tr>
					<td class="dataCell noBorders strong">Feature(s):</td>
					<td class="dataCell noBorders" colspan=2><?php echo implode("<br>", $merchant['PaymentFusionFeatures']); ?></td>
				</tr>
				<tr>
					<td class="dataCell noBorders" colspan=2>
						<div style="width: 250px" class="panel panel-default center-block">
	                        <div class="panel-heading"><strong>Other Feature(s): </strong></div>
	                        <div class="panel-body" style="white-space: normal;"><?php echo h(__(Hash::get($merchant, 'PaymentFusion.other_features'))); ?></div>
	                    </div>
					</td>
				</tr>
				<tr><td class="dataCell noBorders strong">Account Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['account_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['account_fee'], 'USD3dec') : h("--"); ?>
					</td>
				</tr>
				<tr><td class="dataCell noBorders strong">Rate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['rate'])) ? $this->Number->toPercentage($merchant['PaymentFusion']['rate']) : h("--"); ?></td>
				</tr>
			</table>
		</td>
		<td class="dataCell noBorders">
			<table class="table-striped table-hover">
				<tr><td class="dataCell noBorders strong">Per Item</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['per_item_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['per_item_fee'], 'USD3dec') : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong"># of Devices - Standard</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['standard_num_devices'])) ? h($merchant['PaymentFusion']['standard_num_devices']): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong">Standard Device Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['standard_device_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['standard_device_fee'], 'USD3dec'): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong"># of Devices - VP2PE</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['vp2pe_num_devices'])) ? h($merchant['PaymentFusion']['vp2pe_num_devices']): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong">VP2PE Device Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['vp2pe_device_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['vp2pe_device_fee'], 'USD3dec'): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong"># of Devices - PFCC</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['pfcc_num_devices'])) ? h($merchant['PaymentFusion']['pfcc_num_devices']): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong">PFCC Device Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['pfcc_device_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['pfcc_device_fee'], 'USD3dec'): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong"># of Devices - VP2PE + PFCC</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['vp2pe_pfcc_num_devices'])) ? h($merchant['PaymentFusion']['vp2pe_pfcc_num_devices']): h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders strong">VP2PE + PFCC Device Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['vp2pe_pfcc_device_fee'])) ? $this->Number->currency($merchant['PaymentFusion']['vp2pe_pfcc_device_fee'], 'USD3dec'): h("--"); ?></td></tr>
				<tr class="bg-success"><td class="dataCell noBorders strong">Total Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['PaymentFusion']['monthly_total'])) ? $this->Number->currency($merchant['PaymentFusion']['monthly_total'], 'USD3dec'): h("--"); ?></td></tr>
			</table>
		</td>
	</tr>
</table>