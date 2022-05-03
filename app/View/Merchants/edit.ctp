<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($this->request->data[Inflector::singularize($this->name)]['merchant_dba']), '/' . $this->name . '/' . '/view/' . '/' . $this->request->data[Inflector::singularize($this->name)]['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($this->request->data['Merchant']['merchant_dba'] . " / " . $this->request->data['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Overview')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<span class="contentModuleTitle">Edit Account Information</span>
<?php
echo $this->Form->create('Address', array(
	'novalidate' => true,
	'inputDefaults' => array(
				'div' => 'form-group',
				'label' => array(
					'class' => 'col col-md-4 control-label'
				),
				'wrapInput' => 'col-md-8 small',
				'class' => 'form-control input-sm'
			),
	'class' => 'form-horizontal'
));
?>

<div class="col-md-4">
	<?php 
	echo $this->Form->input('Merchant.merchant_mid', array('label' => 'MID'));
	$midTypeSettings = array('label' => 'MID Type', 'empty' => 'Select merchant MID type:');
	if (empty($this->request->data('Merchant.merchant_type_id')) && strlen($this->request->data('Merchant.merchant_mid')) == 16) {
		$midTypeSettings['default'] = $acquiringTypeId;
	}
	echo $this->Form->input('Merchant.merchant_type_id', $midTypeSettings);
	echo $this->Form->hidden('Merchant.id'); 
	echo $this->Form->input('Merchant.merchant_dba', array('label' => 'DBA')); 
	echo $this->Form->hidden('AddressBus.id'); 
	echo $this->Form->hidden('AddressBus.merchant_id'); 
	echo $this->Form->input('AddressBus.address_street', array('label' => 'Address')); 
	echo $this->Form->input('AddressBus.address_city', array('label' => 'City')); 
	echo $this->Form->input('AddressBus.address_phone', array('label' => 'Business Phone')); 
	echo $this->Form->input('AddressBus.address_fax', array('label' => 'Business Fax')); 
	echo $this->Form->input('Merchant.merchant_email', array('label' => 'Email')); 
	echo $this->Form->input('Merchant.chargebacks_email', array('label' => 'Email for chargeback notice:')); 
	echo $this->Form->input('Merchant.merchant_url', array('label' => 'Web site')); 
	echo $this->Form->input('Merchant.merchant_ownership_type', array('label' => 'Ownership Type')); 
	echo $this->Form->input('Merchant.merchant_contact', array('label' => 'Contact')); 
	echo $this->Form->input('Merchant.merchant_contact_position', array('label' => 'Contact Position'));
	echo $this->Form->input('Merchant.client_id_autocomplete', array(
		'value' => (!(empty($this->request->data('Merchant.client_id'))))? $this->request->data('Client.client_id_global') . " - " . $this->request->data('Client.client_name_global') : null,
		'label' => 'Client ID',
		'placeholder' => '(Type an ID or Name)',
		'before' => "<div id='clAutoCompNote' class='text-primary text-right strong small'>Select Client from the list shown as you type, manual entry not allowed.</div>",
		'after' => "<div id='clAutoCompErr' class='text-danger text-right strong small'></div>"
	));
	
	echo $this->Form->hidden('Merchant.client_id');
	
	echo $this->element('Forms/OrganizationDrilldown');
	echo $this->Form->input('Merchant.reporting_user', array('label' => array('text' => 'Reporting User ID')));
	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->html->tag('label', __('Acquiring Client: '), array("class" => "col-md-4 control-label"));
		echo $this->Form->input('Merchant.is_acquiring_only', array('label' => false, 'wrapInput' => false, 'class' => false, 'style' => 'margin-left:11px')); 
	echo $this->Html->tag('/div', null);//close div tag
	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->html->tag('label', __('Payment Fusion Client: '), array("class" => "col-md-4 control-label"));
		echo $this->Form->input('Merchant.is_pf_only', array('label' => false, 'wrapInput' => false, 'class' => false, 'style' => 'margin-left:11px')); 
	echo $this->Html->tag('/div', null);//close div tag
	

	if (!empty($nonWomplyUsers)) {
		echo $this->Html->tag('div', null, array('class' => 'form-group'));
			echo $this->html->tag('label', __('Womply is Disabled for: '), array("class" => "col-md-4 control-label"));
			echo $this->html->tag('div', implode(',', $nonWomplyUsers), array("class" => "col-md-8"));
		echo $this->Html->tag('/div', null);//close div tag
	} else {
		echo $this->Form->input('Merchant.womply_status_id', array('label' => array('text' => 'Womply Call Status', 'class' => 'col col-md-4 nowrap'), 'empty' => '--')); 
	 	echo $this->Form->input('Merchant.womply_merchant_enabled', array('label' => false, 'wrapInput' => false, 'before' => '<label for="MerchantWomplyMerchantEnabled" class="form-inline col col-md-4 nowrap">Send to Womply &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 'after'=> '</label>')); 
	}
	?>
</div>
<div class="col-md-4">
	<?php
	$relAcqMidFieldSettings = array('label' => 'Related Acquiring MID');
	if (strlen($this->request->data('Merchant.merchant_mid')) == 16) {
		$relAcqMidFieldSettings['readonly'] = 'readonly';
		$relAcqMidFieldSettings['disabled'] = 'disabled';
	}
	echo $this->Form->input('Merchant.related_acquiring_mid', $relAcqMidFieldSettings); 
	echo $this->Form->input('Merchant.user_id', array('label' => 'Sales Rep', 'options' => $ddOptns['User'], 'empty' => '--')); 
	echo $this->Form->input('Merchant.sm_user_id', array('label' => 'Mgr 1', 'options' => $ddOptns['Sm'], 'empty' => '--')); 
	echo $this->Form->input('Merchant.sm2_user_id', array('label' => 'Mgr 2', 'options' => $ddOptns['Sm2'], 'empty' => '--')); 
	$timeType = array();
	$approvedDate = (!empty($this->request->data('UwStatusMerchantXref.0.datetime')))? $this->AxiaTime->date($this->request->data('UwStatusMerchantXref.0.datetime')) : '&nbsp;&nbsp&nbsp;&nbsp;-- / -- / ----';
	foreach ($this->request->data['TimelineEntry'] as $key => $timeEntries) {
		($timeEntries['timeline_item_id'] === TimelineItem::SUBMITTED) ? $timeType['SUB'] = $key : '';
		($timeEntries['timeline_item_id'] === TimelineItem::GO_LIVE_DATE) ? $timeType['INS'] = $key : '';
		($timeEntries['timeline_item_id'] === TimelineItem::AGREEMENT_ENDS) ? $timeType['AGREEMENT_ENDS'] = $key : '';
	}
	if (!is_null(Hash::get($timeType, 'SUB'))) {
		echo $this->Form->hidden("TimelineEntry." . $timeType['SUB'] . ".id");
		echo $this->Form->hidden("TimelineEntry." . $timeType['SUB'] . ".merchant_id");
	} else {
		$timeType['SUB'] = 4;//set static offset for this input group
	}

	if (!is_null(Hash::get($timeType, 'INS'))) {
		echo $this->Form->hidden("TimelineEntry." . $timeType['INS'] . ".id");
		echo $this->Form->hidden("TimelineEntry." . $timeType['INS'] . ".merchant_id");
	} else {
		$timeType['INS'] = 6;//set static offset for this input group
	}

	if (!is_null(Hash::get($timeType, 'AGREEMENT_ENDS'))) {
		echo $this->Form->hidden("TimelineEntry." . $timeType['AGREEMENT_ENDS'] . ".id");
		echo $this->Form->hidden("TimelineEntry." . $timeType['AGREEMENT_ENDS'] . ".merchant_id");
	} else {
		$timeType['AGREEMENT_ENDS'] = 8;//set static offset for this input group
	}

	echo $this->Form->input("TimelineEntry." . $timeType['SUB'] . ".timeline_date_completed", array(
		'class' => "roundEdges",
		'style' => "padding:3px;",
		'wrapInput' => 'col-md-8 nowrap',
		'label' => array("text" => 'Submitted Date:'),
		'empty' => '--'
	));
	echo $this->Form->hidden("TimelineEntry." . $timeType['SUB'] . ".timeline_item_id", array('value' => TimelineItem::SUBMITTED));

	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->Form->label('ApprovalTeLabel', __('Approval Date:'), array('class' => 'col col-md-4 control-label'));
		echo $this->Html->tag('div', "<h6>" . $approvedDate . "</h6>", array('class' => 'col col-md-8', "data-toggle" =>"tooltip", "data-placement" => "left", "data-original-title" => "Update this from underwriting"));
	echo $this->Html->tag('/div', null);//close div tag

	echo $this->Form->input("TimelineEntry." . $timeType['INS'] . ".timeline_date_completed", array(
		'class' => "roundEdges",
		'style' => "padding:3px;",
		'wrapInput' => 'col-md-8 nowrap',
		'label' => array("text" => 'Go-Live Date:'),
		'empty' => '--'
		));
	echo $this->Form->hidden("TimelineEntry." . $timeType['INS'] . ".timeline_item_id", array('value' => TimelineItem::GO_LIVE_DATE));
	$aggEndAttributes = array(
		'class' => "roundEdges",
		'style' => "padding:3px;",
		'wrapInput' => 'col-md-8 nowrap',
		'label' => array("text" => 'Agreement Ends:'),
		'empty' => '--',
		);
	if (!empty($this->request->data('UwStatusMerchantXref.0.datetime')) && empty($this->request->data("TimelineEntry." . $timeType['AGREEMENT_ENDS'] . ".timeline_date_completed"))) {
		$aggEndAttributes['value'] = $this->Time->format($this->request->data('UwStatusMerchantXref.0.datetime') . "+3 year", '%Y-%m-%d');
		$aggEndAttributes['after'] = '<span class="small text-muted bg-warning center-block text-center">(Notice: This date has been automatically set based on approval date +3 years)</span>';
	}
	echo $this->Form->input("TimelineEntry." . $timeType['AGREEMENT_ENDS'] . ".timeline_date_completed", $aggEndAttributes);
	echo $this->Form->hidden("TimelineEntry." . $timeType['AGREEMENT_ENDS'] . ".timeline_item_id", array('value' => TimelineItem::AGREEMENT_ENDS));
	echo $this->Form->input('CardType.CardType', array('type' => 'select', 'multiple' => 'checkbox', 'class' => 'checkbox', 'wrapInput' => 'col-md-8 col-md-offset-5 col-sm-offset-1'));
	echo $this->Form->input('Merchant.network_id', array('empty' => __('--')));
	echo $this->Form->input('Merchant.back_end_network_id', array('empty' => __('--')));
	echo $this->Form->input('Merchant.merchant_acquirer_id', array('label' => 'ISO/Acquirer', 'empty' => __('--')));
	echo $this->Form->input('Merchant.merchant_bin_id', array('label' => 'Bin', 'empty' => '--'));
	echo $this->Form->input('Merchant.original_acquirer_id', array('label' => 'Originally Signed Through', 'empty' => '--'));
	echo $this->Form->input('Merchant.bet_network_id', array('label' => 'Bet Network', 'empty' => '--'));
	echo $this->Form->input('Merchant.cancellation_fee_id', array('empty' => '--'));
	echo $this->Form->input('Merchant.partner_id', array('empty' => '--'));
	echo $this->Form->input('Merchant.partner_exclude_volume', array(
		'class' => false,
		'wrapInput' => 'col-md-8',
		'label' => array('text' => 'Exclude volume from non referral total', 'class' => 'col col-md-12 col-md-offset-5')));
	echo $this->Form->input('Merchant.entity_id', array('label' => 'Company', 'empty' => '--'));
	echo $this->Form->input('Merchant.brand_id', array('empty' => '--'));
	?>
</div>
<div class="col-md-4">
	<?php
	echo $this->Form->hidden('MerchantUw.id');
	echo $this->Form->hidden('MerchantUw.merchant_id');
	echo $this->Form->input('MerchantUw.mcc', array('label' => 'MCC'));
	echo $this->Form->input('Merchant.general_practice_type');
	echo $this->Form->input('Merchant.specific_practice_type');
	echo $this->Form->input('Merchant.merchant_bustype', array('label' => 'Business Type'));
	echo $this->Form->input('Merchant.merchant_ps_sold', array('label' => array('text' => 'Products/Services sold'), 'rows'=>2));
	echo $this->Form->input('Merchant.merchant_buslevel', array('label' => 'Business Level'));

	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->Form->label('RateStructureLabel', __('V/MC/DS Pricing Structure:'), array('class' => 'col col-md-5 control-label'));
		$betTableName = !empty($this->request->data['MerchantPricing']['visa_bet_table_id']) ? Hash::get($betRateStructures, $this->request->data['MerchantPricing']['visa_bet_table_id']) : h("--");
		echo $this->Html->tag('div', "<h6>" . h($betTableName) . "</h6>", array('class' => 'col col-md-7'));
	echo $this->Html->tag('/div', null);//close div tag

	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->Form->label('RateStructureLabel', __('Amex Pricing Structure:'), array('class' => 'col col-md-4 control-label'));
		$betTableName = !empty($this->request->data['MerchantPricing']['amex_bet_table_id']) ? Hash::get($betRateStructures, $this->request->data['MerchantPricing']['amex_bet_table_id']) : h("--");
		echo $this->Html->tag('div', "<h6>" . h($betTableName) . "</h6>", array('class' => 'col col-md-8'));
	echo $this->Html->tag('/div', null);//close div tag

	echo $this->Html->tag('div', null, array('class' => 'form-group'));
		echo $this->Form->label('ProcessingRateLabel', __('Processing Rate:'), array('class' => 'col col-md-4 control-label'));
		$procRate = !empty($this->request->data['MerchantPricing']['processing_rate']) ? $this->Number->toPercentage($this->request->data['MerchantPricing']['processing_rate']) : h("--");
		echo $this->Html->tag('div', "<h6>" . h($procRate) . "</h6>", array('class' => 'col col-md-8'));
	echo $this->Html->tag('/div', null);//close div tag
	
	echo $this->Html->tag('div', null, array('class' => 'form-group'));
	echo $this->Form->label('PerItemFeeLabel', __('Per Item Fee:'), array('class' => 'col col-md-4 control-label'));
	$perTiemFee = !empty($this->request->data['MerchantPricing']['mc_vi_auth']) ? $this->Number->currency($this->request->data['MerchantPricing']['mc_vi_auth'], 'USD3dec', array('after' => false, 'negative' => '-')) : h("--");
		echo $this->Html->tag('div', "<h6>" . h($perTiemFee) . "</h6>", array('class' => 'col col-md-8'));
	echo $this->Html->tag('/div', null);//close div tag
	echo $this->Form->input('Merchant.source_of_sale', array('label' => 'Reseller/Direct:', 'options' => ['Reseller' => 'Reseller','Direct' => 'Direct'], 'empty' => '--'));
	?>
	<?php 
	$refResPTypeOptions = array(
		'' => 'BET',
		'percentage' => 'Profit Percentage: Calculate Only',
		'points' => 'Basis Points Subtracted from Gross Profit',
		'percentage-grossprofit' => 'Profit Percentage Subtracted from Gross Profit',
		'points-calculateonly' => 'Basis Points: Calculate Only'
	);
	$attributes = array('legend' => false, 'default' => '', 'label' => array('class' => 'col-md-offset-1'));
	echo $this->Html->tag('ul', null, array( "class" => "list-group col-md-11", "style" => "margin-left:50px"));
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.referer_id', array('label' => array('text' => 'Referrer', 'class' => 'col col-md-3 control-label text-info'), 'empty' => '--')) 
				,array( "class" => "list-group-item list-group-item-info")
			);
		echo $this->Html->tag('li', 
				$this->Html->tag('div', 
				$this->Form->radio('Merchant.ref_p_type', $refResPTypeOptions, $attributes), array('class' => 'col-md-offset-1 col-sm-offset-1 col-xs-offset-1')),
				array( "class" => "list-group-item ")
			);
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.ref_p_value', array("step" => "0.001", "wrapInput" => "col-md-8 small input-group", 'label' => 'Value', 'afterInput' => '<div class="input-group-addon">%</div>')),
				array( "class" => "list-group-item")
			);
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.ref_p_pct', array("wrapInput" => "col-md-8 small input-group", 'label' => 'Percent of GP', 'afterInput' => '<div class="input-group-addon">%</div>')),
				array( "class" => "list-group-item ")
			);
		//Reseller data fields
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.reseller_id', array('label' => array('text' => 'Reseller', 'class' => 'col col-md-3 control-label text-info'), 'empty' => '--')) 
				,array( "class" => "list-group-item list-group-item-info")
			);
		echo $this->Html->tag('li', 
				$this->Html->tag('div',
				$this->Form->radio('Merchant.res_p_type', $refResPTypeOptions, $attributes), array('class' => 'col-md-offset-1 col-sm-offset-1 col-xs-offset-1')),
				array( "class" => "list-group-item ")
			);
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.res_p_value', array("wrapInput" => "col-md-8 small input-group", 'label' => 'Value', 'afterInput' => '<div class="input-group-addon">%</div>')),
				array( "class" => "list-group-item ")
			);
		echo $this->Html->tag('li', 
				$this->Form->input('Merchant.res_p_pct', array("wrapInput" => "col-md-8 small input-group", 'label' => 'Percent of GP', 'afterInput' => '<div class="input-group-addon">%</div>')),
				array( "class" => "list-group-item ")
			);
	echo $this->Html->tag('/ul');
	echo $this->Form->input('Merchant.group_id', array('empty' => '--')); 
	?>
</div>


<div class="col-md-12" colspan="3">
	<?php
	if ($isEditLog) {
		echo $this->Form->hidden('MerchantNote.0.id');
	}
	echo $this->element('Layout/Merchant/merchantNoteForChanges');
	?>
</div>
<?php 
echo $this->element('Layout/Merchant/mNotesDefaultBttns');
echo $this->Form->end();
echo $this->AssetCompress->script('merchants', array('raw' => (bool)Configure::read('debug')));
?>
<script type='text/javascript'>
activateNav('MerchantsView');
$(function() {
		$( "#MerchantClientIdAutocomplete" ).autocomplete({
			source: "/Clients/searchSimilar",
			minLength: 3,
			response: function( event, ui ) {
				if (ui.content.length == 0) {
					$('#clAutoCompErr').html('Client not found! Contact admin to add this client if needed.');
					$('#MerchantClientIdAutocomplete').addClass('error');
				} else {
					$('#MerchantClientIdAutocomplete').removeClass('error');
				}
			},
			change: function( event, ui ) {
				if ($('#MerchantClientIdAutocomplete').val().length <= 9) {
					$('#MerchantClientId').val('');
					if ($('#MerchantClientIdAutocomplete').val().length == 0) {
						$('#MerchantClientIdAutocomplete').removeClass('error');
						$('#clAutoCompNote').removeClass('text-danger');
						$('#clAutoCompErr').html('');
					} else {
						$('#MerchantClientIdAutocomplete').focus();
						$('#MerchantClientIdAutocomplete').addClass('error');
						$('#clAutoCompNote').addClass('text-danger');
					}
				}
			},
			select: function( event, ui ) {
				$('#MerchantClientId').val(ui.item.id);
				$('#MerchantClientIdAutocomplete').removeClass('error');
				$('#clAutoCompNote').removeClass('text-danger');
				$('#clAutoCompErr').html('');
			}
		});
} );
</script> 