<?php
echo $this->Form->hidden('equipment_type_id');
echo $this->Form->input('equipment_item_description', array('label' => __('Description')));
echo $this->Form->input('equipment_item_true_price', array('label' => __('True Cost')));
echo $this->Form->input('equipment_item_rep_price', array('label' => __('Rep Cost')));
echo $this->Form->input('active', array('type' => 'checkbox', 'checked' => true, 'wrapInput' => 'col col-md-12 col col-sm-12'));
?>
