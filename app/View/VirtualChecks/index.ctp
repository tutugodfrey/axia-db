<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Virtual Checks'); ?> List" />
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
			<th><?php echo $this->Paginator->sort('merchant_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('vc_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('vc_web_based_rate'); ?></th>
			<th><?php echo $this->Paginator->sort('vc_web_based_pi'); ?></th>
			<th><?php echo $this->Paginator->sort('vc_monthly_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('vc_gateway_fee'); ?></th>

		</tr>
		<?php foreach ($virtualChecks as $virtualCheck): ?>
			<tr>
				<td><?php echo h($virtualCheck['VirtualCheck']['id']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($virtualCheck['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $virtualCheck['Merchant']['id'])); ?>
				</td>
				<td><?php echo h($virtualCheck['VirtualCheck']['vc_mid']); ?>&nbsp;</td>
				<td><?php echo h($virtualCheck['VirtualCheck']['vc_web_based_rate']); ?>&nbsp;</td>
				<td><?php echo h($virtualCheck['VirtualCheck']['vc_web_based_pi']); ?>&nbsp;</td>
				<td><?php echo h($virtualCheck['VirtualCheck']['vc_monthly_fee']); ?>&nbsp;</td>
				<td><?php echo h($virtualCheck['VirtualCheck']['vc_gateway_fee']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>