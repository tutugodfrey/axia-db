<?php

App::uses('AppController', 'Controller');

/**
 * Clients Controller
 *
 * @property Client $Client
 */
class ClientsController extends AppController {

/**
 * searchSimilar
 * Finds clients that closely match the term sent in the request query
 * 
 * @param string $id an Client id.
 * @param string $merchantNoteId a merchant id
 * @return string JSON
 */
	public function searchSimilar() {
		$this->layout = 'ajax';
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			if ($this->Session->read('Auth.User.id')) {
				$searchTerm = $this->request->query['term'];
				if (!empty($searchTerm)) {
					return $this->Client->getAutocompleteMatches($searchTerm);
				} else {
					return '[]';
				}
			} else {
				//session expired
				$this->response->statusCode(401);
			}
		} else {
			//Bad Request
			$this->response->statusCode(400);
		}
	}
}
