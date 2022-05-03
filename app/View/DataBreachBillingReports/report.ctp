<?php

	if (!empty($results)) {
		if ($type == 'axiatech') {
			$headers = array(
				'UMcommand', 'UMinvoice', 'UMdescription', 'UMname',
				'CustomerNumber', 'UMrouting', 'UMaccount', 'UMamount'
			);

			echo $this->Csv->row($headers);

			$counter = 0;

			foreach ($results as $result):
				$row = array();

				// UMcommand
				$row[] = "check:sale";
				// UMinvoice
				$row[] = date('Ymd') . $counter++;
				// UMdescription
				$row[] = date('m-y') . "Data Breach Program";
				// UMname
				$row[] = h($result['Merchant']['merchant_dba']);
				// CustomerNumber
				$row[] = h($result['Merchant']['merchant_mid']);
				// UMrouting
				$row[] = h($result['MerchantBank']['fees_routing_number']);
				// UMaccount
				$row[] = h($result['MerchantBank']['fees_dda_number']);
				// UMamount
				$row[] = h($result['MerchantPci']['insurance_fee']);

				echo $this->Csv->row($row);

			endforeach;
		} elseif ($type == 'axiapayments') {
			$xxStrMonth = date("M", strtotime("-1 month", time()));

			if (date("m") == '01') {
				$xxStrYear = date("Y", strtotime("-1 year", time()));
			} else {
				$xxStrYear = date("Y");
			}

			$xxStr = str_pad("XX", 10) . str_pad("Data Breach Program " . $xxStrMonth . " " . $xxStrYear, 88) . "\r\n";
			$fhStr = str_pad("FH", 5) . date('YmdHi') . str_pad("A1015920720DataBreachProgram", 81) . "\r\n";

			echo $xxStr;
			echo $fhStr;

			$total = 0;
			$rowCount = 0;

			foreach ($results as $result):
				//output the csv
				$str = str_pad("TD", 5);
				$str .= h(substr($result['Merchant']['merchant_mid'], 1, 15));
				$str .= h($result['MerchantBank']['fees_routing_number']);
				$str .= "DDA";
				$str .= h(str_pad($result['MerchantBank']['fees_dda_number'], 17));
				$str .= date('Ymd');
				$str .= "D";
				$str .= h(str_pad(number_format($result['MerchantPci']['insurance_fee'], 2, '.', ''), 11));
				$str .= h(str_pad(substr($result['Merchant']['merchant_mid'], 1, 15), 31));
				$str .= "\r\n";

				echo $str;

				$total += $result['MerchantPci']['insurance_fee'];
				$rowCount += 1;
			endforeach;

			$total = number_format($total, 2, '.', '');
			$tsStr = str_pad("TS", 5) . str_pad($rowCount, 6) . str_pad($total, 13) . str_pad($rowCount, 6) . str_pad($total, 13) . str_pad("0", 6) . str_pad("0.00", 51) . "\r\n";
			$str = "XX         End of File                                                                              ";

			echo $tsStr;
			echo $str;
		}
	} else {
		$this->Html->addCrumb(__('Data Breach Billing Reports'), array(
			'plugin' => false,
			'controller' => $this->name,
			'action' => $this->action
		));

		echo $this->Html->link('Axia Tech Data Breach Program Billing Report',
			array(
				'controller' => 'DataBreachBillingReports',
				'action' => 'report?type=axiatech'
			)
		);

		echo "</br></br>";

		echo $this->Html->link('Axia Payments Data Breach Program Billing Report',
			array(
				'controller' => 'DataBreachBillingReports',
				'action' => 'report?type=axiapayments'
			)
		);
	}
