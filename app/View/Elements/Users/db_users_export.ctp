<?php

if (!empty($users)) {
	$labels = [
		'user_last_name' => __('Last Name'),
		'user_first_name' => __('First Name'),
		'role_name' => __('Role(s)'),
		'username' => __('Username'),
		'initials' => __('Initials'),
		'user_email' => __('Email'),
		'entity' => __('Company'),
		'active' => __('Active'),
		'is_blocked' => __("Blocked")
	];

	$headers = [];
	$cells = [];
	foreach ($users as $user) {
		// Create the headers the first time
		if (empty($headers)) {
			foreach ($user as $key => $value) {
				$headers[] = Hash::get($labels, $key);
			}
		}

		$row = [];
		foreach ($user as $key => $value) {
			switch ($key) {
				case 'active':
				case 'is_blocked':
					$row[$key] = ($value == 1) ? "YES" : "NO";
				break;
				default:
					$row[$key] = $value;
			}
		}
		$cells[] = $row;
	}

	echo $this->Csv->row($headers);
	echo $this->Csv->rows($cells);
}
