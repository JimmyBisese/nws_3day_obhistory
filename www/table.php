<?php 
	$h1_label = 'NWS Weather Viewer';
	$select_label = 'Select a National Weather Service (NWS) Weather station';
	$footer_label = 'Jimmy Bisese - Tetra Tech Inc., Fairfax VA';
	
	include './lib/table_lib.php';
?>

<!--- Begin HTML --->
<head>
	<title><?php echo $h1_label; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="data for NWIS weather station taken from '3 Day History' HTML" />
	<meta name="robots" content="index, follow" />

	<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.1/css/jquery.dataTables_themeroller.css" rel="stylesheet" />
	<link type="text/css" href="//code.jquery.com/ui/1.9.1/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css" rel="stylesheet" />

	<link type="text/css" href="style.css" rel="stylesheet">
	
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
	
 	<style type="text/css">
		#MainDataTables {display: none;} 
	</style> 
	
	<script type="text/javascript">

		var defaultOptions = {
			"bJQueryUI": true,
			"order": [[ 0, "desc" ]],

	        'scrollY':        '65vh',
	        'scrollCollapse': true,
	        'paging':         false,
			

			"columnDefs": [
				{ "width": "15px", "targets": 0 },
				{ "sClass": "right", "targets": [1,2,3,4] },
				{ "width": "15px", "targets": [1,2,3,4]},
				{ "width": "120px", "targets": [5]}
			]
		} ;
		
		 $('html').addClass('js');
	
		$(document).ready(function(){
 			$('#WeatherTable').dataTable(defaultOptions);
			$('#MainDataTables').show();
		});
	</script>
</head>

<body>
	<div class="header">
		<img src="./images/tt_shortcut.PNG" alt="Tetra Tech, Inc">
		<H1 ><?php echo $h1_label; ?></H1>
	</div>

	<div id="ChartLink" >
		<H1 style="display: inline; text-align:left"><?php echo $station_name; ?></H1>
		<a style="float:right" href="http://<?php echo $_SERVER['SERVER_NAME'] . $other_script_name; ?>?station=<?php echo($station); ?>">
			Chart for <?php echo $station_name; ?></a>
	</div>
	<div class="make_radius_border">
		<div class="select_form" >
			<form class="lfloat" action="http://<?php echo $_SERVER['SERVER_NAME'] . $script_name; ?>">
				<?php echo $select_label; ?></br>
				<select name="station" onchange="this.form.submit()" >
				<?php 
					while ($row = $station_list_result->fetch_assoc())
					{
						$selected = (isset($station) && $station ==  $row['station_code']) ? 'selected' : '';
						echo "<option value='" . $row['station_code'] . "' $selected >" . $row['station_name'] . "</option>";
					}
				?>
				</select>
			</form>
		</div>	
		<div id="MainDataTables">
			<table id="WeatherTable" class="compact cell-border pageResize nowrap "  >
			<thead>
				<th>Date</th>
				<th>Air Temp</th>
				<th>Dew Point</th>
				<th>Rel Humidity</th>
				<th>Air Pressure</th>
				<th>Wind</th>
				<th>Visibility</th>
				<th>Weather</th>
				<th>Sky Conditions</th>
			</thead>
			
			<tbody>
				<?php 
					while ($row = $weather_result->fetch_assoc())
					{
						echo "<tr>
							<td>{$row['DateTimeF']}</td>
							<td>{$row['AirTemp']}</td>
							<td>{$row['DewPoint']}</td>
							<td>{$row['RelativeHumidity']}</td>
							<td>{$row['AirPressure']}</td>
							<td>{$row['Wind']}</td>
							<td>{$row['Visibility']}</td>
							<td>{$row['Weather']}</td>
							<td>{$row['SkyCondition']}</td>
						</tr>";
					}
				?>
			</tbody>
			</table>
		</div>
	</div>
		
	<div class="footer">
		<div class="footer_text"><?php echo $footer_label; ?></div>
	</div>
<body>
</html>