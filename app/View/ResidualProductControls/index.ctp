<style>
	input[type="text"] {
		width: 50px;
	}
</style>
<script>
	$(document).ready(function() {
		$('input[type="text"].percent').change(function() {
			value = $(this).val();
			value = value.replace(/[^0-9\.]+/g, '');
			if (value.substr(-1) !== '%') {
				$(this).val(value + '%');
			}
		});
		$('input[type="text"]').trigger('change');
	});
</script>
<?php
echo $this->Form->create(null);
?>

<table>
	<thead>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="6">
				<?php echo __('Rep Parameters'); ?>
			</td>
			<td colspan="3">
				<?php echo __('Manager 1 Parameters'); ?>
			</td>
			<td colspan="3">
				<?php echo __('Manager 2 Parameters'); ?>
			</td>
			<td colspan="4">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				&nbsp;
			</td>
			<td colspan="3">
				<?php echo __('% of Gross Profit'); ?>
			</td>
			<td colspan="3">
				<?php echo __('% of Gross Profit'); ?>
			</td>
			<td colspan="3">
				<?php echo __('% of Gross Profit'); ?>
			</td>
			<td colspan="4">
				<?php echo __('Manual Override'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo __('Product'); ?>
			</td>
			<td>
				<?php echo __('Do Not Display'); ?>
			</td>
			<td>
				<?php echo __('Tier Products'); ?>
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
	<tbody>
		<?php $i = -1; ?>
		<?php foreach ($residualProductControls as $key => $control): ?>
			<tr>
				<td>
					<?php
					echo h($control['ProductServiceType']['products_services_description']);
					echo $this->Form->input($key . '.ResidualProductControl.id', array(
							  'type' => 'hidden',
							  'value' => $control['ResidualProductControl']['id']
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.do_no_display', array(
							  'type' => 'checkbox',
							  'label' => false,
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.tier_product', array(
							  'type' => 'checkbox',
							  'label' => false,
							  'class' => 'percent'
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.enabled_for_rep', array(
							  'type' => 'checkbox',
							  'label' => false,
							)
					);
					?>
				</td>

				<?php /* Rep Parameters % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.rep_params_gross_profit_tsys', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.rep_params_gross_profit_sage', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.rep_params_gross_profit_dc', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>

				<?php /* Manager 1 Params % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager1_params_gross_profit_tsys', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager1_params_gross_profit_sage', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager1_params_gross_profit_dc', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>

				<?php /* Manager 2 Params % of Gross Profit */ ?>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager2_params_gross_profit_tsys', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager2_params_gross_profit_sage', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.manager2_params_gross_profit_dc', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>

				<?php /* Manual Override */ ?>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.override_rep_percentage', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.override_multiple', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.override_manager1', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
				<td>
					<?php
					echo $this->Form->input($key . '.ResidualProductControl.override_manager2', array(
							  'label' => false,
							  'type' => 'text',
							  'class' => 'percent',
							)
					);
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
echo $this->Form->submit(__('Submit'));
echo $this->Form->end();
?>