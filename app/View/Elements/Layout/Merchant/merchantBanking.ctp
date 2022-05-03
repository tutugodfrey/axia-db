<table>
	<?php if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) : ?>
		<tr>
			<td colspan="2" class="text-right">
				<strong> Update Accounts <span class="glyphicon glyphicon-arrow-right"></span> <?php 
				$ajaxEditUrl = '/Users/checkDecrypPassword/' . $merchantBank['MerchantBank']['id'] . '/MerchantBanks/ajaxAddAndEdit';
				echo $this->Html->editImage(array("data-toggle" => "modal", "data-target" => "#myModal", 'url' => 'javascript:void(0)', 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxEditUrl . "')"));?></strong>
			</td>
		</tr>
				
		<?php endif; ?>
	
		<tr>
		<td>
			<strong>Depository Transit Routing Number:</strong>
		</td>
		<td class='pull-right'>
			<?php
			if (!empty($merchantBank['MerchantBank']['bank_routing_number_disp'])) {
				echo 'xxxx' . h($merchantBank['MerchantBank']['bank_routing_number_disp']) . '&nbsp;';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchantBank['MerchantBank']['id'] . '/MerchantBanks/ajaxDisplayDecryptedVal/bank_routing_number';
					echo $this->Html->modalDecryptIcon($ajaxUrl);
				}
			}

			?>                             
		</td>
	</tr>
	<tr>
		<td>
			<strong>Depository DDA Number:</strong>
		</td>
		<td class='pull-right'>
			<?php
			if (!empty($merchantBank['MerchantBank']['bank_dda_number_disp'])) {
				echo'xxxx' . h($merchantBank['MerchantBank']['bank_dda_number_disp']) . '&nbsp';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchantBank['MerchantBank']['id'] . '/MerchantBanks/ajaxDisplayDecryptedVal/bank_dda_number';
					echo $this->Html->modalDecryptIcon($ajaxUrl);					
				}
			}

			?>
		</td>
	</tr>
	<tr>
		<td>
			<strong>Fees Transit Routing Number:</strong>
		</td>
		<td class='pull-right'>
			<?php
			if (!empty($merchantBank['MerchantBank']['fees_routing_number_disp'])) {
				echo 'xxxx' . h($merchantBank['MerchantBank']['fees_routing_number_disp']) . '&nbsp';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchantBank['MerchantBank']['id'] . '/MerchantBanks/ajaxDisplayDecryptedVal/fees_routing_number';
					echo $this->Html->modalDecryptIcon($ajaxUrl);				
				}
			}

			?>
		</td>
	</tr>
	<tr>
		<td>
			<strong>Fees DDA Number:</strong>
		</td>
		<td class='pull-right'>
			<?php
			if (!empty($merchantBank['MerchantBank']['fees_dda_number_disp'])) {
				echo 'xxxx' . h($merchantBank['MerchantBank']['fees_dda_number_disp']) . '&nbsp';
				if ($this->Rbac->isPermitted('app/actions/Addresses/business_info_edit') && $this->Rbac->isPermitted('app/actions/Addresses/view/module/ssnAndBankEdit', true)) {
					$ajaxUrl = '/Users/checkDecrypPassword/' . $merchantBank['MerchantBank']['id'] . '/MerchantBanks/ajaxDisplayDecryptedVal/fees_dda_number';
					echo $this->Html->modalDecryptIcon($ajaxUrl);
				}
			}
			?>
		</td>
	</tr>
</table>