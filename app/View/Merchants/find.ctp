
<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchants'); ?> List" />
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<table cellpadding="0" cellspacing="0">
		<tr>            <th><?php echo $this->Paginator->sort('merchant_dba', 'DBA'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid', 'MID'); ?></th>
			<th><?php echo $this->Paginator->sort('user_first_name', 'Rep'); ?></th>
			<th><?php echo $this->Paginator->sort('address_city', 'City'); ?></th>
			<th><?php echo $this->Paginator->sort('address_state', 'State'); ?></th>
			<th><?php echo $this->Paginator->sort('address_phone', 'Phone'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_contact', 'Contact'); ?></th>
			<th><?php echo $this->Paginator->sort('active', 'Active/Inactive'); ?></th>                        

		</tr>
		<?php foreach ($merchants as $merchant): ?>
			<tr>    
				<td><?php echo $this->Html->link($merchant['Merchant']['merchant_dba'], array('controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>&nbsp;</td>
				<td><?php echo $this->Html->link($merchant['Merchant']['merchant_mid'], array('controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>&nbsp;</td>
				<td><?php echo h($merchant['User']['user_first_name'] . ' ' . $merchant['User']['user_last_name']); ?>&nbsp;</td>
				<td><?php echo h($merchant['Address'][0]['address_city']); ?>&nbsp;</td>
				<td><?php echo h($merchant['Address'][0]['address_state']); ?>&nbsp;</td>
				<td><?php echo h($merchant['Address'][0]['address_phone']); ?>&nbsp;</td>
				<td><?php echo h($merchant['Merchant']['merchant_contact']); ?>&nbsp;</td>
				<td><?php
					if ($merchant['Merchant']['active'])/* binary boolean values */
						echo $this->Html->image('green_orb.gif', array('title' => 'Active'));
					else
						echo $this->Html->image('red_orb.png', array('title' => 'Inactive'));
					?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php if (empty($merchants)) : ?>
		<br />
		<div style="text-align:center;">
			<span style="border:1px solid #CCC; padding:4px 5px;" class="roundEdges">
				<strong><span style='color:red'>Sorry, your search did not yield any results</span></strong> 
			</span>
		</div>
		<br />
	<?php endif; ?>
</div>