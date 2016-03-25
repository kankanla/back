@echo off 
rem 12:17 2016/03/25


rem SETUP
rem ////////////////////////////////////
SET USBDRV=F:\
SET MOUNTDRV=C:\indrv\
SET ADDDRV=%MOUNTDRV%ADDDrivers
SET OSIMGDRV=OSIMG
SET IMAGEFILE=%USBDRV%sources\install.wim
SET IMAGENAME="Windows 7 PROFESSIONAL"
SET BOOTWIM=%USBDRV%sources\boot.wim

:setp1
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%%OSIMGDRV% /discard
%MOUNTDRV%DISM\DISM /get-imageinfo /imagefile:%IMAGEFILE%

rem Name : Windows 7 HOMEBASIC
rem Description : Windows 7 HOMEBASIC
rem Size : 11,681,373,841 bytes
rem 
rem Index : 2
rem Name : Windows 7 HOMEPREMIUM
rem Description : Windows 7 HOMEPREMIUM
rem Size : 12,194,650,761 bytes
rem 
rem Index : 3
rem Name : Windows 7 PROFESSIONAL
rem Description : Windows 7 PROFESSIONAL
rem Size : 12,106,260,814 bytes
rem 
rem Index : 4
rem Name : Windows 7 ULTIMATE
rem Description : Windows 7 ULTIMATE
rem Size : 12,259,188,245 bytes

%MOUNTDRV%DISM\DISM /mount-image /imagefile:%IMAGEFILE% /name:%IMAGENAME% /MountDir:%MOUNTDRV%%OSIMGDRV%
for /D /R %ADDDRV% %%a in (*) do %MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%%OSIMGDRV% /Add-Driver /Driver:%%a /forceunsigned
rem  %MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%%OSIMGDRV% /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%%OSIMGDRV% /Commit
pause

:stup2
%MOUNTDRV%DISM\DISM /get-imageinfo /imagefile:%BOOTWIM%

echo "Microsoft Windows PE (x64)"
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Discard
%MOUNTDRV%DISM\DISM /mount-image /imagefile:%BOOTWIM% /name:"Microsoft Windows PE (x64)" /mountdir:%MOUNTDRV%boot
for /D /R %ADDDRV% %%a in (*) do %MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%%a /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Commit

:step3
%MOUNTDRV%DISM\DISM /get-imageinfo /imagefile:%BOOTWIM%

echo "Microsoft Windows Setup (x64)"
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Discard
%MOUNTDRV%DISM\DISM /mount-image /imagefile:%BOOTWIM% /name:"Microsoft Windows Setup (x64)" /mountdir:%MOUNTDRV%boot
for /D /R %ADDDRV% %%a in (*) do %MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%%a /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Commit
pause
rem %MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Remove-Driver /Driver:asmthub3.cat


:end
rem https://technet.microsoft.com/ja-jp/library/dd744355(v=ws.10).aspx
rem https://msdn.microsoft.com/ja-jp/library/hh824838.aspx
rem C:\Program Files (x86)\Windows Kits\8.1\Assessment and Deployment Kit\Deployment Tools\x86\DISM>
rem Dism /Image:C:\mount /Add-Driver /Driver:C:\Win7\x64 /forceunsigned
pause