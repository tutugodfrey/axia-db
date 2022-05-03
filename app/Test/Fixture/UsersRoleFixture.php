<?php
/**
 * UsersRoleFixture
 *
 */
class UsersRoleFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'role_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'user_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'pk_roles_users' => array('unique' => false, 'column' => array('role_id', 'user_id'))
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
			'id' => '5637c65c-3ce4-4f32-9941-7da934627ad4',
			'role_id' => '5536d1cf-8854-495d-b727-103134627ad4',
			'user_id' => 'a7c2365f-4fcf-42bf-a997-fa3faa3b0eda'
		),
		array(
			'id' => 'lorem13-d3c8-4953-9648-7da934627ad4',
			'role_id' => '5536d1cf-47b0-401c-b52b-103134627ad4',
			'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
		),
		array(
			'id' => 'Impsum-dolor-4953-9648-7da934627ad4',
			'role_id' => '5536d1cf-c50c-47d7-a03b-103134627ad4',
			'user_id' => 'd2b7550c-e761-40b7-a769-ca1cf2ac9332'
		),
		array(
			'id' => '5637c65c-610c-4e68-849e-7da934627ad4',
			'role_id' => '5536d1cf-22f4-4347-a922-103134627ad4',
			'user_id' => '551184fe-d814-4a78-82a8-7e4134627ad4'
		),
		array(
			'id' => '5637c65c-c484-4ffc-8a36-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '274ed5c5-d177-452f-bd7f-554b6c59918b'
		),
		array(
			'id' => '5637c65c-64ec-4fc3-855b-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
		),
		array(
			'id' => '5637c65c-dcb4-4ba8-b673-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '29f4096d-88c6-420f-b59b-1137b3bcb309'
		),
		array(
			'id' => '5637c65c-7164-4374-803a-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => 'b95cc81a-3f9d-4af8-81a8-f5a316e5e49b'
		),
		array(
			'id' => '5637c65c-e0f8-4e22-bbcf-7da934627ad4',
			'role_id' => '5536d1cf-a728-48ac-ac23-103134627ad4',
			'user_id' => '55144783-77a0-43a6-8474-554534627ad4'
		),
		array(
			'id' => '5637c65c-553c-468b-906f-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '8ed66034-354b-4fa7-8ca4-758f2261daf4'
		),
		array(
			'id' => '5637c65c-c084-4361-86cf-7da934627ad4',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '6c87a79c-a112-443a-a872-9033e13bab97'
		),
		array(
			'id' => 'sm-role-bill',
			'role_id' => '5536d1cf-47b0-401c-b52b-103134627ad4',
			'user_id' => '6139999-5c48-4703-8fb3-68a7fe275ea2'
		),
		array(
			'id' => 'mark-sales-manager',
			'role_id' => '5536d1cf-47b0-401c-b52b-103134627ad4',
			'user_id' => '00ccf87a-4564-4b95-96e5-e90df32c46c1'
		),
		array(
			'id' => 'Bill-rep',
			'role_id' => '5536d1cf-6ef4-4664-be12-103134627ad4',
			'user_id' => '003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6'
		),
	);
}
