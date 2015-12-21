<?php 

	$configs = include('./lib/weather.conf.php');
	
	$script_name = $_SERVER['SCRIPT_NAME'];
	
	// this is used to toggle between the two web page formats ('Chart' and 'Table')
	$other_script_name = str_replace('table','chart', $script_name);
	
	$site_code = "KANK";  # default station for first time visitor.  This is in Salida Colorado
	
	if(isset($_GET['station']))
	{
		$site_code = $_GET['station'];
		setcookie("weather-table-station", $site_code);
	}
	elseif(isset($_COOKIE["weather-table-station"]))
	{
		$site_code = $_COOKIE["weather-table-station"];
	}
	
	// connect to the database, or die with a warning
	$error_reporting_setting = error_reporting();
	error_reporting(0);
	
	$db_connection = new mysqli($configs['HOST'], $configs['USER'], $configs['PASSWD'], $configs['DB'], $configs['PORT']);
	
	if($db_connection->connect_errno)
	{
		die("Unable to connect to weather database.  Please contact the system administrator");
	}
	
	error_reporting($error_reporting_setting);
	
	// get the station name for the UI
	$result = $db_connection->query("
SELECT station_name
FROM stations
WHERE station_code LIKE '$site_code'
	");
	$row = $result->fetch_assoc();
	$station_name = $row['station_name'];

	// get the list of available stations for the scroll list
	$station_list_result = $db_connection->query("
SELECT station_code,station_name
FROM stations LEFT JOIN weather_data ON station_code = STATION
GROUP BY STATION
	");

	// get the data that will go in the table
	$weather_result = $db_connection->query("
SELECT STATION, DATE_FORMAT(DateTime, '%a %b %d %h:%i %p') AS DateTimeF, 
AirTemp, DewPoint, Wind, Visibility, Weather, SkyCondition, RelativeHumidity, AirPressureAltimeter AS AirPressure
FROM weather_data
WHERE STATION LIKE '$site_code'
ORDER BY STATION,DateTime
	");

	
?>