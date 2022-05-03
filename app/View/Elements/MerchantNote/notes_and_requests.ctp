<?php
if (!isset($output)) {
	$output = null;
}

$isCsvExport = ($output === GUIbuilderComponent::OUTPUT_CSV);

if (!empty($merchantNotes)) {
	$headers = array();
	$cells = array();
	$slectedType = Hash::get($this->request->query, 'action_type');
	
	$headers[] = ($isCsvExport)? 'MID': $this->Paginator->sort('Merchant.merchant_mid', 'MID');
	$headers[] = ($isCsvExport)? 'DBA': $this->Paginator->sort('Merchant.merchant_dba', 'DBA');
	$headers[] = ($isCsvExport)? 'User': $this->Paginator->sort('User.user_first_name', 'User');

	if (strpos(Hash::get($actionTypes, $slectedType), 'Invoice') !== false) {
		$headers[] = ($isCsvExport)? 'Date Submitted' : $this->Paginator->sort('MerchantAch.ach_date', 'Date Submitted');
		$headers[] = 'Type';
		$headers[] = ($isCsvExport)? 'Status' :$this->Paginator->sort('MerchantAch.status', 'Status');
	} elseif (!empty($slectedType)) {
		$headers[] = ($isCsvExport)? 'Date Submitted' : $this->Paginator->sort('MerchantNote.note_date', 'Date Submitted');
		$headers[] = ($isCsvExport)? 'Type' : $this->Paginator->sort('NoteType.note_type_description', 'Type');
		$headers[] = ($isCsvExport)? 'Status' : $this->Paginator->sort('MerchantNote.general_status', 'Status');
	} else {
		$headers[] = 'Date Submitted';
		$headers[] = 'Type';
		$headers[] = 'Status';
	}

	$headers[] = 'Title';
	$headers[] = 'Details';
	$count  = count($merchantNotes);
	//Memoize outputted record ids to avoid ouput duplication 
	$processedRecords = [];
	foreach ($merchantNotes as $idx => $merchantNote) {
		$row = [];
		if (!empty(Hash::get($merchantNotes, "$idx.MerchantNote.id")) && empty(Hash::get($processedRecords, Hash::get($merchantNotes, "$idx.MerchantNote.id")))) {
			$processedRecords[Hash::get($merchantNotes, "$idx.MerchantNote.id")] = Hash::get($merchantNotes, "$idx.MerchantNote.id");
			//add Merchant note row to cells
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'Merchant.merchant_mid') : $this->Html->link(Hash::get($merchantNote, 'Merchant.merchant_mid'), array('controller' => 'merchants', 'action' => 'view', Hash::get($merchantNote, 'Merchant.id')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'Merchant.merchant_dba') : $this->Html->link(Hash::get($merchantNote, 'Merchant.merchant_dba'), array('controller' => 'merchants', 'action' => 'view', Hash::get($merchantNote, 'Merchant.id')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'User.user_first_name') : $this->Html->link(Hash::get($merchantNote, 'User.user_first_name') . " " . Hash::get($merchantNote, 'User.user_last_name'),
							array('controller' => 'users', 'action' => 'view', Hash::get($merchantNote, 'User.id')));
			$row[] = $this->MerchantNote->noteDateTime($merchantNote['MerchantNote']['note_date']);
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'NoteType.note_type_description') : h(__(Hash::get($merchantNote, 'NoteType.note_type_description')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'MerchantNote.general_status') : $this->Html->showStatus(Hash::get($merchantNote, 'MerchantNote.general_status'));
			$url = array(
				'plugin' => false,
				'controller' => 'merchant_notes',
				'action' => 'edit',
				Hash::get($merchantNote, 'MerchantNote.id')
			);
			$title = Hash::get($merchantNote, 'MerchantNote.note_title');
			if (empty($title)) {
				$row[] = ($isCsvExport)? '' : $this->Html->editImageLink($url);
			} else {
				$row[] = ($isCsvExport)? $title : $this->Html->link($title, $url);
			}
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'MerchantNote.note') : array(h(__(Hash::get($merchantNote, 'MerchantNote.note'))), array('style' => 'max-width:500px'));
			$cells[] = $row;
		}
		$row = [];
		if (!empty(Hash::get($merchantNotes, "$idx.MerchantAch.id")) && empty(Hash::get($processedRecords, Hash::get($merchantNotes, "$idx.MerchantAch.id")))) {
			$processedRecords[Hash::get($merchantNotes, "$idx.MerchantAch.id")] = Hash::get($merchantNotes, "$idx.MerchantAch.id");
			//add Merchant ach row to cells
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'Merchant.merchant_mid') : $this->Html->link(Hash::get($merchantNote, 'Merchant.merchant_mid'), array('controller' => 'merchants', 'action' => 'view', Hash::get($merchantNote, 'Merchant.id')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'Merchant.merchant_dba') : $this->Html->link(Hash::get($merchantNote, 'Merchant.merchant_dba'), array('controller' => 'merchants', 'action' => 'view', Hash::get($merchantNote, 'Merchant.id')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'User.user_first_name') : $this->Html->link(Hash::get($merchantNote, 'User.user_first_name') . " " . Hash::get($merchantNote, 'User.user_last_name'),
							array('controller' => 'users', 'action' => 'view', Hash::get($merchantNote, 'User.id')));
			$row[] = $this->AxiaTime->date($merchantNote['MerchantAch']['ach_date']);
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'MerchantAch.type') : h(__(Hash::get($merchantNote, 'MerchantAch.type')));
			$row[] = ($isCsvExport)? Hash::get($merchantNote, 'MerchantAch.status') : $this->Html->showStatus(Hash::get($merchantNote, 'MerchantAch.status'));
			$url = array(
				'plugin' => false,
				'controller' => 'MerchantAches',
				'action' => 'view',
				Hash::get($merchantNote, 'Merchant.id')
			);
			$row[] = ($isCsvExport)? __("Axia Invoices") : $this->Html->link(__("Axia Invoices"), $url);
			$row[] = "Invoice item(s): " . Hash::get($merchantNote, 'MerchantAch.item_count') . ". Total Amount: " . $this->Number->currency(Hash::get($merchantNote, 'MerchantAch.total_ach'));
			$cells[] = $row;
		}
	}
	
	if ($isCsvExport) {
		echo $this->Csv->row($headers);
		echo $this->Csv->rows($cells);
	} else { ?>
		<table class="table table-condensed reportTables">
			<?php
			echo $this->Html->tableHeaders($headers);
			echo $this->Html->tableCells($cells);
			?>
		</table>
	<?php
	}
}