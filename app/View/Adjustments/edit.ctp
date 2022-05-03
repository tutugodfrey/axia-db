<div class="Adjustments panel panel-default">
	<div class="panel-heading strong"><?php echo __('Edit Adjustment') ?></div>
		<?php
			echo $this->Form->create('Adjustment',
				array(
					'url' => '/Adjustments/edit/' . $adjustmentId,
					'inputDefaults' => array(
						'div' => 'form-group',
						'wrapInput' => true,
						'class' => 'form-control'
					),
					'class' => 'well well-sm form-inline col-xs-7 col-md-12'
				)
			);
		?>

		<table class="table table-condensed" style="width:auto">
			<tr>
				<th><?php echo __('Date') ?></th>
				<th><?php echo __('Amount') ?></th>
				<th><?php echo __('Description') ?></th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<td>
					<?php
						echo $this->Form->input(
							'adjustment_month',
							array(
								'options' => $months,
								'label' => false,
								'type' => 'select',
								'selected' => !empty($adjustmentMonth) ? $adjustmentMonth : null,
								'default' => date('m')
							)
						);
					?>

					<?php
						echo $this->Form->input(
							'adjustment_day',
							array(
								'options' => $days,
								'label' => false,
								'type' => 'select',
								'selected' => !empty($adjustmentDay) ? $adjustmentDay : null,
								'default' => date('d')
							)
						);
					?>

					<?php
						echo $this->Form->input(
							'adjustment_year',
							array(
								'options' => $years,
								'label' => false,
								'type' => 'select',
								'selected' => !empty($adjustmentYear) ? $adjustmentYear : null,
								'default' => date('Y')
							)
						);
					?>
				</td>

				<td>
					<?php
						echo $this->Form->input(
							'adjustment_amount',
							array(
								'label' => false,
								'value' => $adjustmentAmount
							)
						);
					?>
				</td>

				<td>
					<?php
						echo $this->Form->input(
							'adjustment_description',
							array(
								'label' => false,
								'value' => $adjustmentDescription
							)
						);
					?>
				</td>

				<td>
					<?php
						echo $this->Form->end(array('label' => 'Save Adjustment', 'class' => 'btn btn-success', 'div' => array('class' => 'form-group')));
					?>
				</td>
				<td></td>
			</tr>
		</table>
</div>
