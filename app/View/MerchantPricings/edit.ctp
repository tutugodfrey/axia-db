<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Products and Services', '/' . $this->name . '/' . $this->action . '/' . $this->request->data('MerchantPricing.id'));
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Edit Products & Services')); ?>" />
<div>
	<div>
		<?php //************************************Merchant Pricing & Settings *************************************    ?>
		<span class="contentModuleTitle">Merchant Pricing & Settings</span>
	</div>
	<?php
	echo $this->Form->create('MerchantPricing', array(
			  'inputDefaults' => array(
				//validation error text needs to wrap inside the input div only when error text shows up
				'div' => array('class' => 'form-group', 'style' => "white-space:pre-line"),
				'wrapInput' => 'col col-md-9',
				'class' => 'form-control',
				'label' => false
			),
			'class' => 'form-horizontal',
			)
		);
	$numInputOptions = array(
		'type' => 'number',
		"step" => ".001",
	); 
	echo $this->Form->hidden('id');
	echo $this->Form->hidden('merchant_id');
	 ?>
	<div class="row">
		<div class="col-md-3">
				<div class="row">
					<div class='col col-sm-5'>Visa <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class='col col-sm-7'><?php echo $this->Form->input('visa_bet_table_id', array('empty' => __('Please select...'))); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>MasterCard <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class='col col-sm-7'><?php echo $this->Form->input('mc_bet_table_id', array('empty' => __('Please select...'))); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>Discover <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class='col col-sm-7'><?php echo $this->Form->input('ds_bet_table_id', array('empty' => __('Please select...'))); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>American Express <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class='col col-sm-7'><?php echo $this->Form->input('amex_bet_table_id', array('empty' => __('Please select...'))); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>V/MC Discount <br />Processing Rate</div>
					<div class='col col-sm-7'><?php echo $this->Form->input('processing_rate', $numInputOptions); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>American Express Discount Processing Rate</div>
					<div class='col col-sm-7'><?php echo $this->Form->input('amex_processing_rate', $numInputOptions); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>Discover Discount <br />Processing Rate</div>
					<div class='col col-sm-7'><?php echo $this->Form->input('ds_processing_rate', $numInputOptions); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-7'>Rep/Mgr <strong>NOT</strong> paid on<br />Discover Discount/Settled Items </div>
					<div class='col col-sm-4'><?php echo $this->Form->input('ds_user_not_paid', array("wrapInput" => false)); ?></div>
				</div>
		</div>
		<div class="col-md-3">
			<span class="contentModuleHeader">Authorization Fees</span>
			<div class="row">
				<div class="col col-sm-5">V/MC Auth Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('mc_vi_auth', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Amex Auth Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('amex_auth_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Discover Auth Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('ds_auth_fee'); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Discount Item Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('discount_item_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">ARU/Voice Auth Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('aru_voice_auth_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Wireless Auth Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('wireless_auth_fee', $numInputOptions, $numInputOptions); ?></div>
			</div>
		</div>
		<div class="col-md-3">
			<span class="contentModuleHeader">Monthly Fees</span><br />
			<div class="row">
				<div class="col col-sm-5">Statement Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('statement_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Monthly Minimum Fee</div>
				<div class="col col-sm-7"><?php  echo $this->Form->input('min_month_process_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Debit Access Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('debit_access_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">EBT Access Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('ebt_access_fee', $numInputOptions); ?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Gateway</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('gateway_id', ['empty' => '--']);?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Gateway Access Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('gateway_access_fee', $numInputOptions);?></div>
			</div>
			<div class="row">
				<div class="col col-sm-5">Wireless Access Fee</div>
				<div class="col col-sm-7"><?php echo $this->Form->input('wireless_access_fee', $numInputOptions);?></div>
			</div>
		</div>
		<div class="col-md-3">
			<span class="contentModuleHeader">Miscellaneous Fees</span><br />
			
				<div class="row">
					<div class="col col-sm-5">Annual Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('annual_fee', $numInputOptions); ?></div>
				</div>
				<div class="row">
					<div class="col col-sm-5">Chargeback Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('chargeback_fee', $numInputOptions); ?></div>
				</div>
				<div class="row">
					<div class='col col-sm-5'>MasterCard Acquirer Fee</div>
					<div class='col col-sm-7'><?php echo $this->Form->input('mc_acquirer_fee', $numInputOptions); ?></div>
				</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<span class="contentModuleHeader">Billing Auth Fees</span>
				<div class="row">
					<div class="col col-sm-5">Billing V/MC <br/>Auth Fee </div>
					<div class="col col-sm-7"><?php echo $this->Form->input('billing_mc_vi_auth', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Billing Amex <br/>Auth Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('billing_amex_auth', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Billing Discover <br/>Auth Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('billing_discover_auth', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Billing Debit <br/>Auth Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('billing_debit_auth', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Billing EBT <br/>Auth Fee</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('billing_ebt_auth', $numInputOptions);?></div></div>
		</div>
		<div class="col-md-3">
			<span class="contentModuleHeader">Wireless Pricing</span>
				<div class="row">
					<div class="col col-sm-5">Wireless <br/>Per Item Cost</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('wireless_auth_cost', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Number of <br/>Wireless Terminals</div>
					<div class="col col-sm-7"><?php echo  $this->Form->input('num_wireless_term');?></div></div>
				<div class="row">
					<div class="col col-sm-5">Per Wireless <br/>Terminal Cost</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('per_wireless_term_cost', $numInputOptions); ?></div></div>
				<div class="row">
					<div class="col col-sm-5">Total Monthly <br/>Wireless Cost</div>
					<div class="col col-sm-7"><?php echo $this->Form->input('total_wireless_term_cost', $numInputOptions); ?></div></div>
		</div>
		<div class="col-md-6">
			<span class="contentModuleHeader">Debit & EBT Pricing</span>
				<div class="row">
					<div class="col col-sm-2">Debit <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class="col col-sm-4"><?php echo $this->Form->input('db_bet_table_id', array('empty' => __('Please select...'))); ?>
					</div>
					<div class="col col-sm-2">EBT <abbr title="Billing Elements Table" class="initialism">BET</abbr></div>
					<div class="col col-sm-4"><?php echo $this->Form->input('ebt_bet_table_id', array('empty' => __('Please select...'))); ?>
					</div>
				</div>					
				<div class="row">
					<div class="col col-sm-2">Pin Debit<br />Authorization</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('MerchantPricing.debit_auth_fee', $numInputOptions); ?>
					</div>
					<div class="col col-sm-2">EBT Authorization</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('MerchantPricing.ebt_auth_fee', $numInputOptions); ?>
					</div>
				</div>
				<div class="row">
					<div class="col col-sm-2">Pin Debit<br />Discount %</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('debit_processing_rate', $numInputOptions); ?>
					</div>
					<div class="col col-sm-2">EBT Discount %</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('ebt_processing_rate', $numInputOptions); ?>
					</div>
				</div>
				<div class="row">
					<div class="col col-sm-2">Debit Discount P/I</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('debit_discount_item_fee', $numInputOptions); ?>
					</div>
					<div class="col col-sm-2">EBT Discount P/I</div>
					<div class="col col-sm-4"><?php echo $this->Form->input('ebt_discount_item_fee', $numInputOptions); ?>
					</div>
				</div>	
				<div class="row">
					<div class="col col-sm-2">Debit/EBT Acquirer</div>
					<div class="col col-sm-4">
						<?php
						echo $this->Form->input('debit_acquirer_id', array('empty' => __('Please select...')));
						echo $this->Form->hidden('ebt_acquirer_id');
						?>
					</div>
				</div>
		</div>
	</div>
<table class='table table-condensed'>
	<tr>
		<td>
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
echo $this->AssetCompress->script('merchantPricing', array('raw' => (bool)Configure::read('debug')));
?>
</div>
