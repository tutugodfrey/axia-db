<link rel="stylesheet" type="text/css" href="/dataTables/css/jquery.dataTables.min.css"/ media='all'>
 
<script type="text/javascript" src="/dataTables/js/jquery.dataTables.min.js"></script>
<?php
$achDateDefault = array();
$acctgMonth = - null;
$acctgYear = - null;
if (!empty($this->request->data('MerchantAch.id'))) {
	$reqUrl = array('action' => $this->action, $this->request->data('MerchantAch.id'));
} else {
	$reqUrl = array('action' => $this->action, $merchant['Merchant']['id']);
	$achDateDefault = array('default' => date('Y-m-d'));
	$acctgMonth = array('default' => date('m'));
	$acctgYear = array('default' => date('Y'));
}
echo $this->Form->create('MerchantAch', array('url' => $reqUrl, 
	'inputDefaults' => array(
				'div' => 'form-group',
				'label' => false,
				'wrapInput' => false,
				'class' => 'form-control',
				'style' => 'padding:2px'
			),
	'class' => 'form-inline input-sm'
	)); 

echo $this->Form->hidden('merchant_id', array('value' => $merchant['Merchant']['id']));
echo $this->Form->hidden('MerchantUw.id');
echo $this->Form->hidden('MerchantUw.merchant_id', array('value' => $merchant['Merchant']['id']));
echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User.id')));
if (!empty($this->request->data['MerchantAch']['invoice_number'])) {
	echo $this->Form->hidden('invoice_number', array('value' => $this->request->data['MerchantAch']['invoice_number']));
}

?>

<div name="InvoiceContanerMain">
		<div name="InvoiceContanersSub" class="col-md-12 col-sm-12">
			<div class="panel panel-default shadow">
				<blockquote class="panel-primary strong">
					<span class="text-primary">Axia Invoice</span>
					<?php 
						if (!empty($this->request->data('MerchantAch.id'))) {
							echo '<small class="pull-right"><span class="visible-print-inline-block">' . h($merchant['Merchant']['merchant_dba']) . " - " . h($merchant['Merchant']['merchant_mid']) . '</span> Invoice #: ' . h($this->request->data('MerchantAch.invoice_number')) . '</small>';
						} else {
							echo '<small class="pull-right"><span class="visible-print-inline-block">' . h($merchant['Merchant']['merchant_dba']) . " - " . h($merchant['Merchant']['merchant_mid']) . '</span> New Invoice #: ' . h($this->request->data('MerchantAch.invoice_number')) . '</small>';
						}
					?>
					<hr class="panel-primary">
				</blockquote>
				<div class="panel-body">
					<div class="col-md-3 col-sm-6">
						<table class="table table-bordered table-extra-condensed">
							<tr>
								<th class="label-info text-center">
									Related External Invoice #:
								</th>
								<td data-toggle="tooltip" data-placement="top" data-original-title="The number of a related invoice that exists in salesforce or other external system">
									<?php 
										echo $this->Form->input('related_foreign_inv_number', array('placeholder' => 'Inv # From External System'));
									?>
								</td>
							</tr>
							<tr>
								<th class="label-info text-center text-nowrap">
									Date Created
								</th>
								<td>
									<?php
										echo $this->Form->input('ach_date', array_merge(array('orderYear' => 'asc', 'empty' => '--'), $achDateDefault));
									?>
								</td>
							</tr>
							<tr>
								<th class="label-info text-center text-nowrap">
									Date Completed
								</th>
								<td>
									<?php
										echo $this->Form->input('date_completed', array('orderYear' => 'asc', 'empty' => '--'));
									?>
								</td>
							</tr>
							<tr>
								<th class="label-info text-center text-nowrap">
									Accounting Mo/Yr
								</th>
								<td>
									<?php 
										echo $this->Form->input('acctg_month', array('after' => '-','options' => $moOptns, 'default' => $acctgMonth, 'empty' => 'Month', 'wrapInput' => false)); 
										echo $this->Form->input('acctg_year', array('options' => $yrOptns, 'default' => $acctgYear, 'empty' => 'Year', 'wrapInput' => false)); 
									?>
								</td>
							</tr>
							<tr>
								<th class="label-info text-center text-nowrap">
									Bill To
								</th>
								<td >
									<?php 
										$defaultBillOptnId = null;
										if (empty($this->request->data('MerchantAch.id'))) {
											foreach ($billingOptns as $id => $val) {
												if ($val === $defaultBillOptnStr) {
													$defaultBillOptnId = $id;
												}
											}
										}
										echo $this->Form->input('merchant_ach_billing_option_id', array('options' => $billingOptns, 'default' => $defaultBillOptnId));
									?>
								</td>
							</tr>
							<tr>
								<th class="label-info text-center text-nowrap">
									Ship To
								</th>
								<td>
									<?php 
										echo $this->Form->input('MerchantUw.same_as_bus_addss', array('wrapInput' => false, 'type' => 'checkbox', 'label' => false, 'after' => '<span style="vertical-align: bottom;">Set as business address</span>'));
										echo $this->Form->input('ship_to_street', array('style' => 'padding:2px;width:200px', 'placeholder' => 'Street Address', 'data-default-option' => $merchBusAddss['Address']['address_street']));
										echo $this->Form->input('ship_to_city', array('placeholder' => 'City', 'data-default-option' => $merchBusAddss['Address']['address_city']));
										echo $this->Form->input('ship_to_state', array('placeholder' => 'State (format CA, OR)', 'data-default-option' => $merchBusAddss['Address']['address_state']));
										echo $this->Form->input('ship_to_zip', array('placeholder' => 'Zip', 'data-default-option' => $merchBusAddss['Address']['address_zip']));
									?>
								</td>
							</tr>
						</table>
					</div>
					<div class="clearfix"></div>
					<span class="hidden-print">
						<?php
						echo $this->Html->link('<span class="glyphicon glyphicon-plus"></span> Item', 'javascript:void(0)', [
							'class' => 'btn-sm btn-success',
							'escape' => false,
							'onClick' => "insertNewItem()"
						]);
						?>
						<span class="pull-right text-info">
							<span class="glyphicon glyphicon-info-sign"></span> Tip: Enter credits/refunds as negative amounts.
						</span>
					</span>
					<table id="itemsTable" class='table-bordered table-extra-condensed' style="width:100%">
						<thead>
							<tr>
								<th class="label-info text-center text-nowrap">Item #</th>
								<th class="label-info text-center text-nowrap">Description</th>
								<th class="label-info text-center text-nowrap">Commissionable?</th>
								<th class="label-info text-center text-nowrap">Taxable?</th>
								<th class="label-info text-center text-nowrap">Non-Taxable Reason</th>
								<th class="label-info text-center text-nowrap">Amount</th>
								<th class="label-info text-center text-nowrap">Tax</th>
							</tr>
						</thead>
							<?php
							if (!empty($this->request->data('InvoiceItem'))) {
								foreach ($this->request->data['InvoiceItem'] as $idx => $itemData) {
									echo $this->Element('Layout/Merchant/invoiceLines', ['idx' => $idx]);
								} 
							} else {
									echo $this->Element('Layout/Merchant/invoiceLines',['idx' => 0]);
								}

							?>
					</table>
					<br/>
					<table id="staticItemsTable" class='table-bordered table-extra-condensed dataTable'>
						<tr>
							<td class="panel-footer"></td><td class="strong">Exact Shipping Amount</td><td class="panel-footer"></td><td class="panel-footer"></td><td class="panel-footer"></td>
							<td class="text-center"><?php echo $this->Form->input("exact_shipping_amount", [
							'div' => 'form-group input-group has-success', 'before' => '<span class="input-group-addon strong" style="padding:2px 3px 2px 3px">$ </span>']); ?>
							</td>
							<td class="panel-footer"></td>
						</tr>
						<tr>
							<td class="panel-footer"></td><td class="strong">General Shipping Amount</td><td class="panel-footer"></td><td class="panel-footer"></td><td class="panel-footer"></td>
							<td class="text-center"><?php echo $this->Form->input("general_shipping_amount", [
							'div' => 'form-group input-group has-success', 'before' => '<span class="input-group-addon strong" style="padding:2px 3px 2px 3px">$ </span>']); ?></td>
							<td class="panel-footer"></td>
						</tr>
						<tr>
							<td class="panel-footer"></td><td class="strong">Total Non Taxable Amount</td><td class="panel-footer"></td><td class="panel-footer"></td><td class="panel-footer"></td>
							<td class="text-center">
								<?php echo $this->Form->input("non_taxable_ach_amount", [
							'div' => 'form-group input-group has-success', 'before' => '<span class="input-group-addon strong" style="padding:2px 3px 2px 3px">$ </span>']); ?>
							</td>
							<td class="panel-footer"></td>
						</tr>
						<tr>
							<td class="panel-footer"></td><td class="strong">Total Taxable Amount</td><td class="panel-footer"></td><td class="panel-footer"></td><td class="panel-footer"></td>
							<td class="text-center">
								<?php echo $this->Form->input("ach_amount", [
							'div' => 'form-group input-group has-success', 'before' => '<span class="input-group-addon strong" style="padding:2px 3px 2px 3px">$ </span>']); ?>
							</td>
							<td class="panel-footer"></td>
						</tr>
					</table>
					<div class="row col-md-6 col-sm-12">
						<table class="table table-bordered table-extra-condensed">
							<tr>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									App Status
								</th>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									Comm Month Override
								</th>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									Expedited?
								</th>
							</tr>
							<tr>
								<td class="text-center" >
									<span name='appFeeOnlyInputContainer' class='center-block' data-toggle="tooltip" data-placement="top" data-original-title="This option is enabled if an 'Application Fee' item is on this invoice.">
									<?php
									echo $this->Form->input('merchant_ach_app_status_id', array('options' => $appStatusOptns, 'empty' => '--'));
									?>
									</span>
								</td>
								<td class="text-center">
									<?php 
										echo $this->Form->input('commission_month', array('after' => '-', 'options' => $moOptns, 'empty' => 'Month', 'wrapInput' => false)); 
										echo $this->Form->input('commission_year', array('options' => $yrOptns, 'empty' => 'Year', 'wrapInput' => false)); 
									?>
								</td>
								<td class="text-center">
									<span name='appFeeOnlyInputContainer' class='center-block' data-toggle="tooltip" data-placement="top" data-original-title="This option is enabled if an 'Application Fee' item is on this invoice.">
										<?php 
											echo $this->Form->input('MerchantUw.expedited', array('label' => false, 'after' => '<strong style="vertical-align: bottom;"> YES</strong>'));
										?>
										<div class="small hidden-print text-left">This Expedite value is from Underwriting, a change here will be applied to Underwriting.</div>
									</span>
								</td>
							</tr>
							<tr>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									Rejected?
								</th>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									Date Resubmitted
								</th>
								<th class="col-md-4 col-sm-4 label-info text-center text-nowrap">
									ACH Collected?
								</th>
							</tr>
							<tr>
								<td class="text-center">
									<?php
									echo $this->Form->input('rejected', array('after' => '<strong style="vertical-align: bottom;"> Yes</strong>'));
									?>
								</td>
								<td class="text-center">
									<?php
										echo $this->Form->input('resubmit_date', array('orderYear' => 'asc', 'empty' => '--'));
									?>
								</td>
								<td class="text-center">
									<?php
									echo $this->Form->input('ach_not_collected', array('after' => '<strong style="vertical-align: bottom;"> Not collected</strong>'));
									?>
								</td>
							</tr>
						</table>
					</div>
					<div class="row col-md-5 col-sm-12 pull-right">
						<span class="strong hidden-print" id="taxApiNotifications"><!--API messages will render here--></span>
						<table class="table-bordered table-hover table-extra-condensed">
							<tr class="bg-info">
								<th class="strong text-center text-nowrap">Tax</th>
								<th class="strong text-center text-nowrap">Entity</th>
								<th class="strong text-center text-nowrap">%</th>
								<th class="strong text-center text-nowrap">$</th>
							</tr>
							<tr class="bg-info">
								<td class="strong text-nowrap">State:</td>
								<td class="strong text-center text-nowrap"><?php 
									$taxStateName = (!isset($taxApiData))? $this->request->data('MerchantAch.tax_state_name') : Hash::get($taxApiData, 'geoState');
									if (empty($taxStateName) && $merchBusinessState !== 'CA') {
										echo '<span class="text-muted">(outside CA not tax)</span>';
									}
									echo $taxStateName . $this->Form->hidden('tax_state_name', array('value' => $taxStateName));
								?></td>
								<td class="strong text-center text-nowrap"><?php 
									$stateTaxRate = (!isset($taxApiData))? $this->request->data('MerchantAch.tax_rate_state') : Hash::get($taxApiData, 'stateSalesTax');
									echo $this->Form->hidden('tax_rate_state', array('value' => $stateTaxRate));
									echo $this->Html->tag('span', $this->Number->toPercentage($stateTaxRate, 2, array('multiply' => true)), array('id' => 'stateTaxPctDisplay'));
									?>
								</td>
								<td class="strong text-center text-nowrap"><?php
									echo $this->Form->hidden('tax_amount_state');
									echo $this->Html->tag('span', $this->Number->currency($this->request->data('MerchantAch.tax_amount_state'), 'USD2dec'), array('id' => 'stateTaxAmntDisplay'));
								?></td>

							</tr>
							<tr class="bg-info">
								<td class="strong text-nowrap">County:</td>
								<td class="strong text-center text-nowrap"><?php
									$taxCountyName = (!isset($taxApiData))? $this->request->data('MerchantAch.tax_county_name') : Hash::get($taxApiData, 'geoCounty');
									echo $taxCountyName . $this->Form->hidden('tax_county_name', array('value' => $taxCountyName));
									?>
								</td>
								<td class="strong text-center text-nowrap"><?php
									$countyTaxRate = (!isset($taxApiData))? $this->request->data('MerchantAch.tax_rate_county') : Hash::get($taxApiData, 'countySalesTax');
									echo $this->Form->hidden('tax_rate_county', array('value' => $countyTaxRate));
									echo $this->Html->tag('span', $this->Number->toPercentage($countyTaxRate, 2, array('multiply' => true)), array('id' => 'countyTaxPctDisplay'));
								?></td>
								<td class="strong text-center text-nowrap"><?php 
									echo $this->Form->hidden('tax_amount_county');
									echo $this->Html->tag('span', $this->Number->currency($this->request->data('MerchantAch.tax_amount_county'), 'USD2dec'), array('id' => 'countyTaxAmntDisplay'));
								?></td>

							</tr>
							<tr class="bg-info">
								<td class="strong text-nowrap">City:</td>
								<td class="strong text-center text-nowrap"><?php 
									$taxCityName = (!isset($taxApiData))? $this->request->data('MerchantAch.tax_city_name') : Hash::get($taxApiData, 'geoCity');
									echo $taxCityName . $this->Form->hidden('tax_city_name', array('value' => $taxCityName));
									?>
								</td>
								<td class="strong text-center text-nowrap"><?php
									if (!isset($taxApiData)) {
										$cityTaxRate = $this->request->data('MerchantAch.tax_rate_city');
									} else {
										$cityTaxRate = Hash::get($taxApiData, 'citySalesTax');
									}

									echo $this->Html->tag('span', $this->Number->toPercentage($cityTaxRate, 2, array('multiply' => true)), array('id' => 'cityTaxPctDisplay'));
									echo $this->Form->hidden('tax_rate_city', array('value' => $cityTaxRate));
									?>
								</td>
								<td class="strong text-center text-nowrap"><?php 
									echo $this->Form->hidden('tax_amount_city');
									echo $this->Html->tag('span', $this->Number->currency($this->request->data('MerchantAch.tax_amount_city'), 'USD2dec'), array('id' => 'cityTaxAmntDisplay'));
								?></td>

							</tr>
							<tr class="bg-info">
								<td class="strong text-nowrap">District Tax:</td>
								<td class="strong text-center text-nowrap"></td>
								<td class="strong text-center text-nowrap"><?php
									if (!isset($taxApiData)) {
										$distTaxRate = $this->request->data('MerchantAch.tax_rate_district');
									} else {
										$distTaxRate = Hash::get($taxApiData, 'districtSalesTax');
									}

									echo $this->Html->tag('span', $this->Number->toPercentage($distTaxRate, 2, array('multiply' => true)), array('id' => 'distTaxPctDisplay'));
									echo $this->Form->hidden('tax_rate_district', array('value' => $distTaxRate));
									?>
								</td>
								<td class="strong text-center text-nowrap"><?php 
									echo $this->Form->hidden('tax_amount_district');
									echo $this->Html->tag('span', $this->Number->currency($this->request->data('MerchantAch.tax_amount_district'), 'USD2dec'), array('id' => 'distTaxAmntDisplay'));
								?></td>

							</tr>
							<tr class="bg-info">
								<td colspan='3' class="strong text-nowrap text-right">Total Tax Amount:</td>
								<td class="strong text-center text-nowrap">
									<?php
										$totalTax = $this->request->data('MerchantAch.tax');
										echo '<span id="totalAchTaxDisp" class="alert-info">' . $totalTax . '</span>';
										echo $this->Form->hidden('tax', array("class" => "form-control", "step" => "any", "style" => "font-size:12px;padding:1px;max-width:60px; width:60px"));
									?>
								</td>

							</tr>
							<tr class="bg-info">
								<td colspan='3' class="strong text-nowrap text-right">Total Amount Billed:</td>
								<td class="strong text-center text-nowrap">
									<?php echo $this->Form->hidden('total_ach');
										$totalAchAmount = isset($this->request->data['MerchantAch']['total_ach']) ? $this->request->data['MerchantAch']['total_ach'] : '';
										echo '<span id="totalAchDisp" class="alert-success">' . $totalAchAmount . '</span>';
									?>
								</td>

							</tr>
						</table>
					</div>
					<div class='clearfix'></div>
				<div class="panel-footer text-center center-block">
					<span class="small input-sm hidden-print">
						<?php
						$options = array('PEND' => '&nbsp;<img src="/img/icon_redflag.gif" class="icon"> Pending&nbsp;&nbsp;',
										'COMP' => '&nbsp;<img src="/img/icon_greenflag.gif" class="icon"> Complete ');
						$attributes = array('legend' => False, 'class' => 'radio-inline small','default' => 'PEND');
						echo $this->Form->radio('status', $options, $attributes);
						?>
					</span>
					<span class="hidden-print">
					<?php 
					echo $this->Html->link('Cancel', ['action' => 'view', $merchant['Merchant']['id']], ["class" => "btn btn-sm btn-danger text-primary"]);
					echo $this->Form->end(['label' =>__('Save'), 'div' => false, 'class' => 'btn btn-default btn-sm']);
					?>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function() {
	/** Listen for print events to increase the vewport size to full width to try to show the ivoice in one full page when printing
	*/
    var beforePrint = function() {
    	$('#mainContentPane').attr('class', 'row col-xs-12 col-sm-12 col-md-12');
    	$("[name^='InvoiceContaner']").attr('class', 'row col-xs-12 col-sm-12 col-md-12');
    };
    var afterPrint = function() {
        $('#mainContentPane').attr('class', 'col-xs-9 col-sm-9 col-md-10');
    	$("[name='InvoiceContanerMain']").attr('class', 'row col-md-offset-1');
    	$("[name='InvoiceContanersSub']").attr('class', 'col-md-11 col-sm-12');
    };

    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }

    window.onbeforeprint = beforePrint;
    window.onafterprint = afterPrint;
}());


	EXACT_SHIPPING = $('#MerchantAchExactShippingAmount');
	GEN_SHIPPING = $('#MerchantAchGeneralShippingAmount');
	DEFAULT_NOTAX_REASON = <?php echo $defaultReasonNoTax; ?>;
	OUTOFSTATE_NOTAX_REASON = <?php echo "'$outOfStateNonTaxId'"; ?>;
	EQMNT_FEE_REASON = <?php echo "'$eqpmntFeeAchReasonId'"; ?>;
	APP_FEE_REASON = <?php echo "'" . MerchantAchReason::APP_FEE . "'"; ?>;
	IS_CALI_MERCHANT = <?php echo (int)($merchBusinessState === 'CA'); ?>;

	$(document).ready(function() {
		$('#MerchantUwSameAsBusAddss').on('click',function() {
			shipStreet = '';
			shipCity = '';
			shipState = '';
			shipZip = '';
			if ($(this).is(":checked")) {
				shipStreet = $('#MerchantAchShipToStreet').attr('data-default-option');
				shipCity = $('#MerchantAchShipToCity').attr('data-default-option');
				shipState = $('#MerchantAchShipToState').attr('data-default-option');
				shipZip = $('#MerchantAchShipToZip').attr('data-default-option');
			}
			$('#MerchantAchShipToStreet').val(shipStreet);
			$('#MerchantAchShipToCity').val(shipCity);
			$('#MerchantAchShipToState').val(shipState);
			$('#MerchantAchShipToZip').val(shipZip);
		});
		//When DOM ready set available imputs
		setAvailableInputs();
		// //Init data table plugin
		$('#itemsTable').DataTable( {
			"scrollY":        "300px",
			"scrollCollapse": true,
			"paging":         false,
			"sort":         false,
			"dom": 'Brt'//hide search input
		} );
		$(".dataTables_scrollHeadInner th").each(function(index){
			$( "#staticItemsTable td:eq( " + index + " )" ).css('width', $(this).css('width'));
		});

		$('#totalAchTaxDisp').text("$ " + setNumOfDecimals(Number($('#MerchantAchTax').val())));
		$('#totalAchDisp').text("$ " + setNumOfDecimals(Number($('#MerchantAchTotalAch').val())));
		setNegativeFormat('totalAchTaxDisp', Number($('#MerchantAchTax').val()));
		setNegativeFormat('totalAchDisp', Number($('#MerchantAchTotalAch').val()));

		if (EXACT_SHIPPING.val() > 0) {
			GEN_SHIPPING.attr('disabled', 'disabled');
		} else if (GEN_SHIPPING.val() > 0) {
			EXACT_SHIPPING.attr('disabled', 'disabled');
		}

		EXACT_SHIPPING.keyup(function(event) {
			if (isNumericInput(event.which)) {
				updateInvoice();
			} else {
				return false;
			}
		});
		GEN_SHIPPING.keyup(function(event) {
			if (isNumericInput(event.which)) {
				updateInvoice();
			} else {
				return false;
			}
		});

		EXACT_SHIPPING.blur(function() {
			if (EXACT_SHIPPING.val() > 0) {
				GEN_SHIPPING.attr('disabled', 'disabled');
			} else {
				GEN_SHIPPING.effect("highlight");
				GEN_SHIPPING.attr('disabled', false);
			}
		});

		GEN_SHIPPING.blur(function() {
			if (GEN_SHIPPING.val() > 0) {
				EXACT_SHIPPING.attr('disabled', 'disabled');
			} else {
				EXACT_SHIPPING.effect("highlight");
				EXACT_SHIPPING.attr('disabled', false);
			}
		});
	});
/**
 * updateInvoice
 * Main function to make updates on all invoice amounts
 *
 * @param integer itemIdxNumber optional parameter representing the index number (zero based) of an invoice line item
 */
	function updateInvoice(itemIdxNumber) {
		if (itemIdxNumber !== undefined) {
			updateItemTax(itemIdxNumber);
		}
		updateTaxableTotal();
		updateNonTaxableTotal();
		updateTaxBreakdown();
		updateGrandTotalInvoiceTax();
		updateGrandTotalInvoiceAmount();
	}
/**
 * updateItemTax
 * Calculates and updates the tax for the invoice item at specified index position
 *
 * @param integer itemIdxNumber parameter representing the index number (zero based) of an invoice line item
 */
	function updateItemTax(itemIdxNumber) {
		n = itemIdxNumber;
		itemTaxAmount = 0;
		if ($('#InvoiceItem' + n + 'Taxable').is(":checked")) {
			itemAmount = Number($('#InvoiceItem' + n + 'Amount').val());
			//Remove decimals
			stTxRate = Number($('#MerchantAchTaxRateState').val()) * 100;
			cntyTxRate = Number($('#MerchantAchTaxRateCounty').val()) * 100;
			ctyTxRate = Number($('#MerchantAchTaxRateCity').val()) * 100;
			disTxRate = Number($('#MerchantAchTaxRateDistrict').val()) * 100;
			taxRate = (stTxRate + cntyTxRate + ctyTxRate + disTxRate);
			//Multiply without auto-rounding
			itemTaxAmount = (itemAmount * taxRate) / 100;
		}
		if (Number($('#InvoiceItem' + n + 'Amount').val()) != 0) {
				$('#InvoiceItem' + n +'MerchantAchReasonId').prop('required', true);
			} else {
				$('#InvoiceItem' + n +'MerchantAchReasonId').prop('required', false);
			}
		$('#InvoiceItem' + n + 'TaxAmount').val(setNumOfDecimals(itemTaxAmount));
		$('#Item' + n + 'TaxAmnt').text('$ ' + setNumOfDecimals(itemTaxAmount));
	}

/**
 * updateTaxableTotal
 * Calculates and updates the total tax for the taxable invoice amounts including taxable invoice items.
 *
 */
	function updateTaxableTotal() {
		//reset to zero
		$('#MerchantAchAchAmount').val('0.00');
		itemAmount = 0;
		for (n = 0; n < $("[name='invItemRow']").length; n++) {
			if ($('#InvoiceItem' + n + 'Taxable').is(":checked")) {
				itemAmount = addDec(itemAmount, Number($('#InvoiceItem' + n + 'Amount').val()), 3);
			}
		}
		//General Shipping taxable IFF any item is taxable as well
		if ($("input[type='checkbox'][name*='taxable']:checked").length) {
			itemAmount = addDec(itemAmount, Number($('#MerchantAchGeneralShippingAmount').val()), 3);
		}
		$('#MerchantAchAchAmount').val(itemAmount);
	}

/**
 * updateNonTaxableTotal
 * Calculates and updates total amount for non taxable items and amounts on invoice
 *
 */
	function updateNonTaxableTotal() {
		//reset to zero
		$('#MerchantAchNonTaxableAchAmount').val('0.00');
		itemAmount = 0;
		for (n = 0; n < $("[name='invItemRow']").length; n++) {
			if ($('#InvoiceItem' + n + 'Taxable').is(":checked") === false) {
				itemAmount = addDec(itemAmount, Number($('#InvoiceItem' + n + 'Amount').val()), 3);
			}
		}
		//Exact shipping is not taxable
		itemAmount = addDec(itemAmount, Number($('#MerchantAchExactShippingAmount').val()), 3);
		//General Shipping NOT taxable IFF no item are taxable
		if ($("input[type='checkbox'][name*='taxable']:checked").length === 0) {
			itemAmount = addDec(itemAmount, Number($('#MerchantAchGeneralShippingAmount').val()), 3);
		}
		$('#MerchantAchNonTaxableAchAmount').val(itemAmount);
	}

/**
 * updateTaxBreakdown
 * Calculates and updates each tax field in the tax breakdown section of the invoice
 *
 */
	function updateTaxBreakdown() {
		taxableAmount = Number($('#MerchantAchAchAmount').val());
		//reset all hidden field amounts
		$('#MerchantAchTaxAmountState, #MerchantAchTaxAmountCounty, #MerchantAchTaxAmountCity, #MerchantAchTaxAmountDistrict').val(0);
		//reset all read-only amounts
		$('#stateTaxAmntDisplay, #countyTaxAmntDisplay, #cityTaxAmntDisplay, #distTaxAmntDisplay').text('$ 0.00');
		stTxAmnt = 0;
		cntyTxAmnt = 0;
		ctyTxAmnt = 0;
		disTxAmnt = 0;
		if (taxableAmount != 0) {
			//Remove decimals
			stTxRate = Number($('#MerchantAchTaxRateState').val()) * 100;
			cntyTxRate = Number($('#MerchantAchTaxRateCounty').val()) * 100;
			ctyTxRate = Number($('#MerchantAchTaxRateCity').val()) * 100;
			disTxRate = Number($('#MerchantAchTaxRateDistrict').val()) * 100;
			stTxAmnt = (taxableAmount * stTxRate) / 100;
			cntyTxAmnt = (taxableAmount * cntyTxRate) / 100;
			ctyTxAmnt = (taxableAmount * ctyTxRate) / 100;
			disTxAmnt = (taxableAmount * disTxRate) / 100;
		}
			$('#stateTaxAmntDisplay').text('$ ' + setNumOfDecimals(stTxAmnt));
			$('#countyTaxAmntDisplay').text('$ ' + setNumOfDecimals(cntyTxAmnt, 3));
			$('#cityTaxAmntDisplay').text('$ ' + setNumOfDecimals(ctyTxAmnt, 3));
			$('#distTaxAmntDisplay').text('$ ' + setNumOfDecimals(disTxAmnt, 3));
			$('#MerchantAchTaxAmountState').val(setNumOfDecimals(stTxAmnt, 3));
			$('#MerchantAchTaxAmountCounty').val(setNumOfDecimals(cntyTxAmnt, 3));
			$('#MerchantAchTaxAmountCity').val(setNumOfDecimals(ctyTxAmnt, 3));
			$('#MerchantAchTaxAmountDistrict').val(setNumOfDecimals(disTxAmnt, 3));
	}
/**
 * setNumOfDecimals
 * Sets the number of decimals in a floating point number without 
 * rounding by truncating the number of decimals down to the specified precision
 *
 * @param float floatNum a decimal number
 * @param integer precision the number of decimals to truncate the decimal number to
 */
	function setNumOfDecimals(floatNum, precision) {
		if (precision === undefined) {
			return floatNum.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
		} else if (precision === 3){
			return floatNum.toString().match(/^-?\d+(?:\.\d{0,3})?/)[0];
		} else if (precision === 4){
			return floatNum.toString().match(/^-?\d+(?:\.\d{0,4})?/)[0];
		}
	}
/**
 * updateGrandTotalInvoiceTax
 * Calculates and updates the grand total tax on the invoice
 *
 */
	function updateGrandTotalInvoiceTax() {
		taxableAmount = Number($('#MerchantAchAchAmount').val());
		stTxRate = Number($('#MerchantAchTaxRateState').val()) * 100;
		cntyTxRate = Number($('#MerchantAchTaxRateCounty').val()) * 100;
		ctyTxRate = Number($('#MerchantAchTaxRateCity').val()) * 100;
		disTxRate = Number($('#MerchantAchTaxRateDistrict').val()) * 100;
		stTxAmnt = (taxableAmount * stTxRate);
		cntyTxAmnt = (taxableAmount * cntyTxRate);
		ctyTxAmnt = (taxableAmount * ctyTxRate);
		disTxAmnt = (taxableAmount * disTxRate);
		grandTotalInvoiceTax = (stTxAmnt + cntyTxAmnt + ctyTxAmnt + disTxAmnt) / 100
		grandTotalInvoiceTax = setNumOfDecimals(grandTotalInvoiceTax);
		$('#totalAchTaxDisp').text('$ ' + grandTotalInvoiceTax);
		setNegativeFormat('totalAchTaxDisp', grandTotalInvoiceTax);
		$('#MerchantAchTax').val(grandTotalInvoiceTax);
	}
/**
 * updateGrandTotalInvoiceAmount
 * Calculates and updates the grand total on the invoice
 *
 */
	function updateGrandTotalInvoiceAmount() {
		gTotat = ($('#MerchantAchNonTaxableAchAmount').val() * 100 + $('#MerchantAchAchAmount').val() * 100 + $('#MerchantAchTax').val() * 100) / 100;
		gTotat = setNumOfDecimals(gTotat);
		$("#MerchantAchTotalAch").val(gTotat);
		$('#totalAchDisp').text('$ ' + gTotat);
		setNegativeFormat('totalAchDisp', gTotat);
	}
/**
 * setNegativeFormat
 * Toggles bootstrap css formatting for the object identified by the first param
 * Negative values will be displayed red while positives will diplay green
 *
 * @param string objId the id of the object to apply the css class
 * @param float val a value
 */
	function setNegativeFormat(objId, val) {
		if (val < 0) {
			$('#' + objId).removeClass('alert-success');
			$('#' + objId).addClass('alert-danger');
		} else {
			$('#' + objId).removeClass('alert-danger');
			$('#' + objId).addClass('alert-success');
		}
	}

/**
 * setAvailableInputs
 * Sets inputs that should be enabled/disabled depeding on whether there in a quialfying reason selected on an invoice item 
 * Enables inputs when at least one item has a "Application Fee" reason selected as the reason for the charge. Disabled otherwise.
 *
 */
	function setAvailableInputs() {
		hasAppFeeItem = false;
		$("[name*='merchant_ach_reason_id']").each(function(){
			if ($(this).val() === APP_FEE_REASON) {
				hasAppFeeItem = true;
				return false;//break
			}
		});
		if (hasAppFeeItem) {
			if ($('#MerchantAchMerchantAchAppStatusId').hasClass('disabled')) {
				$("[name='appFeeOnlyInputContainer']").effect('highlight', {color:"green"}, 1500);
			}
			$('#MerchantUwExpedited').attr('disabled', false);
			$('#MerchantUwExpedited').removeClass('disabled');
			$('#MerchantAchMerchantAchAppStatusId').attr('disabled', false);
			$('#MerchantAchMerchantAchAppStatusId').removeClass('disabled');
		} else {
			$('#MerchantUwExpedited').attr('disabled', 'disabled');
			$('#MerchantUwExpedited').addClass('disabled');
			$('#MerchantAchMerchantAchAppStatusId').attr('disabled', 'disabled');
			$('#MerchantAchMerchantAchAppStatusId').addClass('disabled');
		}
	}

	function setDefaultNonTaxableReason(itemIdxNumber) {
		achReasonId = $('#InvoiceItem'+itemIdxNumber+'MerchantAchReasonId').val();
		if (achReasonId !== "" && DEFAULT_NOTAX_REASON[achReasonId] !== undefined) {
			$('#InvoiceItem'+itemIdxNumber+'NonTaxableReasonId').val(DEFAULT_NOTAX_REASON[achReasonId]);
		} else if(IS_CALI_MERCHANT == false && achReasonId == EQMNT_FEE_REASON) {
			$('#InvoiceItem'+itemIdxNumber+'NonTaxableReasonId').val(OUTOFSTATE_NOTAX_REASON);
		} else {
			$('#InvoiceItem'+itemIdxNumber+'NonTaxableReasonId').val('');
		}
	}
	function isNumericInput(keyVal) {
		if (keyVal >= 48 && keyVal <= 57)
			return true;
		else if (keyVal >= 96 && keyVal <= 105)
			return true;
		else if (keyVal === 110 || keyVal === 190 || keyVal === 46 || keyVal === 8) //allow periods and delete keystrokes
			return true;
		else
			return false;
	}

/**
 * insertNewItem
 * Dynamically injects a new line item by retrieving the invoice line DOM html structure from the server
 *
 */
	function insertNewItem() {
		nextItemIdxPos = $("[name='invItemRow']").length;
		$.ajax({
            type: "POST",
            url: '/MerchantAches/renderNewItem/' + nextItemIdxPos,
            dataType: 'html',
            success: function(data) {
                $('#itemsTable').append(data);
            },
            error: function(data) {
				/*If user session expired the server will return a Forbidden status 403
				*Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
				if(data.status===403){                     
					location.reload();
				}
				$('#itemsTable').parent().parent().prepend('<div class="alert alert-danger strong text-center">Server Request Error: Sorry try again later.</div>');
			}
        }); 
	}

</script>