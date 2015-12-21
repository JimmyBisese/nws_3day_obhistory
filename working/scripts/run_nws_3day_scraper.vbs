Set WshShell = WScript.CreateObject("WScript.Shell")
cmd = "C:\python27\python.exe C:\Data_and_Tools\salida_weather\working_data\scripts\nws_3day_scraper.py"
Return = WshShell.Run(cmd, 0, True)

set WshShell = Nothing
