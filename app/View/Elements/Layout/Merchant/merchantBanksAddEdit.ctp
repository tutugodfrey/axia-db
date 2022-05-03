<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>                
			<?php 
			echo $this->form->hidden("MerchantBank.id"); ?>  
			<?php echo $this->form->hidden("MerchantBank.merchant_id"); ?>  
			<?php
			echo $this->Form->input("MerchantBank.bank_routing_number", array(
					'type' => 'password',
					'value' => $this->request->data("MerchantBank.bank_routing_number"),
					'placeholder' => 'Routing #',
					'label' => array(
						'text' => 'Depository Transit Routing Number',
					),
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
												'onClick' => "toggleShowPwField('MerchantBankBankRoutingNumber')")) .'</span>',
					'afterInput' => '</div>'
			));

			?>
		</td>
	</tr>     
	<tr>
		<td>
			<?php
			echo $this->Form->input("MerchantBank.bank_dda_number", array(
					'type' => 'password',
					'value' => $this->request->data("MerchantBank.bank_dda_number"),
					'placeholder' => 'Account #',
					'label' => array(
						'text' => 'Depository DDA Number',
					),
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
												'onClick' => "toggleShowPwField('MerchantBankBankDdaNumber')")) .'</span>',
					'afterInput' => '</div>'
			));
			?>
		</td>
	</tr>     
	<tr>
		<td>
			<?php
			echo $this->Form->input("MerchantBank.fees_routing_number", array(
					'type' => 'password',
					'value' => $this->request->data("MerchantBank.fees_routing_number"),
					'placeholder' => 'Routing #',
					'label' => array(
						'text' => 'Fees Transit Routing Number',
					),
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
												'onClick' => "toggleShowPwField('MerchantBankFeesRoutingNumber')")) .'</span>',
					'afterInput' => '</div>'
			));
			?>
		</td>
	</tr>     
	<tr>
		<td><?php
			echo $this->Form->input("MerchantBank.fees_dda_number", array(
					'type' => 'password',
					'value' => $this->request->data("MerchantBank.fees_dda_number"),
					'placeholder' => 'Account #',
					'label' => array(
						'text' => 'Fees DDA Number',
					),
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
												'onClick' => "toggleShowPwField('MerchantBankFeesDdaNumber')")) .'</span>',
					'afterInput' => '</div>'
			));
			?>
		</td>
	</tr>     
</table>