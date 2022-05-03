<div class="row">
	<div class="col-xs-6 col-md-1 nowrap" >Shipping, Axia to Merchant:</div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_axia_to_merchant_id", array(
	'options' => $formOptns['ShippingType'], 'empty' => '--')); ?></div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_axia_to_merchant_cost", array(
	'style' => 'width:65px', 'placeholder' => 'Cost')); ?></div>
	<div class="col-xs-6 col-md-2" >RA #:</div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.ra_num"); ?></div>
</div>
<div class="row">
	<div class="col-xs-6 col-md-1 nowrap" >Shipping, Merchant to Vendor:</div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_merchant_to_vendor_id", array(
	'options' => $formOptns['ShippingType'], 'empty' => '--')); ?></div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_merchant_to_vendor_cos", array(
	'style' => 'width:65px', 'placeholder' => 'Cost')); ?></div>
	<div class="col-xs-6 col-md-2" >Date shipped to vendor:</div>
	<div class="col-xs-6 col-md-1 pull-left nowrap" ><?php echo $this->Form->dateTime("Orderitem.$n.OrderitemsReplacement.date_shipped_to_vendor", 'MDY', null, array('empty' => '--')); ?></div>
</div>
<div class="row">
	<div class="col-xs-6 col-md-1 nowrap" >Shipping, Vendor to Axia:</div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_vendor_to_axia_id", array(
	'options' => $formOptns['ShippingType'], 'empty' => '--')); ?></div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.shipping_vendor_to_axia_cost", array(
	'style' => 'width:65px', 'placeholder' => 'Cost')); ?></div>
	<div class="col-xs-6 col-md-2" >Date terminal arrived from vendor:</div>
	<div class="col-xs-6 col-md-1 pull-left nowrap" ><?php echo $this->Form->dateTime("Orderitem.$n.OrderitemsReplacement.date_arrived_from_vendor", 'MDY', null, array(
	'empty' => '--')); ?></div>
</div>
<div class="row">
	<div class="col-xs-6 col-md-2" >Amount billed to merchant:</div>
	<div class="col-xs-6 col-md-1 pull-left" > <?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.amount_billed_to_merchant", array(
	'style' => 'width:65px', 'placeholder' => '$')); ?></div>
	<div class="col-xs-6 col-md-2" >Tracking #:</div>
	<div class="col-xs-6 col-md-1 pull-left" ><?php echo $this->Form->input("Orderitem.$n.OrderitemsReplacement.tracking_num"); ?></div>
</div>