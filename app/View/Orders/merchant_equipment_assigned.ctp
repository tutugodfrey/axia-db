<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Equipment, Peripherals & Other Accessories', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Equipment, Peripherals & Other Accessories')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
<p>
	<?php
	echo $this->Paginator->counter(array(
			  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
<table class="table table-condensed table-hover">
	<tr class="reportTables">

		<th><?php echo h('Type'); ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)): ?>
			<th><?php echo $this->Paginator->sort('Order.date_ordered', 'Axia Order Date'); ?></th>
		<?php endif; ?>
		<th><?php echo h('Item'); ?></th>
		<th><?php echo h('Item Order Date'); ?></th>
		<th><?php echo h('Qty'); ?></th>
		<th><?php echo h('New S/N'); ?></th>
		<th><?php echo h('Broken S/N'); ?></th>	
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
			<th><?php echo h('Item Rep Cost'); ?></th>			
		<?php endif; ?>
		<th><?php echo h('Item Partner Cost'); ?></th>
		<th><?php echo 'Shipping Type'; ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
			<th><?php echo $this->Paginator->sort('Order.shipping_cost', 'Shipping Cost'); ?></th>
		<?php endif; ?>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
			<th><?php echo $this->Paginator->sort('Order.invoice_number', 'Vendor Invoice #'); ?></th>
			<th><?php echo $this->Paginator->sort('Order.Vendor.vendor_description', 'Vendor'); ?></th>                                            
		<?php endif; ?>
		<th><?php // Spacer  ?></th>
	</tr>
	<?php
	$orderGroupStyle = "";
	$echoOrder = false;
	foreach ($orders as $order):
		/* Toggle background color to differentiate the group of items in each "Orders" */
		$orderGroupStyle = ($orderGroupStyle === "style='background:#C8C8C8'") ? "" : "style='background:#C8C8C8'";
		$echoOrder = true;
		foreach ($order['Order']['Orderitem'] as $item):
			?>
			<tr <?php echo $orderGroupStyle; ?>>
				<td><?php echo h($item['OrderitemType']['orderitem_type_description']); ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)): ?>
					<td><?php echo (!empty($order['Order']['date_ordered'])) ? date('M j, Y', strtotime($order['Order']['date_ordered'])) : '--'; ?>&nbsp;</td>
				<?php endif; ?>
				<td><?php echo h($item['equipment_item_description']); ?>&nbsp;</td>		
				<td><?php echo (!empty($item['item_date_ordered'])) ? date('M j, Y', strtotime($item['item_date_ordered'])) : '--'; ?>&nbsp;</td>		
				<td><?php echo h($item['quantity']); ?>&nbsp;</td>
				<td><?php echo h($item['hardware_sn']); ?>&nbsp;</td>
				<td><?php echo h($item['hardware_replacement_for']); ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
					<td><?php echo $this->Number->currency($item['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')); ?>&nbsp;</td>
				<?php endif; ?>
				<td><?php echo $this->Number->currency($item['equipment_item_partner_price'], 'USD', array('after' => false, 'negative' => '-')); ?>&nbsp;</td>
				<td><?php echo ($echoOrder && !empty($order['Order']['ShippingType']['shipping_type_description'])) ? h($order['Order']['ShippingType']['shipping_type_description']) : ''; ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Order']['shipping_cost'])) ? $this->Number->currency($order['Order']['shipping_cost'], 'USD', array('after' => false, 'negative' => '-')) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Order']['invoice_number'])) ? $this->Html->link($order['Order']['invoice_number'], array('controller' => 'Orders', 'action' => 'equipment_invoice', $order['Order']['id'])) : ''; ?>&nbsp;</td>

					<td><?php echo ($echoOrder && !empty($order['Order']['Vendor']['vendor_description'])) ? h($order['Order']['Vendor']['vendor_description']) : ''; ?>&nbsp;</td>                                        
				<?php endif; ?>
				<td class="nowrap">
					<?php
					if ($echoOrder) {
						echo $this->Html->image("/img/" . $FlagStatusLogic->getThisStatusFlag($order['Order']['status']), array("class" => "icon"));
						if ($this->Rbac->isPermitted('Orders/edit_equipment_invoice')) {
							echo $this->Html->image("/img/detail_page.png", array("title" => "View Order Details", "class" => "icon", 'url' => array('controller' => 'Orders', 'action' => 'equipment_invoice', $order['Order']['id'])));
						}
						if ($this->Rbac->isPermitted('Orders/edit_equipment_invoice') && $this->Rbac->isPermitted('Orderitems/edit'))
							echo $this->Html->image("/img/editPencil.gif", array("title" => "Edit Order Invoice", "class" => "icon", 'url' => array('controller' => 'Orders', 'action' => 'edit_equipment_invoice', $order['Order']['id'])));
					}
					$echoOrder = false; /* Do not displayed the same order data again */
					?>
				</td>                    
			</tr>
		<?php
		endforeach;
	endforeach;
	?>
</table>
<script type='text/javascript'>activateNav('OrdersMerchantEquipmentAssigned'); </script>