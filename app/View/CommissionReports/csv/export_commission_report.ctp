<?php
echo $this->element('Layout/Reports/applied_filters');
echo $this->Csv->emptyRow();
echo $this->element('CommissionReports/income');
echo $this->Csv->emptyRow();
echo $this->element('CommissionReports/rep_adjustments');
echo $this->Csv->emptyRow();
echo $this->element('CommissionReports/net_income');
echo $this->Csv->emptyRow();
echo $this->element('CommissionReports/commission_reports');
