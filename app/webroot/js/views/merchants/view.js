$(document).ready(function (){
	$('.close-button').click(function (){
		$('#addNote_frm').slideUp();
	});
});

function ajaxRequestCompleteListener() {
	$(document).ajaxComplete(function(event, xhr, settings) {
		if (settings.url.indexOf("MerchantNotes/add") > 0) {
			/*Refresh page if a note is successfully submited but not when the note form HTML is returned in the response because it must be displayed*/
			if (xhr.status === 200 && xhr.responseText.indexOf('id="MerchantNoteAddForm"') == -1) {
				location.reload();
			}
		}
	});
}