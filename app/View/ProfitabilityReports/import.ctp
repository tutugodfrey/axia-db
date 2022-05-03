<input type="hidden" id="thisViewTitle"
	   value="Profitability Report Data Import <a class='pull-right' target='_blank' style='color:white' href='/Documentations/help#ProfitabilityReport'><span class='glyphicon glyphicon-question-sign
'></span> Help</a>"/>
<div class="col-md-12 col-sm-12 col-xs-12  well well-sm">
<?php
	echo $this->Html->tag('span', 'List Overview of Existing Data', array('class' => 'col-md-12 col-sm-12 strong text-primary'));
	echo $this->Html->tag('hr', null, array('class' => 'alert-info')); //applying alert-info to get a blue stripe effect
	echo $this->Form->createFilterForm('ProfitabilityReport', array('class' => 'form-inline col-xs-7 col-md-7'));
	echo $this->Form->input('pr_years', array('label' => 'Year', 'type' => 'date', 'dateFormat' => 'Y', 'minYear' => 2018, 'maxYear' => date('Y')));
	echo $this->Form->input('pr_months', array('label' => 'Month', 'empty' => '--', 'type' => 'date', 'dateFormat' => 'M', 'minYear' => 2018, 'maxYear' => date('Y')));
	echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default btn-sm', 'div' => array('class' => 'form-group')));
?>

</div>
<table class="table table-condensed table-hover">
	<?php if (!empty($prList)) { ?>
	<tr>
		<th><?php echo __('Year'); ?></th>
		<th><?php echo __('Month'); ?></th>
		<th class = 'text-right'><?php
			if ($this->Rbac->isPermitted('ProfitabilityReports/delete')) {
				echo __('Functions');
			}
		?>
		</th>
	</tr>
	<?php
		foreach ($prList as $data) {
			$month = date("M", mktime(0, 0, 0, Hash::get($data, 'ProfitabilityReport.month'), 10));
			$tableCells = $this->Html->tag('td', Hash::get($data, 'ProfitabilityReport.year')) . $this->Html->tag('td', $month);
			if ($this->Rbac->isPermitted('ProfitabilityReports/delete')) {
				$deleteBtn = $this->Form->postLink('<span class="glyphicon glyphicon-trash"></span>', 
					array('action' => 'delete', Hash::get($data, 'ProfitabilityReport.year'), Hash::get($data, 'ProfitabilityReport.month')), 
					array(
						'class' => 'btn btn-xs btn-danger pull-right',
						'data-original-title' => "Delete all",
						'data-placement' => "top",
						'data-toggle' => "tooltip",
						'escape' => false,
						'confirm' => "Delete all data for $month " . Hash::get($data, 'ProfitabilityReport.year') . '?'
				));
				$tableCells .= $this->Html->tag('td', $deleteBtn);
			}
			echo $this->Html->tag('tr', $tableCells);
		}
	} else {
		echo $this->Html->tag('tr', $this->Html->tag('th', '<i>'.__('No search results found').'</i>', array('class' => 'bg-warning text-center text-muted')));
	}
	?>
</table>

<?php
	echo $this->Form->createFilterForm('ProfitabilityReport',
		array(
			'type' => 'file',
			'url' => array(
				'controller' => 'ProfitabilityReports',
				'action' => 'import'
			)
		)
	);

	echo $this->Html->tag('span', 'Import New Report Data', array('class' => 'col-md-12 col-sm-12 strong text-primary'));
	echo $this->Html->tag('hr', null, array('class' => 'alert-info')); //applying alert-info to get a blue stripe effect
	echo $this->Form->input('year', array('label' => 'Year', 'type' => 'date', 'dateFormat' => 'Y', 'minYear' => 2018, 'maxYear' => date('Y')));
	echo $this->Form->input('month', array('label' => 'Month', 'type' => 'date', 'dateFormat' => 'M', 'minYear' => 2018, 'maxYear' => date('Y')));
	echo $this->Form->input('profitabilityReportFile', array('label' => 'Data file', 'class' => 'form-control btn-primary strong', 'type' => 'file', 'required' => 'required'));
	echo $this->Form->input('serverside_processing', array('label' => '<strong>Process data on server</strong>', 'after' => '<div class="small text-muted"><i>(Check if file has > 3000 rows)</i></div>','wrapInput' => 'col-md-12 col-sm-12','type' => 'checkbox', 'checked' => true));
	echo $this->Form->end(array('label' => 'Upload Data', 'class' => 'btn btn-success','div' => array('class' => 'form-group')));
?>
