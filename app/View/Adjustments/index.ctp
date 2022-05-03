<div class="Adjustments">
	<?php
		echo $this->Form->create('Adjustment',
			array(
				'type' => 'get',
				'url' => '/Adjustments/index/' . $userId,
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => true,
					'class' => 'form-control'
				),
				'class' => 'well well-sm form-inline col-xs-7 col-md-12'
			)
		);
	?>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="4"><?php echo __('Adjustments') ?></td>
		</tr>
		<tr>
			<td>
				<?php echo __('Begin') ?>
			</td>
			<td>
				<?php echo __('End') ?>
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td width="10%">
				<?php
					echo $this->Form->input(
						'begin_year',
						array(
							'options' => $years,
							'label' => false,
							'type' => 'select',
							'selected' => !empty($beginYearSelected) ? $beginYearSelected : null,
							'default' => date('Y')
						)
					);
				?>

				<?php
					echo $this->Form->input(
						'begin_month',
						array(
							'options' => $months,
							'label' => false,
							'type' => 'select',
							'selected' => !empty($beginMonthSelected) ? $beginMonthSelected : null,
							'default' => date('m')
						)
					);
				?>
			</td>

			<td width="10%">
				<?php
					echo $this->Form->input(
						'end_year',
						array(
							'options' => $years,
							'label' => false,
							'type' => 'select',
							'selected' => !empty($endYearSelected) ? $endYearSelected : null,
							'default' => date('Y')
						)
					);
				?>

				<?php
					echo $this->Form->input(
						'end_month',
						array(
							'options' => $months,
							'label' => false,
							'type' => 'select',
							'selected' => !empty($endMonthSelected) ? $endMonthSelected : null,
							'default' => date('m')
						)
					);
				?>
			</td>

			<td style="width: 40%">
				<?php
					echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default btn-sm', 'div' => array('class' => 'form-group')));
				?>
			</td>
			<td></td>

		</tr>
	</table>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('Adjustment.adj_date', 'Date'); ?></th>
			<th><?php echo $this->Paginator->sort('Adjustment.adj_amount', 'Amount'); ?></th>
			<th><?php echo $this->Paginator->sort('Adjustment.adj_description', 'Description'); ?></th>
		</tr>
		<?php
			foreach ($adjustmentList as $adjustment) {
				echo "<tr>";

					echo "<td>" . h($adjustment['Adjustment']['adj_date']) . "</td>";
					echo "<td>" . h($adjustment['Adjustment']['adj_amount']) . "</td>";
					echo "<td>" . h($adjustment['Adjustment']['adj_description']) . "</td>";

					echo "<td>";
					echo $this->Html->editImageLink(array('action' => 'edit', $adjustment['Adjustment']['id']));
					echo $this->Html->link('<span class="glyphicon glyphicon-trash"></span>',
							array(
								'controller' => 'Adjustments',
								'action' => 'delete',
								$adjustment['Adjustment']['id'],
								$adjustment['Adjustment']['user_id']
							),
							array(
								'class' => "btn-xs btn-danger",
								'data-original-title' => "Delete",
								'data-placement' => "top",
								'data-toggle' => "tooltip",
								'escape' => false,
								'confirm' => 'Are you sure you wish to delete this?'
							)
						);
					echo "</td>";
				echo "</tr>";
			}
		?>
	</table>
	<p>
	
	<table cellpadding="0" cellspacing="0">
		<?php
			echo $this->Form->create('Adjustment',
				array(
					'url' => '/Adjustments/add/' . $userId,
					'inputDefaults' => array(
						'div' => 'form-group',
						'wrapInput' => true,
						'class' => 'form-control'
					),
					'class' => 'well well-sm form-inline col-xs-7 col-md-12'
				)
			);
		?>
		<tr>
			<td>
				<?php echo __('Add an adjustment') ?>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo __('Date') ?></td>
			<td><?php echo __('Amount') ?></td>
			<td><?php echo __('Description') ?></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="width: 15%">
				<?php
					echo $this->Form->input(
						'adjustment_month',
						array(
							'options' => $months,
							'label' => false,
							'type' => 'select',
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
							'default' => date('Y')
						)
					);
				?>
			</td>

			<td style="width: 10%">
				<?php
					echo $this->Form->input(
						'adjustment_amount',
						array(
							'label' => false,
							'size' => 10
						)
					);
				?>
			</td>

			<td style="width: 20%">
				<?php
					echo $this->Form->input(
						'adjustment_description',
						array(
							'label' => false,
							'size' => 40
						)
					);
				?>
			</td>

			<td width="40%">
				<?php
					echo $this->Form->end(array('label' => 'Save Adjustment', 'class' => 'btn btn-success', 'div' => array('class' => 'form-group')));
				?>
			</td>
			<td></td>
		</tr>
	</table>

	<?php
		echo $this->Element('paginatorBottomNav');
	?>
</div>
