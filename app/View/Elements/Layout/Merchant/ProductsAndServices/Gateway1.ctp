
<?php 
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'Gateway1s', 'action' => 'edit', $merchant['Gateway1']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#Gateway1Content').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>

<table id='Gateway1Content'>
	<tr>
		<td class="threeColumnGridCell">			
			<table class="table table-condensed table-hover table-striped">								
				<tr><td class="dataCell noBorders">Gateway</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gateway_id'])) ? h($merchant['Gateway1']['Gateway']['name']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Gateway ID</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_mid'])) ? h($merchant['Gateway1']['gw1_mid']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Merchant Gateway Processing Rate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_rate'])) ? $this->Number->toPercentage($merchant['Gateway1']['gw1_rate']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Merchant Gateway Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_per_item'])) ? $this->Number->currency($merchant['Gateway1']['gw1_per_item'], 'USD3dec') : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Merchant Gateway Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_statement'])) ? $this->Number->currency($merchant['Gateway1']['gw1_statement'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Volume</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_monthly_volume'])) ? $this->Number->currency($merchant['Gateway1']['gw1_monthly_volume'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Item Count</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['gw1_monthly_num_items'])) ? h($merchant['Gateway1']['gw1_monthly_num_items']) : h("--"); ?></td></tr>
				<tr data-toggle="tooltip" data-placement="bottom" data-original-title="This addt'l monthtly cost will be added to any Gateway Monthly cost defined in this Rep's UCP"><td class="dataCell noBorders">Additional Rep Monthly Cost</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['Gateway1']['addl_rep_statement_cost'])) ? h($merchant['Gateway1']['addl_rep_statement_cost']) : h("--"); ?></td></tr>
				<tr><td class="dataCell noBorders">Features</td>
					<td class="noBorders" style="max-width: 500px"><?php echo (!empty($merchant['Gateway1']['gw1_rep_features'])) ? h($merchant['Gateway1']['gw1_rep_features']) : h("--"); ?></td></tr>
			</table>                        
		</td>		
	</tr>
</table>