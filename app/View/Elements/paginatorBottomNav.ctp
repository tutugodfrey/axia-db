
  <p><?php
  echo $this->Paginator->counter(array(
    'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
  ));
  ?>
</p>
<!--  <div class="paging">
  <?php echo "\t" . $this->Paginator->prev('<< ' . __('previous'), array('tag' => 'span'), null, array('class' => 'disabled')) . "\n";?>
   | <?php echo $this->Paginator->numbers() . "\n"?>
  <?php echo "\t ". $this->Paginator->next(__('next') .' >>', array('tag' => 'span'), null, array('class' => 'disabled')) . "\n";?>
  </div>-->
<?php echo $this->Paginator->pagination(array(
	'ul' => 'pagination'
)); ?>

