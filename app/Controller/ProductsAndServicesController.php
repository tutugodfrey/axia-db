<?php

App::uses('AppController', 'Controller');

/**
 * ProductsAndServices Controller
 *
 * @property ProductsAndService $ProductsAndService
 */
class ProductsAndServicesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = [
		'Search.Prg' => [
			'commonProcess' => ['paramType' => 'querystring'],
			'presetForm' => ['paramType' => 'querystring']
		]
	];
/**
 * add method
 *
 * Method to add products/services to a merchant
 *
 * @param string $merchantId the UUID of the merchant to add a a product to
 * @param string $productId the UUID of the product to be added.
 *
 * @return void
 */
	public function add($merchantId, $productId) {
		if ($this->request->is('post')) {
			if ($this->ProductsAndService->hasProduct($merchantId, $productId)) {
				$this->_success(__('Product enabled for this Merchant.'), ['controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchantId]);
			}
			try {
				$route = $this->ProductsAndService->add($merchantId, $productId);
				$this->Session->setFlash(__("Product added! Please update product's settings to finish setting up this product."), 'default', ['class' => 'alert-success panel-heading panel-title']);
				$this->redirect($route);
			} catch (Exception $e) {
				$this->Session->setFlash(__($e->getMessage()), "default", ['class' => 'alert-danger panel-heading']);
				$this->redirect(['controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchantId]);
			}
		}
	}

/**
 * delete method
 *
 * @param string $id a ProductsAndService.id
 * @param string $merchantId a Merchant.id
 * @return void
 * @throws MethodNotAllowedException
 */
	public function delete($id, $merchantId) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$redirectUrl = ['controller' => 'MerchantPricings', 'action' => 'products_and_services', $merchantId];
		$this->ProductsAndService->id = $id;
		if (!$this->ProductsAndService->exists()) {
			$this->_failure(__('Error: Record not found!'), $redirectUrl);
		}
		$productId = $this->ProductsAndService->field('products_services_type_id', ['id' => $id]);
		if ($this->ProductsAndService->delete()) {
			//update MerchantCardType
			$this->ProductsAndService->Merchant->MerchantCardType->updateWithProductId($merchantId, $productId, false);
			$this->_success(__('Product has been removed from this merchant.'), $redirectUrl);
		}
		$this->_failure(__('Error: Product was not deleted.'), $redirectUrl);
	}

/**
 * addMany Ajax handling method
 *
 * Method to add many products/services to a merchant
 *
 * @return string
 */
	public function addMany() {
		$this->autoRender = false;
		if ($this->Session->check('Auth.User.id')) {
			if (!empty($this->request->data('product_ids'))) {
				$merchantId = $this->request->data('merchant_id');
				foreach ($this->request->data('product_ids') as $productId) {
					$productName = $this->ProductsAndService->ProductsServicesType->getNameById($productId);
					try {
						$this->ProductsAndService->add($merchantId, $productId);
						echo "$productName added ...EOL"; //EOL will be replaced with an HTML braking space on the client side
					} catch (Exception $e) {
						$errors[] = "Error: Failed to add '$productName' " . $e->getMessage();
					}
				}
				if (!isset($errors)) {
						echo "Finished!";
						$this->Session->setFlash(__("All Products added! Note: Some products may require additional manual configuration, verify new products below."), 'default', ['class' => 'alert-success panel-heading panel-title']);
				} else {
					echo "Errors occured processing request!";
					$this->Session->setFlash($errors, 'Flash/listErrors', array('class' => 'alert alert-danger'));
				}
			} else {
				return "ERROR: Products data missing in request, could not add products!";
			}
		} else {
			/*Session expired*/
			$this->response->statusCode(401);
		}
	}

/**
 * deleteMany Ajax method
 *
 * Method to remove many products/services from a merchant
 *
 * @param string $merchantId the UUID of the merchant to remove products from
 * @param mixed $productIds JSON string|array $productIds a JSON encoded array or a PHP array containing product IDs to remove from merchant.
 *
 * @return void
 */
	public function deleteMany() {
		$this->autoRender = false;
		if ($this->Session->check('Auth.User.id')) {
			if (!empty($this->request->data('product_ids')) && !empty($this->request->data('merchant_id'))) {
				$conditions = array(
					'ProductsAndService.id' => $this->request->data('product_ids'),
					'ProductsAndService.merchant_id' => $this->request->data('merchant_id'),
				);
				$productsIds = $this->ProductsAndService->find('list', ['fields'=> ['products_services_type_id'], 'conditions' => $conditions]);
				if ($this->ProductsAndService->deleteAll($conditions, false)) {
					//update MerchantCardType
					$this->ProductsAndService->Merchant->MerchantCardType->updateWithProductId($this->request->data('merchant_id'), $productsIds, false);
					echo 'Finished removing selected products.';
					$this->Session->setFlash(__("Selected products have been removed from this merchant!"), 'default', ['class' => 'alert-info panel-heading panel-title']);
				}
			} else {
				return 'Error! Missing request data, failed to remove prducts.';
			}
		} else {
			/*Session expired*/
			$this->response->statusCode(401);
		}
	}

/**
 * merchant_products_report method
 * Report view action
 *
 * @return void
 */
	public function merchant_products_report() {
		$this->Prg->commonProcess();
		$filterParams = $this->Prg->parsedParams();
		if (!empty($filterParams)) {
			$this->request->data['ProductsAndService'] = $filterParams;
			//Set sort order
			$sortField = Hash::get($this->request->params, 'named.sort');
			$order = ($sortField)? "$sortField " . Hash::get($this->request->params, 'named.direction') : "Merchant.merchant_mid asc";
			$this->Paginator->settings = [
				'findType' => 'merchantProducts',
				'conditions' => $this->ProductsAndService->parseCriteria($filterParams),
				'limit' => 10000,
				'maxLimit' => 10000,
				'order' => $order
			];
			//Increase memory, this report can be very big
			ini_set('memory_limit', '10G');
			set_time_limit(0);
			$reportData = $this->paginate();
			$this->set('reportData', $reportData);
		}
		$this->set($this->ProductsAndService->getReportViewData());
	}
}
