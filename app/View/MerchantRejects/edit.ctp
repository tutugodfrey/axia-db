<?php $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit Merchant Rejects'); ?>" />

<?php
$merchantId = Hash::get($merchant, 'Merchant.id');
echo $this->element('MerchantRejects/add', array('fixedMerchantId' => $merchantId));
echo $this->Html->tag('h1', __('Edit Merchant Reject'));
echo $this->Form->create('MerchantReject');
echo $this->Form->hidden('MerchantReject.id');
?>
<div class="reportTables">
	<table cellpadding="0" cellspacing="0" id="rejectFilterTable" data-graph-container-before="1" data-graph-type="column">
		<thead>
			<tr>
				<th><?php echo __('Trace'); ?></th>
				<th><?php echo __('Merchant ID'); ?></th>
				<th><?php echo __('DBA'); ?></th>
				<th><?php echo __('Rep'); ?></th>
				<th><?php echo __('Reject Date'); ?></th>
				<th><?php echo __('Type'); ?></th>
				<th><?php echo __('Code'); ?></th>
				<th><?php echo __('Amount'); ?></th>
				<th><?php echo __('Status'); ?></th>
				<th><?php echo __('Reject Fee'); ?></th>
				<th><?php echo __('Submitted Amount'); ?></th>
				<th><?php echo __('Status Date'); ?></th>
				<th><?php echo __('Note'); ?></th>
				<th><?php echo __('Recurrance'); ?></th>
				<th><?php echo __('Open'); ?></th>
				<th><?php echo __('Loss Axia'); ?></th>
				<th><?php echo __('Loss Mgr1'); ?></th>
				<th><?php echo __('Loss Mgr2'); ?></th>
				<th><?php echo __('Loss Rep'); ?></th>
				<th><?php echo __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h($this->request->data('MerchantReject.trace')); ?></td>
				<td><?php echo h($this->request->data('Merchant.merchant_mid')); ?></td>
				<td><?php echo h($this->request->data('Merchant.merchant_dba')); ?></td>
				<td><?php echo h($this->request->data('Merchant.User.initials')); ?></td>
				<td><?php echo $this->Time->date($this->request->data('MerchantReject.reject_date')); ?></td>
				<td><?php echo h($this->request->data('MerchantRejectType.name')); ?></td>
				<td><?php echo h($this->request->data('MerchantReject.code')); ?></td>
				<td><?php echo h($this->Number->currency($this->request->data('MerchantReject.amount'))); ?></td>
				<td><?php echo h($this->request->data('CurrentMerchantRejectLine.MerchantRejectStatus.name')); ?></td>
				<td><?php echo $this->Form->input('reject_fee', array(
					'label' => false,
					'placeholder' => __('Reject Fee')
				)); ?></td>
				<td>[submitted amount]</td>
				<td>
					<?php
					$statusDate = $this->request->data('MerchantRejectLine.0.status_date');
					if (!empty($statusDate)) {
						echo $this->Time->date($statusDate);
					}
					?>
				</td>
				<td><?php echo h($this->request->data('MerchantRejectLine.0.notes'));?></td>
				<td><?php echo h($this->request->data('MerchantRejectRecurrance.name'));?></td>
				<td>
					<?php if ($this->request->data('MerchantReject.open')): ?>
						<span class="label label-primary">open</span>
					<?php else: ?>
						<span class="label label-danger">close</span>
					<?php endif; ?>
				</td>
				<td><?php echo h($this->Number->currency($this->request->data('MerchantReject.loss_axia'))); ?></td>
				<td><?php echo h($this->Number->currency($this->request->data('MerchantReject.loss_mgr1'))); ?></td>
				<td><?php echo h($this->Number->currency($this->request->data('MerchantReject.loss_mgr2'))); ?></td>
				<td><?php echo h($this->Number->currency($this->request->data('MerchantReject.loss_rep'))); ?></td>
				<td><?php echo $this->Form->submit(__('Save')); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php echo $this->Form->end();
