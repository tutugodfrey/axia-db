<?php 
echo $this->AssetCompress->css('custom-bootstrap.css', array('raw' => (bool)Configure::read('debug')));
echo $this->element('Layout/selectizeAssets'); ?>
<?php
/* Drop breadcrumb */
$this->Html->addCrumb($this->name . ' ' . $this->action, '/' . $this->name);
?>

<input type="hidden" id="thisViewTitle" value="<?php echo __('Merchants'); ?> List" />
<?php
echo $this->Form->createFilterForm('Merchant');


//Remember previous selection
$this->request->data['Merchant'] = $this->request->query;
echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
echo $this->Form->input('partner_id', array('label' => 'Partner', 'empty' => '--'));
echo $this->Form->input('dba_mid', array('label' => 'DBA or MID', 'style' => 'max-width:100%'));
echo $this->Form->input('address_city', array('label' => 'City'));
echo $this->Form->input('address_state', array('label' => 'State', 'options' => $statesOptns,
	'empty' => '--'));
echo "<br>";
echo $this->element('Forms/OrganizationDrilldown');
echo $this->Form->input('active', array('label' => 'Status', 'options' => array(
		1 => 'Active', 0 => 'Inactive', 2 => 'All'), 'empty' => '--', 'default' => 1));
echo $this->Form->input('PCI_compliance', array('label' => 'PCI Compliance', 'options' => array('yes' => 'Compliant',
		'no' => 'Non-Compliant', 'all' => 'All'), 'empty' => '--', 'default' => 'all'));
echo $this->Form->input('name', array('label' => 'Company', 'options' => $entities, 'empty' => '--'));
echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default', 'div' => array('class' => 'form-group')));
?>

<div class="reportTables">
	<?php
	echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini'));
	?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<table class="table-condensed">
		<tr>            <th><?php echo $this->Paginator->sort('merchant_dba', 'DBA'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid', 'MID'); ?></th>
			<th><?php echo $this->Paginator->sort('Client.client_id_global', 'Client ID'); ?></th>
			<th><?php echo $this->Paginator->sort('User.user_first_name', 'Rep'); ?></th>
			<th><?php echo $this->Paginator->sort('AddressBus.address_city', 'City'); ?></th>
			<th><?php echo $this->Paginator->sort('AddressBus.address_state', 'State'); ?></th>
			<th><?php echo $this->Paginator->sort('AddressBus.address_phone', 'Phone'); ?></th>
			<th><?php echo $this->Paginator->sort('Merchant.merchant_contact', 'Contact'); ?></th>
			<th><?php echo $this->Paginator->sort('SaqControlScan.pci_compliance', 'PCI'); ?></th>
			<th><?php echo $this->Paginator->sort('EquipmentProgramming.hardware_serial', 'V#'); ?></th>
			<th><?php echo $this->Paginator->sort('Entity.entity_name', 'Company'); ?></th>
			<th><?php echo $this->Paginator->sort('Organization.name', 'Organization'); ?></th>
			<th><?php echo $this->Paginator->sort('Region.name', 'Region'); ?></th>
			<th><?php echo $this->Paginator->sort('Subregion.name', 'Subregion'); ?></th>
			<th><?php echo $this->Paginator->sort('AddressBus.address_street', 'Location'); ?></th>
			<th><?php echo $this->Paginator->sort('Partner.user_first_name', 'Partner'); ?></th>
			<th><?php echo $this->Paginator->sort('LastDepositReport.last_deposit_date', 'Last Activity Date'); ?></th>
			<th><?php echo $this->Paginator->sort('Merchant.active', 'Active'); ?></th>
		</tr>

<?php foreach ($merchants as $merchant): ?>
			<tr>
				<td><?php echo $this->Html->link($merchant['Merchant']['merchant_dba'], array(
		'controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>&nbsp;</td>
				<td><?php echo $this->Html->link($merchant['Merchant']['merchant_mid'], array(
					'controller' => 'Merchants', 'action' => 'view', $merchant['Merchant']['id'])); ?>&nbsp;</td>
				<td><?php
					if (!empty($merchant['Client']['client_id_global'])) {
						echo h($merchant['Client']['client_id_global']);
					}
					?>&nbsp;</td>
				<td><?php echo h($merchant['User']['user_first_name'] . ' ' . $merchant['User']['user_last_name']); ?>&nbsp;</td>
				<td><?php
					if (!empty($merchant['AddressBus']['address_city'])) {
						echo h($merchant['AddressBus']['address_city']);
					}
					?>&nbsp;</td>
				
				<td><?php
					if (!empty($merchant['AddressBus']['address_state'])) {
						echo h($merchant['AddressBus']['address_state']);
					}
					?>&nbsp;</td>
				<td><?php
					if (!empty($merchant['AddressBus']['address_phone'])) {
						echo h($merchant['AddressBus']['address_phone']);
					}
					?>&nbsp;
				</td>
				<td><?php echo h($merchant['Merchant']['merchant_contact']); ?>&nbsp;</td>
				<td><?php echo h($merchant['SaqControlScan']['pci_compliance']); ?>&nbsp;</td>

				<td>
					<?php
						if (!empty($merchant['EquipmentProgramming']['hardware_serial'])) {
							echo h($merchant['EquipmentProgramming']['hardware_serial']);
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['Entity']['entity_name'])) {
							echo h($merchant['Entity']['entity_name']);
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['Organization']['name'])) {
							echo "<span  class='text-truncate'>" . h($merchant['Organization']['name']) . "</span>";
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['Region']['name'])) {
							echo "<span  class='text-truncate'>" . h($merchant['Region']['name']) . "</span>";
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['Subregion']['name'])) {
							echo "<span  class='text-truncate'>" . h($merchant['Subregion']['name']) . "</span>";
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['AddressBus']['address_street'])) {
							echo "<span  class='text-truncate'>" . h($merchant['AddressBus']['address_street']) . "</span>";
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['Partner']['user_first_name'])) {
							echo h($merchant['Partner']['user_first_name'] . ' ' . $merchant['Partner']['user_last_name']);
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if (!empty($merchant['LastDepositReport']['last_deposit_date'])) {
							echo $this->AxiaTime->date($merchant['LastDepositReport']['last_deposit_date']);
						}
					?>&nbsp;
				</td>
				<td>
					<?php
						if ($merchant['Merchant']['active']) { /* binary boolean values */
							echo $this->Html->image('green_orb.gif', array('title' => 'Active'));
						} else {
							echo $this->Html->image('red_orb.png', array('title' => 'Inactive'));
						}
					?>&nbsp;
				</td>
			</tr>
<?php endforeach; ?>
	</table>
	<?php
		if (!empty($merchants)) {
			echo $this->element('Layout/reportFooter');
		}
	?>
</div>
<script>
	$('#MerchantUserId').selectize();
	$('#MerchantUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("Merchant.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>