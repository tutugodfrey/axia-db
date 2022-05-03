
<input type="hidden" id="thisViewTitle" value="<?php echo __('View Merchant Reject Line'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit Merchant Reject Line'), array('action' => 'edit', $merchantRejectLine['MerchantRejectLine']['id'])); ?> </li>

	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rejectid'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['rejectid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Fee'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['fee']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Statusid'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['statusid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status Date'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['status_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Notes'); ?></dt>
		<dd>
			<?php echo h($merchantRejectLine['MerchantRejectLine']['notes']); ?>
			&nbsp;
		</dd>
	</dl>
</div>


