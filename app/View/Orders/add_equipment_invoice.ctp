<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Equipment Invoice ');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h(" Santa Barbara Office | " . __('Equipment Invoice ')); ?>" />
<div >
	<?php
	echo $this->Form->create('Order', array('novalidate' => true, 'inputDefaults' => array('label' => false, 'div' => false, 'class' => false), 'class' => false, 'div' => false));
	echo $this->Form->hidden("Order.user_id", array('value' => $this->Session->read('Auth.User.id')));
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
						<tr><td>Commission Month/Year:</td><td>
							<?php 
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
						<tr><td class="tab formName">No Charge</td><td><?php echo $this->Form->checkbox('Order.commission_month_nocharge'); ?></td></tr>
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
				<table class="table table-condensed table-hover" id="orderItemsTable">
					<?php
					echo $this->element('Orders/editModeMainHeaders');
					$rowBg = "";
					for ($n = 0; $n < 4; $n++):
						/* Toggle background color to denote the items and any corresponding Replacement item data */
						$rowBg = ($rowBg === "class='reportTableRowGroup'") ? "" : "class='reportTableRowGroup'";
						?>
						<tr <?php echo $rowBg; ?>>
							<td><?php echo $this->Form->hidden("Orderitem.$n.equipment_type_id", array('value' => EquipmentType::HARDWARE_ID)); ?>
								<?php echo $this->Form->input("Orderitem.$n.orderitem_type_id", array("onChange" => "equipmentItemChanged(this, 'replacementRow" . $n . "', 'Orderitem" . $n . "ItemShipCost')", 'options' => $formOptns['OrderitemType'], 'empty' => '--')); ?></td>
							<td class="nowrap">
								<?php echo $this->Form->input("Orderitem.$n.item_date_ordered", array('empty' => '--'));
									if (empty($this->request->data['Orderitem'][$n]['item_date_ordered'])) {
										echo $this->Html->image("/img/clock.png", array(
											'data-toggle' => "tooltip",
											'data-placement' => "top",
											"title" => _("Set to today"),
											"class" => "pull-right",
											"url" => "javascript:void(0)",
											"onClick" => "setTimeStampNow('Orderitem" . $n . "ItemDateOrdered')"
										));
									}
								?>
							</td>
							<td><?php
								echo $this->Form->input("Orderitem.$n.equipment_item_id", array('style' => 'width:200px', "onChange" => "$('#Orderitem" . $n . "EquipmentItemDescription').val($('#Orderitem" . $n . "EquipmentItemId option:selected').text());", 'options' => $formOptns['EquipmentItem'], 'empty' => ''));
								echo $this->Form->hidden("Orderitem.$n.equipment_item_description");
								?>
							</td>
							<td><?php 
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
							<td>&nbsp;<!--Spacer--></td>
							<td>&nbsp;<!--Spacer--></td>
							<td>&nbsp;<!--Spacer--></td>
							<td><?php echo $this->Form->input("Orderitem.$n.shipping_type_item_id", array('options' => $formOptns['ShippingTypeItem'], 'empty' => '--'));
								echo $this->Form->input("Orderitem.$n.item_ship_cost", array('placeholder' => 'Shipping Cost', 'style' => 'max-width:90px'));
								?>
							</td>
							<td><?php echo $this->Form->input("Orderitem.$n.Merchant.merchant_mid"); ?></td>
						</tr>
						<tr id="replacementRow<?php echo $n ?>" style='display:none' <?php echo $rowBg; ?>>
							<td colspan="16">
								<?php echo $this->element('Orders/orderItemReplacementRows', array('n' => $n));?>
							</td>
						</tr>
					<?php
					endfor;
					?>
				</table>
            </li>
			<li class="list-group-item">
                <div class="contrTitle">Supplies ordered:</div>
				<table class="table table-condensed table-striped table-hover">
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
					//Resuming with counter from from previous iteration
					for ($n = $n + 1; $n < 9; $n++):
						?>
						<tr>
							<td>
								<?php echo $this->Form->hidden("Orderitem.$n.equipment_type_id", array('value' => EquipmentType::SUPPLIES_ID)); ?>
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
							<td><?php 
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
							<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_true_price", array('style' => 'width:65px')); ?></td>
							<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_rep_price", array('style' => 'width:65px')); ?></td>
							<td><?php echo $this->Form->input("Orderitem.$n.equipment_item_partner_price", array('style' => 'width:65px')); ?></td>
							<td><?php echo $this->Form->input("Orderitem.$n.shipping_type_item_id", array('options' => $formOptns['ShippingTypeItem'], 'empty' => '--'));
								echo $this->Form->input("Orderitem.$n.item_ship_cost", array('placeholder' => 'Shipping Cost', 'style' => 'max-width:90px'));
							?>
							</td>
							<td><?php echo $this->Form->input("Orderitem.$n.Merchant.merchant_mid"); ?></td>
						</tr>
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
