<span class="text-center center-block" style="font-size:10pt;">
	<strong>Merchant Activity 
		<?php echo $this->Form->month('piChartMonth', array('empty' => false, 'default' => date('m'), 'style' => 'border-style: none none solid none;'));?>
	</strong>
</span>
<canvas id="canvas-31415"></canvas>
<script>
	var options = {
		responsive: true,
		responsiveAnimationDuration : 1500,
		maintainAspectRatio:true,
			yAxes: [{
				stacked: true,
				gridLines: {
					display: true,
					color: "rgba(255,99,132,0.2)"
				}
		}],
		legend: {
			position: 'bottom'
		},
		title: {
			display: false,
			text: 'Merchant Activity This Month'
		}
	};
	var chrt1 = document.getElementById('canvas-31415').getContext('2d');
	var chrt1 = new Chart(chrt1, {
		// The type of chart we want to create
		type: 'pie',

		// The data for our dataset
		data: {
			labels: ["Cancelled", "Approved", "Are Live", "In Underwriting", "No data found"],
			datasets: [{
				backgroundColor: ["rgba(255, 0, 0, 0.9)","rgba(48, 209, 46, 0.9)","rgba(51, 204, 255, 0.4)","rgba(255, 255, 0, 0.2)"],
				borderWidth:0,
				data: <?php echo $piChartData; ?>,
			}],
		},

		// Configuration options go here
		options: options
	});

 $("#piChartMonthMonth").change(function () {
 	updateChart(chrt1, 'getPiChartData', $(this).val());
 });
</script>