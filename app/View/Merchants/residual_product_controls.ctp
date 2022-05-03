<?php
echo $this->Form->create();
?>

<table>
	<thead>
		<tr>
			<td>&nbsp;</td>
			<td>
				<?php echo __('Rep Parameters'); ?>
			</td>
			<td>
				<?php echo __('Manager 1 Parameters'); ?>
			</td>
			<td>
				<?php echo __('Manager 2 Parameters'); ?>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>

		</tr>
		<tr>
			<td>
				<?php echo __('Product'); ?>
			</td>
			<td>
				<?php echo __('Do Not Display'); ?>
			</td>
			<td>
				<?php echo __('Enabled for Rep'); ?>
			</td>
			<td>
				<?php echo __('TSYS'); ?>
			</td>
			<td>
				<?php echo __('Sage'); ?>
			</td>
			<td>
				<?php echo __('Direct Connect'); ?>
			</td>
			<td>
				<?php echo __('TSYS'); ?>
			</td>
			<td>
				<?php echo __('Sage'); ?>
			</td>
			<td>
				<?php echo __('Direct Connect'); ?>
			</td>
			<td>
				<?php echo __('TSYS'); ?>
			</td>
			<td>
				<?php echo __('Sage'); ?>
			</td>
			<td>
				<?php echo __('Direct Connect'); ?>
			</td>
			<td>
				<?php echo __('Rep %'); ?>
			</td>
			<td>
				<?php echo __('Multiple'); ?>
			</td>
			<td>
				<?php echo __('Mgr 1 %'); ?>
			</td>
			<td>
				<?php echo __('Mgr 2 %'); ?>
			</td>
		</tr>
	</thead>
	<tbod>
		<?php foreach ($products as $product): ?>
			<tr>
				<td>

				</td>

				<td>
					<?php
					echo $this->Form->input('do_no_display', array(
							  'type' => 'checkbox',
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('tier_products', array(
							  'type' => 'checkbox',
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('enabled_for_rep', array(
							  'type' => 'checkbox',
							  'label' => false,
							)
					);
					?>
				</td>

				<?php /* Rep Parameters % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input('rep_params_gross_profit_tsys', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('rep_params_gross_profit_sage', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('rep_params_gross_profit_dc', array(
							  'label' => false,
							)
					);
					?>
				</td>

				<?php /* Manager 1 Params % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input('manager1_params_gross_profit_tsys', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('manager1_params_gross_profit_sage', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('manager1_params_gross_profit_dc', array(
							  'label' => false,
							)
					);
					?>
				</td>

				<?php /* Manager 2 Params % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input('manager2_params_gross_profit_tsys', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('manager2_params_gross_profit_sage', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('manager2_params_gross_profit_dc', array(
							  'label' => false,
							)
					);
					?>
				</td>

				<?php /* Manual Override */ ?>
				<td>
					<?php
					echo $this->Form->input('override_rep_percentage', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('override_multiple', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('override_manager1', array(
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input('override_manager2', array(
							  'label' => false,
							)
					);
					?>
				</td>

			</tr>
		<?php endforeach; ?>
	</tbod>
</table>

<?php
echo $this->Form->submit(__('Submit'));
echo $this->Form->end();
?>