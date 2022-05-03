<?php

App::uses('AppController', 'Controller');

/**
 * Organizations Controller
 *
 * @property SalesGoal $SalesGoal
 */
class OrganizationsController extends AppController {
/**
 * Get regions by Org
 *
 * @return void
 */

	public function getRegions() {
		//this method can handle ajax and non-ajax calls
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}
		$orgId = ($this->request->data('Merchant.organization_id'))?: $this->request->data('organization_id');
		/*Using the organization passed in request data as condition to find and return corresponding regions*/
		$regions = $this->Organization->Region->getByOrganization($orgId);

		echo json_encode($regions);
	}

/**
 * Get subregions by Region
 *
 * @return void
 */
	public function getSubregionsByRegion() {
		//this method can handle ajax and non-ajax calls
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}
		$regionId = ($this->request->data('Merchant.region_id'))?: $this->request->data('region_id');
		/*Using conditions passed in request data*/
		$subregions = $this->Organization->Subregion->getByRegion($regionId);

		echo json_encode($subregions);
	}

}
