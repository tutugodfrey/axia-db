<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEmail', 'Network/Email');

class EmailAlert extends CakeObject implements CakeEventListener {

/**
 * implementedEvents
 * 
 * @return array
 */
	public function implementedEvents() {
		return array(
			'App.Model.womplyAlert' => 'sendEmailAlert',
			'App.Model.readyForEmail' => 'sendEmailAlert',
			'App.Model.userPasswordReset' => 'sendEmailAlert'
		);
	}

/** __setupEmail method
 *  Sets up email data
 *
 * @param type $data
 * 	- emailBody: required parameter
 * 	- to / bcc: one but not both are required
 * @throws InvalidArgumentException when required parameters are missing in data array
 * @return array $emailProperties
 */
	private function __setupEmail($data) {
		//Check emailBody
		if (empty($data['emailBody']) || (empty($data['to']) && empty($data['bcc']))) {
			throw new InvalidArgumentException();
		}
		$emailProperties['template'] = (empty($data['template'])) ? Configure::read('App.defaultTemplate') : $data['template'];
		$emailProperties['emailBody'] = array('content' => $data['emailBody']);
		$emailProperties['from'] = (empty($data['from'])) ? Configure::read('App.defaultSender') : $data['from'];
		$emailProperties['to'] = Hash::get($data, 'to', '');
		$emailProperties['bcc'] = Hash::get($data, 'bcc', '');
		$emailProperties['subject'] = Hash::get($data, 'subject', '');
		$emailProperties['attachment'] = Hash::get($data, 'attachment', '');

		return $emailProperties;
	}

/** sendEmailAlert
 *
 * Sends an email
 *
 * @param object $event $event->data must contain the following keys:
 *	
 *	- "emailBody"
 *	- "to" and/or "bcc" (one but not both are required)
 *	- emailBody
 *  All other array keys must match that of CakeEmail object properties which can be optionally included to override the defaults set by this event handler.
 *
 * @return boolean true on success false on failure.
 */
	public function sendEmailAlert($event) {
		try { //try to __setupEmail
			$emailProperties = $this->__setupEmail($event->data);
		} catch (Exception $e) {
			return false;
		}
		$Email = new CakeEmail();
		//disable sending emails when in dev/stage
		if (Configure::read('debug') > 0) {
			$Email->transport('Debug');
		}
		$Email->template($emailProperties['template']);
		$Email->viewVars($emailProperties['emailBody']);
		$Email->emailFormat('html');
		$Email->from($emailProperties['from']);
		if (!empty($emailProperties['to'])) {
			$Email->to($emailProperties['to']);
		}
		if (!empty($emailProperties['bcc'])) {
			$Email->bcc($emailProperties['bcc']);
		}
		if (!empty($emailProperties['attachment'])) {
			$Email->attachments($emailProperties['attachment']);
		}
		$Email->subject($emailProperties['subject']);
		$Email->send();
		return true;
	}

}
