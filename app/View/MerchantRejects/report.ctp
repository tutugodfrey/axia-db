<?php /* Drop breadcrumb */ 
//Load form imput control plugin
echo $this->element('Layout/selectizeAssets');
$this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->request->url); 
?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchant Rejects'); ?> List" />

<?php
if ($this->Rbac->isPermitted('MerchantRejects/add')) {
	echo $this->element('MerchantRejects/add');
} ?>

<div class="row">
	<div class="col-xs-12">
		<h1><?php echo __('Search Merchant Rejects'); ?></h1>
		<?php
		if (!empty($merchantRejects) && $this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)) {
			$icon = $this->Csv->icon(null, [
			'title' => __('Export Rejects Report'),
			'class' => 'icon'
			]);
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->Html->link($icon, '#', ['escape' => false, 'onClick' => "exportTableToCSV('rejects-report.csv', 'rejectFilterTable')"]);
			echo '</span>';			
		}
		echo $this->Form->createFilterForm('MerchantReject');
		echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
		echo $this->Form->input('merchant_dba', array('label' => 'Merchant'));
		echo $this->Form->input('merchant_reject_type_id', array('options' => $merchantRejectTypes, 'empty' => true, 'label' => 'Type', 'required' => false));
		echo $this->Form->input('from_date', array(
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->input('end_date', array(
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->input('merchant_reject_status_id', array('options' => $merchantRejectStatuses, 'empty' => true));
		echo $this->Form->input('open', array( 'options' => array( 1 => 'Open', 0 => 'Closed'), 'empty' => true, 'required' => false));

		echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default', 'div' => array('class' => 'form-group')));
		
		?>
	</div>
</div>

<div class="reportTables row">
	<div class="col-xs-12">
		<?php echo $this->element('pagination'); ?>
		<table class="table-condensed" id="rejectFilterTable" data-graph-container-before="1" data-graph-type="column">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('trace'); ?></th>
					<th><?php echo $this->Paginator->sort('Merchant.merchant_mid', 'Merchant ID'); ?></th>
					<th><?php echo $this->Paginator->sort('Merchant.merchant_dba', 'DBA'); ?></th>
					<th><?php echo 'Rep'; ?></th>
					<th><?php echo $this->Paginator->sort('reject_date'); ?></th>
					<th><?php echo $this->Paginator->sort('MerchantRejectType.name', 'Type'); ?></th>
					<th><?php echo $this->Paginator->sort('code'); ?></th>
					<th><?php echo $this->Paginator->sort('amount', null, array('direction' => 'desc')); ?></th>
					<th><?php echo 'Status'; ?></th>
					<th><?php echo $this->Paginator->sort('CurrentMerchantRejectLine.fee', 'Reject Fee', array('direction' => 'desc')); ?></th>
					<th><?php echo 'Submitted Amount'; ?></th>
					<th><?php echo $this->Paginator->sort('CurrentMerchantRejectLine.status_date', 'Status Date', array('direction' => 'desc')); ?></th>
					<th><?php echo 'Note'; ?></th>
					<th><?php echo $this->Paginator->sort('open'); ?></th>
					<th><?php echo $this->Paginator->sort('loss_axia', null, array('direction' => 'desc')); ?></th>
					<th><?php echo $this->Paginator->sort('loss_mgr1', null, array('direction' => 'desc')); ?></th>
					<th><?php echo $this->Paginator->sort('loss_mgr2', null, array('direction' => 'desc')); ?></th>
					<th><?php echo $this->Paginator->sort('loss_rep', null, array('direction' => 'desc')); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ((array)$merchantRejects as $merchantReject): ?>
				<tr>
					<td><?php echo h(Hash::get($merchantReject, 'MerchantReject.trace')); ?></td>
					<td>
						<?php echo $this->Html->link($merchantReject['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantReject['Merchant']['id'])); ?>
					</td>
					<td><?php echo $this->Html->link($merchantReject['Merchant']['merchant_dba'], array('controller' => 'merchants', 'action' => 'view', $merchantReject['Merchant']['id'])); ?></td>
					<td><?php echo h(Hash::get($merchantReject, 'Merchant.User.initials')); ?></td>
					<td>
						<?php echo $this->Time->date(Hash::get($merchantReject, 'MerchantReject.reject_date')); ?>
					</td>
					<td><?php echo h($merchantReject['MerchantRejectType']['name']); ?></td>
					<td><?php echo h($merchantReject['MerchantReject']['code']); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.amount'))); ?></td>
					<td><?php echo h(Hash::get($merchantReject, 'CurrentMerchantRejectLine.MerchantRejectStatus.name')); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'CurrentMerchantRejectLine.fee'))); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'CurrentMerchantRejectLine.submitted_amount'))); ?></td>
					<td>
						<?php
						$statusDate = Hash::get($merchantReject, 'CurrentMerchantRejectLine.status_date');
						if (!empty($statusDate)) {
							echo $this->Time->date($statusDate);
						}
						?>
					</td>
					<td><?php echo h(Hash::get($merchantReject, 'CurrentMerchantRejectLine.notes'));?></td>
					<td><?php echo $this->MerchantReject->showOpenStatus(Hash::get($merchantReject, 'MerchantReject.open')); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.loss_axia'))); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.loss_mgr1'))); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.loss_mgr2'))); ?></td>
					<td><?php echo h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.loss_rep'))); ?></td>
					<td>
						<?php
						echo $this->Html->editImageLink(
							'#',
							array(
								'class' => 'edit-merchant-rejects',
								'data-target' => Router::url(array(
									'plugin' => null,
									'controller' => 'MerchantRejects',
									'action' => 'editRow',
									Hash::get($merchantReject, 'MerchantReject.id'),
									true
								))
							)
						);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$('#MerchantRejectUserId').selectize();
	$('#MerchantRejectUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("MerchantReject.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
<?php
echo $this->AssetCompress->script('reports', array('raw' => (bool)Configure::read('debug')));
$this->AssetCompress->autoInclude = false;
echo $this->Html->script('views/merchant_rejects/index', array('block' => 'script'));