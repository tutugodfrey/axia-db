<div class="row">
	<div class="col-xs-12">
		<?php
		echo $this->AssetCompress->css('custom-form-inputs', array(
			'raw' => (bool)Configure::read('debug')
		));
		//Load form imput control plugin
		echo $this->element('Layout/selectizeAssets');
		if ($resultCount < ResidualReport::MAX_RESULTS && !empty($exportLinks) && $this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}
		?>
		
		<?php
		echo $this->Form->createFilterForm('ResidualReport');
		echo $this->Form->input('products', array('label' => __('Products'), 'empty' => 'All Products'));
		echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
		echo $this->Form->input('dba_mid', array('label' => 'Merchant DBA or MID'));
		//When curent user is a partner this will be empty and we do not want to display it
		if (!empty($partners)) {
			echo $this->Form->input('partners', array('label' => __('Partner'), 'empty' => true));
		}
		echo "<div></div>";//break form
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

		echo '<div class="form-group">';
		echo $this->Form->submit(__('Generate'), array(
			'div' => array('class' => 'input-group'),
			'class' => 'btn btn-sm btn-success',
			'after' => $this->Form->button('Generate as CSV only', array('type' => 'button', 'class' => 'btn btn-sm btn-default', 'id' => 'csvOutBtn'))
		));
		echo '</div>';
		echo $this->Form->end();
		?>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('#csvOutBtn').on('click', function(){
			$('#ResidualReportReportForm').prop('action', 
				$('#ResidualReportReportForm').prop('action').replace('/report?', '/report.csv?'));
			$('#ResidualReportReportForm').submit();
			$('#ResidualReportReportForm').prop('action', 
				$('#ResidualReportReportForm').prop('action').replace('/report.csv?', '/report?'));
			$("#downloadingCsvAlert").remove();
			$('#ResidualReportReportForm').append('<div class="alert alert-info text-center" id="downloadingCsvAlert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Please wait..</strong> report CSV download will begin soon.</div>');
		});
	});
	$('#ResidualReportUserId').selectize();
	$('#ResidualReportUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("ResidualReport.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>