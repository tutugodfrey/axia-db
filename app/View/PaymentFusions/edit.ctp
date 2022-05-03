
<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Products and Services', '/MerchantPricings/products_and_services/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Payment Fusion');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Products & Services') . " | " . __($this->name)); ?>" />

<?php
echo $this->Form->create('PaymentFusion', array(
	'inputDefaults' => array(
			'label' => false,
			'wrapInput' => 'col col-sm-12 col-md-12 col-lg-12',
			'div' => false,
		)
	));
$decInputOptions = array(
	'type' => 'number',
	"step" => ".001",
);
$naturalInputOptions = array(
	'type' => 'number',
	"step" => "1",
);
?>
<?php
if(!empty($this->request->data('PaymentFusion.id'))) {
	echo $this->Form->hidden('id');
}
echo $this->Form->hidden('merchant_id');
?>
<div class="row">
		<?php //************************************Merchant Pricing & Settings *************************************    ?>
		<span  class="col col-md-6 contentModuleTitle">Payment Fusion</span>
</div>
<div class="row panel">
	<div class='col col-sm-12 col-md-12 col-lg-12'>
		<div class="row">
			<div class='col col-sm-12 col-md-4 col-lg-4 text-center'>
			<?php echo $this->Form->input('is_hw_as_srvc', array('label' => array('text'=> '<strong>HaaS </strong>&nbsp;' . $this->Html->image('/img/icon_greenflag.gif', ['style' => 'vertical-align:baseline']), 'class' => 'strong'),'class' => false, 'div' => false, 'wrapInput' => 'btn btn-xs btn-info strong'));?>
			</div>
		</div>
	</div>
	<div class='col col-sm-12 col-md-2 col-lg-2'>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Payment Fusion ID:</div>
			<?php echo $this->Form->input('generic_product_mid');?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Feature(s):</div>
			<?php echo $this->Form->input('ProductFeature'); ?>
		</div>
		
				<div class="panel panel-default">
					<div class="panel-heading contentModuleTitle">Other Features:</div>
					<?php echo $this->Form->textarea('other_features', array('class' => 'panel-body col col-md-12 col-sm-12 roundEdges', 'rows' => '5', 'style' => "resize: none;")); ?>
				</div>
		
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Account Fee:</div>
			<?php echo $this->Form->input('account_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Rate:</div>
			<?php echo $this->Form->input('rate', $decInputOptions);?>
		</div>
	</div>
	<div class='col col-sm-12 col-md-2 col-lg-2'>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Per Item:</div>
			<?php echo $this->Form->input('per_item_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'># of Devices - Standard:</div>
			<?php echo $this->Form->input('standard_num_devices', $naturalInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Standard Device Fee:</div>
			<?php echo $this->Form->input('standard_device_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'># of Devices - VP2PE:</div>
			<?php echo $this->Form->input('vp2pe_num_devices', $naturalInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>VP2PE Device Fee:</div>
			<?php echo $this->Form->input('vp2pe_device_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'># of Devices - PFCC:</div>
			<?php echo $this->Form->input('pfcc_num_devices', $naturalInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>PFCC Device Fee:</div>
			<?php echo $this->Form->input('pfcc_device_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'># of Devices - VP2PE + PFCC:</div>
			<?php echo $this->Form->input('vp2pe_pfcc_num_devices', $naturalInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>VP2PE + PFCC Device Fee:</div>
			<?php echo $this->Form->input('vp2pe_pfcc_device_fee', $decInputOptions); ?>
		</div>
		<div class="row">
			<div class='col col-sm-12 col-md-12 col-lg-12 strong'>Total Monthly Fee:</div>
			<?php 
				$toolTip = ["readonly", "data-toggle" => "tooltip", "data-placement" => "right", "data-original-title" => "(Read only) Calculates automatically."];
				echo $this->Form->input('monthly_total', array_merge($decInputOptions, $toolTip)); ?>
		</div>
	</div>
</div>
<table>
	<tr>
		<td class="threeColumnGridCell" colspan="3">
			<?php
			if ($isEditLog) {
				echo $this->Form->hidden('MerchantNote.0.id');
			}
			echo $this->element('Layout/Merchant/merchantNoteForChanges');
			?>
		</td>
	</tr>
</table>
<?php 
echo $this->element('Layout/Merchant/mNotesDefaultBttns');
echo $this->Form->end(); 
echo $this->AssetCompress->script('merchantPandSNav', array('raw' => (bool)Configure::read('debug')));
?>

