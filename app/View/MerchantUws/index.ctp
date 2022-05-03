<?php
echo $this->element('Layout/selectizeAssets');
/* Drop breadcrumb */
$this->Html->addCrumb("Underwriting Report", '/' . $this->name);
?>
<input type="hidden" id="thisViewTitle" value="Underwriting Report" />
<?php
$exportLinks = [];
if (!empty($merchantUws) && $this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Report'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Html->link($icon, "#", [
			'onClick' => "exportTableToCSV('merchantProductReport.csv', 'uwReportTable')",
			'class' => 'pull-right',
			'escape' => false
		]);

	echo $this->Html->tag('span',
		"<strong>Export Data: <br></strong>" . $this->element('Layout/exportCsvUi', array('exportLinks' => $exportLinks)),
		array('class' => 'pull-left well-sm')
	);
}
echo $this->Form->create('MerchantUw', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'type' => 'get',
	'class' => 'well well-sm form-inline'
));
//Remember previous selection
$this->request->data['MerchantUw'] = $this->request->query;

echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));

echo $this->Form->input('dba_mid', array('label' => 'DBA or MID'));

echo $this->Form->input('beginM', array('label' => 'Start', 'options' => $optnsMonths, 'default' => date('n')));
echo $this->Form->input('beginY', array('label' => false, 'options' => $optnsYears, 'default' => date('Y')));

echo $this->Form->input('endM', array('label' => 'End', 'options' => $optnsMonths, 'default' => date('n')));
echo $this->Form->input('endY', array('label' => false, 'options' => $optnsYears, 'default' => date('Y')));
echo '&nbsp;';
$warnMsg = (empty($this->request->query['beginM']) || empty($this->request->query['beginY']))? '&nbsp;&nbsp;<span class="list-group-item-info">-Start date ignored.</span>' : '';
$warnMsg .= (empty($this->request->query['endM']) || empty($this->request->query['endY']))? '<br/>&nbsp;&nbsp;<span class="list-group-item-info">-End date ignored.</span>' : '';
if (!empty($warnMsg)) {
	$warnMsg = "<div class='form-group'>" . $warnMsg . " <span class='list-group-item-info'> (-To set optional date filters, select both year and month)</span></div>";
}
echo $this->Form->end(array('label' => 'Generate', 'after' => $warnMsg, 'class' => 'btn btn-default', 'div' => array('class' => 'form-group')));

?>
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} out of {:count}, starting at {:start}, ending {:end}')
		));
		?>	</p>
	<table id ='uwReportTable' class="table-condensed">
		<tr>
			<th><?php echo "Signed"; ?></th>
			<th><?php echo $this->Paginator->sort('Merchant.merchant_mid', 'MID'); ?></th>
			<th><?php echo $this->Paginator->sort('Merchant.merchant_dba', 'DBA'); ?></th>
			<th><?php echo $this->Paginator->sort('Client.client_id_global', 'Client ID'); ?></th>
			<?php if($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/indexRepCol', true)): ?>
			<th><?php echo $this->Paginator->sort('User.user_first_name', 'Rep'); ?></th>
			<?php endif;?>
			<th><?php echo $this->Paginator->sort('MerchantUw.app_quantity_type', 'New/Addl App'); ?></th>
			<th><?php echo $this->Paginator->sort('MerchantUw.expedited', 'Exp'); ?></th>
			<th><?php echo "Received"; ?></th>
			<th><?php echo "Illegible"; ?></th>
			<th><?php echo "Incomplete"; ?></th>
			<th><?php echo "Complete"; ?></th>
			<th><?php echo "Submitted to UW"; ?></th>
			<th><?php echo "Add'l UW Items Needed"; ?></th>
			<th><?php echo "Rec'd Add'l UW Items"; ?></th>
			<th><?php echo "Approved"; ?></th>
			<th><?php echo "Declined"; ?></th>
			<?php if($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/indexPartnerCol', true)): ?>
			<th><?php echo "Partner"; ?></th>
			<?php endif;?>
			<th><?php echo $this->Paginator->sort('Merchant.active','Active'); ?></th>
		</tr>
		<?php foreach ($merchantUws as $merchantUw):
			$disabledCSS = ($merchantUw['Merchant']['active'])? "": "style='background-color:#f2dede; font-style:italic;'";
		?>
			<tr <?php echo $disabledCSS; ?>>
				<td><?php echo !empty($merchantUw['TimelineEntry'][0])? $this->Time->format($merchantUw['TimelineEntry'][0]['timeline_date_completed'], '%m/%d') : '--'; ?></td>
				<td><?php echo $this->Html->link($merchantUw['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantUw['Merchant']['id'])); ?></td>
				<td><?php echo h($merchantUw['Merchant']['merchant_dba']); ?></td>
				<td><?php echo h($merchantUw['Client']['client_id_global']); ?></td>
				<?php if($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/indexRepCol', true)): ?>
					<?php $repName = $merchantUw['User']['user_first_name'] . ' ' .  substr($merchantUw['User']['user_last_name'], 0, 1); ?>
					<td><?php echo h($repName); ?></td>
				<?php endif;?>
				<td><?php 
					echo h(__(Hash::get($appQuantities, $merchantUw['MerchantUw']['app_quantity_type'], '--'))); ?></td>
				<td><?php echo ($merchantUw['MerchantUw']['expedited'])?"Yes":"No"; ?></td>
				<?php for($x = 0; $x < $uwStatusCount; $x++): ?>
				<td><?php echo !empty($merchantUw['UwStatusMerchantXref'][$x]['datetime'])? $this->Time->format($merchantUw['UwStatusMerchantXref'][$x]['datetime'], '%m/%d %H:%M%p') : '--'; ?></td>
				<?php endfor; ?>
				<?php if($this->Rbac->isPermitted('app/actions/MerchantUws/view/module/indexPartnerCol', true)): ?>
				<td><?php echo (h($merchantUw['Partner']['fullname']))? : "--"; ?></td>
				<?php endif;?>
				<td><?php
					if ($merchantUw['Merchant']['active'])/* binary boolean values */
						echo $this->Html->image('green_orb.gif', array('title' => 'Active'));
					else
						echo $this->Html->image('red_orb.png', array('title' => 'Inactive'));
				?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
	if(!empty($merchantUws)){
		echo $this->element('Layout/reportFooter', array('exportLinks' => $exportLinks));
	}
	?>
</div>
<script>
	$('#MerchantUwUserId').selectize();
	$('#MerchantUwUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("MerchantUw.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
<?php
echo $this->AssetCompress->script('reports', [
		'raw' => (bool)Configure::read('debug')
	]);