<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" onClick="location.reload();
                "data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="panel-title" id="myModalLabel"><?php echo (!empty($viewTitle) ? $viewTitle : ''); ?></h4>
    </div>
    <div class="modal-body">
        <span class="form-control"><span class="text-info small"><?php echo h($decriptedValue); ?></span></span>
    </div>
    <div class="modal-footer">          
        <button type="button" class="btn btn-danger" onClick="location.reload();" data-dismiss="modal">Close</button>        
    </div>
</div>