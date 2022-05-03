
<div class="row">
	<div class="col-xs-12">
		<?php
		if (!empty($exportLinks)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}
		?>
		<?php
		echo $this->Form->createFilterForm(Inflector::singularize($this->name));

		echo $this->Form->input('dba_mid', [
			'label' => __('Merchant MID/DBA')
		]);
		if (!empty($partners)) {
			echo $this->Form->input('partners', [
				'options' => $partners,
				'label' => __('Partner'),
				'empty' => __('--'),
				"style" => "max-width:200px"
			]);
		}
		echo $this->element('Forms/OrganizationDrilldown');

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
			'div' => array('class' => 'input-group'),
			'class' => 'btn btn-sm btn-success',
			'after' => $this->Form->button('Generate as CSV only', array('type' => 'button', 'class' => 'btn btn-sm btn-default', 'id' => 'csvOutBtn'))
		));
		echo $this->Form->end();
		?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#csvOutBtn').on('click', function(){
			$('#ImportedDataCollectionReportForm').prop('action', 
				$('#ImportedDataCollectionReportForm').prop('action').replace('/report', '/report.csv?'));
			$('#ImportedDataCollectionReportForm').submit();
			$('#ImportedDataCollectionReportForm').prop('action', 
				$('#ImportedDataCollectionReportForm').prop('action').replace('/report.csv?', '/report'));
			$("#downloadingCsvAlert").remove();
			$('#ImportedDataCollectionReportForm').append('<div class="alert alert-info text-center" id="downloadingCsvAlert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Please wait..</strong> report CSV download will begin soon.</div>');
		});
	});
</script>