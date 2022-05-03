<div class="leftNavSection roundEdges ">       
    <table>
        <tr>
            <th class="contrTitle roundEdges">Merchant Pages</th> 
		</tr>                      
		<tr>
            <td id="merchNavContents">

                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Overview'), array('controller' => 'merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Notes'), array('controller' => 'merchants', 'action' => 'notes', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Installation & Timeline'), array('controller' => 'merchants', 'action' => 'timeline', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Programming'), array('controller' => 'merchants', 'action' => 'equipment', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Products & Services'), array('controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Business Information'), array('controller' => 'merchants', 'action' => 'business_info', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Assigned Equipment'), array('controller' => 'merchants', 'action' => 'assigned_equipment', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Axia Invoices'), array('controller' => 'merchants', 'action' => 'assigned_equipment', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('PCI DSS Compliance'), array('controller' => 'merchants', 'action' => 'pci', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Underwriting'), array('controller' => 'merchants', 'action' => 'underwriting', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Rejects'), array('controller' => 'merchants', 'action' => 'rejects', $merchant['Merchant']['id'])); ?>
                </div>
                <div class="MerchLeftNavItem">
					<?php echo $this->Html->link(__('Cancellation'), array('controller' => 'MerchantCancellations', 'action' => 'view', $merchant['Merchant']['id'])); ?>
                </div>
            </td>
		</tr>
    </table>
</div>