# NWS3DayObHistory
Proof of concept project that includes backend scripts to scrape National Weather Service (NWS) 
"Weather observations for the past three days" (3 Day Observed History) HTML pages and store contents in a database.  
Also includes simple PHP/Javascript web application to share data in chart and table formats.
<p>
An example of the files that are being scraped is:<p>
http://w1.weather.gov/data/obhistory/KANK.html
<P>
<p>
There are 16 data fields, but only the Air Temperature and Pressure (altimeter (in.)) are reliable numeric values.  
<br>
These are stored as floats in the database.  The rest of the values are stored as strings.


<table style="font-size: 8px;" cellspacing="3" cellpadding="2" border="0" width="670"><tr align="center" bgcolor="#b0c4de"><th rowspan="3" width="17">D<br>a<br>t<br>e</th><th rowspan="3" width="32">Time<br>(mst)</th>
							<th rowspan="3" width="80">Wind<br>(mph)</th><th rowspan="3" width="40">Vis.<br>(mi.)</th><th rowspan="3" width="80">Weather</th><th rowspan="3" width="65">Sky Cond.</th>
							<th colspan="4">Temperature (&ordm;F)</th><th rowspan="3" width="65">Relative<br>Humidity</th><th rowspan="3" width="80">Wind<br>Chill<br>(&deg;F)</th><th rowspan="3" width="80">Heat<br>Index<br>(&deg;F)</th><th colspan="2">Pressure</th><th colspan="3">Precipitation (in.)</th></tr>
							<tr align="center" bgcolor="#b0c4de"><th rowspan="2" width="45">Air</th><th rowspan="2" width="26">Dwpt</th><th colspan="2">6 hour</th>
							<th rowspan="2" width="40">altimeter<br>(in)</th><th rowspan="2" width="40">sea level<br>(mb)</th><th rowspan="2" width="24">1 hr</th>
							<th rowspan="2" width="24">3 hr</th><th rowspan="2" width="30">6 hr</th></tr>
							<tr align="center" bgcolor="#b0c4de"><th width="26">Max.</th><th width="26">Min.</th></tr><tr align="center" valign="top" bgcolor="#eeeeee"><td>19</td><td align="right">14:55</td><td>S 17 G 29</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>45</td><td>21</td>
    <td></td><td></td><td>39%</td><td>38</td><td>NA</td><td>30.14</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#f5f5f5"><td>19</td><td align="right">14:35</td><td>S 15 G 24</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>45</td><td>22</td>
    <td></td><td></td><td>40%</td><td>38</td><td>NA</td><td>30.14</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#eeeeee"><td>19</td><td align="right">14:15</td><td>SW 14 G 24</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>46</td><td>22</td>
    <td></td><td></td><td>39%</td><td>40</td><td>NA</td><td>30.14</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#f5f5f5"><td>19</td><td align="right">13:55</td><td>S 17 G 28</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>46</td><td>22</td>
    <td></td><td></td><td>38%</td><td>39</td><td>NA</td><td>30.14</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#eeeeee"><td>19</td><td align="right">13:35</td><td>S 18 G 29</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>46</td><td>22</td>
    <td></td><td></td><td>38%</td><td>39</td><td>NA</td><td>30.14</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#eeeeee"><td>19</td><td align="right">03:35</td><td>N 13</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>23</td><td>15</td>
    <td></td><td></td><td>70%</td><td>11</td><td>NA</td><td>30.28</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" valign="top" bgcolor="#f5f5f5"><td>19</td><td align="right">03:15</td><td>N 16 G 21</td><td>10.00</td><td align="left">Fair</td><td>CLR</td><td>24</td><td>15</td>
    <td></td><td></td><td>69%</td><td>11</td><td>NA</td><td>30.27</td><td>NA</td><td></td><td></td><td></td></tr><tr align="center" bgcolor="#b0c4de"><th rowspan="3">D<br>a<br>t<br>e</th><th rowspan="3">Time<br>(mst)</th>
							<th rowspan="3">Wind<br>(mph)</th><th rowspan="3">Vis.<br>(mi.)</th><th rowspan="3">Weather</th><th rowspan="3" align="CENTER">Sky Cond.</th>
							<th rowspan="2">Air</th><th rowspan="2">Dwpt</th><th>Max.</th><th>Min.</th><th rowspan="3" width="65">Relative<br>Humidity</th><th rowspan="3" width="80">Wind<br>Chill<br>(&deg;F)</th><th rowspan="3" width="80">Heat<br>Index<br>(&deg;F)</th><th rowspan="2">altimeter<br>(in.)</th><th rowspan="2">sea level<br>(mb)</th>
							<th rowspan="2">1 hr</th><th rowspan="2">3 hr</th><th rowspan="2">6 hr</th></tr>
							<tr align="center" bgcolor="#b0c4de"><th colspan="2">6 hour</th></tr><tr align="center" bgcolor="#b0c4de">
							<th colspan="4">Temperature (&ordm;F)</th><th colspan="2">Pressure</th><th colspan="3">Precipitation (in.)</th></tr></table>

