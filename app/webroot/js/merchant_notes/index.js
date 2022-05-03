$(function() {
	toggleRepAuthorDisplay($("#MerchantNoteAuthorTypeAuthor"));
	toggleRepAuthorDisplay($("#MerchantNoteAuthorTypeRep"));
	$("input[name=author_type]").on('click' ,function() {
		toggleRepAuthorDisplay($(this));
	});
});

function toggleRepAuthorDisplay(obj){
	if ($(obj).val() === "author" && $(obj).is(":checked")) {
		$("#MerchantNoteAuthorName").parent().show();
		$("#MerchantNoteUserId").parent().hide();
	} else if($(obj).val() === "rep" && $(obj).is(":checked")) {
		$("#MerchantNoteAuthorName").val(null);
		$("#MerchantNoteUserId").parent().show();
		$("#MerchantNoteAuthorName").parent().hide();
	}
}