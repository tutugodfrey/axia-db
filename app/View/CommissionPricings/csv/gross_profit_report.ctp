<?php
echo $this->element('Layout/Reports/applied_filters');
echo $this->Csv->emptyRow();
if ($getRolledUpData == false) {
	echo $this->element('CommissionPricings/gross_profit_report');			
} else {
	echo $this->element('CommissionPricings/gpr_rolled_up');
}