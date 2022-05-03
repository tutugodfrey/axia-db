<div>                          
    <span class="contentModuleTitle">Travel & Entertainment Merchant Numbers</span>
	<?php
	if (empty($merchant['MerchantUwVolume']['id']) && !empty($this->request->data('MerchantUw.MerchantUwVolume.id'))) {
		$merchant['MerchantUwVolume'] = $this->request->data('MerchantUw.MerchantUwVolume');
	}
	$enableEdit = ($this->name === 'MerchantUws'); //Editing is only allowed when this element is rendered within the underwriting view	
	$merchId = $merchant['Merchant']['id'];
	if ($this->Rbac->isPermitted('MerchantUws/edit') && $enableEdit) {
		$ajaxUrl = '/Users/checkDecrypPassword/' . $merchId . '/MerchantUwVolumes/ajaxAddAndEdit';
		echo $this->Html->image("/img/editPencil.gif", array("title" => "Edit", "class" => "icon", "data-toggle" => "modal", "data-target" => "#myModal", 'url' => 'javascript:void(0)', 'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $ajaxUrl . "')"));
	}
	?>
	<?php $hasTEData = false; ?>
    <table style="width: auto" cellpadding="0" cellspacing="0" border="0">
        <tr>
			<?php if (!empty($merchant['MerchantUwVolume']['te_amex_number_disp'])): ?>            
				<td>American Express<br />                                        
					<?php
					echo str_repeat("x", 8) . h($merchant['MerchantUwVolume']['te_amex_number_disp']) . '&nbsp;&nbsp;';
					$hasTEData = true;
					if ($this->Rbac->isPermitted('MerchantUws/edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchId . '/MerchantUwVolumes/ajaxDisplayDecryptedVal/te_amex_number';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
					?>

				</td>
			<?php endif; ?>
			<?php if (!empty($merchant['MerchantUwVolume']['te_diners_club_number_disp'])): ?>
				<td>Diners<br />
					<?php
					echo str_repeat("x", 8) . h($merchant['MerchantUwVolume']['te_diners_club_number_disp']) . '&nbsp;&nbsp;';
					$hasTEData = true;
					if ($this->Rbac->isPermitted('MerchantUws/edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchId . '/MerchantUwVolumes/ajaxDisplayDecryptedVal/te_diners_club_number';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
					?>                    
				</td>
			<?php endif; ?>
			<?php if (!empty($merchant['MerchantUwVolume']['te_discover_number_disp'])): ?>
				<td>Discover<br />
					<?php
					echo str_repeat("x", 8) . h($merchant['MerchantUwVolume']['te_discover_number_disp']) . '&nbsp;&nbsp;';
					$hasTEData = true;
					if ($this->Rbac->isPermitted('MerchantUws/edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchId . '/MerchantUwVolumes/ajaxDisplayDecryptedVal/te_discover_number';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
					?> 
				</td>
			<?php endif; ?>
			<?php if (!empty($merchant['MerchantUwVolume']['te_jcb_number_disp'])): ?>
				<td>JCB<br />
					<?php
					echo str_repeat("x", 8) . h($merchant['MerchantUwVolume']['te_jcb_number_disp']) . '&nbsp;&nbsp;';
					$hasTEData = true;
					if ($this->Rbac->isPermitted('MerchantUws/edit')) {
						$ajaxUrl = '/Users/checkDecrypPassword/' . $merchId . '/MerchantUwVolumes/ajaxDisplayDecryptedVal/te_jcb_number';
						echo $this->Html->modalDecryptIcon($ajaxUrl);
					}
					?> 
				</td>
			<?php endif; ?>
			<?php if ($hasTEData === false): ?>
				<td>
					<span class="list-group-item text-center text-muted">- Merchant does not have any T&E numbers. -</span> 
				</td>
			<?php endif; ?>
        </tr>
    </table>
</div>