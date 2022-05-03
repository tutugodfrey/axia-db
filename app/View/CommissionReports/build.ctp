<div class="CommissionReports">
	<?php
		echo $this->Form->create('CommissionReport',
			array(
				'type' => 'get',
				'url' => array(
					'controller' => 'CommissionReports',
					'action' => 'build'
				),
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => true,
					'class' => 'form-control'
				),
				'class' => 'well well-sm form-inline col-xs-7 col-md-7'
			)
		);
	?>

	<?php if (!empty($response)) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><font size="+1"><?php echo $response ?></font></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	<?php } ?>

	<?php
		echo $this->Form->input(
			'c_year',
			array(
				'options' => $years,
				'label' => 'Year',
				'type' => 'select',
				'selected' => !empty($yearSelected) ? h($yearSelected): null,
				'default' => date('Y')
			)
		);

		echo $this->Form->input(
			'c_month',
			array(
				'options' => $months,
				'label' => 'Month',
				'type' => 'select',
				'selected' => !empty($monthSelected) ? h($monthSelected) : null,
				'default' => ''
			)
		);

		echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default btn-sm', 'div' => array('class' => 'form-group')));
	?>

	<table class="table table-condensed table-hover">
		<tr>
			<th><?php echo 'Year'; ?></th>
			<th><?php echo 'Month'; ?></th>
			<th><?php echo 'Active?'; ?></th>
			<th class = "text-right"><?php echo __('Functions'); ?></th>
		</tr>
		<?php
			foreach ($commissionReportList as $commissionReport) {
				$year = $commissionReport['c_year'];
				$year = preg_replace('/\.\d+/', '', $year);

				$month = $commissionReport['c_month'];
				$month = preg_replace('/\.\d+/', '', $month);

				if ($month != null && $month != '') {
					$month = date('M', mktime(0, 0, 0, $month, 10));
				}

				echo "<tr>";
				echo "<td>" . $year . "</td>";
				echo "<td>" . $month . "</td>";
				
				if (Hash::get($commissionReport, 'status') == 'A') {
					echo "<td>Active</td>";
				} elseif (Hash::get($commissionReport, 'status') == 'I') {
					echo "<td>Inactive</td>";
				} elseif (!isset($commissionReport['status'])) {
					echo "<td><span class='help-block small'><i>N/A<i></span></td>";
				}

				echo "<td class = 'text-right'>";
				if (isset($commissionReport['status'])) {
					if ($commissionReport['status'] == 'A') {
						echo $this->Html->link('<span class="glyphicon glyphicon-ban-circle"></span>',
							array(
								'controller' => 'CommissionReports',
								'action' => 'build',
								'?' => array(
									'action' => 'deactivate',
									'year' => $commissionReport['c_year'],
									'month' => $commissionReport['c_month']
								)
							),
							array(
								'class' => 'btn btn-xs btn-default',
								'data-original-title' => "Deactivate",
								'data-placement' => "top",
								'data-toggle' => "tooltip",
								'escape' => false,
								'confirm' => 'Are you sure you wish to deactivate this report?'
							)
						);
					} elseif ($commissionReport['status'] == 'I') {
						echo $this->Html->link('<span class="glyphicon glyphicon-off"></span>',
							array(
								'controller' => 'CommissionReports',
								'action' => 'build',
								'?' => array(
									'action' => 'activate',
									'year' => $commissionReport['c_year'],
									'month' => $commissionReport['c_month']
								)
							),
							array(
								'class' => 'btn btn-xs btn-success',
								'data-original-title' => "Activate",
								'data-placement' => "top",
								'data-toggle' => "tooltip",
								'escape' => false,
								'confirm' => 'Are you sure you wish to activate this report?'
							)
						);
					
					}
				}
				echo $this->Html->link("<span class='glyphicon glyphicon-trash'></span>",
					array(
						'controller' => 'CommissionReports',
						'action' => 'build',
						'?' => array(
							'action' => 'delete',
							'year' => $commissionReport['c_year'],
							'month' => $commissionReport['c_month']
						)
					),
					array(
						'class' => "btn btn-xs btn-danger",
						'data-original-title' => "Delete",
						'data-placement' => "top",
						'data-toggle' => "tooltip",
						'escape' => false,
						'confirm' => 'Are you sure you wish to delete this report?'
					)
				);
				echo "</td>";

				echo "</tr>";
			}
		?>
	</table>
	<div class="row well well-sm">
		<?php echo $this->element('AjaxElements/Admin/bg_processes_tracker')?>
		<div class="col-lg-offset-1 col-md-offset-1 col-xs-5 col-sm-5 col-md-3 col-lg-3">
			<?php
			echo $this->Html->tag('div', null, ['class' => 'panel panel-info']);
				echo $this->Form->create('CommissionReport',
					array(
						'type' => 'post',
						'url' => array(
							'controller' => 'CommissionReports',
							'action' => 'build'
						),
						'inputDefaults' => array(
							'div' => 'form-group',
							'wrapInput' => true,
							'class' => 'form-control'
						),
						'class' => 'form-inline panel-body text-center'
					)
				);
				echo $this->Html->tag('span', "Build New Commissions Data", ['class' => 'col-md-12 col-sm-12 strong text-primary']);
				echo $this->Html->tag('hr', null, ['class' => 'alert-info']);
				echo $this->Form->input(
					'c_year',
					array(
						'options' => $years,
						'label' => 'Year',
						'type' => 'select',
						'default' => date('Y')
					)
				);

				echo $this->Form->input(
					'c_month',
					array(
						'options' => $months,
						'label' => 'Month',
						'type' => 'select',
						'default' => date('m')
					)
				);

				echo $this->Form->end(array('label' => 'Build commission report', 'class' => 'btn btn-success', 'div' => array('class' => 'form-group')));
				echo $this->Html->tag('/div'); //closing panel panel-info div
			?>
		</div>
	</div>
</div>
