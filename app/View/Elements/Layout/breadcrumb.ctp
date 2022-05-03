<?php

$breadcrumbData = array();
$refererText = null;
$refererUrl = array();

if (!empty($user)) {
	$breadcrumbData = $user;
} elseif (!empty($this->request->data)) {
	$breadcrumbData = $this->request->data;
}
$this->Html->setBreadcrumbs($this->params, $breadcrumbData, $refererText, $refererUrl);

$this->startIfEmpty('breadcrumbs');
echo $this->Html->displayCrumbs();
$this->end();
