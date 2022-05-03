<?php 
if ($this->request->data('ResidualReport.roll_up_view')) {
	echo $this->element('ResidualReports/rollup_report_content');
} else {
	echo $this->element('ResidualReports/content');
}
?>