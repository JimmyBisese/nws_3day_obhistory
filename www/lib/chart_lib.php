<?php 

	$configs = include('./lib/weather.conf.php');

	// this makes the script easier to move around in the filesystem
	$script_name = $_SERVER['SCRIPT_NAME'];
	$other_script_name = str_replace('chart','table', $script_name);

	$site_code = "KAEJ";  # default station for first time visitor
	
	$site_codeB = "KANK"; # station 'to compare to' that is always showing
	
	if(isset($_GET['station']))
	{
		$site_code = $_GET['station'];
		setcookie("weather-chart-station", $site_code);
	}
	elseif(isset($_COOKIE["weather-chart-station"]))
	{
		$site_code = $_COOKIE["weather-chart-station"];
	}
	
	if(isset($_GET['stationB']))
	{
		$site_codeB = $_GET['stationB'];
		setcookie("weather-chart-stationB", $site_codeB);
	}
	elseif(isset($_COOKIE["weather-chart-stationB"]))
	{
		$site_codeB = $_COOKIE["weather-chart-stationB"];
	}

	// connect to the database, or die with a warning
	$error_reporting_setting = error_reporting();
	error_reporting(0);
	date_default_timezone_set('America/Denver');
	
	// all the connection information is from the configuration file - not stored anywhere else
	$db_connection = new mysqli($configs['HOST'], $configs['USER'], $configs['PASSWD'], $configs['DB'], $configs['PORT']);	
	
	if($db_connection->connect_errno)
	{
		die("Unable to connect to weather database.  Please contact the system administrator");
	}
	error_reporting($error_reporting_setting);

	// get the site_name
	$result = $db_connection->query("
SELECT site_name as site_name, short_name as short_site_name
FROM stations
WHERE site_code like '$site_code'
");
	$row = $result->fetch_assoc();
	$site_name = $row['site_name'];
	$short_site_name = $row['short_site_name'];

	// get the 'compare to' site_name
	$result = $db_connection->query("
SELECT site_name as site_name, short_name as short_site_name
FROM stations
WHERE site_code like '$site_codeB'
");
	$row = $result->fetch_assoc();
	$site_nameB = $row['site_name'];
	
	function site_name()
	{
		return "<h1>$site_name</h1>";
	}
	function site_nameB()
	{
		return $site_nameB;
	}
	
	
	$short_site_nameB = $row['short_site_name'];
	
	// get the list of stations shown in the select list
	$station_list_result = $db_connection->query("
SELECT s.site_code,s.site_name as site_name
FROM stations s left join weather_data d on s.site_code = d.site_code
GROUP BY s.site_code
");
	
	// get the list of stations shown in the select list
$station_list_resultB = $db_connection->query("
SELECT s.site_code as site_code,s.site_name as site_name
FROM stations s left join weather_data d on s.site_code = d.site_code
GROUP BY s.site_code
");

	// get the time-series data
	$station_data = $db_connection->query("
select (UNIX_TIMESTAMP(d.DateTime)- 7*60*60)   as date_seconds, d.AirTemp, d.AirPressureAltimeter
from weather_data d
where d.site_code like '$site_code'
  and d.DateTime > DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
ORDER BY d.DateTime
");

	$timeseriesA = array();
	$timeseriesA_pressure = array();

	while ($row = $station_data->fetch_assoc())
	{
		$test = array((int) $row['date_seconds'] * 1000, (int) $row['AirTemp']);
		$timeseriesA[] = $test;
		$test = array((int) $row['date_seconds']* 1000, (double) $row['AirPressureAltimeter']);
		$timeseriesA_pressure[] = $test;
	}

	// get the time-series data for the 'compare to' station
	$station_dataB = $db_connection->query("
select (UNIX_TIMESTAMP(d.DateTime)- 7*60*60)   as date_seconds, d.AirTemp, d.AirPressureAltimeter
from weather_data d
where d.site_code like '$site_codeB'
  and d.DateTime > DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
ORDER BY d.DateTime
");

	$timeseriesB = array();
	$timeseriesB_pressure = array();
	
	while ($row = $station_dataB->fetch_assoc())
	{
		$test = array((int) $row['date_seconds'] * 1000, (int) $row['AirTemp']);
		$timeseriesB[] = $test;
		$test = array((int) $row['date_seconds']* 1000, (double) $row['AirPressureAltimeter']);
		$timeseriesB_pressure[] = $test;
	}
?>