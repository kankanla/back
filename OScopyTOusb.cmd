@echo off 
rem 9:56 2016/03/30

rem SETUP
rem ////////////////////////////////////
SET USBDRV=D
SET ODDDRV=E


goto step1

:step1
format %USBDRV%: /fs:fat32 /v:FileToUsb  /x /q


:step2
xcopy %ODDDRV%:* %USBDRV%: /e /g /h /r /y

:step3
rem UEFI Boot Support
mkdir %USBDRV%:\efi\boot\
copy %windir%\Boot\EFI\bootmgfw.efi %USBDRV%:\efi\boot\bootx64.efi



pause