<?php
/* Drop breadcrumb */
$this->Html->addCrumb('Underwriting', '/' . $this->name . '/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->action)), $this->request->here);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($this->request->data['Merchant']['merchant_dba'] . " / " . $this->request->data['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Edit Underwriting.')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

    <span class="contentModuleTitle">Edit Underwriting</span>
	<?php
	echo $this->Form->create('MerchantUw', array(
       'inputDefaults' => array(
                'label' => array(
                    'class' => 'col col-md-4 control-label'
                ),
                'wrapInput' => 'col-md-12',
                'class' => 'form-control'
            ),
       "class" => "form-inline"
    ));
	echo $this->Form->hidden("Merchant.id");
	echo $this->Form->hidden("Merchant.user_id");
	echo $this->Form->hidden("Merchant.merchant_mid");
    echo $this->Form->hidden('Request.isBulkedData', array('value' => true));
    //HTML attributes for notes fields
    $textInput = array('wrapInput' => false, 'div' => 'form-group col-sm-12 col-md-12', 'label' => false, 'type' => 'text', 'style' => "width:100%");
    $textareaInput = array('wrapInput' => false, 'div' => 'form-group col-sm-12 col-md-12', 'label' => false, 'type' => 'textarea', 'style' => "font-size:9pt;height:40px;width:100%");
	?>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <?php echo (!empty($this->request->data('MerchantUw.id')))? $this->Form->hidden('MerchantUw.id'): ''; ?>
        <?php echo $this->Form->hidden('MerchantUw.merchant_id', array('value' => $this->request->data['Merchant']['id'])); ?>
        <table style="width: auto" cellpadding="0" cellspacing="0" border="0">
            <tr><td colspan="2" class="dataCell noBorders">&nbsp;<!-- SPACER ROW--></td></tr>
            <tr><td class="dataCell noBorders">DBA</td>
                <td class="dataCell noBorders"><?php echo (!empty($merchant['Merchant']['merchant_dba'])) ? h($merchant['Merchant']['merchant_dba']) : "--"; ?></td></tr>
            <tr><td class="dataCell noBorders">MID</td>
                <td class="dataCell noBorders"><?php echo (!empty($this->request->data['Merchant']['merchant_mid'])) ? h($this->request->data['Merchant']['merchant_mid']) : "--"; ?></td></tr>
            <tr><td class="dataCell noBorders">New or Additional?</td>
                <td class="dataCell noBorders">
                <?php
                    $options = GUIbuilderComponent::getAppQuantityTypeList();
                    $attributes = array('legend' => False, "class" => 'col-sm-12 col-md-12');
                    echo $this->Form->radio('app_quantity_type', $options, $attributes);
                ?>
                </td></tr>
            <tr><td class="dataCell noBorders">Expedited</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.expedited', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Bank</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.sponsor_bank_id', array('label' => false, 'options' => $sponsorBankOptns, 'empty' => '--')); ?></td></tr>
            <tr><td class="dataCell noBorders">Merchant Tier Assignment</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.tier_assignment', array('options' => array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5), 'empty' => '--', 'label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Business Type</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('Merchant.merchant_bustype', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Business Level</td>
                <td class="dataCell noBorders"><?php echo (!empty($this->request->data['Merchant']['merchant_buslevel'])) ? h($this->request->data['Merchant']['merchant_buslevel']) : "--"; ?></td></tr>
            <tr><td class="dataCell noBorders">MCC</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.mcc', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Annual Volume</td>
                <td class="dataCell noBorders"><?php echo (!empty($this->request->data('MerchantUw.MerchantUwVolume.mo_volume'))) ? $this->Number->currency($this->request->data('MerchantUw.MerchantUwVolume.mo_volume') * 12, 'USD', array('after' => false, 'negative' => '-')) : "--"; ?></td></tr>
            <tr><td class="dataCell noBorders">Daily/Monthly Discount</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.discount_frequency', array('label' => false, 'options' => $discountFeq, 'empty' => 'N/A')); ?></td></tr>
                <tr><td class="dataCell noBorders">Fees Collected Daily</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.fees_collected_daily', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Next Day Funding</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.next_day_funding', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Funding Delay - Sales</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.funding_delay_sales', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Funding Delay - Credits</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.funding_delay_credits', array('label' => false)); ?></td></tr>
            <tr><td class="dataCell noBorders">Final Status</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.merchant_uw_final_status_id', array('label' => false, 'options' => $assocUwData['finalStatusOptns'], 'empty' => '--')); ?></td></tr>
            <tr><td class="dataCell noBorders">Approved By</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.merchant_uw_final_approved_id', array('label' => false, 'options' => $assocUwData['approversOptns'], 'empty' => '--')); ?></td></tr>
            <tr><td class="dataCell noBorders">Date
                    <?php
                    if (empty($this->request->data['MerchantUw']['final_date']))
                        echo $this->Html->image("/img/clock.png", array('data-toggle' => "tooltip", 'data-placement' => "top", "title" => "Set to current time", "class" => "pull-right", "url" => "javascript:void(0)", "onClick" => "setTimeStampNow('UwFinalDate')"));
                    ?>
                </td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.final_date', array('label' => false, 'empty' => '--')); ?></td></tr>
        </table>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-7 ">
        <table  cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class='text-center'>Underwriting Status</th>
                <th class='text-center'>Date & Time</th>
                <th class='text-center'></th>
                <th class='text-center'>Notes</th>
            </tr>
			<?php foreach ($assocUwData['uwSatuses'] as $n => $uwSatus): ?>
				<tr><td style="width: 100px" class="dataCell noBorders"><?php echo h($uwSatus['UwStatus']['name']); ?></td>
					<td style='width: 100px' class="dataCell noBorders">
						<?php echo (!empty($this->request->data("Merchant.UwStatusMerchantXref.$n.merchant_id")))? $this->Form->hidden("Merchant.UwStatusMerchantXref.$n.merchant_id") : ""; ?>
						<?php echo (!empty($this->request->data("Merchant.UwStatusMerchantXref.$n.id")))? $this->Form->hidden("Merchant.UwStatusMerchantXref.$n.id") : ""; ?>
						<?php echo $this->Form->hidden("Merchant.UwStatusMerchantXref.$n.uw_status_id", array('value' => $uwSatus['UwStatus']['id'])); ?>
						<?php echo $this->Form->input("Merchant.UwStatusMerchantXref.$n.datetime", array("wrapInput" => false, 'label' => false, 'empty' => '--')); ?>
					</td>
					<td class="dataCell noBorders">
						<?php
                        $imgUrlAttributes = array('data-toggle' => "tooltip", 'data-placement' => "top", "title" => "Set to current time", "class" => "icon", "url" => "javascript:void(0)", "onClick" => "setTimeStampNow('UwStatusMerchantXref" . $n . "Datetime');");
						if (empty($this->request->data['Merchant']['UwStatusMerchantXref'][$n]['datetime']))
							echo $this->Html->image("/img/clock.png", $imgUrlAttributes);
							?>
					</td>
					<td class="noBorders"><?php
						$settings = empty($this->request->data['Merchant']['UwStatusMerchantXref'][$n]['notes']) ? $textInput : $textareaInput;
						echo $this->Form->input("Merchant.UwStatusMerchantXref.$n.notes", $settings);
						?></td>
				</tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>
<hr>
<div class="row">
	<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/activitySectionEdit', true)): ?>
	<div class="col-md-3">
			<span class="contentModuleTitle">Activity</span><br />
			<?php echo (!empty($this->request->data('MerchantUw.MerchantUwVolume.id')))? $this->Form->hidden('MerchantUw.MerchantUwVolume.id') : ""; ?>
           <?php echo $this->Form->hidden('MerchantUw.MerchantUwVolume.merchant_id', array('value' => $this->request->data['Merchant']['id'])); ?>
           <?php echo (!empty($this->request->data('MerchantUw.MerchantUwVolume.merchant_uw_id')))? $this->Form->hidden('MerchantUw.MerchantUwVolume.merchant_uw_id') : ""; ?>
			<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="noBorders">Monthly Volume</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.mo_volume', array('label' => false)); ?></td></tr>
				<tr><td class="noBorders">Average Ticket</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.average_ticket', array('label' => false, 'decimals' => 2)); ?></td></tr>
				<tr><td class="noBorders">Max Trans. Amount</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.max_transaction_amount', array('label' => false)); ?></td></tr>
				<tr><td class="noBorders">Sales</td>
					<td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.sales', array('label' => false)); ?></td></tr>
				<tr><td class="noBorders">Projected Gross Profit</td>
					<td class="dataCell noBorders"></td></tr>
				<tr><td class="noBorders">Projected Rep Profit</td>
					<td class="dataCell noBorders"></td></tr>
				<tr><td class="noBorders">Projected Axia Profit</td>
					<td class="dataCell noBorders"></td></tr>
			</table>
    </div>
    <?php endif; ?>
    <div class="col-md-3">
        <span class="contentModuleTitle">Volume Breakdown</span><br />

        <table style="width: auto" cellpadding="0" cellspacing="0" border="0">
            <tr><td class="noBorders">Visa Volume</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.visa_volume', array('label' => false, 'required' => false)); ?></td></tr>
            <tr><td class="noBorders">MasterCard Volume</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.mc_volume', array('label' => false, 'required' => false)); ?></td></tr>
            <tr><td class="noBorders">Discover Volume</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.ds_volume', array('label' => false, 'required' => false)); ?></td></tr>
            <tr><td class="noBorders">Pin Debit Volume</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.pin_debit_volume', array('label' => false, 'required' => false)); ?></td></tr>
            <tr><td class="noBorders">Pin Debit Avg Ticket</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.pin_debit_avg_ticket', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">American Express Volume</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.amex_volume', array('label' => false, 'required' => false)); ?></td></tr>
            <tr><td class="noBorders">American Express Average Ticket</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.amex_avg_ticket', array('label' => false)); ?></td></tr>
        </table>
    </div>
    <div class="col-md-3">
        <span class="contentModuleTitle">Method of Sales</span><br />

        <table style="width: auto" cellpadding="0" cellspacing="0" border="0">
            <tr><td class="noBorders">Card present swipe</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.card_present_swiped', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">Card present imprint</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.card_present_imprint', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">Card not present Keyed</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.card_not_present_keyed', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">Card not present Internet</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.card_not_present_internet', array('label' => false)); ?></td></tr>

        </table>
    </div>
    <div class="col-md-3">
        <span class="contentModuleTitle">Percentage of Products Sold</span><br />

        <table style="width: auto" cellpadding="0" cellspacing="0" border="0">
            <tr><td class="noBorders">Direct to consumer</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.direct_to_consumer', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">Business to business</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.direct_to_business', array('label' => false)); ?></td></tr>
            <tr><td class="noBorders">Direct to Government</td>
                <td class="dataCell noBorders"><?php echo $this->Form->input('MerchantUw.MerchantUwVolume.direct_to_government', array('label' => false)); ?></td></tr>
        </table>
    </div>
</div>
<hr>

		<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/hiddenS1', true)): ?>
<div class="row">
   <div class="col-md-6">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<th class="text-center">Required Information/Documents</th>
				<th class="text-center">Received</th>
				<th class="text-center">Notes</th>
			</tr>
			<?php
			
            $maxCountInfoDocs = count($assocUwData['uwRequiredInfoDocs']);
            $maxCountInfoDocs += count($assocUwData['uwOtherInfoDocs']) + 1;
            foreach ($assocUwData['uwRequiredInfoDocs'] as $r => $rDocs) {
                $curIndex = null;
                foreach($this->request->data("Merchant.UwInfodocMerchantXref") as $idx => $data) {
                    if ($rDocs['UwInfodoc']['id'] === $data['uw_infodoc_id']) {
                         $curIndex = $idx;
                         break;
                    }
                }
                if (is_null($curIndex)) {
                    $curIndex = $maxCountInfoDocs;
                    $maxCountInfoDocs++;
                }
				?>
				<tr><td class="noBorders"><?php echo $rDocs['UwInfodoc']['name']; ?></td>
					<td class="dataCell noBorders">
                        <?php echo (!empty($this->request->data("Merchant.UwInfodocMerchantXref.$curIndex.id")))? $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.id") : ""; ?>
						<?php echo $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.required", array("value" => true)); ?>
						<?php echo $this->Form->input("Merchant.UwInfodocMerchantXref.$curIndex.uw_received_id", array('label' => false, 'options' => $uwReceivedOptns, 'empty' => '--')); ?>
                        <?php echo (!empty($this->request->data("Merchant.UwInfodocMerchantXref.$curIndex.merchant_id")))? $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.merchant_id") : ""; ?>
						<?php echo $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.uw_infodoc_id", array('value' => $rDocs['UwInfodoc']['id'])); ?>
					</td>
					<td class="noBorders"><?php
						$settings = empty($this->request->data['Merchant']['UwInfodocMerchantXref'][$curIndex]['notes']) ? $textInput : $textareaInput;
						echo $this->Form->input("Merchant.UwInfodocMerchantXref.$curIndex.notes", $settings);
						?></td>
				</tr>
       <?php }; ?>
			<tr>
				
				<td colspan="3" class="text-right"><?php echo $this->Form->input("MerchantUw.credit_pct", array(
                'wrapInput' => 'col-md-8 input-group',
                'afterInput' => '<div class="input-group-addon" style="padding:1px">%</div>', 
                'label' => 'Credit')); ?></td>
			</tr>
			<tr>
				<td colspan="3" class="text-right"><?php echo $this->Form->input("MerchantUw.chargeback_pct", array(
                'wrapInput' => 'col-md-8 input-group',
                'afterInput' => '<div class="input-group-addon" style="padding:1px">%</div>',
                'label' => 'Chargeback')); ?></td>
			</tr>
		</table>
	</div>
	<div class="col-md-6">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<th class="text-center">Other Information/Documents</th>
				<th class="text-center">Received</th>
				<th class="text-center">Notes</th>
			</tr>
			<?php
			//Resuming max count
			$maxCountInfoDocs++;
			foreach ($assocUwData['uwOtherInfoDocs'] as $otherDocs):
                $curIndex = null;
                foreach($this->request->data("Merchant.UwInfodocMerchantXref") as $idx => $data) {
                    if ($otherDocs['UwInfodoc']['id'] === $data['uw_infodoc_id']) {
                         $curIndex = $idx;
                         break;
                    }
                }
                if (is_null($curIndex)) {
                    $curIndex = $maxCountInfoDocs;
                    $maxCountInfoDocs++;
                }
				?>
				<tr><td class="noBorders"><?php echo h($otherDocs['UwInfodoc']['name']); ?></td>
					<td class="dataCell noBorders">
                        <?php echo (!empty($this->request->data("Merchant.UwInfodocMerchantXref.$curIndex.id")))? $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.id") : ""; ?>
						<?php echo $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.required", array("value" => 0)); ?>

						<?php echo $this->Form->input("Merchant.UwInfodocMerchantXref.$curIndex.uw_received_id", array('label' => false, 'options' => $uwReceivedOptns, 'empty' => '--')); ?>
                        <?php echo (!empty($this->request->data("Merchant.UwInfodocMerchantXref.$curIndex.merchant_id")))? $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.merchant_id") : ""; ?>
						<?php echo $this->Form->hidden("Merchant.UwInfodocMerchantXref.$curIndex.uw_infodoc_id", array('value' => $otherDocs['UwInfodoc']['id'])); ?>
					</td>
					<td class="noBorders"><?php
						$settings = empty($this->request->data['Merchant']['UwInfodocMerchantXref'][$curIndex]['notes']) ? $textInput : $textareaInput;
						echo $this->Form->input("Merchant.UwInfodocMerchantXref.$curIndex.notes", $settings);
						?></td>
				</tr>
				<?php
			endforeach;
			?>
		</table>
    </div>
</div>
<hr>
<div class="row">
	<div class="col-sm-12 col-md-6">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<th class="text-center">Approval Information</th>
				<th>Verified</th>
				<th class="text-center">Notes</th>
			</tr>
			<?php foreach ($assocUwData['approvalInfos'] as $n => $approvalInfo): ?>
				<?php
				if ($approvalInfo['UwApprovalinfo']['name'] === 'Acceptable Credit Report/OFAC') {
					$allowedModuleAccess = $this->Rbac->isPermitted('app/actions/MerchantUws/view/module/credScoreSection', true);
				} else {
					$allowedModuleAccess = true;
				}
				?>
				<?php if ($allowedModuleAccess) : ?>
					<tr><td class="dataCell noBorders"><?php echo h($approvalInfo['UwApprovalinfo']['name']); ?></td>
						<td class="dataCell noBorders"><?php
							if ($n === 0)
								$options = $uwVerifiedOptns['nrp'];
							elseif ($n === 2)
								$options = $uwVerifiedOptns['match'];
							else
								$options = $uwVerifiedOptns['yn'];
							echo $this->Form->input("Merchant.UwApprovalinfoMerchantXref.$n.uw_verified_option_id", array('label' => false, 'options' => $options, 'empty' => '--'));
							?></td>
						<td class="noBorders"><?php
							$settings = empty($this->request->data['Merchant']['UwApprovalinfoMerchantXref'][$n]['notes']) ? $textInput: $textareaInput;
							echo $this->Form->input("Merchant.UwApprovalinfoMerchantXref.$n.notes", $settings);
							?>
							<?php echo (!empty($this->request->data("Merchant.UwApprovalinfoMerchantXref.$n.merchant_id")))? $this->Form->hidden("Merchant.UwApprovalinfoMerchantXref.$n.merchant_id") : ""; ?>
                            <?php echo (!empty($this->request->data("Merchant.UwApprovalinfoMerchantXref.$n.id")))? $this->Form->hidden("Merchant.UwApprovalinfoMerchantXref.$n.id") : ""; ?>
				            <?php echo $this->Form->hidden("Merchant.UwApprovalinfoMerchantXref.$n.uw_approvalinfo_id", array('value' => $approvalInfo['UwApprovalinfo']['id'])); ?>
						</td>
					</tr>
                <?php endif; ?>
	         <?php endforeach; ?>
		</table>
    </div>
</div>
<?php if ($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/credScoreSection', true)): ?>
    <div class="row">
    			<div class="col-sm-5 col-md-5">
    				<table style="width: auto" cellpadding="0" cellspacing="0" border="0">
    					<tr><th class="noBorders">Merchant Credit Score</th></tr>
    					<tr><td class="dataCell noBorders"><?php echo $this->Form->input("MerchantUw.credit_score", array('label' => false)); ?></td>
    					</tr>
    				</table>
    			</div>
    </div>
<?php endif; ?>
<hr>
	<?php endif; ?>
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
?>



<script type="text/javascript">
    //activate this view navigation
    activateNav('MerchantUwsView');

	$(function() {

		$('#MerchantUwVolumeMoVolume').keyup(function() {
			calculateSales();
		});

		$('#MerchantUwVolumeAverageTicket').keyup(function() {
			calculateSales();
		});

	});

	function calculateSales() {
		if ($('#MerchantUwVolumeAverageTicket').val() > 0) {
			var sales = ($('#MerchantUwVolumeMoVolume').val() / $('#MerchantUwVolumeAverageTicket').val()) | 0; //bitwise OR operation to remove decimals (only works with unsigned floats)
			$('#MerchantUwVolumeSales').val(sales);
		}
	}

</script>


