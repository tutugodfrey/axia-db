
<?php
if (!isset($plainText)) {
	$plainText = false;
}
 if (!$plainText) {
 	echo $this->Html->tag('span',
		'HaaS ' . $this->Html->image('/img/icon_greenflag.gif', ['style' => 'vertical-align:top']),
		['class' => 'label label-info', 'style' => 'font-size:110%', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'data-original-title' => 'Hardware as a Service']
	);
 } else {
 	echo " (HaaS)";
 }