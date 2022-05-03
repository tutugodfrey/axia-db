
<input type="hidden" id="thisViewTitle" value="<?php echo __('View Virtual Check'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit Virtual Check'), array('action' => 'edit', $virtualCheck['VirtualCheck']['id'])); ?> </li>

	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Merchant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($virtualCheck['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $virtualCheck['Merchant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vc Mid'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['vc_mid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vc Web Based Rate'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['vc_web_based_rate']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vc Web Based Pi'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['vc_web_based_pi']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vc Monthly Fee'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['vc_monthly_fee']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vc Gateway Fee'); ?></dt>
		<dd>
			<?php echo h($virtualCheck['VirtualCheck']['vc_gateway_fee']); ?>
			&nbsp;
		</dd>
	</dl>
</div>


