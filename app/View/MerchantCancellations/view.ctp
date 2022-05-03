<?php
/* Drop breadcrumb */
$this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)), '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Cancellations')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<div>

	<div class="col-xs-12 col-sm-12 col-md-4">
		<span class="contentModuleTitle">Current Cancellation: </span> 
		<?php
		if ($this->Rbac->isPermitted('MerchantCancellations/edit')) {
			if (!empty($merchant['MerchantCancellation']['id'])) {
				echo '&nbsp;' . $this->Html->image("/img/editPencil.gif", array("title" => "Edit Cancellation", "class" => "icon", 'url' => array('controller' => 'MerchantCancellations', 'action' => 'edit', $merchant['MerchantCancellation']['id'])));
			} else {
				echo '&nbsp;' . $this->Html->image("/img/editPencil.gif", array("title" => "Add Cancellation", "class" => "icon", 'url' => array('controller' => 'MerchantCancellations', 'action' => 'add', $merchant['Merchant']['id'])));
			}
		}
		?>

		<?php if (!empty($merchant['MerchantCancellation']['id'])): ?>

			<table style="width: auto;margin-bottom: -5px">
				<tr>				
					<td class="dataCell">
						Date Submitted:    
					</td>
					<td class="dataCell">
						<?php echo (!empty($merchant['MerchantCancellation']['date_submitted'])) ? date('M jS Y', strtotime($merchant['MerchantCancellation']['date_submitted'])) : "--"; ?>    
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Date Completed:    
					</td>
					<td class="dataCell">
						<?php echo (!empty($merchant['MerchantCancellation']['date_completed'])) ? date('M jS Y', strtotime($merchant['MerchantCancellation']['date_completed'])) : "--"; ?>
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Date Of Last Activity:	  
					</td>
					<td class="dataCell">
						<?php echo (!empty($merchant['MerchantCancellation']['date_inactive'])) ? date('M jS Y', strtotime($merchant['MerchantCancellation']['date_inactive'])) : "--"; ?>
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Fee Charged:	   
					</td>
					<td class="dataCell">
						<?php echo (!empty($merchant['MerchantCancellation']['fee_charged'])) ? $this->Number->currency($merchant['MerchantCancellation']['fee_charged'], 'USD', array('after' => false, 'negative' => '-')) : "--"; ?>     
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Status:   
					</td>
					<td class="dataCell">
						<?php echo ($merchant['MerchantCancellation']['status'] === 'PEND') ? "Pending" : "Completed"; ?>
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Details:    
					</td>
					<td class="dataCell">
						<?php echo (!empty($merchant['MerchantCancellation']['reason'])) ? h($merchant['MerchantCancellation']['reason']) : "--"; ?>
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Reason:
					</td>
					<td class="dataCell">
						<?php
						if (!empty($merchant['MerchantCancellation']['merchant_cancellation_subreason']))
							echo h($merchant['MerchantCancellation']['MerchantCancellationSubreason']['name'] . ": " . $merchant['MerchantCancellation']['merchant_cancellation_subreason']);
						else if (!empty($merchant['MerchantCancellation']['MerchantCancellationSubreason']['name']))
							echo h($merchant['MerchantCancellation']['MerchantCancellationSubreason']['name']);
						else
							echo '--';
						?>
					</td>
				</tr>
				<tr>				
					<td class="dataCell">
						Exclude from Attrition Ratio:
					</td>
					<td class="dataCell">
						<?php echo ($merchant['MerchantCancellation']['exclude_from_attrition'] == true) ? '<h6><span class="label label-success"> YES</span></h6>' : '<h6><span class="label label-danger"> NO</span></h6>';
						?>
					</td>
				</tr>
			</table>
		<?php else : ?>
			<div class="text-center">
				<span style="margin-top:25px" class="list-group-item text-center text-muted">- Merchant has not cancelled -</span> 
			</div>
		<?php endif ?>
	</div>

	<div class="col-xs-12 col-sm-12 col-md-8">
		<div class="panel panel-info" style="width:100%;overflow-x: auto;">
			<div class="panel-heading contentModuleTitle">Previous Cancellations History 
				<?php 
					echo $this->Html->link($this->Html->tag('span', " ", ['class' => "glyphicon glyphicon-info-sign", 'style' => 'vertical-align:bottom']), 
					"/Documentations/help#cancellations",
					['escape' => false, 'target' => '_blank', 'class' => 'pull-right','style' => 'font-size:12pt']);
				?>
			</div>
			<table style="margin-bottom: -5px" class="table">
				<?php if (empty($merchant['CancellationsHistory'])): ?>
					<tr>
						<th><span class="list-group-item text-center text-muted">- Merchant has no previous cancellations -</span></th>
					</tr>
				<?php else : ?>
					<tr>					
						<th>Reason</th>
						<th>Subreason</th>
						<th>Submitted</th>
						<th>Completed</th>
						<th>Deactivated</th>
						<th>Reactivated</th>
						<th>Fee Charged</th>
						<th>Invoice #</th>
					</tr>
				<?php endif; ?>
				<?php foreach ($merchant['CancellationsHistory'] as $cancelHistory): ?>
					<tr>					
						<td class="text-center">
							<?php echo (!empty($cancelHistory['reason'])) ? h($cancelHistory['reason']) : "--" ?>    
						</td>
						<td class="text-center">
							<?php
							$subReason = !empty($cancelHistory['merchant_cancellation_subreason_id']) ? $cancelHistory['MerchantCancellationSubreason']['name'] . " " : "";
							$subReasonDetail = !empty($cancelHistory['merchant_cancellation_subreason']) ? $cancelHistory['merchant_cancellation_subreason'] : "";
							echo (($subReason . $subReasonDetail) === "") ? "--" : h($subReason . $subReasonDetail);
							?>
						</td>
						<td class="text-center">
							<?php echo (!empty($cancelHistory['date_submitted'])) ? date('M jS Y', strtotime($cancelHistory['date_submitted'])) : "--"; ?>    
						</td>
						<td class="text-center">
							<?php echo (!empty($cancelHistory['date_completed'])) ? date('M jS Y', strtotime($cancelHistory['date_completed'])) : "--"; ?>    
						</td>
						<td class="text-center">
							<?php echo (!empty($cancelHistory['date_inactive'])) ? date('M jS Y', strtotime($cancelHistory['date_inactive'])) : "--"; ?>    
						</td>
						<td class="text-center">
							<?php echo (!empty($cancelHistory['date_reactivated'])) ? date('M jS Y', strtotime($cancelHistory['date_reactivated'])) : "--"; ?>    
						</td>
						<td class="dataCell text-center">
							<?php echo (!empty($cancelHistory['fee_charged'])) ? $this->Number->currency($cancelHistory['fee_charged'], 'USD', array('after' => false, 'negative' => '-')) : "--"; ?>    
						</td>
						<td class="text-center">
							<?php echo (!empty($cancelHistory['axia_invoice_number'])) ? H($cancelHistory['axia_invoice_number']) : "--"; ?>    
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>
<script type='text/javascript'>activateNav('MerchantCancellationsView'); </script>