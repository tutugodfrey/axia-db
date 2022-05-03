<div class="row">
	<div class="col-xs-12">
		<?php
		if (!empty($exportLinks) && $this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}
		echo $this->Form->createFilterForm('ProfitabilityReport');
		echo $this->Form->input('dba_mid', array('label' => 'Merchant DBA or MID'));
		echo $this->Form->input('from_date', array(
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->input('end_date', array(
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->submit(__('Generate'), array(
			'div' => array('class' => 'form-group'),
			'class' => 'btn btn-default'
		));
		echo $this->Form->end();
		?>
	</div>
</div>