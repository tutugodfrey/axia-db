<?php

App::uses('AppController', 'Controller');

class MerchantPricingArchivesController extends AppController {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array('Paginator', 'GUIbuilder', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$optnsYears = $this->GUIbuilder->getDatePartOptns('y', 2015);
		$optnsMonths = $this->GUIbuilder->getDatePartOptns('m');

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->MerchantPricingArchive->set($this->request->data);
			/* If archive button is pressed */
			if ($this->request->data('archiveBtn')) {

				if ($this->MerchantPricingArchive->validates(array('fieldList' => array('archive_year', 'archive_month', 'products')))) {

					$year = $this->request->data('MerchantPricingArchive.archive_year');
					$month = $this->request->data('MerchantPricingArchive.archive_month');
					$producsToArchive = $this->request->data('MerchantPricingArchive.products');

					/* Check if an archive already exists for selected options */
					if ($this->MerchantPricingArchive->archiveExists($year, $month, $producsToArchive) === false) {
						$BackgroundJob = ClassRegistry::init('BackgroundJob');
						$jobTrackerId = $BackgroundJob->addToQueue('Archiving ' . "$month/$year for " . count($producsToArchive) . ' product(s)');
						if ($jobTrackerId === false) {
							$this->_failure(__('Failed to create a Background Job Tracker for this process! Try again later.'), ['action' => 'index']);
						}
						//Since this can be very long process
						//we must put it on a CakeResque background process
						CakeResque::enqueue(
							'pricingArchiveQueue',
							'PricingArchiveShell',
							array('processArchiveJob', $producsToArchive, $month, $year, $this->Auth->user('user_email'), $jobTrackerId)
						);
						$this->Session->setFlash(__('Archive is now running on the server, once it is finished an email' .
							' notification will be sent to ' . h($this->Auth->user('user_email')) . "."), 'Flash/bgProcessMsg', array('class' => 'text-center alert alert-success'));
					} else {
						$this->Session->setFlash(__('Archive already exists for the selected product and dates'));
					}
				}
			} else {
				/* If generate button is pressed */
				if ($this->MerchantPricingArchive->validates(array('fieldList' => array('year')))) {
					$year = $this->request->data('MerchantPricingArchive.year');
					$month = $this->request->data('MerchantPricingArchive.month');
					$data = $this->MerchantPricingArchive->checkExistingArchiveByMonthYear($year, $month);
					$this->set('data', $data);
				}
			}
		} else { //request is not post
			/* Display current year archive (default behaviour) */
			if (empty($this->request->data) && empty($this->request->query)) {
				$year = date("Y");
			} else {
				$year = Hash::get($this->request->data, 'MerchantPricingArchive.year')?: Hash::get($this->request->query, 'year');
			}

			$this->request->data['MerchantPricingArchive']['year'] = $year;
			$data = $this->MerchantPricingArchive->checkExistingArchiveByMonthYear($year, null);
			$this->set('data', $data);
		}
		/*List of products */
		$optnsProducs = $this->MerchantPricingArchive->getArchivableProducts();
		$this->set(compact('optnsProducs'));
		$this->set('years', $optnsYears);
		$this->set('months', $optnsMonths);
	}

/**
 * edit method
 *
 * @param string $id MerchantPricingArchive.id
 * @return void
 * @throws NotFoundException
 */

	public function edit($id = null) {
		$this->MerchantPricingArchive->id = $id;
		if (!empty($id) && !$this->MerchantPricingArchive->exists()) {
			throw new NotFoundException(__('Invalid archive'));
		}
		if (!empty($id) && ($this->request->is('post') || $this->request->is('put'))) {
			$redirectUrl = ['controller' => 'MerchantPricings', 'action' => 'products_and_services', $this->request->data('MerchantPricingArchive.merchant_id')];
			$this->MerchantPricingArchive->filterData($this->request->data);
			$this->MerchantPricingArchive->setEditModeValidation();
			if ($this->MerchantPricingArchive->saveAssociated($this->request->data)) {
				$this->_success(__('The archived pricing has been updated'), $redirectUrl);
			} else {
				$errors = Hash::extract($this->MerchantPricingArchive->validationErrors, '{s}.{n}.{s}.{n}');
				if (!empty($errors)) {
					$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
					$this->redirect($this->referer());
				}
				$this->_failure(__('The archived pricing could not be saved. Please, try again.'), $this->referer());
			}
		} else {
			if (empty($id)) {
				$id = $this->request->data("MerchantPricingArchive.id");
			}
			$this->request->data = $this->MerchantPricingArchive->setFormData($this->MerchantPricingArchive->getDataById($id));
		}

		$merchant = $this->MerchantPricingArchive->Merchant->getSummaryMerchantData($this->request->data('MerchantPricingArchive.merchant_id'));
		$this->set($this->MerchantPricingArchive->getEditViewVars());
		$this->set(compact('merchant'));
	}

/**
 * deleteMany method
 *
 * Cascade delete MerchantPricingArchive and dependent models' data.
 *
 * @throws MethodNotAllowedException 
 * @return void
 */
	public function deleteMany() {
		$passedJsonParams = json_decode($this->request->data['MerchantPricingArchive']['json_delete_data'], true);
		$conditions = Hash::filter($passedJsonParams);
		try {
			if ($this->MerchantPricingArchive->deleteMany($conditions)) {
				$msg = "All archives deleted for the selected month(s) and products.";
				$this->Session->setFlash(__($msg), 'default', array('class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->_failure(__('ERROR: Failed to Delete records!'), array('action' => 'index'));
			}
		} catch (InvalidArgumentException $e) {
			$this->Session->setFlash(__('ERROR: ' . $e->getMessage()), 'default', array('class' => 'alert alert-danger strong'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * delete method
 *
 * Cascade delete MerchantPricingArchive and dependent models' data.
 *
 * @param string $year the year
 * @param string $month the month
 * @param string $prodId the product id
 * @param string $prodName the product name
 * @throws MethodNotAllowedException 
 * @return void
 */
	public function delete($year, $month, $prodId, $prodName) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$isDeleted = $this->MerchantPricingArchive->deleteAll(array('year' => $year, 'month' => $month, 'products_services_type_id' => $prodId), true);
		//since delete all returns true even if nothing is deleted verify

		if ($isDeleted) {
			$this->Session->setFlash(__("Archive deleted for " . h($prodName) . " $month/$year."), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('Unexpected Error: Records may not have been deleted please check and try again.'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * ajaxShowArchiveEditMenu method
 *
 * Handle only ajax request to display an archived product edit menu element.
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @return void
 */
	public function ajaxShowArchiveEditMenu($merchantId, $productId) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Check if user session is still active*/
			if ($this->Session->check('Auth.User.id')) {
				$productName = $this->MerchantPricingArchive->ProductsServicesType->field('products_services_description', ['id' => $productId]);
				$archiveMoYrs = $this->MerchantPricingArchive->getArchivedMoYears($merchantId, $productId);
				$this->set(compact('archiveMoYrs', 'productName', 'productId', 'merchantId'));
				$this->render('/Elements/Layout/Merchant/ProductsAndServices/archiveEditMenu', 'ajax');
			} else {
				//Session expired, redirect user to login page with Forbidden status 403
				$this->response->statusCode(403);
			}
		}
	}

/**
 * createManyMenu method
 *
 * Handles only ajax request to display ajax element used for creating archives by merchant.
 *
 * @param string $merchantId a merchant id
 * @param string $productId a product id
 * @return void
 */
	public function createManyMenu($merchantId, $month = null, $year = null) {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			/* Check if user session is still active*/
			if ($this->Session->check('Auth.User.id')) {
				if (empty($month) && empty($year)) {
					$month = date('m');
					$year = date('Y');
				}

				$this->request->data['date']['month'] = $month;
				$this->request->data['date']['year'] = $year;
				if (empty($this->request->data['MerchantPricingArchive'])) {
					$this->request->data['MerchantPricingArchive']['merchant_id'] = $merchantId;
					$this->request->data['MerchantPricingArchive']['archive_month'] = $this->request->data['date']['month'];
					$this->request->data['MerchantPricingArchive']['archive_year'] = $this->request->data['date']['year'];
				}
				if (((int)$month > (int)date('m') && $year == date('Y'))) {
					//set empty array of metadata for invalid date selection
					$archiveMetadata = [];
				} else {
					$archiveMetadata = $this->MerchantPricingArchive->getCreateManyViewData($this->request->data);
				}
				$this->set(compact('merchantId', 'archiveMetadata'));
				$this->render('/Elements/Layout/Merchant/ProductsAndServices/createManyMenu', 'ajax');
			} else {
				//Session expired, redirect user to login page with Forbidden status 403
				$this->response->statusCode(403);
			}
		}
	}

/**
 * createManyByMerchant method
 * Action handles requests to create new or rebuild MerchantPricingArchives
 *
 * @return void
 */
	public function createManyByMerchant() {
		$merchantId = $this->request->data('MerchantPricingArchive.merchant_id');
		$month = $this->request->data('MerchantPricingArchive.archive_month');
		$year = $this->request->data('MerchantPricingArchive.archive_year');
		//extract the ids of the products that were chosen to create archives
		$productIds = Hash::extract($this->request->data, 'MerchantPricingArchive.{n}[create=1].products_services_type_id');
		if (!empty($productIds)) {
			try {
				$this->MerchantPricingArchive->createByMerchant($merchantId, $productIds, $month, $year);
				$this->_success(__('Pricing archived successfully for selected products'), $this->referer(), array('class' => 'alert alert-success strong'));
			} catch (Exception $e) {
				$this->_failure(__($e->getMessage()), $this->referer(), array('class' => 'alert alert-danger strong'));
			}
		} else {
			$this->_success(__('No products were selected for which to create archives. Please select products and try again.'), $this->referer(), array('class' => 'alert alert-warning'));
		}
	}
}
