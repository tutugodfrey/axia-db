<?php 
$message = 'Loading ';

if (!empty($contentName)) {
	$message .= $contentName;
}

if (empty($class)) {
	$alertStyle = 'text-center col col-md-2 col-md-offset-4 alert alert-info';
} else {
	$alertStyle = $class;
}
echo $this->Html->tag('div', 
		$this->Html->tag('span', $message . '... ' . '<img src="/img/indicator.gif">', ['class' => $alertStyle]),
		['class' => 'col-md-12 col-sm-12 col-xs-12 col-lg-12']
	);
