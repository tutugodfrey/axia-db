<?php
$merchantId = Hash::get((array)$merchant, 'Merchant.id');
$merchantDba = h(Hash::get((array)$merchant, 'Merchant.merchant_dba'));

/* Drop breadcrumb */
$this->Html->addCrumb(__('Merchants'), '/merchants/index');
$this->Html->addCrumb($merchantDba, "/merchants/view/{$merchantId}");
$this->Html->addCrumb('Rejects', "/merchant_rejects/index/{$merchantId}" );

$thisViewTitle = h($merchant['Merchant']['merchant_mid']??'') . " / " . h($merchant['User']['user_first_name']??'') . " " . h($merchant['User']['user_last_name']??'')
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $merchantDba . " / " . $thisViewTitle . " | " . __('Merchant Rejects'); ?>" />
<script type='text/javascript'>activateNav('MerchantsRejects'); </script>
<div class="well well-sm">
	<div class="row">
		<div class="col-xs-12">
			<?php
			$viewTitle = $merchantDba . ' / ' . h(Hash::get((array)$merchant, 'Merchant.merchant_mid') . ' / ' . Hash::get((array)$merchant, 'User.fullname') . ' | ' . __('Merchant Rejects'));
			echo $this->Form->create('MerchantReject', array(
				'inputDefaults' => array(
					'div' => 'form-group',
					'label' => array('class' => 'col col-md-12 control-label'),
					'wrapInput' => false,
					'class' => 'form-control'
				),
				'type' => 'get',
				'class' => 'form-inline'
			));
			echo $this->Form->input('merchant_reject_type_id', array('options' => $merchantRejectTypes, 'empty' => true, 'label' => 'Type', 'required' => false));
			echo $this->Form->input('merchant_reject_status_id', array('options' => $merchantRejectStatuses, 'empty' => true));
			echo $this->Form->input('open', array( 'options' => array( 1 => 'Open', 0 => 'Closed'), 'empty' => true, 'required' => false));
			echo $this->Form->end(array('label' => 'Search', 'div' => array('class' => 'form-group')));
			?>
		</div>
	</div>
<?php if ($this->Rbac->isPermitted('MerchantRejects/add')) {
		echo $this->Html->tag('br');
		echo $this->element('MerchantRejects/add', array('fixedMerchantId' => $merchantId));
	}
	?>
</div>
<?php 
if (empty($merchantRejects)) {
	echo $this->Html->tag('span', __('- No Rejects at this Time -'), array('class' => 'list-group-item text-center text-muted'));
} else {
?>
	<div class="reportTables row">
		<div class="col-xs-12">
			<input type="hidden" id="thisViewTitle" value="<?php echo $viewTitle; ?>" />
			<?php
			echo $this->element('pagination'); ?>
			<table class="table table-condensed table-hover" id="merchant-rejects-index" data-graph-container-before="1" data-graph-type="column">
				<thead>
				<?php
				$headers = array(
					__('Trace #'),
					__('Date'),
					__('Type'),
					__('Code'),
					__('Amount'),
					__('Reject Fee'),
					__('Submitted Amount'),
					__('Status'),
					__('Status Date'),
					__('Notes'),
					__('Recurrance'),
					__('Open'),
					
				);
				if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/axLoss', true)) {
					$headers[] = __('Loss Axia');
				}
				if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/smLoss', true)) {
					$headers[] = __('Loss Mgr1');
				}
				if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/sm2Loss', true)) {
					$headers[] = __('Loss Mgr2');
				}
				$headers[] = __('Loss Rep');
				if ($this->Rbac->isPermitted('MerchantRejects/add') && $this->Rbac->isPermitted('MerchantRejects/edit') && $this->Rbac->isPermitted('MerchantRejects/delete')) {
				$headers[] = __('Actions');
				}
				echo $this->Html->tableHeaders($headers);
				?>
				</thead>
				<tbody>
				<?php
				foreach ((array)$merchantRejects as $merchantReject) {
					$firstLine = true;
					foreach ((array)Hash::get($merchantReject, 'MerchantRejectLine') as $rejectLine) {
						echo $this->element('MerchantRejects/view_row', compact('rejectLine', 'firstLine', 'merchantReject'));
						$firstLine = false;
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php
} // close else block
$this->AssetCompress->autoInclude = false;
echo $this->Html->script('views/merchant_rejects/index', array('block' => 'script'));