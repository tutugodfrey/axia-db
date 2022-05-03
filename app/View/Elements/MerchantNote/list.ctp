<?php
foreach ($data as $noteData):
	$url = array(
		'plugin' => false,
		'controller' => 'merchant_notes',
		'action' => 'edit',
		Hash::get($noteData, 'id')
	);
	$link = $this->Html->link(Hash::get($noteData, 'note_title'), $url);
	$date = date_format(date_create(Hash::get($noteData, 'note_date')), 'M jS Y');


	echo "{$link} | {$date}";
	echo '&nbsp;<span class="iconInline">';
	if (Hash::get($noteData, 'general_status') === GUIbuilderComponent::STATUS_PENDING): ?>		
			<?php echo $this->Html->editIcon(null, array('url' => $url)); ?>
	<?php
	endif;
	if (!empty(Hash::get($noteData, 'flag_image'))){
		echo $this->Html->image("/img/" . $noteData['flag_image'], array('class' => 'icon'));
	}
	?>
	</span>
	<br>
<?php
endforeach;
