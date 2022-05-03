<div class="row">
	<div class="col-xs-12">
		<?php
		echo $this->Form->createFilterForm('SystemTransaction');
		echo $this->Form->complexUserInput('user_id', array(
			'required' => false,
		));
		echo $this->Form->input('from_date', array(
			'label' => __('From date'),
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->input('end_date', array(
			'label' => __('End date'),
			'type' => 'date',
			'dateFormat' => 'YM',
			'maxYear' => date('Y')
		));
		echo $this->Form->input('row_limit', array(
			'label' => __('Show'),
		));
		echo $this->Form->submit(__('Generate'), array(
			'div' => array('class' => 'form-group'),
			'class' => 'btn btn-default'
		));
		echo $this->Form->end();
		?>
	</div>
</div>
