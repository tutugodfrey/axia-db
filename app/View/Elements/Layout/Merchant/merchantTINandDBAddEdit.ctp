<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
	<tr>            
		<td><strong>Federal Tax ID</strong><br />                                        
			<?php echo $this->form->hidden('Merchant.id'); ?>                                    
			<?php 
			echo $this->Form->input("Merchant.merchant_tin", array(
					'type' => 'password',
					'value' => $this->request->data("Merchant.merchant_tin"),
					'placeholder' => 'Tax ID #',
					'wrapInput' => 'col-md-12',
					'label' => false,
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 'onClick' => "toggleShowPwField('MerchantMerchantTin')")) .'</span>',
					'afterInput' => '</div>'
			));



			?>                                    
		</td>
		<td><strong>D&B</strong><br />
			<?php 
				echo $this->Form->input("Merchant.merchant_d_and_b", array(
						'type' => 'password',
						'value' => $this->request->data("Merchant.merchant_d_and_b"),
						'placeholder' => 'D&B #',
						'wrapInput' => 'col-md-12',
						'label' => false,
						'beforeInput' => '<div class="input-group"><span class="input-group-addon">'. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 'onClick' => "toggleShowPwField('MerchantMerchantDAndB')")) .'</span>',
						'afterInput' => '</div>'
				));
			?>
		</td>                           
	</tr>
</table>