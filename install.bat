@echo off

setlocal EnableDelayedExpansion

Title XAMPP Installer and Modifier by Xnuvers007 and Lukman754
 
@REM  Bypass UAC as Admin
PUSHD %~DP0 & cd /d "%~dp0"
%1 %2
mshta vbscript:createobject("shell.application").shellexecute("%~s0","goto :runas","","runas",1)(window.close)&goto :eof
:runas
color b
cls

set "XAMPP_PATH=C:\xampp"
set "HTDOCS_PATH=%XAMPP_PATH%\htdocs"
set "DOWNLOAD_PATH=C:\users\%username%\Downloads"

:menu
cls
echo =============================================
echo Pilih opsi yang ingin Anda lakukan:
echo 1. Instalasi XAMPP
echo 2. Modifikasi index.php di C:\xampp\htdocs
echo 3. Backup dan Restore
echo 4. Keluar
echo =============================================
echo.
echo Date: %date%
echo Time: %time%
echo User: %username%
echo Current Directory: %cd%
echo %DOWNLOAD_PATH%
echo.
echo NOTE: Pastikan Anda menjalankan script ini sebagai Administrator/Double klik kiri
echo Rekomendasi: Install XAMPP di C:\xampp or C:\
echo =============================================

set /p choice="Masukkan pilihan Anda (1, 2, 3, atau 4): "

if "%choice%"=="1" goto install
if "%choice%"=="2" goto modify
if "%choice%"=="3" goto backup_menu
if "%choice%"=="4" exit
echo Pilihan tidak valid, silakan coba lagi.
pause
goto menu

:install
cls
echo =============================================
echo Pilih versi XAMPP yang ingin diinstal:
echo 1. XAMPP 8.0.30 / PHP 8.0.30
echo 2. XAMPP 8.1.25 / PHP 8.1.25
echo 3. XAMPP 8.2.12 / PHP 8.2.12
echo =============================================
set /p version="Masukkan pilihan versi (1, 2, atau 3): "

if "%version%"=="1" (
    set "XAMPP_URL=https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.0.30/xampp-windows-x64-8.0.30-0-VS16-installer.exe"
    set "XAMPP_VERSION=8.0.30"
) else if "%version%"=="2" (
    set "XAMPP_URL=https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.1.25/xampp-windows-x64-8.1.25-0-VS16-installer.exe"
    set "XAMPP_VERSION=8.1.25"
) else if "%version%"=="3" (
    set "XAMPP_URL=https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe"
    set "XAMPP_VERSION=8.2.12"
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto install
)

if not defined url (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto install
)

echo Mengunduh XAMPP %XAMPP_VERSION%...

@REM Metode download file ini lebih cepat sama seperti Download dengan browser
@REM Referensi: https://stackoverflow.com/questions/28682642/powershell-why-is-using-invoke-webrequest-much-slower-than-a-browser-download#:~:text=%24wc%20%3D%20New%2DObject%20net.webclient%0A%24wc.Downloadfile(%24video_url%2C%20%24local_video_url)

powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('%XAMPP_URL%', '%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe')"

@REM powershell -Command "Invoke-WebRequest -Uri %url% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe"
@REM powershell -Command "Invoke-WebRequest -Uri %url% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe -UseBasicParsing"
@REM powershell -Command "& {$ProgressPreference='SilentlyContinue'; Invoke-WebRequest -Uri %XAMPP_URL% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe}"

if not exist "%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe" (
    echo Gagal mengunduh XAMPP %XAMPP_VERSION%.
    pause
    goto menu
)

echo Menginstal XAMPP %XAMPP_VERSION%...
start /wait xampp-installer.exe

echo Instalasi selesai.
pause
goto menu

:modify
cls
echo =============================================
echo Modifikasi index.php di %HTDOCS_PATH%
echo =============================================

if not exist "%HTDOCS_PATH%" (
    echo Error: XAMPP installation not found at %XAMPP_PATH%
    echo Please install XAMPP first.
    pause
    goto menu
)

echo Mengunduh file index.php dari GitHub...
@REM powershell -Command "& {$ProgressPreference='SilentlyContinue'; Invoke-WebRequest -Uri https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php -OutFile index.php"

powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php', 'index.php')"

echo Membuat backup index.php yang ada...
if exist "%HTDOCS_PATH%\index.php" (
    copy "%HTDOCS_PATH%\index.php" "%HTDOCS_PATH%\index.php.bak"
    echo Backup index.php berhasil dibuat.
) else (
    echo File index.php tidak ditemukan, tidak ada backup yang dibuat.
)

echo Menyalin file index.php yang baru ke %HTDOCS_PATH%...
copy index.php "%HTDOCS_PATH%\index.php" 

echo Membersihkan file index.php yang diunduh...
del index.php

echo Modifikasi selesai.
pause
goto menu

:backup_menu
cls
echo =============================================
echo Pilih opsi yang ingin Anda lakukan:
echo 1. Create Backup
echo 2. Restore Backup
echo 3. Create Backup files website (From htdocs)
echo 4. Restore Backup files website (To htdocs)
echo 5. Kembali ke menu utama
echo =============================================
set /p backup_choice="Masukkan pilihan Anda (1, 2, 3, 4, atau 5): "

if "%backup_choice%"=="1" goto create_backup
if "%backup_choice%"=="2" goto restore_backup
if "%backup_choice%"=="3" goto create_backup_files
if "%backup_choice%"=="4" goto restore_backup_files
if "%backup_choice%"=="5" goto menu
echo Pilihan tidak valid, silakan coba lagi.
pause
goto backup_menu

:create_backup
cls
echo =============================================
echo Membuat backup
echo =============================================
if not exist "%XAMPP_PATH%\backup" mkdir "%XAMPP_PATH%\backup"
set "BACKUP_FILE=%XAMPP_PATH%\backup\xampp_backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%.zip"
powershell -Command "Compress-Archive -Path '%XAMPP_PATH%\apache\conf', '%XAMPP_PATH%\mysql\data' -DestinationPath '%BACKUP_FILE%' -Force -CompressionLevel Fastest"
echo Backup created at %BACKUP_FILE%
pause
goto backup_menu

:restore_backup
cls
echo =============================================
echo Memulihkan backup
echo =============================================
dir /b /a-d "%XAMPP_PATH%\backup\xampp_backup_*.zip"
set /p "RESTORE_FILE=Masukkan nama file backup yang ingin dipulihkan: "
if not exist "%XAMPP_PATH%\backup\%RESTORE_FILE%" (
    echo File backup tidak ditemukan.
    pause
    goto backup_menu
)
powershell -Command "Expand-Archive -Path '%XAMPP_PATH%\backup\%RESTORE_FILE%' -DestinationPath '%XAMPP_PATH%' -Force"
echo Backup restored.
pause
goto backup_menu

:create_backup_files
cls
echo =============================================
echo Membuat backup files website
echo =============================================
if not exist "%XAMPP_PATH%\backup" mkdir "%XAMPP_PATH%\backup"
set "BACKUP_FILE=%XAMPP_PATH%\backup\files_backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%.zip"
powershell -Command "Compress-Archive -Path '%HTDOCS_PATH%' -DestinationPath '%BACKUP_FILE%' -CompressionLevel Fastest -Force"
echo Backup created at %BACKUP_FILE%
pause
goto backup_menu

:restore_backup_files
cls
echo =============================================
echo Memulihkan backup files website
echo =============================================
dir /b /a-d "%XAMPP_PATH%\backup\files_backup_*.zip"
set /p "RESTORE_FILE=Masukkan nama file backup files yang ingin dipulihkan: "
if not exist "%XAMPP_PATH%\backup\%RESTORE_FILE%" (
    echo File backup files tidak ditemukan.
    pause
    goto backup_menu
)

@REM :EOF
@REM endlocal
@REM exit /b
