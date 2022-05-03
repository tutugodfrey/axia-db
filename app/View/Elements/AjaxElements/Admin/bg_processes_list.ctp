<?php
if (empty($processesList)) {
	echo $this->Html->tag('li', $this->Html->tag('i', 'No processes queued') ,['class' => 'list-group-item text-center text-muted small']);	
} else {
	foreach($processesList as $listItem) {
		$itemAttr = [
			"data-toggle" => "tooltip",
			"data-placement" => "right",
			"data-original-title" => $listItem['JobStatus']['name'],
			'class' => 'list-group-item',
		];
		if ($listItem['JobStatus']['id'] === $statusList['processing']) {
			$indicator = $this->Html->image('green_orb.gif', ['class' => 'blinking pull-right']);
			$itemAttr['class'] .= ' strong';
		} elseif ($listItem['JobStatus']['id'] === $statusList['idle']) {
			$indicator = $this->Html->image('green_orb.gif', ['class' => 'gray-scale pull-right']);
			$itemAttr['class'] .= ' text-muted';
		} elseif ($listItem['JobStatus']['id'] === $statusList['error']) {
			$indicator = $this->Html->image('errorCross29x29.jpg', ['class' => 'pull-right', 'style' => 'width:13px;']);
			$itemAttr['class'] .= ' strong text-danger';
		} elseif ($listItem['JobStatus']['id'] === $statusList['done']) {
			$indicator = $this->Html->image('greenCheck29x29.jpg', ['class' => 'pull-right', 'style' => 'width:13px;']);
			$itemAttr['class'] .= ' strong text-success';
		}
		$itemText = h($listItem['BackgroundJob']['description']) . ' ';
		$itemText .= h($listItem['JobStatus']['name']) . ' ';
		$itemText .= ($listItem['JobStatus']['id'] === $statusList['processing'] || $listItem['JobStatus']['id'] === $statusList['idle'])? 'since ': '';

		$itemText .= $this->AxiaTime->relativeTime(strtotime($listItem['BackgroundJob']['modified']));
		echo $this->Html->tag('li', $indicator . $itemText, $itemAttr);	
	}
}
