$('#subreasonField').hide();
$('#srLabel').hide();

$('#subReasonDD').on('change', subReasonHandler);
function subReasonHandler() {
	if ($("#subReasonDD option:selected").text() === 'Switched MSP') {
		$('#subreasonField').show();
		$('#srLabel').show();
	} else {
		$('#subreasonField').hide();
		$('#subreasonField').val('');
		$('#srLabel').hide();
	}
}
subReasonHandler();
activateNav('MerchantCancellationsView');