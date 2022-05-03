<span class="text-center center-block" style="font-size:10pt;">
	<strong>Acquiring Merchants Year 
		<?php echo $this->Form->year('barChartYear', date('Y') - 7, date('Y'), array('empty' => false, 'default' => date('Y'), 'style' => 'border-style: none none solid none;'));?>
	</strong>
</span>
<canvas id="canvas-1551"></canvas>
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
	    scales: {
			xAxes: [{
				ticks: {
					suggestedMax: 100,
					max: 1000,
					min: 0,
					stepSize: 2
				}
			}]
		},
	    title: {
            display: false,
            text: 'Acquiring Merchants This Year'
        },
        legend: {
        	display: false
        }
    };
	var chrt2 = document.getElementById('canvas-1551').getContext('2d');
	var chrt2 = new Chart(chrt2, {
	    // The type of chart we want to create
	    type: 'bar',

	    // The data for our dataset
	    data: {
	        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
	        datasets: [{
	            label: "Merchants",
	            backgroundColor: [
	            	"rgba(255, 0, 0, 0.2)","rgba(48, 209, 46, 0.2)","rgba(255, 178, 0, 0.2)","rgba(201, 203, 207, 0.2)",
	            	"rgba(255, 0, 0, 0.2)","rgba(48, 209, 46, 0.2)","rgba(255, 178, 0, 0.2)","rgba(201, 203, 207, 0.2)",
	            	"rgba(255, 0, 0, 0.2)","rgba(48, 209, 46, 0.2)","rgba(255, 178, 0, 0.2)","rgba(201, 203, 207, 0.2)"
            	],
				borderColor: [
				"rgb(255, 255, 255)","rgb(212, 255, 175)","rgb(255, 205, 86)","rgb(174, 176, 178)",
				"rgb(255, 255, 255)","rgb(212, 255, 175)","rgb(255, 205, 86)","rgb(174, 176, 178)",
				"rgb(255, 255, 255)","rgb(212, 255, 175)","rgb(255, 205, 86)","rgb(174, 176, 178)"
				],
				borderWidth:1,
	            data: <?php echo $acquiringMerchCount; ?>,
	        }],
	    },

	    // Configuration options go here
	    options: options
	});
 $("#barChartYearYear").change(function () {
 	updateChart(chrt2, 'getAcquiringMerch', null,$(this).val());
 });
</script>