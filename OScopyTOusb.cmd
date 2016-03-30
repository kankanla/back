@echo off 
rem 9:56 2016/03/30

rem SETUP
rem ////////////////////////////////////
SET USBDRV=G
SET ODDDRV=D


goto step1

:step1
format %USBDRV%: /fs:fat /v:FileToUsb  /x /q


:step2
xcopy %ODDDRV%:* %USBDRV%: /e /g /h /r /y




pause