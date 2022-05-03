<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Equipment, Peripherals & Other Accessories', '/' . $this->name . '/' . $this->action);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo '<strong>Axia<strong> | Equipment, Peripherals & Other Accessories'; ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<div class="contentModule text-center">
	<?php
	echo $this->Form->create('Order', array('novalidate' => true, 'inputDefaults' => array(
						'div' => false,
						'label' => array('class' => 'nowrap'),
						'wrapInput' => false,
						'class' => 'form-control'
			  ),
			  'class' => 'well well-sm form-inline'));
	?>
    <table>
        <tr><td>
				<?php
				echo $this->Form->input('equipment_item_id', array('div' => false, 'label' => 'Item ', 'options' => $filterOptns['EquipmentItem'], 'empty' => '--')) . '&nbsp;';
				echo $this->Form->input('dba_mid', array('div' => false, 'label' => 'Merchant ')) . '&nbsp;';
				echo $this->Form->input('status', array('div' => false, 'label' => 'Order Status ', 'options' => $filterOptns['OrderStatuses'], 'empty' => 'All'));
				echo $this->Form->input('orderitem_type_id', array('div' => false, 'label' => 'Order Type ', 'options' => $filterOptns['OrderitemType'], 'empty' => 'All'));
				echo $this->Form->input('commission_month', array('div' => false, 'label' => 'Commission Month', 'options' => array('IS NOT NULL' => 'Assigned', 'IS NULL' => 'Unassigned'), 'empty' => 'All')) . '&nbsp;';
				?>
			</td></tr>
        <tr><td>
				<?php
				echo $this->Form->input('invoice_number', array('div' => false, 'label' => 'Vendor Invoice # ')) . '&nbsp;';
				echo $this->Form->input('hardware_sn', array('div' => false, 'label' => 'New S/N ')) . '&nbsp;';
				echo $this->Form->input('hardware_replacement_for', array('div' => false, 'label' => 'Broken S/N ')) . '&nbsp;';
				echo $this->Form->input('date_ordered_b_month', array('div' => false, 'label' => 'Begin ', 'options' => $filterOptns['months'], 'empty' => '--', 'default' => date('m') - 1)) . '&nbsp;';
				echo $this->Form->input('date_ordered_b_year', array('div' => false, 'label' => false, 'options' => $filterOptns['years'], 'empty' => '--', 'default' => date('Y'))) . '&nbsp;';
				echo $this->Form->input('date_ordered_e_month', array('div' => false, 'label' => 'End ', 'options' => $filterOptns['months'], 'empty' => '--')) . '&nbsp;';
				echo $this->Form->input('date_ordered_e_year', array('div' => false, 'label' => false, 'options' => $filterOptns['years'], 'empty' => '--')) . '&nbsp;';
				?>
				<?php echo $this->Form->end(array('label' => 'Generate', 'class' => 'btn btn-success btn-sm', 'div' => false)); ?>
			</td></tr>
    </table>

    <div class="pager text-center" style="margin:5px 0px 5px 0px">
		<?php
		if (!empty($orders)) {
			echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
			echo $this->Paginator->numbers(array('separator' => ''));
			echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		} else {
			echo "<h5><span class='label label-danger'>Sorry, nothing was found.</span></h5>";
		}
		?>
        &nbsp;&nbsp;
		<?php if ($this->Rbac->isPermitted('Orders/add_equipment_invoice')): ?>

			<?php
			echo '<li>' . $this->Html->link('Create New Order ' .
					$this->Html->image("/img/newNote.png", array('class' => 'icon', "title" => "Create a new order")), array('controller' => 'Orders', 'action' => 'add_equipment_invoice'), array('escape' => false, 'class' => 'strong')) . '</li>';
			?>&nbsp;
		<?php endif; ?>
    </div>
</div>
<table class="table table-condensed table-hover">
	<tr class="contentModuleTitle">
		<?php $columns = 0;

		if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)): ?>
			<th><?php echo $this->Paginator->sort('Order.date_ordered', 'Order Date'); ?></th>
		<?php endif; ?>
		<th><?php 
			$columns++;
			echo h('Item'); ?></th>
		<th><?php 
			$columns++;
			echo h('Item Date'); ?></th>
		<th><?php 
			$columns++;
			echo h('Merchant DBA'); ?></th>
		<th><?php 
			$columns++;
			echo h('Qty'); ?></th>
		<th><?php 
			$columns++;
			echo h('New S/N'); ?></th>
		<th><?php 
			$columns++;
			echo h('Broken S/N'); ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): 
			$columns++;
		?>
			<th><?php echo $this->Paginator->sort('Order.invoice_number', 'Vendor Invoice #'); ?></th>
		<?php endif; ?>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)):
			$columns++;
		 ?>
			<th><?php 
				echo h('Rep Cost'); ?></th>
		<?php endif; ?>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): 
			$columns++;
		?>
			<th><?php echo h('True Cost'); ?></th>
		<?php endif; ?>
		<th><?php 
			$columns++;
			echo h('Tax'); ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): 
			$columns++;
		?>
			<th><?php echo h('Shipping'); ?></th>
		<?php endif; ?>
		<th><?php 
			$columns++;
			echo h('Warranty'); ?></th>
		<th><?php 
			$columns++;
			echo h('Total'); ?></th>
		<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): 
			$columns++;
		?>
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
		$totalCost = 0;
		$totalItemsCost = 0;
		$dateOrdered = (!empty($order['Order']['date_ordered']) && $echoOrder) ? $this->AxiaTime->date($order['Order']['date_ordered']) : '--';
		$actions = $this->Html->image("/img/" . $FlagStatusLogic->getThisStatusFlag($order['Order']['status']), array("class" => "icon"));
		$actions .= $this->Html->image("/img/detail_page.png", array("title" => "View Order Details", "class" => "icon", 'url' => array('controller' => 'Orders', 'action' => 'equipment_invoice', $order['Order']['id'])));
		if ($this->Rbac->isPermitted('Orders/edit_equipment_invoice') && $this->Rbac->isPermitted('Orderitems/edit')) {
			$actions .= $this->Html->editImage(array("title" => "Edit Order Invoice", 'url' => array('controller' => 'Orders', 'action' => 'edit_equipment_invoice', $order['Order']['id'])));
		}
		if ($this->Rbac->isPermitted('Orders/mark_as_paid_order') && $order['Order']['status'] !== 'PAID') {
			$actions .= $this->Form->postLink($this->Html->image("/img/dollar_Icon.jpg", array("title" => "Mark as Paid", "class" => "icon")), array('controller' => 'Orders', 'action' => 'mark_as_paid_order', $order['Order']['id']), array('escape' => false, 'confirm' => __('Mark order as paid?')));
		}
		if ($this->Rbac->isPermitted('Orders/delete')) {
			$actions .= $this->Form->postLink($this->Html->image("/img/redx.png", array("title" => "Delete Order", "class" => "icon")), array('controller' => 'Orders', 'action' => 'delete', $order['Order']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete that order?')));
		}
		//if this order contains no items display only the order row 
		if (empty($order['Orderitem'])) {
				//open row
				echo "<tr $orderGroupStyle >";
				if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)) {
					echo "<td> $dateOrdered </td>";
				}
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td>--</td>"; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Order']['invoice_number'])) ? $this->Html->link($order['Order']['invoice_number'], array('controller' => 'Orders', 'action' => 'equipment_invoice', $order['Order']['id'])) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
					<td> -- </td>
				<?php endif; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
					<td> -- </td>
				<?php endif; ?>
				<td><?php echo ($echoOrder && !empty($order['Order']['tax'])) ? $this->Number->currency($order['Order']['tax'], 'USD', array('after' => false, 'negative' => '-')) : ''; ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
					<td>
					<?php echo ($echoOrder && !empty($order['Order']['shipping_cost'])) ? $this->Number->currency($order['Order']['shipping_cost'], 'USD', array('after' => false, 'negative' => '-')) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				<?php 
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td>--</td>";
				echo "<td> $actions </td> </tr>";
		} else {
			 foreach ($order['Orderitem'] as $item) {
				//open row
				echo "<tr $orderGroupStyle >";
				if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)) {
					echo "<td> $dateOrdered </td>";
				}
				?>
				<td><?php echo h($item['equipment_item_description']); ?>&nbsp;</td>
				<td><?php echo (!empty($item['item_date_ordered'])) ? date('M j, Y', strtotime($item['item_date_ordered'])) : '--'; ?>&nbsp;</td>
				<td><?php echo (!empty($item['Merchant'])) ? h($item['Merchant']['merchant_dba']) : '--'; ?>&nbsp;</td>
				<td><?php echo h($item['quantity']); ?>&nbsp;</td>
				<td><?php echo h($item['hardware_sn']); ?>&nbsp;</td>
				<td><?php echo h($item['hardware_replacement_for']); ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Order']['invoice_number'])) ? $this->Html->link($order['Order']['invoice_number'], array('controller' => 'Orders', 'action' => 'equipment_invoice', $order['Order']['id'])) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
					<td><?php echo $this->Number->currency($item['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')); ?>&nbsp;</td>
				<?php endif; ?>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
					<td><?php echo $this->Number->currency($item['equipment_item_true_price'], 'USD', array('after' => false, 'negative' => '-')); ?>&nbsp;</td>
				<?php endif; ?>
				<td><?php echo ($echoOrder && !empty($order['Order']['tax'])) ? $this->Number->currency($order['Order']['tax'], 'USD', array('after' => false, 'negative' => '-')) : ''; ?>&nbsp;</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Order']['shipping_cost'])) ? $this->Number->currency($order['Order']['shipping_cost'], 'USD', array('after' => false, 'negative' => '-')) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				<td><?php echo (!empty($item['Warranty']['cost'])) ? $this->Number->currency($item['Warranty']['cost'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>&nbsp;</td>

				<td><?php
					if ($echoOrder) {
						//Calculate total
						foreach ($order['Orderitem'] as $iVal) {
							$quantity = ($iVal['quantity'] === 0 || empty($iVal['quantity'])) ? 1 : $iVal['quantity'];
							$totalItemsCost += ($iVal['equipment_item_true_price'] * $quantity);
						}
						$totalCost += $order['Order']['shipping_cost'] + $order['Order']['tax'] + $totalItemsCost;
						echo $this->Number->currency($totalCost, 'USD', array('after' => false, 'negative' => '-'));
					}
					?>&nbsp;
				</td>
				<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
					<td><?php echo ($echoOrder && !empty($order['Vendor']['vendor_description'])) ? h($order['Vendor']['vendor_description']) : ''; ?>&nbsp;</td>
				<?php endif; ?>
				</td>
				<?php if ($echoOrder) {
					echo "<td class='nowrap'> $actions </td>";
				} else {
					echo "<td></td>";
				}
				//end row
				echo "</tr>";
				$echoOrder = false;
			}//endforeach;
		}//end else

		?>
	<?php endforeach;
	?>
</table>
<?php
if(!empty($orders)){
	echo $this->element('Layout/reportFooter');
}
?>
