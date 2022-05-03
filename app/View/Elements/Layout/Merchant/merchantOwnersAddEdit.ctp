<table style="width: auto" cellpadding="0" cellspacing="0" border="0">

	<?php for ($x = 0; $x < count($this->request->data['MerchantOwner']); $x++) : ?>    
		<tr>                  
			<td><span class="contentModuleTitle"> Owner/Partner/Officer <?php echo $x + 1; ?>:</span><br />                                        
				<?php echo $this->form->hidden("MerchantOwner.$x.id"); ?>  
				<?php echo $this->form->hidden("MerchantOwner.$x.merchant_id"); ?>  
				<?php
				echo $this->Form->input("MerchantOwner.$x.owner_social_sec_no", array(
						'type' => 'password',
						'value' => $this->request->data("MerchantOwner.$x.owner_social_sec_no"),
						'placeholder' => 'Social Security #',
						'label' => array(
							'text' => h($this->request->data['MerchantOwner'][$x]['owner_name']),
						),
						'beforeInput' => '<div class="input-group"><span class="input-group-addon">SSN '. $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', array('escape' => false, "title" => "Show/hide", 
													'onClick' => "toggleShowPwField('MerchantOwner".$x."OwnerSocialSecNo')")) .'</span>',
						'afterInput' => '</div>'
				));

				?>
			</td>
		</tr> 
	<?php endfor; ?>
</table>