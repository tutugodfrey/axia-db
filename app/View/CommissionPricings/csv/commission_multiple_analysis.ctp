<?php
echo $this->element('Layout/Reports/applied_filters');
echo $this->element('CommissionPricings/projected_residuals');
echo $this->Csv->emptyRow();
echo $this->element('Layout/Reports/applied_filters');
echo $this->element('CommissionPricings/actual_residuals');