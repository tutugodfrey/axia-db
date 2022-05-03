<div id="leftSideNav" class="col-xs-3 col-sm-3 col-md-2 hidden-print" style="max-width: 250px;top:15px; padding-left: 2px; padding-right: 0px">

	<?php 
	/* Show Merchant Navigation */
	if ($showMerchNav) {
		echo $this->element('Layout/Merchant/navigation');
	}

	/* Show User Navigation */
	if ($showUserNav) {
		echo $this->element('Layout/User/navigation');
	}

	if (isset($displayIconLegend) && $displayIconLegend === true) {
		echo $this->element('Layout/iconLegend');
	}
	?>
</div>
<script>
//Make left nav move with y-axis scrolling
var topOffset = parseInt($("#leftSideNav").css('top'));
$(window).scroll(function(){
    $('#leftSideNav').css({
        'top': $(this).scrollTop() + topOffset
    });
});
</script>