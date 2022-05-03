
<?php
//@RBAC
if ($this->Rbac->isPermitted('Merchants/edit')):
	$content = $this->Html->image("/img/editPencil.gif", array("title" => 'Edit', "class" => "icon", 'url' => array('controller' => 'WebBasedAches', 'action' => 'edit', $merchant['WebBasedAch']['id'])));
	?>
	<script>
		/*this script will display the edit and activate menu buttons on this elements panel title*/
		$(function() {
			appendHTMLContent($('#WebBasedACHContent').parent().parent().find("span.panel-title"), '<?php echo $content ?>', true);
		});
	</script>
<?php endif ?>

<table id='WebBasedACHContent' style='margin-bottom: 0px'>
	<tr>
		<td class="threeColumnGridCell dataCell">			
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">								
				<tr><td class="dataCell noBorders">Processing Rate</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['WebBasedAch']['vcweb_web_based_rate'])) ? $this->Number->toPercentage($merchant['WebBasedAch']['vcweb_web_based_rate']) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Per Item Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['WebBasedAch']['vcweb_web_based_pi'])) ? $this->Number->currency($merchant['WebBasedAch']['vcweb_web_based_pi'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
				<tr><td class="dataCell noBorders">Monthly Fee</td>
					<td class="dataCell noBorders"><?php echo (!empty($merchant['WebBasedAch']['vcweb_monthly_fee'])) ? $this->Number->currency($merchant['WebBasedAch']['vcweb_monthly_fee'], 'USD', array('after' => false, 'negative' => '-')) : h("--"); ?></td></tr>				
			</table>                        
		</td>		
	</tr>
</table>