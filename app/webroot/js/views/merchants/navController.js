function activateNav(objIdStr) {
	navObj = $('#' + objIdStr);

 	$("#merchNavContents>a").removeClass('list-group-item-success strong');
	$('#activeNavChevron').remove();
	$(navObj).prepend('<span class="glyphicon glyphicon-triangle-right" id="activeNavChevron" style="margin-right: 5px; margin-left: -14px;"></span>');
	$(navObj).addClass('list-group-item-success strong');
	$("#activeNavChevron").animate({"margin-left": '0px'}, "fast");
}