@echo off
rem 12:17 2016/03/25


rem SETUP
rem ////////////////////////////////////
SET USBDRV=E:\
SET MOUNTDRV=C:\indrv\
SET ADDDRV=%MOUNTDRV%ADDDrivers
SET OSIMGDRV=OSIMG
SET IMAGEFILE=%USBDRV%sources\install.wim
SET IMAGENAME="Windows 7 PROFESSIONAL"
SET BOOTWIM=%USBDRV%sources\boot.wim

 
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%%OSIMGDRV% /discard
%MOUNTDRV%DISM\DISM /get-imageinfo /imagefile:%IMAGEFILE%
%MOUNTDRV%DISM\DISM /mount-image /imagefile:%IMAGEFILE% /name:%IMAGENAME% /MountDir:%MOUNTDRV%%OSIMGDRV%
%MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%%OSIMGDRV% /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%%OSIMGDRV% /Commit
pause


rem goto end
%MOUNTDRV%DISM\DISM /get-imageinfo /imagefile:%BOOTWIM%

echo "Microsoft Windows PE (x64)"
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Discard
%MOUNTDRV%DISM\DISM /mount-image /imagefile:%BOOTWIM% /name:"Microsoft Windows PE (x64)" /mountdir:%MOUNTDRV%boot
%MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Commit

echo "Microsoft Windows Setup (x64)"
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Discard
%MOUNTDRV%DISM\DISM /mount-image /imagefile:%BOOTWIM% /name:"Microsoft Windows Setup (x64)" /mountdir:%MOUNTDRV%boot
%MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Image:%MOUNTDRV%boot /Add-Driver /Driver:%ADDDRV% /forceunsigned
%MOUNTDRV%DISM\DISM /Unmount-Image /MountDir:%MOUNTDRV%boot /Commit
pause

:end
rem https://technet.microsoft.com/ja-jp/library/dd744355(v=ws.10).aspx
rem https://msdn.microsoft.com/ja-jp/library/hh824838.aspx
rem C:\Program Files (x86)\Windows Kits\8.1\Assessment and Deployment Kit\Deployment Tools\x86\DISM>
rem Dism /Image:C:\mount /Add-Driver /Driver:C:\Win7\x64 /forceunsigned
pause