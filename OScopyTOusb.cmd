@echo off 
rem 12:17 2016/03/25

rem SETUP
rem ////////////////////////////////////
SET USBDRV=E
SET ODDDRV=D

goto step2

:step1
rem 
rem /////////////////////////////////////
diskpart 
select valume %USBDRV%
clean
pause

:step2
xcopy %ODDDRV%:* %USBDRV%: /e /g /h /r /y

pause