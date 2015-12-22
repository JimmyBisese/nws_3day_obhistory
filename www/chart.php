<!--- Begin HTML --->
<?php 
	$h1_label = 'NWS Weather Viewer';
	$select_label = 'Select a National Weather Service (NWS) Weather station';
	$select_labelB = 'Select a comparison NWS Weather  station';
	$footer_label = 'Jimmy Bisese - Tetra Tech Inc., Fairfax VA';
?>
	
<head>
	<title><?php echo $h1_label?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="data for NWIS weather station taken from '3 Day History' HTML" />
	<meta name="robots" content="index, follow" />

	<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.1/css/jquery.dataTables_themeroller.css" rel="stylesheet" />
	<link type="text/css" href="//code.jquery.com/ui/1.9.1/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css" rel="stylesheet" />
	<link type="text/css" href="style.css" rel="stylesheet">
	
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="//code.highcharts.com/stock/highstock.js"></script>
</head>

<?php include './lib/chart_lib.php'; ?>

<script type="text/javascript">

	var orgHighchartsRangeSelectorPrototypeRender = Highcharts.RangeSelector.prototype.render;

	Highcharts.RangeSelector.prototype.render = function (min, max) 
	{
		orgHighchartsRangeSelectorPrototypeRender.apply(this, [min, max]);
		var leftPosition = this.chart.plotLeft,
			topPosition = this.chart.plotTop - 100,
			space = 2;
		this.zoomText.attr({
			x: leftPosition,
			y: topPosition + 15
		});
	   
		leftPosition += this.zoomText.getBBox().width;
		for (var i = 0; i < this.buttons.length; i++) 
		{
			this.buttons[i].attr({
				x: leftPosition,
				y: topPosition 
			});
			leftPosition += this.buttons[i].width + space;
		}
	};

	$(function () 
	{
		$('#HighChartContainer').highcharts('StockChart', 
		{
			chart: 
			{
				type: 'spline',
				zoomType: 'x',
				borderWidth: 2,
				borderRadius: 20,
				borderColor: '#005596',
				renderTo: 'container'
			},
			navigator: 
			{
				height: 60,
				margin: 5
			},
			legend: 
			{
				layout: 'vertical',
				floating: 'true',
				verticalAlign: 'top',
				align: 'right',
				y: -14
			},
			title: 
			{
				text:  '<?php echo 'Air Temperature and Air Pressure at ' . $site_code . ' - ' . $site_name; ?>',
				style: {
					fontFamily: "'Open Sans', Helvetica, Arial"
				}
			},
			subtitle: 
			{
				text:  '<?php echo 'Shown against Air Temperature and Air Pressure at ' . $site_codeB . ' - ' . $site_nameB; ?>',
				style: {
					fontFamily:  "'Open Sans', Helvetica, Arial"
				}
			},
			xAxis: 
			{
				type: 'datetime',
				title: {
					text: 'Date'
				},
				gridLineWidth: 1,
				minorGridLineWidth: 0,
				range: 3 * 24 * 3600 * 1000,
				events: 
				{
					afterSetExtremes: function(e) 
					{
						var maxDistance = 3 * 30 * 24 * 3600 * 1000; //3 months time
						var xaxis = this;
						if ((e.max - e.min) > maxDistance) 
						{
							var min = e.max - maxDistance;
							var max = e.max;
							window.setTimeout(function() {
								xaxis.setExtremes(min, max);
							}, 1);
						}
					}
				}
			},
			rangeSelector: 
			{
				buttonTheme: 
				{ // styles for the buttons
					fill: 'none',
					stroke: 'none',
					'stroke-width': 0,
					r: 8,
					style: 
					{
						color: '#039',
						fontWeight: 'bold'
					},
					states: 
					{
						hover: { },
						select: 
						{
							fill: '#039',
							style: {
								color: 'white'
							}
						}
					}
				},
				inputBoxBorderColor: 'gray',
				inputBoxWidth: 100,
				inputBoxHeight: 18,
				inputStyle: {
					color: '#039',
					fontWeight: 'bold',
					fontFamily: "'Open Sans', Helvetica, Arial"

				},
				labelStyle: {
					color: 'silver',
					fontWeight: 'bold',
					fontFamily: "'Open Sans', Helvetica, Arial"
				},
				selected: 1,
				buttons: 
				[
					{
						type: 'day',
						count: 1,
						text: '1d'
					}, 
					{
						type: 'day',
						count: 3,
						text: '3d'
					}, 
					{
						type: 'week',
						count: 1,
						text: '1w'
					}, 
					{
						type: 'week',
						count: 2,
						text: '2w'
					}, 
					{
						type: 'week',
						count: 3,
						text: '3w'
					}, 
					{
						type: 'all',
						text: '3M'
					}
				]
			},
			yAxis: 
			[
				{ // primary axis
					opposite: false,
					title: 
					{
						text: 'Air Temperature (\xB0F)'
					},
					labels: 
					{
						format: '{value} \xB0F'
					}
				},
				{ // secondary axis
					opposite: true,
					gridLineWidth: 0,
					title: 
					{
						text: 'Air Pressure (inches)',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					},
					labels: 
					{
						format: '{value} in.',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					}
				}
			],
			plotOptions:
			{
				series:{
					dataGrouping: { enabled: false },
					connectNulls: false
					}
			},
			series: 
			[
				{
					name: '<?php echo $short_site_name; ?> Air Temperature',
					data: <?php echo json_encode($timeseriesA); ?>,
					color: '#FF0000'
				},
				{
					name: '<?php echo $short_site_name; ?> Air Pressure',
					data: <?php echo json_encode($timeseriesA_pressure); ?>,
					color: '#FFCCFF',
					yAxis: 1,
					marker: {
						enabled: false
					}
				},
				{
					name: '<?php echo $short_site_nameB; ?> Air Temperature',
					data: <?php echo json_encode($timeseriesB);  ?>,
					yAxis: 0,
					color: '#0000FF'
				},
				{
					name: '<?php echo $short_site_nameB; ?> Air Pressure',
					data: <?php echo json_encode($timeseriesB_pressure); ?>,
					yAxis: 1,
					marker: {
						enabled: false
					},
					color: '#66CCFF'
				}
			]
		});
	});
</script>

<body>

	<div class="header">
		<img src="./images/tt_shortcut.PNG" alt="Tetra Tech, Inc">
		<H1 ><?php echo $h1_label; ?></H1>
	</div>
	
	<div class="make_radius_border">
		<div class="select_form" >
		
			<form class="lfloat" action="http://<?php echo $_SERVER['SERVER_NAME'] . $script_name; ?>">
				<div class="select_label"><?php echo $select_label; ?></div>
				<select class="select_box" name="station" onchange="this.form.submit()" >
				<?php 
					while ($row = $station_list_result->fetch_assoc())
					{
						$selected = (isset($site_code) && $site_code ==  $row['site_code']) ? 'selected' : '';
						echo "<option value='" . $row['site_code'] . "' $selected >" . $row['site_code'] . ' - ' .$row['site_name'] . "</option>";
					}
				?>
				</select>
				<input style="border-left: 20px;" type="button" 
					onclick="window.open('http://<?php echo $_SERVER['SERVER_NAME'] . $other_script_name ; ?>?site_code=<?php echo($site_code); ?>', '_table_target');" 
					value="View Table" />
			</form>
		
			<form class="rfloat" action="http://<?php echo $_SERVER['SERVER_NAME'] . $script_name; ?>">
				<div class="select_label"><?php echo $select_labelB; ?></div>
				<select class="select_box" name="stationB" onchange="this.form.submit()"  >
				<?php 
					while ($row = $station_list_resultB->fetch_assoc())
					{
						$selected = (isset($site_codeB) && $site_codeB ==  $row['site_code']) ? 'selected' : '';
						echo "<option value='" . $row['site_code'] . "' $selected >" . $row['site_code'] . ' - ' .$row['site_name'] . "</option>";
					}
				?>
				</select>
				<input style="border-left: 20px;" type="button" 
					onclick="window.open('http://<?php echo $_SERVER['SERVER_NAME'] . $other_script_name ; ?>?site_code=<?php echo($site_codeB); ?>', '_table_target');" 
					value="View Table" />
			</form>
		</div>	
	
		<div class="mainchart" id="HighChartContainer" ></div>
	</div>
	
	<div class="footer">
		<div class="footer_text"><?php echo $footer_label; ?></div>
	</div>
</body>
</html>