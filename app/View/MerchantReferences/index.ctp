<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchant References'); ?> List" />
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
			<th><?php echo $this->Paginator->sort('merchant_ref_seq_number'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_ref_type'); ?></th>
			<th><?php echo $this->Paginator->sort('bank_name'); ?></th>
			<th><?php echo $this->Paginator->sort('person_name'); ?></th>
			<th><?php echo $this->Paginator->sort('phone'); ?></th>

		</tr>
		<?php foreach ($merchantReferences as $merchantReference): ?>
			<tr>
				<td><?php echo h($merchantReference['MerchantReference']['id']); ?>&nbsp;</td>
				<td><?php echo h($merchantReference['MerchantReference']['merchant_ref_seq_number']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($merchantReference['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantReference['Merchant']['id'])); ?>
				</td>
				<td><?php echo h($merchantReference['MerchantReference']['merchant_ref_type']); ?>&nbsp;</td>
				<td><?php echo h($merchantReference['MerchantReference']['bank_name']); ?>&nbsp;</td>
				<td><?php echo h($merchantReference['MerchantReference']['person_name']); ?>&nbsp;</td>
				<td><?php echo h($merchantReference['MerchantReference']['phone']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>