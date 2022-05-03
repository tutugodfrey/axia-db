<?php

App::uses('AppController', 'Controller');

/**
 * Documentations Controller
 *
 */

/***   Begin API documentation anotations for swagger-php ***/

/**
 *	@OA\Info(
 *		title="AxiaMed REST API",
 *		version="1.0.0",
 *		@OA\Contact(
 *			email="webmaster@axiamed.com"
 *		),
 *	 	description="AxiaMed's REST API allows access to the resources detailed in this document. Access to this API and these resources must be requested and approved by a system administrator. Upon approval, consumers will be given limited access to AxiaMed's [Database system](https://db.axiatech.com/users/login) where they can login and generate their own API access token and password from their own user profiles.",
 *	)
 *
 */

class DocumentationsController extends AppController {

/**
 *	Load Components
 */
	public $components = array('GUIbuilder');

/**
 * help method
 *
 * @return void
 */
	public function help() {
		$this->layout = 'helpDocLayout';
	}
}
