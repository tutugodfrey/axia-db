<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchant Reject Lines'); ?> List" />
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('rejectid'); ?></th>
			<th><?php echo $this->Paginator->sort('fee'); ?></th>
			<th><?php echo $this->Paginator->sort('statusid'); ?></th>
			<th><?php echo $this->Paginator->sort('status_date'); ?></th>
			<th><?php echo $this->Paginator->sort('notes'); ?></th>

		</tr>
		<?php foreach ($merchantRejectLines as $merchantRejectLine): ?>
			<tr>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['id']); ?>&nbsp;</td>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['rejectid']); ?>&nbsp;</td>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['fee']); ?>&nbsp;</td>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['statusid']); ?>&nbsp;</td>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['status_date']); ?>&nbsp;</td>
				<td><?php echo h($merchantRejectLine['MerchantRejectLine']['notes']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>