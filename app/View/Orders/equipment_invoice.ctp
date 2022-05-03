<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Equipment Invoice ' . h($order['Order']['invoice_number']), '/' . $this->name . '/' . $this->action . '/' . $order['Order']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h(" Santa Barbara Office | " . __('Equipment Invoice ' . $order['Order']['invoice_number'])); ?>" />
<div class="well well-sml">
    <div class="contentModuleTitle">
		<?php
		echo (!empty($order['Order']['invoice_number'])) ? h('Invoice #' . $order['Order']['invoice_number'] . ":") : 'Equipment Invoice:';
		if ($this->Rbac->isPermitted('Orders/edit_equipment_invoice') && $this->Rbac->isPermitted('Orderitems/edit'))
			echo $this->Html->image("/img/editPencil.gif", array("title" => "Edit Order Invoice", "class" => "icon", 'url' => array('controller' => 'Orders', 'action' => 'edit_equipment_invoice', $order['Order']['id'])));
		?>
    </div>
    <div class="table-responsive">
        <table>
            <tr>
                <td class="twoColumnGridCell">
                    <table style="width: auto" cellspacing="0" cellpadding="0" border="0">
						<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/invoices', true)): ?>
							<tr><td>Vendor:</td><td><?php echo (!empty($order['Vendor']['vendor_description'])) ? h($order['Vendor']['vendor_description']) : '--'; ?></td></tr>
							<tr><td>Vendor Invoice #:</td><td><?php echo (!empty($order['Order']['invoice_number'])) ? h($order['Order']['invoice_number']) : '--'; ?></td></tr>
						<?php endif; ?>
						<tr><td>Order #:</td><td><?php echo (!empty($order['Order']['display_id'])) ? h($order['Order']['display_id']) : '--'; ?></td></tr>
						<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/orderDate', true)): ?>
							<tr><td>Order Date:</td><td><?php echo (!empty($order['Order']['date_ordered'])) ? date('M j, Y', strtotime($order['Order']['date_ordered'])) : '--'; ?></td></tr>
						<?php endif; ?>
						<tr><td>Date Paid:</td><td><?php echo (!empty($order['Order']['date_paid'])) ? date('M j, Y', strtotime($order['Order']['date_paid'])) : '--'; ?></td></tr>
						<tr><td>Commission Month:</td><td>
							<?php 
							$commMoYr = h($order['Order']['commission_month'] . "/" . $order['Order']['commission_year']);
							echo (!empty($order['Order']['commission_month'])) ? $this->AxiaTime->dateChangeFormat($commMoYr, 'm/Y', 'M Y') : '--'; ?></td></tr>
						<tr><td class="tab formName">No Charge</td><td><?php echo (($order['Order']['commission_month_nocharge'])) ? 'Yes' : 'No'; ?></td></tr>
						<tr><td>Tax:</td><td><?php echo (!empty($order['Order']['tax'])) ? $this->Number->currency($order['Order']['tax'], 'USD', array('after' => false)) : '--'; ?></td></tr>
						<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
							<tr><td>Shipping $:</td><td><?php echo (!empty($order['Order']['shipping_cost'])) ? $this->Number->currency($order['Order']['shipping_cost'], 'USD', array('after' => false)) : '--'; ?></td></tr>
						<?php endif; ?>
						<tr><td class="tab formName">Add Tax to Items</td><td><?php echo ($order['Order']['add_item_tax']) ? 'Yes' : 'No'; ?></td></tr>
						<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
							<tr><td>Total True Cost:</td><td><?php echo (!empty($order['Order']['total_true_cost'])) ? $this->Number->currency($order['Order']['total_true_cost'], 'USD', array('after' => false)) : '--'; ?></td></tr>
						<?php endif; ?>
						<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
							<tr><td>Total Rep Cost:</td><td><?php echo (!empty($order['Order']['total_rep_cost'])) ? $this->Number->currency($order['Order']['total_rep_cost'], 'USD', array('after' => false)) : '--'; ?></td></tr>
						<?php endif; ?>
                    </table>
                </td>
                <td class="twoColumnGridCell">
                    <table style="width: auto" cellspacing="0" cellpadding="0" border="0">
						<tr><td>Order Status:</td><td><?php echo (!empty($order['Order']['status'])) ? h($orderStatuses[$order['Order']['status']]) : '--'; ?></td></tr>
						<tr><td>Shipping Type:</td><td><?php echo (!empty($order['ShippingType']['shipping_type_description'])) ? h($order['ShippingType']['shipping_type_description']) : '--'; ?></td></tr>
						<tr><td>Ship To:</td><td><?php echo (!empty($order['Order']['ship_to'])) ? h($order['Order']['ship_to']) : '--'; ?></td></tr>
						<tr><td>Return Tracking #:</td><td><?php echo (!empty($order['Order']['tracking_number'])) ? h($order['Order']['tracking_number']) : '--'; ?></td></tr>
                    </table>
					<?php if (!empty($order['Order']['notes'])): ?>
						<div style="width: 300px" class="panel panel-warning">
							<div class="panel-heading"><strong>Note: </strong></div>
							<div class="panel-body"><?php echo nl2br(h($order['Order']['notes'])); ?></div>
						</div>
					<?php endif; ?>
                </td>
            </tr>
        </table>
		<?php if (!empty($orderItems)) : ?>
			<div class="list-group">
				<li class="list-group-item">
					<div class="contrTitle">Items ordered:</div>
					<table class="table table-condensed table-hover" id="orderItemsTable">
						<tr class="reportTables">
							<th><?php echo h('Type'); ?></th>
							<th><?php echo h('Date'); ?></th>
							<th><?php echo $this->Paginator->sort('equipment_item_description', 'Item'); ?></th>
							<th><?php echo h('Commission Month'); ?></th>
							<th><?php echo h('Warranty'); ?></th>
							<th><?php echo h('Qty'); ?></th>
							<th><?php echo $this->Paginator->sort('hardware_sn', 'New S/N'); ?></th>
							<th><?php echo $this->Paginator->sort('hardware_replacement_for', 'Broken S/N'); ?></th>
							<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
								<th><?php echo h('True Cost'); ?></th>
							<?php endif; ?>
							<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
								<th><?php echo h('Rep Cost'); ?></th>
							<?php endif; ?>
							<th><?php echo h('Tax'); ?></th>
							<th><?php echo h('Shipping Type'); ?></th>
							<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
								<th><?php echo h('Shipping Cost'); ?></th>
							<?php endif; ?>
							<th><?php echo h('Merchant'); ?></th>
							<th><?php // Spacer   ?></th>
						</tr>

						<?php
						$rowBg = "";
						$hasItems = false;
						$hasSupplies = false;
						foreach ($orderItems as $item):
							if ($item['EquipmentType']['equipment_type_description'] === 'Supplies')
								$hasSupplies = true;
							/* Toggle background color to denote the items and any corresponding Replacement item data */
							$rowBg = ($rowBg === "class='reportTableRowGroup'") ? "" : "class='reportTableRowGroup'";
							if ($item['EquipmentType']['equipment_type_description'] === 'Hardware'):
								$hasItems = true;
								?>
								<tr <?php echo $rowBg; ?>>
									<td><?php echo (!empty($item['OrderitemType']['orderitem_type_description'])) ? h($item['OrderitemType']['orderitem_type_description']) : '--'; ?></td>
									<td><?php echo (!empty($item['Orderitem']['item_date_ordered'])) ? date('M j, Y', strtotime($item['Orderitem']['item_date_ordered'])) : '--'; ?></td>
									<td><?php echo (!empty($item['Orderitem']['equipment_item_description'])) ? h($item['Orderitem']['equipment_item_description']) : '--'; ?></td>
									<td><?php 
									$commMoYr = $item['Orderitem']['commission_month'] . "/" . $item['Orderitem']['commission_year'];
									echo (!empty($item['Orderitem']['commission_month'])) ? $this->AxiaTime->dateChangeFormat($commMoYr, 'm/Y', 'M Y') : '--'; ?></td>
									<td><?php echo (!empty($item['Warranty']['warranty_description'])) ? h($item['Warranty']['warranty_description']) : '--'; ?></td>
									<td><?php echo (!empty($item['Orderitem']['quantity'])) ? h($item['Orderitem']['quantity']) : '--'; ?></td>
									<td><?php echo (!empty($item['Orderitem']['hardware_sn'])) ? h($item['Orderitem']['hardware_sn']) : '--'; ?></td>
									<td><?php echo (!empty($item['Orderitem']['hardware_replacement_for'])) ? h($item['Orderitem']['hardware_replacement_for']) : '--'; ?></td>
									<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
										<td><?php echo (!empty($item['Orderitem']['equipment_item_true_price'])) ? $this->Number->currency($item['Orderitem']['equipment_item_true_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?></td>
									<?php endif; ?>
									<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
										<td><?php echo (!empty($item['Orderitem']['equipment_item_rep_price'])) ? $this->Number->currency($item['Orderitem']['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?></td>
									<?php endif; ?>
									<td><?php echo $this->Number->currency($item['Orderitem']['item_tax'], 'USD', array('after' => false)); ?></td>
									<td><?php echo (!empty($item['ShippingTypeItem']['shipping_type_description'])) ? h($item['ShippingTypeItem']['shipping_type_description']) : '--'; ?></td>
									<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
										<td><?php echo (!empty($item['Orderitem']['item_ship_cost'])) ? $this->Number->currency($item['Orderitem']['item_ship_cost'], 'USD', array('after' => false)) : '--'; ?></td>
									<?php endif; ?>
									<?php $merchInfo = (!empty($item['Orderitem']['merchant_id'])) ? $item['Merchant']['merchant_dba'] . ' ' . $item['Merchant']['merchant_mid'] : '' ?>
									<td><?php echo (!empty($merchInfo)) ? $this->Html->link($merchInfo, array('controller' => 'Merchants', 'action' => 'view', $item['Orderitem']['merchant_id'])) : '--'; ?></td>
									<td><?php
										if ($this->Rbac->isPermitted('Orderitems/delete') && count($orderItems) > 1)
											echo $this->Form->postLink($this->Html->image("/img/redx.png", array("title" => "Delete Item", "class" => "icon")), array('controller' => 'Orderitems', 'action' => 'delete', $item['Orderitem']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete %s?', $item['Orderitem']['equipment_item_description'])));
										?></td>
								</tr>
								<?php if (!empty($item['OrderitemsReplacement']['id'])): ?>

									<tr <?php echo $rowBg; ?>>
										<td colspan="16">
											<div class="row">
												<div class="col-xs-6 col-md-2" >Shipping, Axia to Merchant:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['AxiaToMerchantShippingType'])) ? h($item['OrderitemsReplacement']['AxiaToMerchantShippingType']['shipping_type_description']) . ' ' . $this->Number->currency($item['OrderitemsReplacement']['shipping_axia_to_merchant_cost'], 'USD', array('after' => false)) : '&mdash;&mdash;'; ?></div>
												<div class="col-xs-6 col-md-2" >RA #:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['ra_num'])) ? h($item['OrderitemsReplacement']['ra_num']) : '&mdash;&mdash;'; ?></div>
											</div>
											<div class="row">
												<div class="col-xs-6 col-md-2" >Shipping, Merchant to Vendor:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['MerchantToVendorShippingType'])) ? h($item['OrderitemsReplacement']['MerchantToVendorShippingType']['shipping_type_description']) . ' ' . $this->Number->currency($item['OrderitemsReplacement']['shipping_merchant_to_vendor_cost'], 'USD', array('after' => false)) : '&mdash;&mdash;'; ?></div>
												<div class="col-xs-6 col-md-2" >Date shipped to vendor:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['date_shipped_to_vendor'])) ? date('M j, Y', strtotime($item['OrderitemsReplacement']['date_shipped_to_vendor'])) : '&mdash;&mdash;'; ?></div>
											</div>
											<div class="row">
												<div class="col-xs-6 col-md-2" >Shipping, Vendor to Axia:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['VendorToAxiaShippingType'])) ? h($item['OrderitemsReplacement']['VendorToAxiaShippingType']['shipping_type_description']) . ' ' . $this->Number->currency($item['OrderitemsReplacement']['shipping_vendor_to_axia_cost'], 'USD', array('after' => false)) : '&mdash;&mdash;'; ?></div>
												<div class="col-xs-6 col-md-2" >Date terminal arrived from vendor:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['date_arrived_from_vendor'])) ? date('M j, Y', strtotime($item['OrderitemsReplacement']['date_arrived_from_vendor'])) : '&mdash;&mdash;'; ?></div>
											</div>
											<div class="row">
												<div class="col-xs-6 col-md-2" >Amount billed to merchant:</div>
												<div class="col-xs-6 col-md-1 pull-left" > <?php echo (!empty($item['OrderitemsReplacement']['amount_billed_to_merchant'])) ? $this->Number->currency($item['OrderitemsReplacement']['amount_billed_to_merchant'], 'USD', array('after' => false)) : '&mdash;&mdash;'; ?></div>
												<div class="col-xs-6 col-md-2" >Tracking #:</div>
												<div class="col-xs-6 col-md-1 pull-left" ><?php echo (!empty($item['OrderitemsReplacement']['tracking_num'])) ? h($item['OrderitemsReplacement']['tracking_num']) : '&mdash;&mdash;'; ?></div>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							<?php endif; ?>

						<?php endforeach; ?>
					</table>
					<?php if ($hasItems === false): ?>
						<div class="text-center"><h5><span class="label label-info">No items have been ordered</span></h5></div>
					<?php endif; ?>
				</li>

				<?php if ($hasSupplies): ?>
					<li class="list-group-item">
						<div class="contrTitle">Supplies ordered:</div>
						<table class="table table-condensed table-striped table-hover">
							<tr>
								<th><?php echo $this->Paginator->sort('equipment_item_description', 'Item'); ?></th>
								<th><?php echo h('Date'); ?></th>
								<th><?php echo h('Commission Month'); ?></th>
								<th><?php echo h('Qty'); ?></th>
								<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
									<th><?php echo h('True Cost'); ?></th>
								<?php endif; ?>
								<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
									<th><?php echo h('Rep Cost'); ?></th>
								<?php endif; ?>
								<th><?php echo h('Tax'); ?></th>
								<th><?php echo h('Shipping Type'); ?></th>
								<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
									<th><?php echo h('Shipping Cost'); ?></th>
								<?php endif; ?>
									<th colspan="2"><?php echo h('Merchant'); ?></th>
							</tr>

							<?php
							foreach ($orderItems as $item):
								if ($item['EquipmentType']['equipment_type_description'] === 'Supplies'):
									?>
									<tr>
										<td><?php echo (!empty($item['Orderitem']['equipment_item_description'])) ? h($item['Orderitem']['equipment_item_description']) : '--'; ?></td>
										<td><?php echo (!empty($item['Orderitem']['item_date_ordered'])) ? date('M j, Y', strtotime($item['Orderitem']['item_date_ordered'])) : '--'; ?></td>
										<td><?php 
										$commMoYr = $item['Orderitem']['commission_month'] . "/" . $item['Orderitem']['commission_year'];
										echo (!empty($item['Orderitem']['commission_month'])) ? $this->AxiaTime->dateChangeFormat($commMoYr, 'm/Y', 'M Y') : '--'; ?></td>
										<td><?php echo (!empty($item['Orderitem']['quantity'])) ? h($item['Orderitem']['quantity']) : '--'; ?></td>
										<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/trueCost', true)): ?>
											<td><?php echo (!empty($item['Orderitem']['equipment_item_true_price'])) ? $this->Number->currency($item['Orderitem']['equipment_item_true_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?></td>
										<?php endif; ?>
										<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/repCost', true)): ?>
											<td><?php echo (!empty($item['Orderitem']['equipment_item_rep_price'])) ? $this->Number->currency($item['Orderitem']['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?></td>
										<?php endif; ?>
										<td><?php echo $this->Number->currency($item['Orderitem']['item_tax'], 'USD', array('after' => false)); ?></td>
										<td><?php echo (!empty($item['ShippingTypeItem']['shipping_type_description'])) ? h($item['ShippingTypeItem']['shipping_type_description']) : '--'; ?></td>
										<?php if ($this->Rbac->isPermitted('app/actions/Orders/view/module/shipCost', true)): ?>
											<td><?php echo (!empty($item['Orderitem']['item_ship_cost'])) ? $this->Number->currency($item['Orderitem']['item_ship_cost'], 'USD', array('after' => false)) : '--'; ?></td>
										<?php endif; ?>
										<?php $merchInfo = (!empty($item['Orderitem']['merchant_id'])) ? $item['Merchant']['merchant_dba'] . ' ' . $item['Merchant']['merchant_mid'] : '' ?>
										<td><?php echo (!empty($merchInfo)) ? $this->Html->link($merchInfo, array('controller' => 'Merchants', 'action' => 'view', $item['Orderitem']['merchant_id'])) : '--'; ?></td>
										<td><?php
											if ($this->Rbac->isPermitted('Orderitems/delete'))
												echo $this->Form->postLink($this->Html->image("/img/redx.png", array("title" => "Delete Item", "class" => "icon")), array('controller' => 'Orderitems', 'action' => 'delete', $item['Orderitem']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete %s?', $item['Orderitem']['equipment_item_description'])));
											?></td>
									</tr>
								<?php endif; ?>

							<?php endforeach; ?>
						</table>
					</li>
				<?php else: ?>
					<li class="list-group-item">
						<div class="text-center"><h5><span class="label label-info">No Supplies ordered</span></h5></div>
					</li>
				<?php endif; ?>

			</div><!-- END list-group div-->
		<?php else: ?>

			<div class="text-center"><h4><span class="label label-info">This equipment invoice has no items</span></h4></div>
					<?php endif; ?>
    </div>
</div>
<?php echo $this->AssetCompress->script('ordersCommon', array('raw' => (bool)Configure::read('debug'))); ?>