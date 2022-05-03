<?php
/* This report functions as both the cancellation and the attrition ratio report */
if ($showAttrition) {
	$reportName = 'Attrition Ratio Report';
	$urlAction = 'index/1';
} else {
	$reportName = 'Merchant Cancellations Report';
	$urlAction = 'index';
}
echo $this->element('Layout/selectizeAssets');
?>

<input type="hidden" id="thisViewTitle" value="<?php echo __($reportName); ?>" />
	<?php
	echo $this->Form->create('MerchantCancellations', array(
			  'inputDefaults' => array(
						'div' => 'form-group',
						'label' => false,
						'wrapInput' => false,
						'class' => 'form-control'
			  ),
			  'url' => array(
						'controller' => 'MerchantCancellations',
						'action' => $urlAction,
			  ),
			  'type' => 'get',
			  'class' => 'well well-sm form-inline'
	));
	?>
	<?php
	$this->request->data['MerchantCancellations'] = $this->request->query;
	echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
	?>
<script>
	$('#MerchantCancellationsUserId').selectize();
	$('#MerchantCancellationsUserId')[0].selectize.setValue('<?php echo htmlspecialchars(Hash::get($this->request->query, "user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
	<?php
	for ($x = 0; $x < 25; $ammountOfMonths[$x++] = $x - 1)
		; //Build ammount of months options
	echo $this->Form->input('ammountOfMonths', array('empty' => '# of Months in the past (use 0 to show only selected month/year)', 'options' => $ammountOfMonths));
	?>
	<?php echo $this->Form->input('month', array('empty' => 'Up to month', 'options' => $moYrOptns['Months'], 'required' => true)); ?>
	<?php echo $this->Form->input('year', array('empty' => 'Up to Year', 'options' => $moYrOptns['Years'], 'required' => true)); ?>
	<?php
	echo $this->Form->submit('Generate', array(
			  'div' => 'form-group',
			  'class' => 'btn btn-default'
	));
	if (!empty($reportData)){
		if ($queryParams['amountOfmonths'] == '0') {
			echo " <div class='form-group text-center'><h5><span class='label label-success'>Displaying only " . date("F Y", strtotime($queryParams['toDate'])) . "</span></h5></div>";
		} else {
			echo " <div class='form-group text-center'><h5><span class='label label-success'>Displaying " . h($queryParams['amountOfmonths'] + 1) . " month(s) from " .h( date("F Y", strtotime($queryParams['fromDate']))) . " up to and including " . h(date("F Y", strtotime($queryParams['toDate']))) . "</span></h5></div>";
		}
	}
	?>
	<?php echo $this->Form->end(); ?>

	<?php if (!empty($reportData)): ?>

		<?php
		echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini'));
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}.')
		));
		?>

		<table class="table table-condensed table-hover">
			<tr class="reportTables">
				<th><?php echo $this->Paginator->sort('Merchant.merchant_mid', 'MID', array('direction' => 'asc')); ?></th>
				<th><?php echo $this->Paginator->sort('Merchant.merchant_dba', 'DBA'); ?></th>
				<th><?php echo 'Client ID'; ?></th>
				<th><?php echo 'Rep'; ?></th>
				<th><?php echo 'Volume'; ?></th>
				<!-- ***************************************Estimated profit goes here -->
				<th><?php echo $this->Paginator->sort('MerchantCancellation.axia_invoice_number', 'Axia Inv'); ?></th>
				<th><?php echo 'Date Approved'; ?></th>
				<!-- ***************************************Date Approved goes here -->
				<th><?php echo $this->Paginator->sort('MerchantCancellation.date_submitted', 'Date Submitted'); ?></th>
				<th><?php echo $this->Paginator->sort('MerchantCancellation.date_completed', 'Date Completed'); ?></th>
				<th><?php echo $this->Paginator->sort('MerchantCancellation.date_inactive', 'Date Inactive'); ?></th>
				<th><?php echo 'Fee Charged'; ?></th>
				<th><?php echo $this->Paginator->sort('MerchantCancellation.reason', 'Reason'); ?></th>
				<th><?php echo $this->Paginator->sort('MerchantCancellationSubreason.name', 'Details'); ?></th>
				<th><?php echo 'Status'; ?></th>
				<th><?php echo 'Company'; ?></th>
				<th><?php echo 'Partner'; ?></th>
			</tr>
			<?php foreach ($reportData as $data): ?>
				<?php $repInitials = substr($data['User']['user_first_name'], 0, 1) . substr($data['User']['user_last_name'], 0, 1); ?>
				<tr>
					<td>
						<?php echo $this->Html->link($data['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $data['Merchant']['id'])); ?>
					</td>
					<td><?php echo (!empty($data['Merchant']['merchant_dba'])) ? h($data['Merchant']['merchant_dba']) : '--'; ?>&nbsp;</td>
					<td><?php echo (!empty($data['Client']['client_id_global'])) ? h($data['Client']['client_id_global']) : '--'; ?>&nbsp;</td>
					<td><?php echo $repInitials; ?>&nbsp;</td>
					<td><?php echo (!empty($data['Merchant']['MerchantUwVolume']['mo_volume'])) ? $this->Number->currency($data['Merchant']['MerchantUwVolume']['mo_volume'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>&nbsp;</td>
					<!-- ***************************************Estimated profit goes here -->
					<td><?php echo (!empty($data['MerchantCancellation']['axia_invoice_number'])) ? h($data['MerchantCancellation']['axia_invoice_number']) : '--'; ?>&nbsp;</td>
					<!-- ***************************************Date Approved  goes here -->
					<td class="nowrap"><?php
						$approvedDate = (!empty($data['Merchant']['TimelineEntry'])) ? date("M j, Y", strtotime($data['Merchant']['TimelineEntry'][0]['timeline_date_completed'])) : '--';
						echo $approvedDate;
						?>&nbsp;</td>
					<td class="nowrap"><?php echo (!empty($data['MerchantCancellation']['date_submitted'])) ? date("M j, Y", strtotime($data['MerchantCancellation']['date_submitted'])) : '--'; ?>&nbsp;</td>
					<td class="nowrap"><?php echo (!empty($data['MerchantCancellation']['date_completed'])) ? date("M j, Y", strtotime($data['MerchantCancellation']['date_completed'])) : '--'; ?>&nbsp;</td>
					<td class="nowrap"><?php echo (!empty($data['MerchantCancellation']['date_inactive'])) ? date("M j, Y", strtotime($data['MerchantCancellation']['date_inactive'])) : '--'; ?>&nbsp;</td>
					<td><?php echo (!empty($data['MerchantCancellation']['fee_charged'])) ? $this->Number->currency($data['MerchantCancellation']['fee_charged'], 'USD2dec') : '--'; ?>&nbsp;</td>
					<td><?php echo (!empty($data['MerchantCancellation']['reason'])) ? h($data['MerchantCancellation']['reason']) : '--'; ?>&nbsp;</td>
					<td><?php echo (!empty($data['MerchantCancellationSubreason']['name'])) ? h($data['MerchantCancellationSubreason']['name'] . " " . $data['MerchantCancellation']['merchant_cancellation_subreason']) : h($data['MerchantCancellation']['merchant_cancellation_subreason']); ?>&nbsp;</td>
					<td><?php echo $this->Html->image("/img/" . $data['MerchantCancellation']['statusFlag'], array("class" => "icon")); ?>
					</td>
					<td><?php echo (!empty($data['Entity']['entity_name'])) ? h($data['Entity']['entity_name']) : '--'; ?>&nbsp;</td>
					<td><?php echo (!empty($data['Partner']['fullname'])) ? h($data['Partner']['fullname']) : '--'; ?>&nbsp;</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		if ($showAttrition):
			$queryParams['cancelledMerchants'] = $this->Paginator->counter(array('format' => '{:count}'));
			$activeMerchants = (int) $queryParams['activeMerchants'];
			$cancelledMerchants = (int) $queryParams['cancelledMerchants'];
			?>
			<div class="row">
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<span class="badge"><?php echo h($queryParams['activeMerchants']); ?></span>
							Number of Open Merchants:
						</li>
						<li class="list-group-item list-group-item-info">
							<span class="badge">
								<?php echo h($queryParams['cancelledMerchants']); ?>
							</span>
							Number of Canceled Merchants:
						</li>
						<li class="list-group-item list-group-item-info">
							<span class="badge"><?php echo $this->Number->toPercentage(bcdiv($cancelledMerchants, $activeMerchants), 2, array('multiply' => true)) ?></span>
							Attrition Rate:
						</li>
						<li class="list-group-item list-group-item-info">
							<span class="badge"><?php echo (!empty($userAttritionRate))?$this->Number->toPercentage($userAttritionRate, 2):"<i class='alert-danger roundEdges'>&nbsp;<span class='glyphicon glyphicon-remove-circle'></span> N/A&nbsp;</i>";?></span>
							Plus/Minus Allowance for <?php echo $this->Session->read('Auth.User.fullname') ?>:
						</li>
					</ul>
				</div>
				<div class="col-sm-6 pull-right">
					<?php
					echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini pull-right'));
					?>
				</div>
			</div>
		<?php endif; ?>


	<?php elseif (!empty($queryParams['toDate'])): ?>

		<div class="text-center"><h5><span class='label label-danger'>Sorry no results found from <?php echo h(date("F Y", strtotime($queryParams['fromDate']))) . " to " . h(date("F Y", strtotime($queryParams['toDate']))) ?></span></h5></div>
	<?php else: ?>
		<div class="text-center"><h4><span class='label label-info'>Use options above to display report</span></h4></div>
				<?php endif; ?>
		<?php
	if(!empty($reportData)){
		echo $this->element('Layout/reportFooter');
	}
	?>