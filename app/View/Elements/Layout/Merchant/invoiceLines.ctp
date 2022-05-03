<?php 
/*
This element requires an index number to use as form input index that matches $this->request->data[InvoiceItem][<idx>][<fieldname>]
@var $idx a zero-based index or n-1 (where n is the count of items) variable must be passed to this element from the controller or view rendering it
@var $achReasons a list of MerchantAchReasons must be passed to the element view when rendered asynchronously
*/ ?>
<tr name='invItemRow'>
	<td class="text-center strong"><?php
		echo  $idx + 1;
		if (!empty($this->request->data("InvoiceItem.$idx.id"))) {
			echo $this->Form->hidden("InvoiceItem.$idx.id");
		}
		?>
	</td>
	<td class="text-center strong"><?php 
		echo $this->Form->input("InvoiceItem.$idx.merchant_ach_reason_id", [
			'onChange' => "setAvailableInputs();setDefaultNonTaxableReason($idx);",
			'style' => 'padding:2px',
			'div' => 'form-group',
			'wrapInput' => false,
			'label' => false,
			'empty' => 'Select description',
			'options' => $achReasons,
			"class" => "form-control input-sm"]);
		echo $this->Form->input("InvoiceItem.$idx.reason_other", ['style' => 'padding:2px', 'div' => 'form-group', 'wrapInput' => false, 'label' => false, 'placeholder' => "If 'Other', explain.", "class" => "form-control input-sm"]);
	?>
	</td>
	<td class="text-center strong"><?php echo $this->Form->input("InvoiceItem.$idx.commissionable", ['label' => false, 'after' => '<strong style="vertical-align: bottom;"> Yes</strong>']);?></td>
	<td class="text-center strong"><?php echo $this->Form->input("InvoiceItem.$idx.taxable", [
		'onClick' => "updateInvoice($idx)",
		'label' => false,
		'after' => '<strong style="vertical-align: bottom;"> Yes</strong>']
		);

		?></td>
	<td class="text-center strong"><?php echo $this->Form->input("InvoiceItem.$idx.non_taxable_reason_id", [
		'div' => 'form-group',
		'empty' => '--',
		'label' => false,
		'wrapInput' => false,
		'class' => 'form-control',
		'style' => 'padding:2px']);?>
	</td>
	<td class="text-center strong"><?php echo $this->Form->input("InvoiceItem.$idx.amount", [
		'onkeyup' => "updateInvoice($idx)",
		'style' => 'padding:2px',
		'class' => 'form-control',
		'wrapInput' => false,
		'label' => false,
		'div' => 'form-group input-group has-success',
		'before' => '<span class="input-group-addon strong" style="padding:2px 3px 2px 3px">$ </span>']); ?>
	</td>
	<td class="text-center strong nowrap"><?php
		echo $this->Html->tag('span', $this->Number->currency($this->request->data("InvoiceItem.$idx.tax_amount"), 'USD2dec'), array('id' => "Item".$idx."TaxAmnt"));
		echo $this->Form->hidden("InvoiceItem.$idx.tax_amount");
		 ?>
	</td>
</tr>
