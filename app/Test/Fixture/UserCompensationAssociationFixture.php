<?php
/**
 * UserCompensationAssociationFixture
 *
 */
class UserCompensationAssociationFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'associated_users';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'associated_user_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'role' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'permission_level' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'user_compensation_profile_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '5679eaea-fbec-4c75-8f85-2afc34627ad4',
			'user_id' => 'Lorem ipsum dolor sit amet',
			'associated_user_id' => 'Lorem ipsum dolor sit amet',
			'role' => 'Lorem ipsum dolor sit amet',
			'permission_level' => 'Lorem ipsum dolor sit amet',
			'user_compensation_profile_id' => 'Lorem ipsum dolor sit amet'
		),
	);

}
