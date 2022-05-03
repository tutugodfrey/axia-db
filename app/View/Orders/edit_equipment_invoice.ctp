<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Equipment Invoice ' . h($this->request->data['Order']['invoice_number']), '/' . $this->name . '/' . $this->action . '/' . $this->request->data['Order']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h(" Santa Barbara Office | " . __('Equipment Invoice ' . $this->request->data['Order']['invoice_number'])); ?>" />
<div >
	<?php
	echo $this->Form->create('Order', array('novalidate' => true, 'inputDefaults' => array('hiddenField' => false, 'label' => false, 'div' => false, 'class' => false), 'class' => false, 'div' => false));
	echo $this->Form->hidden("Order.id");
	echo $this->Form->hidden("Order.user_id");
	?>
    <div class="table-responsive">
        <table>
            <tr>
                <td class="twoColumnGridCell">
                    <table style="width: auto" cellspacing="0" cellpadding="0" border="0">
						<tr><td>Vendor:</td><td><?php echo $this->Form->input('Order.vendor_id', array('options' => $formOptns['Vendor'], 'empty' => '--')); ?></td></tr>
						<tr><td>Vendor Invoice #:</td><td><?php echo $this->Form->input('Order.invoice_number'); ?></td></tr>
						<tr><td>Order #:</td><td><?php echo $this->Form->input('Order.display_id', array('type' => 'number')); ?></td></tr>
						<tr><td>Order Date:</td><td class="nowrap"><?php echo $this->Form->input('Order.date_ordered', array('default' => date('Y-m-d'), 'empty' => '--')); ?></td></tr>
						<tr><td>Date Paid:</td><td class="nowrap"><?php echo $this->Form->input('Order.date_paid', array('empty' => '--'));
							if (empty($this->request->data('Order.date_paid'))) {
										echo $this->Html->image("/img/clock.png", array(
											'data-toggle' => "tooltip",
											'data-placement' => "top",
											"title" => _("Set to current date"),
											"class" => "pull-right",
											"url" => "javascript:void(0)",
											"onClick" => "setTimeStampNow('OrderDatePaid')"
										));
									}
						?></td></tr>
						<tr><td>Commission Month/Year:</td><td><?php 
							echo $this->Form->input('Order.commission_month', array('wrapInput' => false, 'options' => $formOptns['CommissionMonths'], 'empty' => '--'));
							echo $this->Form->input('Order.commission_year', array('wrapInput' => false, 'options' => $formOptns['CommissionYears'], 'empty' => '--'));
							if (empty($this->request->data("OrderCommissionMonth")) && empty($this->request->data("OrderCommissionYear"))) {
								echo $this->Html->image("/img/clock.png", array(
									'data-toggle' => "tooltip",
									'data-placement' => "top",
									"title" => __("Set to current Mo/Yr"),
									"url" => "javascript:void(0)",
									"onClick" => "setMonthAndYear('OrderCommissionMonth','OrderCommissionYear')"
								));
							}
						?></td></tr>
						<tr><td class="tab formName">No Charge</td><td><?php echo $this->Form->checkbox('Order.commission_month_nocharge', array('hiddenField' => false)); ?></td></tr>
						<tr><td>Tax:</td><td><?php echo $this->Form->input('Order.tax'); ?></td></tr>
						<tr><td>Shipping $:</td><td><?php echo $this->Form->input('Order.shipping_cost'); ?></td></tr>
						<tr><td class="tab formName">Add Tax to Items</td><td><?php echo $this->Form->checkbox('Order.add_item_tax'); ?></td></tr>
                    </table>
                </td>
                <td class="twoColumnGridCell">
                    <table style="width: auto" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>Order Status:</td>
							<td class="nowrap"><?php echo $this->Order->statusInput('status'); ?></td>
						</tr>
						<tr><td>Shipping Type:</td><td><?php echo $this->Form->input('Order.shipping_type_id', array('options' => $formOptns['ShippingType'], 'empty' => '--')); ?></td></tr>
						<tr><td>Ship To:</td><td><?php echo $this->Form->input('Order.ship_to'); ?></td></tr>
						<tr><td>Return Tracking #:</td><td><?php echo $this->Form->input('Order.tracking_number'); ?></td></tr>
                    </table>
                    <div style="width: 8cm" class="panel panel-warning">
                        <div class="panel-heading"><strong>Note: </strong></div>
                        <div class="panel-body"><?php echo $this->Form->textarea('Order.notes', array('style' => 'border:0px;;margin:-10px;width:7.7cm;max-width:7.7cm', 'rows' => '10')); ?></div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="list-group">
			<li class="list-group-item">
				<div class="contrTitle">Items ordered:</div>
				<table class="table table-condensed table-hover">
					<?php
					echo $this->element('Orders/editModeMainHeaders');
					$rowBg = "";
					$item = $this->request->data['Orderitem'];
					for ($n = 0; $n < $itemCount; $n++):
						/* Toggle background color to denote the items and any corresponding Replacement item data */
						$rowBg = ($rowBg === "class='reportTableRowGroup'") ? "" : "class='reportTableRowGroup'";
						$itemType = (!empty($item[$n]['equipment_type_id'])) ? $item[$n]['equipment_type_id'] : 'AdditionalLineItem';
						if ($itemType === EquipmentType::HARDWARE_ID || $itemType === 'AdditionalLineItem'):
							?>
							<tr <?php echo $rowBg; ?>>
								<td><?php echo $this->Form->hidden("Orderitem.$n.equipment_type_id", array('value' => EquipmentType::HARDWARE_ID)); ?>
									<?php echo $this->Form->hidden("Orderitem.$n.id"); ?>
									<?php
									if (!empty($this->request->data['Orderitem'][$n]['order_id'])) {
										echo $this->Form->hidden("Orderitem.$n.order_id");
									}
									?>
									<?php echo $this->Form->input("Orderitem.$n.orderitem_type_id", array("onChange" => "equipmentItemChanged(this, 'replacementRow" . $n . "', 'Orderitem" . $n . "ItemShipCost')", 'options' => $formOptns['OrderitemType'], 'empty' => '--')); ?>
								</td>
								<td class="nowrap"><?php echo $this->Form->input("Orderitem.$n.item_date_ordered", array('empty' => '--'));
								if (empty($this->request->data['Orderitem'][$n]['item_date_ordered'])) {
									echo $this->Html->image("/img/clock.png", array(
										'data-toggle' => "tooltip",
										'data-placement' => "top",
										"title" => __("Set to today"),
										"class" => "pull-right",
										"url" => "javascript:void(0)",
										"onClick" => "setTimeStampNow('Orderitem" . $n . "ItemDateOrdered')"
									));
								}
								?>
								</td>
								<td><?php
									if (!empty($item[$n]['equipment_item_id'])) {
										echo h($item[$n]['equipment_item_description']);
										echo $this->Form->hidden("Orderitem.$n.equipment_item_id");
									} else {
										echo $this->Form->input("Orderitem.$n.equipment_item_id", array('style' => 'width:150px', "onChange" => "$('#Orderitem" . $n . "EquipmentItemDescription').val($('#Orderitem" . $n . "EquipmentItemId option:selected').text());", 'options' => $formOptns['EquipmentItem'], 'empty' => ''));
									}
									echo $this->Form->hidden("Orderitem.$n.equipment_item_description");
									?>
								</td>
								<td class="nowrap"><?php 
								echo $this->Form->input("Orderitem.$n.commission_month", array('wrapInput' => false, 'options' => $formOptns['CommissionMonths'], 'empty' => '--'));
								echo $this->Form->input("Orderitem.$n.commission_year", array('wrapInput' => false, 'options' => $formOptns['CommissionYears'], 'empty' => '--')); 
								if (empty($this->request->data("Orderitem.$n.commission_month")) && empty($this->request->data("Orderitem.$n.commission_month"))) {
									echo $this->Html->image("/img/clock.png", array(
										'data-toggle' => "tooltip",
										'data-placement' => "top",
										"title" => __("Set to current Mo/Yr"),
										"url" => "javascript:void(0)",
										"onClick" => "setMonthAndYear('Orderitem" . $n . "CommissionMonth','Orderitem" . $n . "CommissionYear')"
									));
								}
								?></td>
								<td><?php echo $this->Form->input("Orderitem.$n.warranty_id", array('options' => $formOptns['Warranty'], 'empty' => '--')); ?></td>
								<td><?php echo $this->Form->imput("Orderitem.$n.quantity", array('style' => 'width:30px')); ?></td>
								<td><?php echo $this->Form->imput("Orderitem.$n.hardware_sn"); ?></td>
								<td><?php echo $this->Form->imput("Orderitem.$n.hardware_replacement_for"); ?></td>
								<?php if (!empty($item[$n]['id'])) { ?>
										<?php if ($this->request->data['Order']['status'] === GUIbuilderComponent::STATUS_PAID) { ?>
											<td>
												<?php 
													echo $this->Form->hidden("Orderitem.$n.equipment_item_true_price");
													echo (!empty($item[$n]['equipment_item_true_price'])) ? $this->Number->currency($item[$n]['equipment_item_true_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
											</td>
											<td>
												<?php 
													echo $this->Form->hidden("Orderitem.$n.equipment_item_rep_price"); 
													echo (!empty($item[$n]['equipment_item_rep_price'])) ? $this->Number->currency($item[$n]['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
											</td>
											<td>
												<?php 
													echo $this->Form->hidden("Orderitem.$n.equipment_item_partner_price");
													echo (!empty($item[$n]['equipment_item_partner_price'])) ? $this->Number->currency($item[$n]['equipment_item_partner_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
											</td>
										<?php } else { ?>
											<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_true_price", array('style' => 'width:65px')); ?></td>
											<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_rep_price", array('style' => 'width:65px')); ?></td>
											<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_partner_price", array('style' => 'width:65px')); ?></td>
										<?php } ?>
								<?php } else { ?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								<?php } ?>
								<td><?php
									echo $this->Form->hidden("Orderitem.$n.item_tax");
									echo $this->Form->input("Orderitem.$n.shipping_type_item_id", array('options' => $formOptns['ShippingTypeItem'], 'style' => 'max-width:95px', 'empty' => 'Shipping Type'));
									echo $this->Form->input("Orderitem.$n.item_ship_cost", array('placeholder' => 'Shipping Cost', 'style' => 'max-width:90px'));
									?></td>
								<td><?php
									if (!empty($item[$n]['merchant_id'])) {
										echo $this->Form->hidden("Orderitem.$n.merchant_id");
									}
									echo $this->Form->input("Orderitem.$n.Merchant.merchant_mid");
									//Display validation errors on the hidden field
									echo $this->Form->error("Orderitem.$n.merchant_id");
									?>
								</td>
							</tr>
							<?php 
							if (isset($item[$n]['OrderitemsReplacement']) && !empty(Hash::filter(Hash::extract($item, "$n.OrderitemsReplacement")))) {
								$displayReplacement = "style='display:display'";
							} else {
								$displayReplacement = "style='display:none'";
							}
							?>
							<tr id="replacementRow<?php echo $n ?>" <?php echo $displayReplacement . $rowBg; ?>>
								<td colspan="16">
								<?php
								echo $this->Form->hidden("Orderitem.$n.OrderitemsReplacement.id");
								//pass current index to element
								echo $this->element('Orders/orderItemReplacementRows', array('n' => $n));
								?>
								</td>
							</tr>
						<?php
						endif;
						?>
					<?php
					endfor;
					?>
				</table>
            </li>
			<li class="list-group-item">
                <div class="contrTitle">Supplies ordered:</div>
				<table class="table table-condensed table-hover">
					<tr>
						<th><?php echo 'Item'; ?></th>
						<th><?php echo 'Date'; ?></th>
						<th><?php echo 'Commission Month/Year'; ?></th>
						<th><?php echo 'Qty'; ?></th>
						<th><?php echo 'True Cost'; ?></th>
						<th><?php echo 'Rep Cost'; ?></th>
						<th><?php echo 'Partner Cost'; ?></th>
						<th><?php echo 'Shipping Details'; ?></th>
						<th><?php echo 'Merchant MID'; ?></th>
					</tr>
					<?php
					$offsetIsSet = false;
					for ($n = 0; $n <= $itemCount; $n++):
						$itemType = (!empty($item[$n]['equipment_type_id'])) ? $item[$n]['equipment_type_id'] : 'AdditionalLineItem';
						if ($itemType === EquipmentType::SUPPLIES_ID || $itemType === 'AdditionalLineItem'):
							if ($itemType === 'AdditionalLineItem' && $offsetIsSet === false) {
								$n = $itemCount;
								$itemCount = $supplyOffset;
								$offsetIsSet = true;
							}
							?>
							<tr>
								<td><?php echo $this->Form->hidden("Orderitem.$n.equipment_type_id", array('value' => EquipmentType::SUPPLIES_ID)); ?>
									<?php echo $this->Form->hidden("Orderitem.$n.id"); ?>
									<?php
									if (!empty($this->request->data['Orderitem'][$n]['order_id'])) {
										echo $this->Form->hidden("Orderitem.$n.order_id");
									}
									?>
									<?php echo $this->Form->input("Orderitem.$n.equipment_item_description"); ?>
								</td>
								<td nowrap>
									<?php
									echo $this->Form->input("Orderitem.$n.item_date_ordered", array('wrapInput' => 'col col-md-8', 'empty' => '--'));
									if (empty($this->request->data['Orderitem'][$n]['item_date_ordered'])) {
										echo $this->Html->image("/img/clock.png", array(
											'data-toggle' => "tooltip",
											'data-placement' => "top",
											"title" => __("Set to today"),
											"url" => "javascript:void(0)",
											"onClick" => "setTimeStampNow('Orderitem" . $n . "ItemDateOrdered')"
										));
									}
									?>
								</td>
								<td class="nowrap"><?php 
								echo $this->Form->input("Orderitem.$n.commission_month", array('wrapInput' => false, 'options' => $formOptns['CommissionMonths'], 'empty' => '--'));
								echo $this->Form->input("Orderitem.$n.commission_year", array('wrapInput' => false, 'options' => $formOptns['CommissionYears'], 'empty' => '--'));
								if (empty($this->request->data("Orderitem.$n.commission_month")) && empty($this->request->data("Orderitem.$n.commission_month"))) {
									echo $this->Html->image("/img/clock.png", array(
										'data-toggle' => "tooltip",
										'data-placement' => "top",
										"title" => __("Set to current Mo/Yr"),
										"url" => "javascript:void(0)",
										"onClick" => "setMonthAndYear('Orderitem" . $n . "CommissionMonth','Orderitem" . $n . "CommissionYear')"
									));
								}
								?></td>
								<td><?php echo $this->Form->imput("Orderitem.$n.quantity", array('style' => 'width:30px')); ?></td>
									<?php if ($this->request->data['Order']['status'] === GUIbuilderComponent::STATUS_PAID) { ?>
										<td>
											<?php 
												echo $this->Form->hidden("Orderitem.$n.equipment_item_true_price"); 
												echo (!empty($item[$n]['equipment_item_true_price'])) ? $this->Number->currency($item[$n]['equipment_item_true_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
										</td>
										<td><?php 
												echo $this->Form->hidden("Orderitem.$n.equipment_item_rep_price");
												echo (!empty($item[$n]['equipment_item_rep_price'])) ? $this->Number->currency($item[$n]['equipment_item_rep_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
										</td>
										<td><?php 
												echo $this->Form->hidden("Orderitem.$n.equipment_item_partner_price");
												echo (!empty($item[$n]['equipment_item_partner_price'])) ? $this->Number->currency($item[$n]['equipment_item_partner_price'], 'USD', array('after' => false, 'negative' => '-')) : '--'; ?>
										</td>
									<?php } else { ?>
										<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_true_price", array('style' => 'width:65px')); ?></td>
										<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_rep_price", array('style' => 'width:65px')); ?></td>
										<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_partner_price", array('style' => 'width:65px')); ?></td>
									<?php } ?>
								<td><?php
								echo $this->Form->hidden("Orderitem.$n.item_tax");
								echo $this->Form->input("Orderitem.$n.shipping_type_item_id", array('div' => false, 'options' => $formOptns['ShippingTypeItem'], 'empty' => '---Shipping Type---'));
								echo $this->Form->input("Orderitem.$n.item_ship_cost", array('div' => false, 'placeholder' => 'Shipping Cost', 'style' => 'max-width:90px'));
									?>
								</td>

								<td><?php
									if (!empty($item[$n]['merchant_id'])) {
										echo $this->Form->hidden("Orderitem.$n.merchant_id");
									}
									echo $this->Form->input("Orderitem.$n.Merchant.merchant_mid");
									//Display validation errors on the hidden field
									echo $this->Form->error("Orderitem.$n.merchant_id");
									?>
								</td>
							</tr>
						<?php
						endif;
						?>
					<?php
					endfor;
					?>
                </table>
            </li>
        </div><!-- END list-group div-->
    </div>
	<div class="btn-group col-md-offset-1">
<?php
echo $this->Form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-floppy-disk')) . ' Save', array('name' => 'Save', 'type' => 'submit', 'div' => false, 'class' => 'btn btn-success'));
echo $this->Form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-list-alt')) . ' Save and add Add More Items', array('name' => 'saveAndAddMore', 'type' => 'submit', 'div' => false, 'class' => 'btn btn-info'));
?>
	</div>
</div>
<?php
echo $this->AssetCompress->script('ordersCommon', array('raw' => (bool)Configure::read('debug')));
