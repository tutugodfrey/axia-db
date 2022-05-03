<?php /* Drop breadcrumb */ $this->Html->addCrumb(Inflector::humanize(Inflector::underscore($this->name)) . ' ' . $this->action, '/' . $this->name); ?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Gift Cards'); ?> List" />
<div class="reportTables">

	<?php echo $this->Paginator->pagination(array('ul' => 'pagination pagination-mini')); ?>
	<p>
		<?php
		echo $this->Paginator->counter(array(
				  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('merchant_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_mid'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_magstripe_loyalty_item_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_magstripe_gift_item_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_chip_card_one_rate'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_chip_card_gift_item_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_chip_card_loyalty_item_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_smart_card_printing'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_mag_card_printing'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_loyalty_mgmt_database'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_statement_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_application_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_equipment_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_misc_supplies'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_merch_prov_art_setup_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_news_provider_artwork_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_training_fee'); ?></th>
			<th><?php echo $this->Paginator->sort('gc_plan'); ?></th>

		</tr>
		<?php foreach ($giftCards as $giftCard): ?>
			<tr>
				<td><?php echo h($giftCard['GiftCard']['id']); ?>&nbsp;</td>
				<td>
					<?php echo $this->Html->link($giftCard['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $giftCard['Merchant']['id'])); ?>
				</td>
				<td><?php echo h($giftCard['GiftCard']['gc_mid']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_magstripe_loyalty_item_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_magstripe_gift_item_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_chip_card_one_rate']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_chip_card_gift_item_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_chip_card_loyalty_item_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_smart_card_printing']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_mag_card_printing']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_loyalty_mgmt_database']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_statement_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_application_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_equipment_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_misc_supplies']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_merch_prov_art_setup_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_news_provider_artwork_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_training_fee']); ?>&nbsp;</td>
				<td><?php echo h($giftCard['GiftCard']['gc_plan']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
	</table>	
</div>