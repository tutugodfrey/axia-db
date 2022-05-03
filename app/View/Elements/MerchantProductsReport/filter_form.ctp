<div class="row">
	<div class="col-xs-12">
		<?php 
		if (!empty($exportLinks)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}

		echo $this->Form->createFilterForm('ProductsAndService');
		echo $this->Form->input('products_services_type_id', array('label' => __('Products'), 'empty' => 'All Products'));
		echo $this->Form->input('entity_id', array('empty' => 'All', 'label' => array('text' => 'Company')));
		echo $this->Form->input('dba_mid', array('label' => array('text' => 'DBA or MID')));
		echo $this->Form->input('m_active_toggle', array(
							'type' => 'checkbox',
							'checked' =>  is_null(Hash::get($this->request->data, 'ProductsAndService.m_active_toggle'))? true : Hash::get($this->request->data, 'ProductsAndService.m_active_toggle'),
							'legend' => false,
							'wrapInput' => false,
							'label' => array('text' => __('<strong>Active Merchants</strong>'),  'class' => 'col col-xs-12 col-md-pull-3 control-label'),
							'class' => 'merchant-note-checkbox'
						));
		echo $this->Form->submit(__('Search'), array(
			'div' => array('class' => 'form-group'),
			'class' => 'btn btn-default'
		));
		echo $this->Form->end();
		?>
	</div>
</div>