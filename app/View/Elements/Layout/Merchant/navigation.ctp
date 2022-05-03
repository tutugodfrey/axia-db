<nav id="merchNavContents" class="navbar navbar-default navbar-fixed-side list-group panel panel-primary">
	<!-- normal collapsible navbar markup -->
	<li class="list-group-item panel-heading"><strong>Merchant Pages</strong></li>
	<?php if ($this->Rbac->isPermitted('Merchants/view')): ?>
			<?php echo $this->Html->link(__('Overview'), array('controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsView")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('Merchants/notes')): ?>
			<?php echo $this->Html->link(__('Notes'), array('controller' => 'merchants', 'action' => 'notes', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsNotes")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('TimelineEntries/timeline')): ?>
			<?php echo $this->Html->link(__('Installation & Timeline'), array('controller' => 'TimelineEntries', 'action' => 'timeline', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsTimeline")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('Merchants/equipment')): ?>
			<?php echo $this->Html->link(__('Programming'), array('controller' => 'Merchants', 'action' => 'equipment', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsEquipment")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('MerchantPricings/products_and_services')): ?>
			<?php echo $this->Html->link(__('Products & Services'), array('controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantPricingsProducts")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('Addresses/business_info')): ?>
			<?php echo $this->Html->link(__('Business Information'), array('controller' => 'Addresses', 'action' => 'business_info', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "AddressesBusinessInfo")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('Orders/merchant_equipment_assigned')): ?>
			<?php echo $this->Html->link(__('Assigned Equipment'), array('controller' => 'orders', 'action' => 'merchant_equipment_assigned', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "OrdersMerchantEquipmentAssigned")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('MerchantAches/view')): ?>
			<?php echo $this->Html->link(__('Axia Invoices'), array('controller' => 'MerchantAches', 'action' => 'view', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantAchesView")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('Merchants/pci')): ?>
			<?php echo $this->Html->link(__('PCI DSS Compliance'), array('controller' => 'Merchants', 'action' => 'pci', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsPci")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('MerchantUws/view')): ?>
			<?php echo $this->Html->link(__('Underwriting'), array('controller' => 'MerchantUws', 'action' => 'view', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantUwsView")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('MerchantRejects/index')): ?>
			<?php echo $this->Html->link(__('Rejects'), array('controller' => 'MerchantRejects', 'action' => 'index', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantsRejects")); ?>
	<?php endif; ?>
	<?php if ($this->Rbac->isPermitted('MerchantCancellations/view')): ?>
			<?php echo $this->Html->link(__('Cancellation'), array('controller' => 'MerchantCancellations', 'action' => 'view', $merchant['Merchant']['id']), array("class" => "list-group-item", "id" => "MerchantCancellationsView")); ?>
	<?php endif; ?>
</nav>

<?php echo $this->AssetCompress->script('merchantNav', array('raw' => (bool)Configure::read('debug'))); ?>

