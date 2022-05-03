<?php

App::uses('Component', 'Controller');
App::uses('GUIbuilderComponent', 'Controller/Component');

class FlagStatusLogicComponent extends Component {

/**
 * Return the path of the flag depending on a status
 *
 * @param type $status Status to display
 *
 * @return string
 */
	public static function getThisStatusFlag($status) {
		$str = false;
		if ($status == GUIbuilderComponent::STATUS_COMPLETED || $status == GUIbuilderComponent::STATUS_PAID) {
			$str = 'icon_greenflag.gif';
		} elseif ($status == GUIbuilderComponent::STATUS_PENDING) {
			$str = 'icon_redflag.gif';
		} elseif ($status == GUIbuilderComponent::STATUS_INVOICED || $status == GUIbuilderComponent::STATUS_REJECTED) {
			$str = 'icon_yellowflag.gif';
		}
		return $str;
	}

}
