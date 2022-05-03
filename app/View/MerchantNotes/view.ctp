
<input type="hidden" id="thisViewTitle" value="<?php echo __('View Merchant Note'); ?>" />
<div>

	<div>
		<li><?php echo $this->Html->link(__('Edit Merchant Note'), array('action' => 'edit', $merchantNote['MerchantNote']['id'])); ?> </li>

	</div>
	<dl>
		<!--dt><?php /* echo __('Id'); ?></dt>
  <dd>
  <?php echo h($merchantNote['MerchantNote']['id']); ?>
  &nbsp;
  </dd>
  <dt><?php echo __('Merchant Note Id'); ?></dt>
  <dd>
  <?php echo h($merchantNote['MerchantNote']['merchant_note_id']); ?>
  &nbsp;
  </dd>
  <dt><?php echo __('Note Type'); ?></dt>
  <dd>
  <?php echo h($merchantNote['MerchantNote']['note_type']); ?>
  &nbsp;
  </dd>
  <dt><?php echo __('User'); ?></dt>
  <dd>
  <?php echo $this->Html->link($merchantNote['User']['id'], array('controller' => 'users', 'action' => 'view', $merchantNote['User']['id'])); ?>
  &nbsp;
  </dd>
  <dt><?php echo __('Merchant'); ?></dt>
  <dd>
  <?php echo $this->Html->link($merchantNote['Merchant']['merchant_mid'], array('controller' => 'merchants', 'action' => 'view', $merchantNote['Merchant']['id']));
 * 
 */ ?>
				 
			&nbsp;
		</dd -->
		<dt><?php echo __('Note Date'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['note_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Note'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['note']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Note Title'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['note_title']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('General Status'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['general_status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date Changed'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['date_changed']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Critical'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['critical']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Note Sent'); ?></dt>
		<dd>
			<?php echo h($merchantNote['MerchantNote']['note_sent']); ?>
			&nbsp;
		</dd>
	</dl>
</div>


