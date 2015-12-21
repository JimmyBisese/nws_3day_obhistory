#! /cygdrive/c/Python27/python

# This script to read the 3-day weather report table and put the data into a CSV file
# As far as I can tell, it is the only way to get the data for the last 3 days.
#

# Jimmy Bisese

import sys
import os
from datetime import datetime
from time import gmtime, strftime
from argparse import ArgumentParser
import logging
import ConfigParser
from collections import OrderedDict, defaultdict
from urllib2 import urlopen, URLError
from bs4 import BeautifulSoup, UnicodeDammit
import pymysql


lib_path = os.path.abspath(os.path.join('lib'))
sys.path.append(lib_path)
if not os.path.exists(lib_path):
	print('Unable to find library folder. %s' % (lib_path))
	sys.exit(1)

try:
	config_file = lib_path + '/' + 'weather.conf'
	config = ConfigParser.SafeConfigParser()
	with open(config_file) as f:
		config.readfp(f)
except:
	ex = sys.exc_info()
	print('Unable to find configuration file. %s' % (ex[1]))
	sys.exit(1)

parser = ArgumentParser(description="""

    Grabs weather data from NWS tables

""")
parser.add_argument('-s', '--station', help='station to get data from (ex. KANK, KMYP, KAEJ',
				choices=['KANK', 'KAEJ', 'KPUB', 'KCOS', 'KLXV', 'KMYP', 'KDCA', 'KGUC', 'KATL'],
				required=False)
parser.add_argument("-r", "--runall", action="store_true", help='process (run) all of stations')
parser.add_argument("-l", "--logger", nargs='?', choices=["DEBUG","INFO","WARNING", "ERROR", "CRITICAL"], \
					default=config.get('DEFAULTS', 'logger_default_level'), help='set the amount of messages to print to STDOUT')

out_file_columns = config.get('DEFAULTS', 'out_file_columns').split(',')

def main():

	stations_data = defaultdict()

	conn = pymysql.connect(host=config.get('DATABASE', 'HOST'),
							user=config.get('DATABASE', 'USER'),
							passwd=config.get('DATABASE', 'PASSWD'),
							db=config.get('DATABASE', 'DB'),
							port=int(config.get('DATABASE', 'PORT')))
	
	query = 'SELECT station_code,station_name,LastRetrieval,LastModified from  ' + config.get('DATABASE', 'STATION_TABLE_NAME')
	
	try:
		cursor = conn.cursor(pymysql.cursors.DictCursor)
		cursor.execute(query)
		station_list = cursor.fetchall()
		for row in station_list:
			stations_data[row['station_code']] = row
	except:
	   logger.info('failed during getting station_list\n' + query)
	   raise

	station_codes = []
	if args.station:
		station_codes.append(args.station)
	else: 
		station_codes = sorted(stations_data.keys())

	startTime = datetime.now()

	logger.info('%s Processing starts at %s %s' % ( '#' * 25, (str(startTime))[:-3], '#' * 25 ))

	for station_code in station_codes:
		logger.info('%s Retrieving data for %-45s %s' % ( '#' * 16, stations_data[station_code]['station_name'], '#' * 16))
	
		"""
			this is where the retrieval occurs.  the web page is down loaded and parsed into data_rows
		"""
		[LastRetrieval, LastModified, data_rows] = get_data(station_code)
		logger.debug("stored LastRetrieval==%s" % (stations_data[station_code]['LastRetrieval']))
		logger.debug("new    LastRetrieval==%s" % (LastRetrieval))
		logger.info('web table contains %d rows' % (len(data_rows) ))
		
		if len(data_rows) > 0:
			"""
				this is where the data is stored
			"""
			[pre_insert_row_count, post_insert_row_count] = store_data(conn, station_code, data_rows, LastRetrieval, LastModified)
	
		if pre_insert_row_count <= 0:
			logger.warn('failed to store data')
		else:
			new_rows = post_insert_row_count - pre_insert_row_count 
			logger.info("number of new rows stored %s" % (new_rows))
	
	conn.close()
	
	logger.info("Processing complete (%s seconds elapsed)" % ((str(datetime.now() - startTime)[:-3])))
	
	return 0

def get_data(station_code):

	url = config.get('DEFAULTS', 'weather_data_url_prefix') + '/' + station_code.upper() + config.get('DEFAULTS', 'weather_data_url_file_extension')
	
	logger.debug('retrieval url: %s' % (url))
	
	# Make soup
	try:
		resp = urlopen(url)

		LastRetrieval = datetime.strptime(resp.headers['Date'], '%a, %d %b %Y %H:%M:%S %Z')
		LastModified = datetime.strptime(resp.headers['Last-Modified'], '%a, %d %b %Y %H:%M:%S %Z')
		
		logger.debug('web page timestamp: Last-Modified: ' + resp.headers['Last-Modified'])
		
		contents = resp.read()
		new_contents = UnicodeDammit.detwingle(contents)
		soup = BeautifulSoup(new_contents,  "html.parser")
		
	except URLError as e:
		logger.warn('An error occurred fetching data\n\t%s\n\t%s' % (url, e.reason))   
		return {}

	# Get table
	try:
	    tables = soup.findAll("table")
	    table = tables[3]
	except AttributeError as e:
	    logger.warn( 'No tables found, exiting' % (url, e.reason))  
	    return 1
	except LookupError as e:
		logger.warn('there is no index table[3] on the page for ' + url)
		return 1
	except IndexError as e:
		logger.warn('there is no index table[3] on the page for ' + url)
		return 1	
	
	# Get rows
	try:
		rows = table.find_all('tr')
	except AttributeError as e:
		logger.warn( 'No table rows found, exiting' % (url, e.reason))  
		return 1
	
	# first two columns are created from the table
	table_columns = out_file_columns[3:len(out_file_columns)]

	# Get data
	table_data = parse_rows(rows)

	# prepare the data read from the web page
	today = datetime.now()
	month = today.month
	year = today.year
	monthedge = 0

	data_rows = {}
	for i in table_data:

		data = dict(zip(table_columns, i))
		
		day = data['Date']

		# this gets over month/year edges.
		if int(day) <= 2 and monthedge == 0:
			monthedge = 1
		
		hour,minute = data['Time'].split(':')
		
		my_month = -1
		
		# this gets over month/year edges.
		if int(day) > 2 and monthedge == 1:
			my_month = month - 1   # the month is coming from 'localtime' not the webpage
			if my_month == 0:      # january fix
				my_month = 12
				year = year - 1
		else:
			my_month = month
		
		obs_datetime = datetime(year, my_month, int(day), int(hour), int(minute))
		
		data['STATION']   = station_code.upper()
		data['DateTime']  = obs_datetime.strftime('%Y-%m-%d %H:%M:00')
		data['TIMESTAMP'] = 'TS:' + data['DateTime']
		
		# these fields are stored in the database as numbers, but the web pages use 'NA' for missing data.  that string needs to be replaced with None
		check_field_values = ['AirTemp', 'Dewpoint', 'AirPressureAltimeter']
		for field in check_field_values:
			if data[field] == 'NA':
				data[field] = None

		data_rows[data['TIMESTAMP']] = data

	return [LastRetrieval, LastModified, data_rows]

def store_data(conn, station_code, data, LastRetrieval, LastModified):
	
	if len(data) <= 0:
		return [-1,-1];

	cursor = conn.cursor()
	
	# get the count of existing rows for this station
	count_query = "SELECT count(STATION) from " + config.get('DATABASE', 'TABLE_NAME') + " where STATION like %s"
	cursor.execute(count_query, [station_code])
	row = cursor.fetchone()
	pre_insert_row_count = row[0]
	
	query = 'insert into ' + config.get('DATABASE', 'TABLE_NAME') + '(' + ','.join(out_file_columns) + ') ' + \
				'values (' + '%s,' * (len(out_file_columns) - 1) + '%s) ' + \
				' ON DUPLICATE KEY UPDATE update_count = update_count + 1'
	
	try:
		for k, v in sorted(data.items()):
			# get all the values from the dictionary in the necessary order, 
			value_list = map(v.get, out_file_columns)
			# then insert the values in the table
			cursor.execute(query,value_list)
		conn.commit()

	except:
	   logger.info('failed during %s insert for station %s\n%s' % (config.get('DATABASE', 'TABLE_NAME'), station_code, query))
	   logger.debug('data during failure is\n\t%s' % (','.join(value_list)))
	   conn.rollback()
	   raise

	cursor.execute(count_query, [station_code])
	row = cursor.fetchone()
	post_insert_row_count = row[0]
	
	query = 'update ' + config.get('DATABASE', 'STATION_TABLE_NAME') + ' SET LastRetrieval=%s, LastModified=%s ' + \
				' WHERE station_code like %s'
	try:
		cursor.execute(query, [LastRetrieval, LastModified, station_code])
		conn.commit() 
	except:
	   logger.info('failed during %s update\n%s' % (config.get('DATABASE', 'STATION_TABLE_NAME'), query))
	   conn.rollback()
	   raise
	
	return [pre_insert_row_count, post_insert_row_count]

def parse_rows(rows):
	""" Get 'table data' from rows """
	results = []
	for row in rows:
		table_data = row.find_all('td')
		if table_data:
			results.append([data.get_text() for data in table_data])
	
	# remove the 'date' that is the last element in the list
	del results[-1]
	
	return results

def decode_html(html_string):
	converted = UnicodeDammit(html_string, is_html=True)
	if not converted.unicode:
		raise UnicodeDecodeError(
			"Failed to detect encoding, tried [%s]",
			', '.join(converted.triedEncodings))

	return converted.unicode

"""
	make logger.  this makes 2 different loggers - one is in a log file, and the other is what is displayed to the user
"""
def create_logger(args):
	
	# these are used to tweak the format of the logs. they allow multi-line log messages that display well
	class MultiLineFormatter(logging.Formatter):
		def format(self, record):
			str = logging.Formatter.format(self, record)
			header, footer = str.split(record.message)
			str = str.replace('\n', '\n' + header)
			return str
	class MultiLineFormatter2(logging.Formatter):
		def format(self, record):
			str = logging.Formatter.format(self, record)
			header, footer = str.split(record.message)
			str = str.replace('\n', '\n' + ' '*len(header))
			return str
	
	if args.logger:
		numeric_level = getattr(logging, args.logger.upper(), None)
		if not isinstance(numeric_level, int):
			raise ValueError('Invalid log level: %s' % args.logger)
		
  	logging.basicConfig(level=logging.DEBUG, filename=os.devnull, format='%(levelname)s: %(message)s')
	logger = logging.getLogger(__name__)
	
	# create a file handler for STDOUT
	stdout_handler = logging.StreamHandler(stream=sys.stdout)
	stdout_handler.setLevel(numeric_level)
	# create a logging format using a cusom formatter
	stdout_formatter = MultiLineFormatter2('%(levelname)-5s: %(message)s')
	stdout_handler.setFormatter(stdout_formatter)
	# add the handlers to the logger
	logger.addHandler(stdout_handler)
	
	# create a file handler for the log file
	log_file_handler = logging.FileHandler(config.get('PATHS', 'log_file'))
	log_file_handler.setLevel(config.get('DEFAULTS', 'log_file_default_level'))
	log_file_formatter = MultiLineFormatter('%(asctime)s - %(name)s - %(levelname)-5s - %(message)s')
	log_file_handler.setFormatter(log_file_formatter)
	logger.addHandler(log_file_handler)
	
	return logger

if __name__ == '__main__':

	config.read(config_file)
	
	try:
		args = parser.parse_args()
	except AttributeError as e:
		logger.info('unable to parse args' % (e.reason))   
		raise
	
	logger = create_logger(args)
	
	status = main()

	sys.exit(status)
