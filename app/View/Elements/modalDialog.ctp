<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" onClick="animationComplete()" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="ModalContainer">
		<!-- Content renders here using Ajax -->
	</div>
</div>

<script type="text/javascript">

	function animationComplete(modalObj) {
		waitAnimationComplete = setTimeout(function(modalObj) {
			if ($('#myModal').is(":hidden")) {
				clearInterval(waitAnimationComplete);
				location.reload();
			} else {
				clearTimeout(waitAnimationComplete);
			}

		}, 500);
	}
</script>