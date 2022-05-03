<?php

App::uses('AppController', 'Controller');

/**
 * TimelineEntries Controller
 *
 * @property TimelineEntry $TimelineEntry
 */
class TimelineEntriesController extends AppController {

/**
 * Timeline Entries method
 *
 * @param string $id merchant.id associated with the timeline
 * @return void
 */
	public function timeline($id = null) {
		$installNoteId = $this->TimelineEntry->Merchant->MerchantNote->NoteType->findByNoteTypeDescription('Installation & Setup Note');
		$installNotes = $this->TimelineEntry->Merchant->MerchantNote->getNotesByMerchantId($id, $installNoteId);
		$timeline = $this->TimelineEntry->getTimelineEntries($id);
		$montsSinceInstall = $this->TimelineEntry->Merchant->getInstMoCount($id);
		$this->set('merchant', $this->TimelineEntry->Merchant->getSummaryMerchantData($id));
		$this->set(compact('montsSinceInstall', 'timeline', 'installNotes', 'installNoteId'));
	}

/**
 * edit method
 *
 * @param string $id merchant.id associated with the timeline
 * @throws NotFoundException
 * @return void
 */
	public function edit($id = null) {
		$this->TimelineEntry->Merchant->id = $id;
		if (!$this->TimelineEntry->Merchant->exists()) {
			throw new NotFoundException(__('Invalid timeline entry'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data = $this->TimelineEntry->cleanRequestData($this->request->data);
			$this->request->data = $this->TimelineEntry->removeDuplicates($this->request->data);

			if ($this->TimelineEntry->saveMany($this->request->data)) {
				$this->Session->setFlash(__('The timeline entry has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'timeline', $id));
			} else {
				$this->Session->setFlash(__('The timeline entry could not be saved. Please, try again.'));
			}
		}
		$this->request->data = $this->TimelineEntry->getTimelineEntries($id);
		$this->set('merchant', $this->TimelineEntry->Merchant->getSummaryMerchantData($id));
	}
}
