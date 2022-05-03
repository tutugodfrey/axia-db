 <span class="contentModuleTitle"> <?php echo H($productName) . ' Archive: ' ?> </span>
<?php
		echo $this->Form->create('MerchantPricingArchive', array(
							'url' => array("controller" => "MerchantPricingArchives", "action" => 'edit'),
							'inputDefaults' => array(
												'div' => 'form-group',
												'label' => false,
												'wrapInput' => false,
												'class' => 'form-control'
							),
							'class' => 'form-inline'));
		echo $this->Form->hidden('merchant_id', array('value' => $merchantId));
		echo $this->Form->input('id', array(
							'multiple' => false,
							'required',
							'autofocus',
							'after' => ' ',
							'div' => false,
							'options' => $archiveMoYrs,
							'empty' => ' -- Select Month/Year -- '));
		echo $this->Form->hidden('re_archive', array('value' => 0));
		echo $this->Form->submit('Edit', array(
							'div' => 'form-group',
							'class' => 'btn btn-success btn-sm'));
		echo $this->Form->end();
?>