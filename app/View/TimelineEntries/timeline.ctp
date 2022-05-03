<?php
/* Drop breadcrumb */
$this->Html->addCrumb(h($merchant['Merchant']['merchant_dba']), '/Merchants/view/' . $merchant['Merchant']['id']);
$this->Html->addCrumb('Installation & Timeline', '/' . $this->name . '/' . $this->action . '/' . $merchant['Merchant']['id']);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo h($merchant['Merchant']['merchant_dba'] . " / " . $merchant['Merchant']['merchant_mid'] . " / " . $merchant['User']['user_first_name'] . " " . $merchant['User']['user_last_name'] . " | " . __('Merchant Installation & Timeline')); ?>" />
<input type="hidden" id="thisIsMerchant" value=1 />

<table>
    <tr><td class="twoColumnGridCell">           
            <div class="panel-heading bg-primary">
                <span class="contentModuleTitle">Installation &amp; Setup Notes</span>
				<?php
					if ($this->Rbac->isPermitted('MerchantNotes/add') && $this->Rbac->isPermitted('app/actions/TimelineEntries/view/module/addInstallNote', true)) {
						echo $this->MerchantNote->addAjaxNoteButton(null, 'addInstNote', "Add Installation note for " . h($merchant['Merchant']['merchant_dba']), ['class' => 'pull-right btn-success btn-xs']);
					}
				?>      
            </div>                        
            <div class="contentModule">
                <div id="addNote_frm" style="margin-bottom: 6px; display:none">                        
				</div>               
				<?php if (!empty($installNotes['MerchantNote'])): ?>
					<?php foreach ($installNotes['MerchantNote'] as $noteData): ?>     
						<div class="panel panel-info">
							<div class="panel-heading contentModuleTitle">
								<?php
								if ($this->Rbac->isPermitted('MerchantNotes/edit')) {
									echo $this->Html->image("editPencil.gif", array("title" => "Edit this note.", "class" => "icon pull-right contrTitle roundEdges", 'url' => array('controller' => 'merchant_notes', 'action' => 'edit', $noteData['id'])));
								}
								?>
								Posted by: <?php echo trim(h($noteData['User']['user_first_name'] . ' ' . $noteData['User']['user_last_name'])); ?> on <?php echo date_format(date_create($noteData['note_date']), 'M jS Y'); ?>:
							</div>
							<div class='panel-body'>
								<?php echo nl2br(trim(h($noteData['note']))); ?> 
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
						
						<span id="noteNothingInst" style="margin-top:200px"class="list-group-item text-center text-muted">- No Install Notes -</span>            
				<?php endif; ?>
            </div>            
        </td>
        <td class="twoColumnGridCell">          
			<?php if ($this->Rbac->isPermitted('app/actions/TimelineEntries/view/module/timelineList', true)) : ?>
				<div class="panel-heading bg-primary">
					<span class="contentModuleTitle">Account Events Timeline</span>
					<span class="icon">
						<?php if ($this->Rbac->isPermitted('TimelineEntries/edit')) : ?>
							<a href="#">
								<?php echo $this->Html->image("editPencil.gif", array("title" => "Edit timeline entries.", "class" => "pull-right icon contrTitle roundEdges", 'url' => array('controller' => 'TimelineEntries', 'action' => 'edit', Hash::get($merchant, 'Merchant.id')))); ?>
						<?php endif; ?>
					</span>
				</div>
				<table class="table-condensed table-hover">
					<?php foreach ($timeline as $timelineEntry): ?>
						<tr>
							<td class="strong"><?php
								echo h($timelineEntry['TimelineItem']['timeline_item_description']);
								echo ($timelineEntry['TimelineEntry']['action_flag'] === true) ? '<div style="display:inline-block; margin-top:-6px;" class="pull-right"><h5><span class="label label-success"> Exclude Multiple</span></h5></div>' : '';
								?>
							</td>
							<td>
								<?php
									if (!empty($timelineEntry['TimelineEntry']['timeline_date_completed'])) {
										$dateFormat = 'M j, Y';
										if ($timelineEntry['TimelineItem']['timeline_item_description'] == 'Install Commissioned' ||
											$timelineEntry['TimelineItem']['timeline_item_description'] == 'Month Paid') {
												$dateFormat = 'M, Y';
										}
										echo date_format(date_create($timelineEntry['TimelineEntry']['timeline_date_completed']), $dateFormat);
									} else {
										echo '';
									}
								?>
							</td>
						</tr>
						<?php if ($timelineEntry['TimelineItem']['timeline_item_description'] == 'Go-Live date') : ?>
						<tr>
							<td class="strong">Months Since Go-Live date:</td>
							<td><?php echo $montsSinceInstall; ?></td>
						</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
        </td>
    </tr>
</table> 
<script type='text/javascript'>
activateNav('MerchantsTimeline');
var merchantId = "<?php echo $merchant['Merchant']['id']; ?>";
$("#addInstNote").on('click', function(e) {
	e.preventDefault();
	ajaxNote(merchantId, 'Installation & Setup Note','addNote_frm'); 
	objFader('addNote_frm'); objFader('noteNothingInst')
});
</script>