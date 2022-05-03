<?php

App::uses('AppModel', 'Model');

/**
 * NoteType Model
 *
 */
class NoteType extends AppModel {

/**
 * Constant Note UUID's
 *  
 */
	const CHANGE_REQUEST_ID = 'a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2';
	const GENERAL_NOTE_ID = '0bfee249-5c37-417c-aec7-83dcd2b2f566';
	const PROGRAMMING_NOTE_ID = '083e3d46-76f2-42d9-ad7b-6fb3a9775b3c';
	const INSTALL_N_SETUP_NOTE_ID = '85b32624-aca2-44f2-9924-49cddc6b2e5a';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'note_type_description';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'note_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'MerchantNote'
	);

/**
 * findByNoteTypeDescription method
 * 
 * @param string $noteDescription the note description
 * @return string containing the note's UUID or null if no description match 
 */
	public function findByNoteTypeDescription($noteDescription) {
		switch ($noteDescription) {
			case 'Change Request':
				$noteUUID = self::CHANGE_REQUEST_ID;
				break;
			case 'General Note':
				$noteUUID = self::GENERAL_NOTE_ID;
				break;
			case 'Programming Note':
				$noteUUID = self::PROGRAMMING_NOTE_ID;
				break;
			case 'Installation & Setup Note':
				$noteUUID = self::INSTALL_N_SETUP_NOTE_ID;
				break;
			default:
				$noteUUID = null;
		}

		return $noteUUID;
	}
}