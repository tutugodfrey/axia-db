<?php 
$viewTitle = __('Accounting Report');
$this->Html->addCrumb($viewTitle, array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));

?>
<input type="hidden" id="thisViewTitle" value="<?php echo $viewTitle; ?>" />
<link rel="stylesheet" type="text/css" href="/dataTables/css/jquery.dataTables.min.css"/ media='all'>
 
<script type="text/javascript" src="/dataTables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/dataTables/js/dataTables.buttons.min.js"></script>

<?php
$exportLinks = [];
if (!empty($reportData)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Accounting Report'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, 
		array_merge([
			'plugin' => false,
			'controller' => 'MerchantAches',
			'action' => 'accounting_report',
			'ext' => 'csv',
			'?' => $this->request->query,
		],
		Hash::extract($this->request->params, 'paging.MerchantAch.options'))
	);
}
echo $this->element('Layout/Reports/accounting_filter_form', compact('exportLinks'));
?>
<div class="row">
	<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
		<?php echo $this->element('MerchantAches/accounting_report_content'); ?>
	</div>
</div>
<script>
	$('#MerchantAchUserId').selectize();
	//Auto select remembered option after form submission
	$('#MerchantAchUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("MerchantAch.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
	$(document).ready(function() {
		// //Init data table plugin
		var table = $('#accountingReportTable').DataTable( {
			"paging": false,
			"sort": false,
			"dom": '<"pull-left"B><"pull-left has-success"f>',//show search 
			buttons: [
	            {
	                text: 'Mark all as Completed',
	                action: function ( e, dt, node, config ) {
	                	pendCount = $('[name="pending-invoice"]').length
	                    if (pendCount) {
	                    	if (confirm('You are about to update ' + pendCount + ' pending invoices as "Completed", are you sure?')) {
	                    		pendInvIds = {};
	                    		$('[name="pending-invoice"]').each(function(idx) {
									pendInvIds[idx] = $(this).attr('data-pend-inv-id');
								});
								updateStatus(true, pendInvIds);
	                    	}
	                    } else {
	                    	alert('Nothing to update, there are no pending invoices in this report.')
	                    }
	                },
	                className: 'btn btn-primary btn-sm'
	            }
	        ]
		});
		$('#accountingReportTable_filter input[type="search"]').prop('placeholder', 'Find data within results');
		$('a.toggle-vis').on( 'click', function (e) {
	        e.preventDefault();
	 		$(this).toggleClass('btn-success');
	 		$(this).toggleClass('btn-default');
	        // Get the column API object
	        var column = table.column( $(this).attr('data-column') );
	 		$('#accountingReportTable').prop( "style", "width:min-content" );
	        // Toggle the visibility
	        column.visible( ! column.visible() );
	    } );
	});

/**
 * updateStatus
 * Sends ajax request to update the status of invoice
 *
 * @param boolean|integer setCompleted when true or equivalent integer, the invoice(s) will be marked as completed
 * @param invIds mixed sting|integer the invoice id or an array of invoice ids to update
 */
	function updateStatus(setCompleted, invIds) {
		if (typeof invIds === 'string' || invIds instanceof String) {
			invIds = {0:invIds};
		}
		$('#updateCompleteMsg').remove();
		$.ajax({
            type: "POST",
            url: '/MerchantAches/updateStatus/' + setCompleted,
            data: invIds,
            dataType: "text",
            success: function(data) {
            	$('#accountingReportTable_wrapper').append('<span id="updateCompleteMsg" class="text-center panel-heading col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 alert alert-success shadow strong">Invoice(s) updated! Refreshing report...</span>');
                location.reload();
            },
            error: function(data) {
                /*If user session expired the server will return a Forbidden status 403
                 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
                    if(data.status===403) {
                    	$('#accountingReportTable_wrapper').append('<span id="updateCompleteMsg" class="text-center panel-heading col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 label-danger error shadow strong">Session Expired!</span>');
						location.reload();
						return;
                    }
                    console.log($.parseJSON(data.responseText));
                    $('#accountingReportTable_wrapper').append('<span id="updateCompleteMsg" class="text-center panel-heading col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 alert alert-danger shadow strong">ERROR: Unexpected error! Please try again.</span>');
                  }
        });
	}
</script>

