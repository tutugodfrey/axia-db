
<input type="hidden" id="thisViewTitle" value="<?php echo __('View Merchant Reference'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit Merchant Reference'), array('action' => 'edit', $merchantReference['MerchantReference']['id'])); ?> </li>

	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant Ref Seq Number'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['merchant_ref_seq_number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($merchantReference['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantReference['Merchant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant Ref Type'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['merchant_ref_type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Bank Name'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['bank_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Person Name'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['person_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Phone'); ?></dt>
		<dd>
			<?php echo h($merchantReference['MerchantReference']['phone']); ?>
			&nbsp;
		</dd>
	</dl>
</div>


