 <?php
 	echo $this->Html->tag('div', null, array('class' => 'text-center form-group form-inline'));
 	echo $this->Form->hidden('merchant_id', array('value' => $merchantId, 'id' => 'currentMerchId'));
	echo $this->Form->month('date', array('empty' => false, 'class' => 'form-control',));
	echo $this->Form->year('date', 2001, (int)date('Y'), array('empty' => false, 'class' => 'form-control'));
	echo $this->Html->tag('/div'); //closing div tag

	if (!empty($archiveMetadata)) {
		echo $this->Html->tag('p', 'This menu includes all products assigned to this merchant and possible actions per product based on selected month/year. <mark>New pricing archives for current month/year cannot be created here. You may only re-build archives for current month/year if any exist.</mark>', array('class' => 'strong'));
		echo $this->Form->create('MerchantPricingArchive', array(
				'url' => array("controller" => "MerchantPricingArchives", "action" => 'createManyByMerchant'),
				'inputDefaults' => array(
					'div' => 'form-group',
					'label' => false,
					'wrapInput' => false,
					'class' => 'form-control'
				),
			'class' => 'form-inline'));
		echo $this->Form->hidden('merchant_id', array('value' => $merchantId));
		echo $this->Form->hidden('archive_month', array('value' => $this->request->data('date.month')));
		echo $this->Form->hidden('archive_year', array('value' => $this->request->data('date.year')));
		$curMoYr = $this->Time->format('M/Y', $this->request->data('date.year') . '-' . $this->request->data('date.month') . '-' . '01');
		$cells = array();
		foreach ($archiveMetadata as $idx => $data) {
			if (is_array($data)) {
				$productName = h($data['ProductsServicesType']['products_services_description']);
				$hasArchiveYesNo = (empty($data['MerchantPricingArchive']['id']))? '<i class="text-muted">NO</i>' : '<strong class="text-success">YES</strong>';
				$hasArchiveYesNo = array($hasArchiveYesNo, array('class' => 'text-center'));
				$actionItem = array('-- NONE --', array('class' => 'text-center'));
				if ($archiveMetadata['allow_create'] === true || !empty($data['MerchantPricingArchive']['id'])) {
					$actionItem[0] = $this->Form->toggleSwitch("$idx.create", array(
							'label_text' => (!empty($data['MerchantPricingArchive']['id']))? 'Rebuild Archive' : 'Create Archive',
							'label_position' => 'left'
						));
					$actionItem[1]['class'] = 'text-right';
				}
				$actionItem[0] .= $this->Form->hidden("$idx.id", ['value' => $data['MerchantPricingArchive']['id']]);
				$actionItem[0] .= $this->Form->hidden("$idx.products_services_type_id", ['value' => $data['ProductsServicesType']['id']]);

				$cells[] = array($productName, $hasArchiveYesNo, $actionItem);
			}
		}
		$headers = $this->Html->tableHeaders(array(array("Product Name" => array('class' => 'bg-primary text-center')), array("Has Archive $curMoYr?" => array('class' => 'bg-primary text-center')), array("Actions" => array('class' => 'bg-primary text-center'))));
		$tableCells = $this->Html->tableCells($cells);
		echo $this->Html->tag('div', null, array('class' => 'table-responsive'));
			echo $this->Html->tag('table', $headers . $tableCells, array('class' => 'table table-hover table-striped table-bordered'));

			echo $this->Html->tag('/div'); //close outtermost div

		echo $this->Form->submit('Submit', array(
							'div' => 'form-group pull-right',
							'id' => 'archiveFrmSubmitBtn',
							'class' => 'btn btn-success btn-sm pull-right'));
		
		echo $this->Form->end();
	} else {
		echo $this->Html->tag('div', '<span class="glyphicon glyphicon-alert pull-left" style="font-size:14pt;margin: 0px 10px 0px 0px"></span>No action is possible with selected dates. Select a current or past month/year.', array('class' => 'alert alert-warning'));
	}
		
?>
<script>
$('#dateMonth, #dateYear').on('change', function() {
	var month = $('#dateMonth').val();
	var year = $('#dateYear').val();
	var mId = $('#currentMerchId').val();
	renderContentAJAX('', '', '', 'archiveEditMenuContainer', '/MerchantPricingArchives/createManyMenu/' + mId + '/' + month + '/' + year);
});

var tgglEnableSubmit = function () {
	if ($("[id$='Create']:checkbox:checked").length) {
		$('#archiveFrmSubmitBtn').prop('disabled', false);
	} else {
		$('#archiveFrmSubmitBtn').prop('disabled', true);
	}
}
tgglEnableSubmit();
$("[id$='Create']:checkbox").on('click', tgglEnableSubmit);
//Newer Internet Explorer Browser do not support CSS transitions2D which are used used by the slider input
if (navigator.userAgent.indexOf("Trident") > -1 || navigator.userAgent.indexOf("WOW64") > -1) {
	$("[name='sliderControlObj']").removeClass('slider');
}
</script>