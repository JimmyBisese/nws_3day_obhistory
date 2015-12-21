<?php 

	$configs = include('./lib/weather.conf.php');
	
	$script_name = $_SERVER['SCRIPT_NAME'];
	
	// this is used to toggle between the two web page formats ('Chart' and 'Table')
	$other_script_name = str_replace('table','chart', $script_name);
	
	$site_code = "KANK";  # default station for first time visitor.  This is in Salida Colorado
	
	if(isset($_GET['site_code']))
	{
		$site_code = $_GET['site_code'];
		setcookie("weather-table-site_code", $site_code);
	}
	elseif(isset($_COOKIE["weather-table-site_code"]))
	{
		$site_code = $_COOKIE["weather-table-site_code"];
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
SELECT s.site_name
FROM stations s
WHERE s.site_code LIKE '$site_code'
	");
	$row = $result->fetch_assoc();
	$site_name = $row['site_name'];

	// get the list of available stations for the scroll list
	$station_list_result = $db_connection->query("
SELECT s.site_code, s.site_name
FROM stations s LEFT JOIN weather_data d ON s.site_code = d.site_code
GROUP BY s.site_code
	");

	// get the data that will go in the table
	$weather_result = $db_connection->query("
SELECT site_code, DATE_FORMAT(DateTime, '%a %b %d %h:%i %p') AS DateTimeF, 
AirTemp, DewPoint, Wind, Visibility, Weather, SkyCondition, RelativeHumidity, AirPressureAltimeter AS AirPressure
FROM weather_data
WHERE site_code LIKE '$site_code'
ORDER BY site_code, DateTime
	");

	
?>