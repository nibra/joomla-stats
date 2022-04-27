<?php
/**
 * @var string $id A unique ID for the chart
 * @var string $title A title for the chart
 * @var string $subtitle A subtitle for the chart
 *
 * All the following lists MUST have the same length.
 * @var string $labels A comma separated list of labels
 * @var string $values A comma separated list of values
 * @var string $colors A comma separated list of colors
 * @var string $borders A comma separated list of border colors
 */
?>
<canvas id="joomlaChart-<?php echo $id; ?>" width="100" height="100"></canvas>

<script type="text/javascript">
	var ctx<?php echo $id; ?> = document.getElementById('joomlaChart-<?php echo $id; ?>').getContext('2d');
	var myChart<?php echo $id; ?> = new Chart(ctx<?php echo $id; ?>, {
		type: 'doughnut',
		data: {
			"labels": [<?php echo $labels; ?>],
			"datasets": [
				{
					"data": [<?php echo $values; ?>],
					"backgroundColor": [<?php echo $colors; ?>],
					"borderColor": [<?php echo $borders; ?>]
				}
			]
		},
		options: {
			legend: {
				display: false,
			},
			plugins: {
				title: {
					display: true,
					text: "<?php echo $title; ?>"
				},
				subtitle: {
					display: true,
					text: "<?php echo $subtitle; ?>"
				}
			},
			tooltips: {
				callbacks: {
					label: function (tooltipItem, data) {
						let value = Math.round(data.datasets[0].data[tooltipItem.index] * 10) / 10;
						let a = Math.floor(value);
						let b = Math.round((value - a) * 10);
						return data.labels[tooltipItem.index] + ': ' + a + '.' + b + '%';
					}
				}
			}
		}
	});
</script>

