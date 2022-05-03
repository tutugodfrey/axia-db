<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Installation & Timeline', '/' . $this->name . '/' . 'timeline' . '/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Edit Installation & Timeline');
?>

<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Installation & Timeline')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />
<?php
echo $this->Form->create('TimelineEntry', array(
	'inputDefaults' => array(
				'div' => 'form-group',
				'label' => array(
					'class' => 'col col-sm-12 col-md-12 control-label'
				),
				'wrapInput' => 'col-xs-12 col-sm-12 col-md-12',
				'class' => 'form-control input-sm'
			),
	'class' => 'form-inline'
));
?>

<div class="contentModuleTitle col-md-12">Edit Installation Information & Timeline: </div>
<br />
<table style="width:500px">
	<?php
		$count = count($this->request->data);
		for ($x = 0; $x < $count; $x++) :
	?>
		<tr>
			<td>
				<?php
				echo "<strong>" . h($this->request->data[$x]["TimelineItem"]["timeline_item_description"]) . "</strong>";
				?>
			</td>
			<td>
				<?php
				if ($this->request->data[$x]['TimelineEntry']['timeline_item_id'] !== TimelineItem::APPROVED) {
					echo $this->Form->hidden("$x.TimelineEntry.id");
					echo $this->Form->hidden("$x.TimelineEntry.merchant_id", array('value' => $merchant["Merchant"]["id"]));
					echo $this->Form->hidden("$x.TimelineEntry.timeline_item_id", array('value' => $this->request->data[$x]["TimelineItem"]["id"]));
					echo $this->Form->input("$x.TimelineEntry.timeline_date_completed", array('before' => '', 'label' => false, 'empty' => '--')) . '&nbsp;&nbsp';
					if ($this->request->data[$x]["TimelineItem"]["timeline_item_description"] === 'Go-Live date') {
						echo $this->Form->input("$x.TimelineEntry.action_flag", array('label' => array('text' => 'Exclude Multiple', 'class' => false), 'div' => false));
					}
				} else {
					$approvedDate = $this->AxiaTime->date($this->request->data[$x]['TimelineEntry']['timeline_date_completed']);
					echo $this->Html->tag('div', $approvedDate, array('class' => 'col col-md-12', "data-toggle" =>"tooltip", "data-placement" =>"right", "data-original-title" => "Update this from underwriting"));
				}
				?>   
			</td>
		</tr>
	<?php
		endfor;
	?>
</table>    
<?php echo $this->Form->end(array('label' => 'Save', 'class' => 'btn btn-success', 'div' => array('class' => 'form-group'))); ?>
<script type='text/javascript'>activateNav('MerchantsTimeline'); </script>