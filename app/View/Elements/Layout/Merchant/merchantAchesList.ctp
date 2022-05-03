<div class="row">
    <table class="table table-condensed table-hover">
        <tr>
            <th colspan='2' class="text-right"><?php echo $this->Paginator->sort('invoice_number'); ?></th>
            <th><?php echo $this->Paginator->sort('ach_date', 'Date Created'); ?></th>
            <th><?php echo $this->Paginator->sort('date_completed', 'Date Completed'); ?></th>
            <th><?php echo $this->Paginator->sort('exact_shipping_amount', 'Exact Shipping'); ?></th>
            <th><?php echo $this->Paginator->sort('general_shipping_amount', 'General Shipping'); ?></th>
            <th><?php echo $this->Paginator->sort('ach_amount', 'Total Taxable Amount'); ?></th>
            <th><?php echo $this->Paginator->sort('non_taxable_ach_amount', 'Total Non-Taxable Amount'); ?></th>
            <th><?php echo $this->Paginator->sort('tax', 'Total Tax'); ?></th>
            <th><?php echo $this->Paginator->sort('total_ach', 'Total Amount Billed'); ?></th>
            <th><?php echo $this->Paginator->sort('rejected', 'Rejected?'); ?></th>
            <th><?php echo $this->Paginator->sort('resubmit_date'); ?></th>
            <th>Bill To</th>
            <th>App Status</th>
			<?php if ($this->Rbac->isPermitted('app/actions/MerchantAches/view/module/hidenS1', true)): ?>
				<th>Commission Month</th>
			<?php endif; ?>
            <th><?php echo $this->Paginator->sort('status'); ?></th>
        </tr>
		<?php foreach ($merchantAches as $merchantAch): ?>
			<tr>
				<td><?php echo $this->Html->link(
							$this->Html->tag('span', '<!--toggle show items-->', array('class' => 'glyphicon glyphicon-resize-vertical')),
							'javascript:void(0)', 
							array('escape' => false,
							'class' => 'btn btn-xs btn-info',
							'onClick' => "$('#itemCollection{$merchantAch['MerchantAch']['id']}').toggle(500)",
							'data-toggle' =>'tooltip', 'data-placement' => 'bottom', 'data-original-title' => 'Show/Hide Invoice Items'
						)); ?>
				</td>
				<td><?php echo h($merchantAch['MerchantAch']['invoice_number']); ?></td>
				<td><?php echo (!empty($merchantAch['MerchantAch']['ach_date'])) ? date('M j, Y', strtotime($merchantAch['MerchantAch']['ach_date'])) : ''; ?></td>
				<td><?php echo (!empty($merchantAch['MerchantAch']['date_completed'])) ? date('M j, Y', strtotime($merchantAch['MerchantAch']['date_completed'])) : '--'; ?></td>
				<td><?php echo h($this->Number->currency($merchantAch['MerchantAch']['exact_shipping_amount'], 'USD', array('after' => false, 'negative' => '-'))); ?></td>
				<td><?php echo h($this->Number->currency($merchantAch['MerchantAch']['general_shipping_amount'], 'USD', array('after' => false, 'negative' => '-'))); ?></td>
				<td><?php echo $this->Number->currency($merchantAch['MerchantAch']['ach_amount'], 'USD', array('after' => false, 'negative' => '-')); ?></td>
				<td><?php echo $this->Number->currency($merchantAch['MerchantAch']['non_taxable_ach_amount'], 'USD', array('after' => false, 'negative' => '-')); ?></td>
				<td><?php echo h($this->Number->currency($merchantAch['MerchantAch']['tax'], 'USD2dec')); ?></td>

				<td><?php echo h($this->Number->currency($merchantAch['MerchantAch']['total_ach'], 'USD', array('after' => false, 'negative' => '-'))); ?></td>
				<td><?php echo ($merchantAch['MerchantAch']['rejected'] === TRUE) ? '<span class="label label-warning" style="font-size:100%">YES</span>' : 'NO'; ?></td>
				<td><?php echo (!empty($merchantAch['MerchantAch']['resubmit_date'])) ? date('M j, Y', strtotime($merchantAch['MerchantAch']['resubmit_date'])) : ''; ?></td>
				<td>
					<?php 
					echo (!empty($merchantAch['MerchantAch']['merchant_ach_billing_option_id'])) ? h($merchantAch['MerchantAchBillingOption']['billing_option_description']) : ''; ?>
				</td>
				<td>
					<?php echo (!empty($merchantAch['MerchantAch']['merchant_ach_app_status_id'])) ? h($merchantAch['MerchantAchAppStatus']['app_status_description']) : ''; ?>
				</td>
				<?php if ($this->Rbac->isPermitted('app/actions/MerchantAches/view/module/hidenS1', true)): ?>
					<td><?php 
					$commMoYr = $merchantAch['MerchantAch']['commission_month'] . "/" .$merchantAch['MerchantAch']['commission_year'];
					echo (!empty($merchantAch['MerchantAch']['commission_month'])) ? $this->AxiaTime->dateChangeFormat($commMoYr, 'm/Y', 'M Y') : ''; ?></td>
				<?php endif; ?>
				<td class="nowrap">
					<?php
					echo $this->Html->image("/img/" . $FlagStatusLogic->getThisStatusFlag($merchantAch['MerchantAch']['status']), array("class" => "icon"));
					if ($this->Rbac->isPermitted('MerchantAches/edit'))
						echo $this->Html->image("/img/editPencil.gif", array("title" => "Edit Invoice", "class" => "icon", 'url' => array('controller' => 'MerchantAches', 'action' => 'edit', $merchantAch['MerchantAch']['id'])));
					if ($this->Rbac->isPermitted('MerchantAches/delete'))
						echo $this->Form->postLink($this->Html->image("/img/redx.png", array("title" => "Delete Invoice", "class" => "icon")), array('controller' => 'MerchantAches', 'action' => 'delete', $merchantAch['MerchantAch']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete invoice %s?', "#" . $merchantAch['MerchantAch']['invoice_number'])));
					?>

				</td>
			</tr>
			<tr style="display:none" id="itemCollection<?php echo $merchantAch['MerchantAch']['id']; ?>">
				<td colspan='2'><!--spacer--></td>
				<td colspan='99'>
			<?php if (!empty($merchantAch['InvoiceItem'])): ?>
					<table class="table-extra-condensed table-striped table-bordered" style="width:85%">
					<tr class="panel-info">
						<th class="text-center panel-heading">Item #</th>
						<th class="text-center panel-heading">Description</th>
						<th class="text-center panel-heading">Commissionable</th>
						<th class="text-center panel-heading">Taxable</th>
						<th class="text-center panel-heading">Non Taxable Reason</th>
						<th class="text-center panel-heading">Amount</th>
						<th class="text-center panel-heading">Tax</th>
					</tr>	
						<?php foreach($merchantAch['InvoiceItem'] as $idx => $item): ?>
					<tr>
						<td class="text-center"><?php echo $idx+1; ?></td>
						<?php
							$descStr = (Hash::get($achReasons, $item['merchant_ach_reason_id']))?: h(Hash::get($item,'MerchantAchReason.reason'));
							$descStr .= (!empty($item['reason_other']))? " - " . ($item['reason_other']) : null;
						?>
						<td class="text-center"><?php echo __($descStr); ?></td>
						<td class="text-center strong text-success"><?php echo ($item['commissionable'])? "<span class='strong text-success'>Yes</span>" : "<span class='text-muted'>No</span>"; ?></td>
						<td class="text-center"><?php echo ($item['taxable'])?"<span class='strong text-success'>Yes</span>" : "<span class='text-muted'>No</span>"; ?></td>
						<td class="text-center"><?php echo h(__(Hash::get($item, 'NonTaxableReason.reason'))); ?></td>
						<td class="text-center"><?php echo $this->Number->currency($item['amount'], 'USD2dec'); ?></td>
						<td class="text-center"><?php echo $this->Number->currency($item['tax_amount'], 'USD2dec'); ?></td>
						<?php if ($this->Rbac->isPermitted('app/actions/InvoiceItems/delete') && empty($item['merchant_ach_reason_id']) &&
							 empty($item['reason_other']) && empty($item['non_taxable_reason_id']) && empty($item['amount'])): ?>
						<td class="text-center panel-heading"><?php 
							$url = array(
									'controller' => 'InvoiceItems',
									'action' => 'delete',
									$item['id']
								);
							$options = array(
								'class' => "btn btn-xs btn-danger",
								'data-original-title' => "Delete empty line item",
								'data-placement' => "right",
								'data-toggle' => "tooltip",
								'escape' => false,
								'confirm' => "Delete item #" . ($idx+1) . "?\nThis action cannot be reversed!"
							);
							echo $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
						?></td>
						<?php endif; ?>
					</tr>
						<?php endforeach; ?>
					</table>
			<?php else: ?>
					<span class="list-group-item text-center text-muted">- No items -</span>
			<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
    </table>
</div>