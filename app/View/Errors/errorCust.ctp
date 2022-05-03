
<div class="jumbotron">
	<div class="col col-md-offset-1">
		<h3><?php echo $name; ?></h3>
		<p class="text-muted">
			<?php
			printf(__('The requested address %s was not found on this server.'), "<strong>'" . htmlspecialchars($url) . "'</strong>");
			?>
		</p>
	</div>
</div>
