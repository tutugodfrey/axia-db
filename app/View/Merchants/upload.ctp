<?php
/* Drop breadcrumb */
$this->Html->addCrumb($this->name . ' ' . $this->action, '/' . $this->name . '/' . $this->action);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchants Data Upload'); ?>" />
<?php

if(!empty($uploadedMerchants)){
	echo '<div class="alert alert-success" role="alert">';
	echo '<h5><strong>Merchants Uploaded Successfully!:<strong></h5>';
	foreach($uploadedMerchants as $info){
		echo $this->Html->link($info['Merchant']['merchant_mid'] ." ". $info['Merchant']['merchant_dba'], array('controller'=>'merchants', 'action'=>'view', $info['Merchant']['id']), array('class'=> 'alert-link'));
		echo '<br />';
	}
	echo '</div>';
}
?>

<div class="reportTables">
	<?php
	echo $this->Form->create('Merchant', array(
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'wrapInput' => false,
			'class' => 'form-control'
		),
		'class' => 'well well-lg form-inline',
		'type' => 'file'
	));

	echo $this->form->input('Choose File', array('label' => array('text' => 'Select a CSV file:', 'class' => 'col-md-12'), 'class' => 'btn btn-default col-md-3',
		'type' => 'file', 'required' => true));
	echo '&nbsp;';
	echo $this->form->button($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-open')) . ' Upload', array(
		'type' => 'submit', 'class' => 'btn btn-sm btn-success'));
	echo ' <a href="/Documentations/help#newAcct" target="_blank" class="icon" style="vertical-align: sub; font-size:13pt"><span class="glyphicon glyphicon-info-sign"> </span></a>';
	echo $this->form->end();
	?>
</div>