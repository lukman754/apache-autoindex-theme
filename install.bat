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

:: Nama file konfigurasi untuk menyimpan path secara permanen
set "CONFIG_FILE=config.bat"

:: Cek apakah file konfigurasi sudah ada
if exist "%CONFIG_FILE%" (
    echo Memuat path yang sudah tersimpan...
    call "%CONFIG_FILE%"
) else (
    echo File konfigurasi tidak ditemukan. Silakan masukkan path untuk pertama kali...

    set /p XAMPP_PATH="Masukkan path XAMPP (default: C:\xampp): "
    if not defined XAMPP_PATH set "XAMPP_PATH="

    set /p HTDOCS_PATH="Masukkan path HTDOCS (default: %XAMPP_PATH%\htdocs): "
    if not defined HTDOCS_PATH set "HTDOCS_PATH="

    set /p DOWNLOAD_PATH="Masukkan path Download (default: C:\users\%username%\Downloads): "
    if not defined DOWNLOAD_PATH set "DOWNLOAD_PATH=C:\users\%username%\Downloads"

    set /p LARAGON_PATH="Masukkan path Laragon (default: C:\laragon): "
    if not defined LARAGON_PATH set "LARAGON_PATH="

    set /p LARAGON_APACHE_PATH="Masukkan path Apache Laragon (default: %LARAGON_PATH%\www): "
    if not defined LARAGON_APACHE_PATH set "LARAGON_APACHE_PATH="

    :: Simpan path ke dalam file konfigurasi agar bersifat permanen
    echo @echo off > "%CONFIG_FILE%"
    echo set "XAMPP_PATH=%XAMPP_PATH%" >> "%CONFIG_FILE%"
    echo set "HTDOCS_PATH=%HTDOCS_PATH%" >> "%CONFIG_FILE%"
    echo set "DOWNLOAD_PATH=%DOWNLOAD_PATH%" >> "%CONFIG_FILE%"
    echo set "LARAGON_PATH=%LARAGON_PATH%" >> "%CONFIG_FILE%"
    echo set "LARAGON_APACHE_PATH=%LARAGON_APACHE_PATH%" >> "%CONFIG_FILE%"

    echo Path berhasil disimpan secara permanen!
)

:menu
cls
echo =============================================
echo Pilih opsi yang ingin Anda lakukan:
echo 1. Instalasi XAMPP
echo 2. Instalasi Laragon
echo 3. Modifikasi index.php di C:\xampp\htdocs
echo 4. Backup dan Restore
echo 5. Keluar
echo =============================================
echo.
echo Date: %date%
echo Time: %time%
echo User: %username%
echo Current Directory: %cd%
echo Download Path: %DOWNLOAD_PATH%
echo XAMPP Path: %XAMPP_PATH%
echo Htdocs Path: %HTDOCS_PATH%
echo Laragon Path: %LARAGON_PATH%
echo Laragon Apache Path: %LARAGON_APACHE_PATH%
echo.
echo NOTE: Pastikan Anda menjalankan script ini sebagai Administrator/Double klik kiri
echo Rekomendasi: Install XAMPP di C:\xampp or C:\
echo =============================================

set /p choice="Masukkan pilihan Anda (1, 2, 3, atau 4): "

if "%choice%"=="1" goto install
if "%choice%"=="2" goto laragon
if "%choice%"=="3" goto modify
if "%choice%"=="4" goto backup_menu
if "%choice%"=="5" exit
echo Pilihan tidak valid, silakan coba lagi.
pause
goto menu

:laragon
cls
echo =============================================
echo Instalasi Laragon
echo =============================================
echo Pilih versi Laragon yang ingin diinstal:
echo 1. Laragon Full (v 7.0.6) DENGAN LISENSI AKAN TETAPI TETAP BISA DIGUNAKAN
echo 2. Laragon Lite (v 7.0.6) DENGAN LISENSI AKAN TETAPI TETAP BISA DIGUNAKAN
echo 3. Laragon Full (v 6.0.0) Tanpa Lisensi dan gratis
echo 4. Laragon Lite (v 6.0.0) Tanpa Lisensi dan gratis
echo 5. Laragon Full 64 bit (v 5.0.0) Tanpa Lisensi dan gratis
echo 6. Laragon Full 32 bit (v 5.0.0) Tanpa Lisensi dan gratis
echo 7. Laragon Lite (v 5.0.0) Tanpa Lisensi dan gratis
echo 8. Laragon Portable (v 5.0.0) Tanpa Lisensi dan gratis
echo 9. Kembali ke menu utama
echo =============================================
echo.
set /p laragon_version="Masukkan pilihan versi (1 atau 2): "

if "%laragon_version%"=="1" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/7.0.6/laragon-wamp.exe"
    set "LARAGON_VERSION=7.0.6_FullWAMP"
) else if "%laragon_version%"=="2" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/7.0.6/laragon.exe"
    set "LARAGON_VERSION=7.0.6_Lite"
) else if "%laragon_version%"=="3" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon-wamp.exe"
    set "LARAGON_VERSION=6.0.0_FullWAMP"
) else if "%laragon_version%"=="4" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon.exe"
    set "LARAGON_VERSION=6.0.0_Lite"
) else if "%laragon_version%"=="5" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/5.0.0/laragon-wamp.exe"
    set "LARAGON_VERSION=5.0.0_wamp_x64"
) else if "%laragon_version%"=="6" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/5.0.0/laragon-wamp.x86.exe"
    set "LARAGON_VERSION=5.0.0_wamp_x86"
) else if "%laragon_version%"=="7" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/5.0.0/laragon.exe"
    set "LARAGON_VERSION=5.0.0_Lite"
) else if "%laragon_version%"=="8" (
    set "LARAGON_URL=https://github.com/leokhoa/laragon/releases/download/5.0.0/laragon-portable.zip"
    set "LARAGON_VERSION=5.0.0_Portable"
) else if "%laragon_version%"=="9" (
    goto menu
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto laragon
)

if not defined LARAGON_URL (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto laragon
)

echo Mengunduh Laragon %LARAGON_VERSION%...

@REM powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('%LARAGON_URL%', '%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe')"

set /p TanyaDownload="Anda ingin mendownload methode apa ? (1. powershell, 2. curl, 3. wget, 4. bitsadmin): "
if "%TanyaDownload%"=="1" (
    powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('%LARAGON_URL%', '%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe')"
) else if "%TanyaDownload%"=="2" (
    curl -o "%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe" "%LARAGON_URL%"
) else if "%TanyaDownload%"=="3" (
    wget -O "%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe" "%LARAGON_URL%"
) else if "%TanyaDownload%"=="4" (
    bitsadmin /transfer myDownloadJob /download /priority normal "%LARAGON_URL%" "%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe"
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto laragon
)

if not exist "%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe" (
    echo Gagal mengunduh Laragon %LARAGON_VERSION%.
    pause
    goto menu
)

echo Menginstal Laragon %LARAGON_VERSION%...
start /wait '%DOWNLOAD_PATH%\laragon-installer-%LARAGON_VERSION%.exe'

echo Instalasi selesai.
pause
goto menu

:install
cls
echo =============================================
echo Pilih versi XAMPP yang ingin diinstal:
echo 1. XAMPP 8.0.30 / PHP 8.0.30
echo 2. XAMPP 8.1.25 / PHP 8.1.25
echo 3. XAMPP 8.2.12 / PHP 8.2.12
echo 4. Kembali ke menu utama
echo =============================================
echo.
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
) else if "%version%"=="4" (
    goto menu
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto install
)

if not defined XAMPP_URL (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto install
)

echo Mengunduh XAMPP %XAMPP_VERSION%...

@REM Metode download file ini lebih cepat sama seperti Download dengan browser
@REM Referensi: https://stackoverflow.com/questions/28682642/powershell-why-is-using-invoke-webrequest-much-slower-than-a-browser-download#:~:text=%24wc%20%3D%20New%2DObject%20net.webclient%0A%24wc.Downloadfile(%24video_url%2C%20%24local_video_url)

@REM powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('%XAMPP_URL%', '%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe')"

set /p TanyaDownloadXAMPP="Anda ingin mendownload methode apa ? (1. powershell (Recommendation), 2. curl, 3. wget, 4. bitsadmin): "
if "%TanyaDownloadXAMPP%"=="1" (
    powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('%XAMPP_URL%', '%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe')"
) else if "%TanyaDownloadXAMPP%"=="2" (
    curl -o "%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe" "%XAMPP_URL%"
) else if "%TanyaDownloadXAMPP%"=="3" (
    wget -O "%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe" "%XAMPP_URL%"
) else if "%TanyaDownloadXAMPP%"=="4" (
    bitsadmin /transfer myDownloadJob /download /priority normal "%XAMPP_URL%" "%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe"
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto install
)

@REM powershell -Command "Invoke-WebRequest -Uri %url% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe"
@REM powershell -Command "Invoke-WebRequest -Uri %url% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe -UseBasicParsing"
@REM powershell -Command "& {$ProgressPreference='SilentlyContinue'; Invoke-WebRequest -Uri %XAMPP_URL% -OutFile %DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe}"

if not exist "%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe" (
    echo Gagal mengunduh XAMPP %XAMPP_VERSION%.
    pause
    goto menu
)

echo Menginstal XAMPP %XAMPP_VERSION%...
start /wait '%DOWNLOAD_PATH%\xampp-installer-%XAMPP_VERSION%.exe'

echo Instalasi selesai.
pause
goto menu

:modify
cls
echo =============================================
echo Modifikasi index.php di %HTDOCS_PATH%
echo Modifikasi index.php di %LARAGON_APACHE_PATH%
echo =============================================

if not exist "%HTDOCS_PATH%" (
    echo Error: XAMPP installation not found at %XAMPP_PATH%
    echo Please install XAMPP first.
    pause
    goto menu
) else if not exist "%HTDOCS_PATH%\index.php" (
    echo Error: index.php not found at %HTDOCS_PATH%
    echo Please check your XAMPP installation.
    pause
    goto menu
) else if not exist "%LARAGON_APACHE_PATH%" (
    echo Error: Laragon installation not found at %LARAGON_PATH%
    echo Please install Laragon first.
    pause
    goto menu
) else if not exist "%LARAGON_APACHE_PATH%\index.php" (
    echo Error: index.php not found at %LARAGON_APACHE_PATH%
    echo Please check your Laragon installation.
    pause
    goto menu
)

echo Mengunduh file index.php dari GitHub...
@REM powershell -Command "& {$ProgressPreference='SilentlyContinue'; Invoke-WebRequest -Uri https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php -OutFile index.php"

@REM powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php', 'index.php')"

set /p TanyaDownloadIndex="Anda ingin mendownload methode apa ? (1. powershell (Recommendation), 2. curl, 3. wget, 4. bitsadmin): "
if "%TanyaDownloadIndex%"=="1" (
    powershell -Command "$wc = New-Object net.webclient; $wc.Downloadfile('https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php', 'index.php')"
) else if "%TanyaDownloadIndex%"=="2" (
    curl -o "index.php" "https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php"
) else if "%TanyaDownloadIndex%"=="3" (
    wget -O "index.php" "https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php"
) else if "%TanyaDownloadIndex%"=="4" (
    bitsadmin /transfer myDownloadJob /download /priority normal "https://raw.githubusercontent.com/lukman754/apache-autoindex-theme/refs/heads/main/index.php" "index.php"
) else (
    echo Pilihan tidak valid, silakan coba lagi.
    pause
    goto modify
)

echo Membuat backup index.php yang ada...
if exist "%HTDOCS_PATH%\index.php" (
    copy "%HTDOCS_PATH%\index.php" "%HTDOCS_PATH%\index.php.bak"
    echo Backup index.php berhasil dibuat.
) else (
    echo File index.php tidak ditemukan, tidak ada backup yang dibuat.
)

if exist "%LARAGON_APACHE_PATH%\index.php" (
    copy "%LARAGON_APACHE_PATH%\index.php" "%LARAGON_APACHE_PATH%\index.php.bak"
    echo Backup index.php berhasil dibuat.
) else (
    echo File index.php tidak ditemukan, tidak ada backup yang dibuat.
)

echo Menyalin file index.php yang baru ke %HTDOCS_PATH%...
if exist "%HTDOCS_PATH%" (
    copy index.php "%HTDOCS_PATH%\index.php"
) else (
    echo Error: Path tidak ditemukan.
)

if exist "%LARAGON_APACHE_PATH%" (
    copy index.php "%LARAGON_APACHE_PATH%\index.php"
) else (
    echo Error: Path tidak ditemukan.
)

echo Membersihkan file index.php yang diunduh...
del index.php

echo Modifikasi selesai.
pause
goto menu

:backup_menu
cls
echo =============================================
echo Pilih opsi yang ingin Anda lakukan:
echo 1. Create Backup (Semua)
echo 2. Restore Backup (Semua)
echo 3. Create Backup files website (backup htdocs and MySQL)
echo 4. Restore Backup files website (restore htdocs and MySQL)
echo 5. Manual Backup (Folder/File)
echo 6. Kembali ke menu utama
echo =============================================
set /p backup_choice="Masukkan pilihan Anda (1, 2, 3, 4, 5, atau 6): "

if "%backup_choice%"=="1" goto create_backup
if "%backup_choice%"=="2" goto restore_backup
if "%backup_choice%"=="3" goto create_backup_files
if "%backup_choice%"=="4" goto restore_backup_files
if "%backup_choice%"=="5" goto manual_backup
if "%backup_choice%"=="6" goto menu
echo Pilihan tidak valid, silakan coba lagi.
pause
goto backup_menu

:create_backup
cls
echo =============================================
echo Membuat backup
echo =============================================

for /f "skip=1 delims=" %%x in ('wmic os get localdatetime') do if not defined date set date=%%x
set DATE_STR=%date:~0,4%%date:~4,2%%date:~6,2%

set /p create_folder="Apakah Anda ingin membuat folder backup di lokasi tertentu? (y/n): "
if /i "%create_folder%"=="y" (
    set /p folder_location="Masukkan lokasi folder backup: "
    if not exist "%folder_location%" mkdir "%folder_location%"
) else (
    set /p "folder_location=Xampp atau Laragon? (1. Xampp, 2. Laragon): "
    if "%folder_location%"=="1" (
        set "folder_location=%XAMPP_PATH%\backup"
        if not exist "%XAMPP_PATH%\backup" mkdir "%XAMPP_PATH%\backup"
    ) else if "%folder_location%"=="2" (
        set "folder_location=%LARAGON_PATH%\backup"
        if not exist "%LARAGON_PATH%\backup" mkdir "%LARAGON_PATH%\backup"
    ) else (
        echo Pilihan tidak valid, silakan coba lagi.
        pause
        goto create_backup_files
    )
)

set "BACKUP_FILE_XAMPP=%folder_location%\xampp_backup_%DATE_STR%.zip"
set "BACKUP_FILE_LARAGON=%folder_location%\laragon_backup_%DATE_STR%.zip"

if exist "%XAMPP_PATH%" (
    powershell -Command "Compress-Archive -Path '%XAMPP_PATH%' -DestinationPath '%BACKUP_FILE_XAMPP%' -Force -CompressionLevel Fastest"
    echo Backup created at %BACKUP_FILE_XAMPP%
)
if exist "%LARAGON_PATH%" (
    powershell -Command "Compress-Archive -Path '%LARAGON_PATH%' -DestinationPath '%BACKUP_FILE_LARAGON%' -Force -CompressionLevel Fastest"
    echo Backup created at %BACKUP_FILE_LARAGON%
)
if not exist "%XAMPP_PATH%" if not exist "%LARAGON_PATH%" (
    echo Error: Path tidak ditemukan.
)

pause
goto backup_menu

:restore_backup
cls
echo =============================================
echo Memulihkan backup
echo =============================================

set /p "locate_backup=Masukan lokasi backup (C:\xampp\backup\): "

if not defined locate_backup (
    set "locate_backup=%XAMPP_PATH%\backup"
    if not exist "%locate_backup%" set "locate_backup=%LARAGON_PATH%\backup"
)

if not exist "%locate_backup%" (
    echo Lokasi backup tidak ditemukan.
    pause
    goto backup_menu
)

dir /b /a-d "%locate_backup%\xampp_backup_*.zip"
dir /b /a-d "%locate_backup%\laragon_backup_*.zip"

set /p "RESTORE_FILE=Masukkan nama file backup yang ingin dipulihkan: "

:: Cek file backup dari lokasi yang dimasukkan pengguna
if exist "%locate_backup%\xampp_backup_%RESTORE_FILE%" (
    echo Backup file ditemukan.
    powershell -Command "Expand-Archive -Path '%locate_backup%\xampp_backup_%RESTORE_FILE%' -DestinationPath '%XAMPP_PATH%' -Force"
    echo Backup restored.
    goto end_restore
)

if exist "%locate_backup%\laragon_backup_%RESTORE_FILE%" (
    echo Backup file ditemukan.
    powershell -Command "Expand-Archive -Path '%locate_backup%\laragon_backup_%RESTORE_FILE%' -DestinationPath '%LARAGON_PATH%' -Force"
    echo Backup restored.
    goto end_restore
)

:: Cek file backup dari default path XAMPP
if exist "%XAMPP_PATH%\backup\%RESTORE_FILE%" (
    echo Backup file ditemukan.
    powershell -Command "Expand-Archive -Path '%XAMPP_PATH%\backup\%RESTORE_FILE%' -DestinationPath '%XAMPP_PATH%' -Force"
    echo Backup restored.
    goto end_restore
)

:: Cek file backup dari default path Laragon
if exist "%LARAGON_PATH%\backup\%RESTORE_FILE%" (
    echo Backup file ditemukan.
    powershell -Command "Expand-Archive -Path '%LARAGON_PATH%\backup\%RESTORE_FILE%' -DestinationPath '%LARAGON_PATH%' -Force"
    echo Backup restored.
    goto end_restore
)

:: Jika tidak ada file backup yang ditemukan
echo File backup tidak ditemukan.

:end_restore
pause
goto backup_menu

:create_backup_files
cls
echo =============================================
echo Membuat backup files website
echo =============================================

for /f "skip=1 delims=" %%x in ('wmic os get localdatetime') do if not defined date set date=%%x
set DATE_STR=%date:~0,4%%date:~4,2%%date:~6,2%

@REM if not exist "%XAMPP_PATH%\backup" mkdir "%XAMPP_PATH%\backup"
@REM if not exist "%LARAGON_PATH%\backup" mkdir "%LARAGON_PATH%\backup"

set /p create_folder="Apakah Anda ingin membuat folder backup di lokasi tertentu? (y/n): "
if /i "%create_folder%"=="y" (
    set /p folder_location="Masukkan lokasi folder backup: "
    if not exist "%folder_location%" mkdir "%folder_location%"
) else (
    
    set /p "folder_location=Xampp atau Laragon? (1. Xampp, 2. Laragon): "
    if "%folder_location%"=="1" (
        set "folder_location=%XAMPP_PATH%\backup"
        if not exist "%XAMPP_PATH%\backup" mkdir "%XAMPP_PATH%\backup"
    ) else if "%folder_location%"=="2" (
        set "folder_location=%LARAGON_PATH%\backup"
        if not exist "%LARAGON_PATH%\backup" mkdir "%LARAGON_PATH%\backup"
    ) else (
        echo Pilihan tidak valid, silakan coba lagi.
        pause
        goto create_backup_files
    )
    
    if not exist "%folder_location%" mkdir "%folder_location%"
)

@REM set "BACKUP_FILE_XAMPP=%XAMPP_PATH%\backup\files_backup_%DATE_STR%.zip"
@REM set "BACKUP_FILE_LARAGON=%LARAGON_PATH%\backup\files_backup_%DATE_STR%.zip"

set "BACKUP_FILE_XAMPP=%folder_location%\xampp_htdocs_files_backup_%DATE_STR%.zip"
set "BACKUP_FILE_LARAGON=%folder_location%\laragon_htdocs_files_backup_%DATE_STR%.zip"
set "BACKUP_FILE_XAMPP_SQL=%folder_location%\xampp_mysql_backup_%DATE_STR%.zip"
set "BACKUP_FILE_LARAGON_SQL=%folder_location%\laragon_mysql_backup_%DATE_STR%.zip"

if exist "%HTDOCS_PATH%" (
    powershell -Command "Compress-Archive -Path '%HTDOCS_PATH%' -DestinationPath '%BACKUP_FILE_XAMPP%' -Force -CompressionLevel Fastest"
    powershell -Command "Compress-Archive -Path '%XAMPP_PATH%\mysql\data' -DestinationPath '%BACKUP_FILE_XAMPP_SQL%' -Force -CompressionLevel Fastest"
    echo Backup created at %BACKUP_FILE_XAMPP%
)
if exist "%LARAGON_APACHE_PATH%" (
    powershell -Command "Compress-Archive -Path '%LARAGON_APACHE_PATH%' -DestinationPath '%BACKUP_FILE_LARAGON%' -Force -CompressionLevel Fastest"
    powershell -Command "Compress-Archive -Path '%LARAGON_PATH%\data' -DestinationPath '%BACKUP_FILE_LARAGON_SQL%' -Force -CompressionLevel Fastest"
    echo Backup created at %BACKUP_FILE_LARAGON%
)
if not exist "%HTDOCS_PATH%" if not exist "%LARAGON_APACHE_PATH%" (
    echo Error: Path tidak ditemukan.
)

pause
goto backup_menu

:restore_backup_files
cls
echo =============================================
echo Memulihkan backup files website
echo =============================================

set /p "locate_backup=Masukkan lokasi backup (C:\xampp\backup\ or C:\laragon\backup\): "

if not defined locate_backup (
    set "locate_backup=%XAMPP_PATH%\backup"
)

@REM jika tidak ada xampp maka otomatis ke laragon

if not exist "%locate_backup%" (
    set "locate_backup=%LARAGON_PATH%\backup"
)

if not exist "%locate_backup%" (
    echo Lokasi backup tidak ditemukan.
    pause
    goto backup_menu
)

dir /b /a-d "%locate_backup%\xampp_htdocs_files_backup_*.zip"
dir /b /a-d "%locate_backup%\laragon_htdocs_files_backup_*.zip"
dir /b /a-d "%locate_backup%\xampp_mysql_backup_*.zip"
dir /b /a-d "%locate_backup%\laragon_mysql_backup_*.zip"

set /p "RESTORE_FILE=Masukkan nama file backup files yang ingin dipulihkan: "

@REM set "backup_file_path=%locate_backup%\%RESTORE_FILE%"
set "backup_file_path=%locate_backup%\%RESTORE_FILE%"

if "%locate_backup%"=="%XAMPP_PATH%\backup" (
    set "backup_file_path=%XAMPP_PATH%\backup\%RESTORE_FILE%"
    set /p "restore_mysql=Apakah Anda ingin merestore database MySQL? (y/n): "
    if /i "%restore_mysql%"=="y" (
        powershell -Command "Expand-Archive -Path '%backup_file_path%' -DestinationPath '%XAMPP_PATH%' -Force"
        echo Backup files restored to %XAMPP_PATH%.
    ) else (
        echo Database MySQL tidak di-restore.
    )
    set /p "restore_htdocs=Apakah Anda ingin merestore files website? (y/n): "
    if /i "%restore_htdocs%"=="y" (
        powershell -Command "Expand-Archive -Path '%backup_file_path%' -DestinationPath '%HTDOCS_PATH%' -Force"
        echo Backup files restored to %HTDOCS_PATH%.
    ) else (
        echo Files website tidak di-restore.
    )
) else if "%locate_backup%"=="%LARAGON_PATH%\backup" (
    set "backup_file_path=%LARAGON_PATH%\backup\%RESTORE_FILE%"
    set /p "restore_mysql=Apakah Anda ingin merestore database MySQL? (y/n): "
    if /i "%restore_mysql%"=="y" (
        powershell -Command "Expand-Archive -Path '%backup_file_path%' -DestinationPath '%LARAGON_PATH%' -Force"
        echo Backup files restored to %LARAGON_PATH%.
    ) else (
        echo Database MySQL tidak di-restore.
    )
    set /p "restore_htdocs=Apakah Anda ingin merestore files website? (y/n): "
    if /i "%restore_htdocs%"=="y" (
        powershell -Command "Expand-Archive -Path '%backup_file_path%' -DestinationPath '%LARAGON_APACHE_PATH%' -Force"
        echo Backup files restored to %LARAGON_APACHE_PATH%.
    ) else (
        echo Files website tidak di-restore.
    )
) else (
    echo Error: Path tidak ditemukan.
)

pause
goto backup_menu

:manual_backup
cls
echo =============================================
echo Manual Backup
echo =============================================

for /f "skip=1 delims=" %%x in ('wmic os get localdatetime') do if not defined date set date=%%x
set DATE_STR=%date:~0,4%%date:~4,2%%date:~6,2%

set /p "source_folder=Masukkan lokasi folder/file yang ingin di-backup: "
set /p "backup_location=Masukkan lokasi tujuan backup: "

if not exist "%backup_location%" mkdir "%backup_location%"

powershell -Command "Compress-Archive -Path '%source_folder%' -DestinationPath '%backup_location%\manual_backup_%DATE_STR%.zip' -Force -CompressionLevel Fastest"
echo Backup selesai di %backup_location%\manual_backup_%DATE_STR%.zip

pause
goto backup_menu


@REM :EOF
@REM endlocal
@REM exit /b
