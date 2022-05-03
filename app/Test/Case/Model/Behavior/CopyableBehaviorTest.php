<?php
App::uses('CopyableBehavior', 'Model/Behavior');
App::uses('Model', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * This test model will need a table "posts" in "test_template" database
 * with at least the following fields:
 * - int `id`
 * - string `title`
 * - string `body`
 */
class CopyableBehaviorTestModel extends Model {

	public $useDbConfig = 'test';

	public $name = 'Post';

	public $alias = 'Post';

	public $useTable = 'posts';

	public $validate = array(
		'title' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'The title cant be empty',
			),

		),
	);

/**
 * copyTargetExists method
 *
 * @param array $targetData Target data
 * @param array $sourceData Source Data
 * @return bool|array
 */
	public function copyTargetExists($targetData, $sourceData) {
		$result = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				"{$this->alias}.title" => Hash::get($targetData, "{$this->alias}.title"),
			)
		));
		return empty($result) ? false : $result;
	}

}


class NotValidCopyableBehaviorTestModel extends Model {

	public $useDbConfig = 'test';

	public $name = 'Post';

	public $alias = 'Post';

	public $useTable = 'posts';

}

class CopyableBehaviorTest extends AxiaTestCase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Post = ClassRegistry::init('CopyableBehaviorTestModel');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Post);
		parent::tearDown();
	}

/**
 * testSetupNotValid
 *
 * @expectedException RuntimeException
 * @expectedExceptionMessage Setting "overwrite" and "create" can not be true at the same time!
 * @return void
 */
	public function testSetupNotValid() {
		$this->Post->Behaviors->load('Copyable', ['overwrite' => true, 'create' => true]);
	}

/**
 * testCopy
 *
 * @expectedException NotFoundException
 * @return void
 */
	public function testCopyNoSourceRecordException() {
		$this->Post->Behaviors->load('Copyable');
		$this->Post->copyRecord('00000000-9999-0000-0000-000000000001', ['title' => 'copy-me']);
	}

/**
 * testCopyFirst
 *
 * @return void
 */
	public function testCopyFirst() {
		$this->Post->Behaviors->load('Copyable');
		$this->assertEquals(3, $this->Post->find('count'));

		$targetData = [
			'Post' => [
				'title' => 'Man land on Venus'
			]
		];
		$result = $this->Post->copyRecord('00000000-0000-0000-0000-000000000001', $targetData, false);
		$this->assertTrue($result);
		$this->assertEquals(4, $this->Post->find('count'));
		$actual = $this->Post->field('body', ['title' => 'Man land on Venus']);
		$this->assertEquals('Sadly, he found nothing', $actual);
	}

/**
 * testCopyMany
 *
 * @return void
 */
	public function testCopyMany() {
		$this->Post->Behaviors->load('Copyable');
		$this->assertEquals(3, $this->Post->find('count'));
		$result = $this->Post->copyRecord(
			[
				'conditions' => [
					'Post.title LIKE' => 'Man%'
				]
			],
			[
				'Post' => [
					'body' => 'We dont know what happened'
				]
			],
			true
		);
		$this->assertTrue($result);
		$posts = $this->Post->find('all');
		$this->assertCount(5, $posts);
		$expected = ['Man land on Mars', 'Man return to Earth'];
		$this->assertEquals($expected, Hash::extract($posts, '{n}.Post[body=We dont know what happened].title'));
	}

/**
 * testCopyOverwrite
 *
 * @return void
 */
	public function testCopyOverwrite() {
		$this->Post->Behaviors->load('Copyable', ['overwrite' => true, 'create' => false]);
		$postId = '00000000-0000-0000-0000-000000000003';
		// Check original values
		$this->assertEquals(3, $this->Post->find('count'));
		$this->assertEquals('Nice try!', $this->Post->field('body', ['id' => $postId]));

		// Check values after overrite
		$result = $this->Post->copyRecord(
			$postId,
			[
				'Post' => [
					'title' => 'Matt Damon reaction',
					'body' => 'I wish i could go there...',
				]
			],
			false
		);
		$this->assertTrue($result);
		$this->assertEquals(3, $this->Post->find('count'));
		$this->assertEquals('I wish i could go there...', $this->Post->field('body', ['id' => $postId]));
	}

/**
 * testFindSourceNotValidFind
 *
 * @expectedException RuntimeException
 * @expectedExceptionMessage Invalid find type, only "all" and "first" are allowed!
 * @return void
 */
	public function testFindSourceNotValidFind() {
		$this->Post->Behaviors->load('Copyable');
		$this->Post->findSource(null, 'not-valid-find');
	}

/**
 * testCopyTargetExistsMissing
 *
 * @expectedException RuntimeException
 * @expectedExceptionMessage Your model must implement method copyTargetExists($target)
 * @return void
 */
	public function testCopyTargetExistsMissing() {
		$Model = ClassRegistry::init('NotValidCopyableBehaviorTestModel');
		$Model->Behaviors->load('Copyable');
		$Model->copyTargetExists(null, null);
	}

/**
 * testGetCopyErrors
 *
 * @return void
 */
	public function testGetCopyErrors() {
		$this->Post->Behaviors->load('Copyable');
		$result = $this->Post->copyRecord(
			'00000000-0000-0000-0000-000000000001',
			[
				'Post' => [
					'title' => 'Man land on Mars'
				]
			],
			false
		);
		$errors = $this->Post->getCopyErrors();
		$this->assertEquals('Target already exists', Hash::get($errors, '0.validation'));

		$result = $this->Post->copyRecord(
			'00000000-0000-0000-0000-000000000001',
			[
				'Post' => [
					'title' => '',
				]
			],
			false
		);
		$errors = $this->Post->getCopyErrors();
		$this->assertEquals('The title cant be empty', Hash::get($errors, '0.validation.title.0'));
	}

}
