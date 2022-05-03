<?php
App::uses('AppController', 'Controller');
/**
 * ProfitProjections Controller
 *
 * @property ProfitProjection $ProfitProjection
 * @property PaginatorComponent $Paginator
 */
class ProfitProjectionsController extends AppController {

/**
 * updateProjections
 * Process POST requests to update ProfitProjection numbers for a specific merchant
 *
 * @return void
 */
	public function updateProjections($merchantId) {
		try {
			$this->ProfitProjection->updateAllProjections($merchantId);
			$this->_success(__("Profit Projections Updated!"), $this->referer(), ['class' => 'alert alert-success']);
		} catch (Exception $e) {
			$this->_failure(__($e->getMessage()), $this->referer(), ['class' => 'alert alert-danger']);
		}
	}
}
