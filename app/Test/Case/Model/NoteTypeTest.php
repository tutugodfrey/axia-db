<?php
App::uses('NoteType', 'Model');
App::uses('AxiaTestCase', 'Test');

/**
 * NoteType Test Case
 *
 */
class NoteTypeTest extends AxiaTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->NoteType = ClassRegistry::init('NoteType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->NoteType);
		parent::tearDown();
	}

/**
 * testFindByNoteTypeDescription
 *
 * @covers NoteType::findByNoteTypeDescription()
 * @return void
 */
	public function testFindByNoteTypeDescription() {
		$descs = [
			NoteType::CHANGE_REQUEST_ID => 'Change Request',
			NoteType::GENERAL_NOTE_ID => 'General Note',
			NoteType::PROGRAMMING_NOTE_ID => 'Programming Note',
			NoteType::INSTALL_N_SETUP_NOTE_ID => 'Installation & Setup Note',
		];
		foreach ($descs as $id => $desc) {
			$actual = $this->NoteType->findByNoteTypeDescription($desc);
			$this->assertSame($id, $actual);
		}
			$this->assertEmpty($this->NoteType->findByNoteTypeDescription('fake description'));
	}
}
