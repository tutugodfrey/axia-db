<input type="hidden" id="thisViewTitle"
	   value="Residual Report Admin <a class='pull-right' target='_blank' style='color:white' href='/Documentations/help#RReport'><span class='glyphicon glyphicon-question-sign
'></span> Help</a>"/>
<div class="ResidualReports">
	<div class="row well well-sm">
	<?php
	$goPrevYr = $this->Html->link('<span name="lftRgtNavArrows" class="glyphicon glyphicon-triangle-left text-success"></span>',
		array(
			'controller' => 'ResidualReports',
			'action' => 'upload',
			'?' => array('r_year' => $yearSelected - 1)
		),
		array(
			'data-original-title' => $yearSelected - 1,
			'data-placement' => "left",
			'data-toggle' => "tooltip",
			'escape' => false,
		)
	);
	$goNextYr = $this->Html->link('<span name="lftRgtNavArrows" class="glyphicon glyphicon-triangle-right text-success"></span>',
		array(
			'controller' => 'ResidualReports',
			'action' => 'upload',
			'?' => array('r_year' => $yearSelected + 1)
		),
		array(
			'data-original-title' => $yearSelected + 1,
			'data-placement' => "right",
			'data-toggle' => "tooltip",
			'escape' => false,
		)
	);
		echo $this->Form->create('ResidualReport',
			array(
				'type' => 'get',
				'url' => array(
					'controller' => 'ResidualReports',
					'action' => 'upload'
				),
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => true,
					'class' => 'form-control'
				),
				'class' => 'form-inline col-xs-7 col-md-7'
			)
		);
	?>
	<?php if (!empty($response)) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><font size="+1"><?php echo h($response) ?></font></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	<?php } ?>

	<?php
		echo $this->Form->input(
			'r_year',
			array(
				'options' => $years,
				'label' => 'Year',
				'type' => 'select',
				'selected' => !empty($yearSelected) ? $yearSelected : null,
				'default' => date('Y')
			)
		);

		echo $this->Form->input(
			'r_month',
			array(
				'options' => $months,
				'label' => 'Month',
				'type' => 'select',
				'selected' => !empty($monthSelected) ? $monthSelected : null,
				'default' => ''
			)
		);

		echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default btn-sm', 'div' => array('class' => 'form-group')));
		if ($this->Rbac->isPermitted('app/actions/ResidualReports/deleteMany')) {
			echo $this->element('Layout/ReportsAdmin/deleteManyForm');
		}
	?>
	</div>
	<h2 class="text-center panel-heading">
		<?php echo $goPrevYr;?><span class="label list-group-item-success panel-heading">
			<span class="glyphicon glyphicon-calendar"></span> 
			 <?php 
			 	echo (!empty($residualReportList))? $yearSelected: $yearSelected . ' (No data)';
			 	//Hidden field used for JS deleteMany 
			 	echo $this->Form->hidden('current_year', ['value' => h(Hash::get($residualReportList, '0.ResidualReport.r_year'))]);
			 ?>
		</span>
		<?php echo $goNextYr; ?>
	</h2>
		<?php if (!empty($residualReportList)) {
			$curMonth = null;
			$nextRecordMonth = null;
			foreach ($residualReportList as $idx => $residualReport) {
				$month = $residualReport['ResidualReport']['r_month'];
				$curMonth = $residualReport['ResidualReport']['r_month'];
				$nextRecordMonth = Hash::get($residualReportList, $idx + 1 . '.ResidualReport.r_month');
				$month = preg_replace('/\.\d+/', '', $month);
				if ($month != null && $month != '') {
					$month = date('F', mktime(0, 0, 0, $month, 10));
				}
				if ($residualReport['ResidualReport']['status'] == $statusActive) {
					$satusBtnHtml = '<img src="/img/green_orb.gif">';
					$dataToggleTitle = 'Deactivate';
				} else {
					$satusBtnHtml = '<img src="/img/red_orb.png">';
					$dataToggleTitle = 'Activate';
				}
				$cells[] = array(
					h($residualReport['ProductsServicesType']['products_services_description']),
					array(
						$this->Form->postLink($satusBtnHtml,
							array(
								'controller' => 'ResidualReports',
								'action' => 'toggleStatus',
								$residualReport['ResidualReport']['status'],
								$residualReport['ResidualReport']['r_year'],
								$residualReport['ResidualReport']['r_month'],
								$residualReport['ProductsServicesType']['id']
							),
							array(
								'class' => 'btn btn-xs btn-default',
								'data-original-title' => $dataToggleTitle,
								'data-placement' => "top",
								'data-toggle' => "tooltip",
								'escape' => false,
								'confirm' => 'Are you sure you wish to deactivate this report?'
							)
						) .
						$this->Html->link('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', 
							array(
								'data-month' => $residualReport['ResidualReport']['r_month'],
								'data-product-id' => $residualReport['ProductsServicesType']['id'],
								'name' => 'deleteBtnGroup',
								'class' => 'btn btn-xs btn-default',
								'escape' => false,
								'onClick' => "toggleToBeDeleted(this)"
							)),
						array('class' => 'text-right')
					)
				);
				if ($curMonth + 1 == $nextRecordMonth || is_null($nextRecordMonth)) {
					//we are at the last record of the curMonth or at the end of the list when nextRecordMonth is null
					//echo everything for curMonth
					echo '<div class ="col col-md-3 col-sm-4 col-xs-4">
							<div class ="panel panel-success">
								<div class ="panel-heading">
									<h3 class="panel-title text-center">' . $month . '</h3>
								</div>
								<table class="table table-condensed table-hover">';
									echo $this->Html->tableHeaders(array('Product', array('Functions' => array('class' => 'text-right'))));
									echo $this->Html->tableCells($cells) .
								'</table>
							</div>
						</div>';
					//for small devices show 3 columns properly lined up
					echo ($curMonth%3===0)? '<div class="clearfix visible-sm-block visible-xs-block"></div>' : null;
					//for larger devices show 4 columns properly lined up
					echo ($curMonth%4===0)? '<div class="clearfix visible-md-block visible-lg-block"></div>':null;
					//reset variables
					$cells = [];
				}
		 	} 
		 };
?>
<div class="clearfix"></div>
<?php if (!empty($residualReportList)) : ?>
		 <h2 class="text-center panel-heading">
		<?php echo $goPrevYr; ?><span class="label list-group-item-success panel-heading">
			<span class="glyphicon glyphicon-calendar"></span> 
			 <?php echo $yearSelected; ?>
		</span>
		<?php echo $goNextYr; ?>
	</h2>
<?php endif; ?>
	<div class="row well well-sm">
		<?php echo $this->element('AjaxElements/Admin/bg_processes_tracker')?>
		<div class="col-lg-offset-1 col-md-offset-1 col-xs-5 col-sm-5 col-md-3 col-lg-4 ">
			<?php
				echo $this->Html->tag('div', null, ['class' => 'panel panel-info']);
				echo $this->Form->create('ResidualReport',
					array(
						'type' => 'file',
						'url' => array(
							'controller' => 'ResidualReports',
							'action' => 'upload'
						),
						'inputDefaults' => array(
							'div' => 'form-group',
							'wrapInput' => true,
							'class' => 'form-control'
						),
						'class' => 'form-inline panel-body'
					)
				);
				
				echo $this->Html->tag('span', "Upload New Report Data", ['class' => 'col-md-12 col-sm-12 strong text-primary']);
				echo $this->Html->tag('hr', null, ['class' => 'alert-info']);
				echo $this->Form->input(
					'r_year',
					array(
						'options' => $years,
						'label' => 'Year',
						'type' => 'select',
						'default' => date('Y')
					)
				);

				echo $this->Form->input(
					'r_month',
					array(
						'options' => $months,
						'label' => 'Month',
						'type' => 'select',
						'default' => date('m')
					)
				);

				echo $this->Form->input(
					'products_services_type_id',
					array(
						'label' => 'Product',
						'style' => "width:180px;"
					)
				);

				echo $this->Form->input(
					'residualReportFile',
					array(
						'label' => 'Data file',
						'type' => 'file',
						'style' => "width:180px;"
					)
				);
				echo $this->Form->end(array('label' => 'Upload Data', 'class' => 'btn btn-success btn-sm', 'div' => array('class' => 'form-group')));
				echo $this->Html->tag('/div'); //closing panel panel-info div
			?>
		</div>
	</div>
</div>
