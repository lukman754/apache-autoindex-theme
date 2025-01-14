<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="description" content="file manager of htdocs or /var/www/html">
    <meta name="language" content="id">
    <meta name="author" content="Lukman754 & Xnuvers007">
    <meta name="keywords" content="htdocs,html,filemanager">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">

    <meta itemprop="name" content="File Manager htdocs (/var/www/html)">
    <meta itemprop="description" content="file manager of htdocs or /var/www/html">
    <meta itemprop="image" content=" ">

    <meta property="og:url" content="http://localhost:80">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="File Manager htdocs (/var/www/html)" />
    <meta property="og:description" content="file manager of htdocs or /var/www/html" />
    <meta property="og:image" content=" " />
    <meta property="og:site_name" content="File Manager htdocs (/var/www/html)" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="File Manager htdocs (/var/www/html)" />
    <meta name="twitter:description" content="file manager of htdocs or /var/www/html" />
    <meta name="twitter:image" content=" " />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>File Manager Htdocs</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }


        .footer {
            display: block;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            padding: 15px 20px;
            font-size: 10px;
        }

        .footer p {
            margin-bottom: 15px;
        }

        .footer a {
            color: #fcd53f;
            text-decoration: none;
            background-color: #2d2d2d;
            border-radius: 5px;
            padding: 5px 10px;
        }


        .footer a:hover {
            color: #ffb02e;
            /* Warna teks link saat di-hover */
        }

        .footer strong {
            font-weight: normal;
            /* Set weight ke normal untuk konsistensi */
        }


        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .search-container {
            margin-bottom: 10px;
        }

        .search-container input[type="text"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 0;
            border-radius: 4px;
            background-color: #252526;
            color: #d4d4d4;
        }


        .search-container button .sort-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .file-table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
            font-size: 12px;
        }

        .file-table th,
        .file-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #2d2d2d;
        }

        .file-table th {
            background-color: #252526;
            font-weight: normal;
            cursor: pointer;
            position: relative;
        }

        .file-table th .sort-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .file-table tr:hover {
            background-color: #2a2d2e;
        }

        .folder-icon::before,
        .file-icon::before {
            margin-right: 5px;
        }

        .folder-icon::before {
            content: "📁";
            color: white;
        }

        .file-icon::before {
            content: "📄";
            color: white;
        }

        .file-table a {
            text-decoration: none;
            color: inherit;
        }

        .file-table th:nth-child(1),
        .file-table td:nth-child(1) {
            width: 40%;
        }

        .file-table th:nth-child(2),
        .file-table td:nth-child(2) {
            width: 25%;
        }

        .file-table th:nth-child(3),
        .file-table td:nth-child(3) {
            width: 20%;
        }

        .file-table th:nth-child(4),
        .file-table td:nth-child(4) {
            width: 15%;
        }

        .grey-text {
            color: #a0a0a0;
            font-size: 12px;
        }

        .highlight {
            background-color: #fcd53f;
            color: black;
        }

        .light-mode {
            background-color: #f0f0f0;
            color: #333;
        }

        .light-mode .container {
            background-color: #fff;
            color: #333;
        }

        .light-mode .search-container input[type="text"] {
            background-color: #e0e0e0;
            color: #333;
        }

        .light-mode .search-container button {
            background-color: #e0e0e0;
            color: #333;
        }

        .light-mode .file-table th,
        .light-mode .file-table td {
            border-bottom: 1px solid #ddd;
        }

        .light-mode .file-table tr:hover {
            background-color: #f5f5f5;
        }

        .light-mode .folder-icon::before,
        .light-mode .file-icon::before {
            color: black;
        }

        .light-mode .file-table th {
            background-color: #fff;
            color: #333;
        }

        .light-mode .file-table th .sort-icon {
            color: #333;
        }

        .sort-icon {
            color: inherit;
        }

        .sort-icon {
            color: #d4d4d4;
        }

        @media (max-width: 768px) {

            .search-container input[type="text"],
            .search-container button {
                flex: 1 1 100%;
            }
        }

        body.light-mode {
            background-color: #ffffff;
            /* Warna latar untuk tema terang */
            color: #333333;
            /* Warna teks untuk tema terang */
        }

        body.dark-mode {
            background-color: #1c1c1c;
            /* Warna latar untuk tema gelap */
            color: #ffffff;
            /* Warna teks untuk tema gelap */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: inherit;
            /* Ikuti warna latar body */
        }

        .button {
            display: flex;
            gap: 10px;
        }

        .toogle {
            padding: 8px 16px;
            font-size: 14px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        body.light-mode .toogle {
            background-color: #2d2d2d;
        }

        body.dark-mode .toogle {
            background-color: #444;
        }


        .light-mode .toogle {
            padding: 8px 16px;
            font-size: 14px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .toogle:hover {
            background-color: #2d2d2d;
            color: #d4d4d4;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #loading {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        #loading h1 {
            margin: 10px 0;
        }

        .dark-mode .path-color {
            color: yellow;
        }

        .light-mode .path-color {
            color: red;
        }
    </style>
    <script>
        function goBack() {
            const currentDir = window.location.search ? new URLSearchParams(window.location.search).get('dir') : './';
            const parentDir = currentDir.substring(0, currentDir.lastIndexOf('/')) || './';
            window.location.href = '?dir=' + encodeURIComponent(parentDir);
        }


        function searchFiles() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("fileTable");
            tr = table.getElementsByTagName("tr");

            if (!filter) {
                // If input is empty, redirect to the root or main folder
                window.location.href = '?dir=./'; // Redirect to root folder
                return;
            }

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Targeting only the name cell
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        // Highlight only the matched text
                        const link = td.getElementsByTagName("a")[0];
                        if (link) {
                            const highlightedText = txtValue.replace(new RegExp(filter, "gi"), match => `<span class='highlight'>${match}</span>`);
                            link.innerHTML = highlightedText;
                        }
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function parseSize(sizeStr) {
            let size = parseFloat(sizeStr);
            if (sizeStr.includes('GB')) return size * (1 << 30);
            if (sizeStr.includes('MB')) return size * (1 << 20);
            if (sizeStr.includes('KB')) return size * (1 << 10);
            return size;
        }

        function sortTable(columnIndex, sortOrder) {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("fileTable");
            switching = true;

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[columnIndex];
                    y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

                    let xValue = columnIndex === 3 ? parseSize(x.textContent) : x.textContent.toLowerCase();
                    let yValue = columnIndex === 3 ? parseSize(y.textContent) : y.textContent.toLowerCase();

                    if (sortOrder) {
                        if (xValue > yValue) {
                            shouldSwitch = true;
                            break;
                        }
                    } else {
                        if (xValue < yValue) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
            }
        }

        function sortByName() {
            sortTable(0, nameSortOrder);
            nameSortOrder = !nameSortOrder;
            updateSortIcons();
        }

        function sortByDate() {
            sortTable(1, dateSortOrder);
            dateSortOrder = !dateSortOrder;
            updateSortIcons();
        }

        function sortBySize() {
            sortTable(3, sizeSortOrder);
            sizeSortOrder = !sizeSortOrder;
            updateSortIcons();
        }

        function sortByType() {
            sortTable(2, typeSortOrder);
            typeSortOrder = !typeSortOrder;
            updateSortIcons();
        }

        function updateSortIcons() {
            var nameSortIcon = document.getElementById("nameSortIcon");
            var dateSortIcon = document.getElementById("dateSortIcon");
            var sizeSortIcon = document.getElementById("sizeSortIcon");
            var typeSortIcon = document.getElementById("typeSortIcon");

            if (nameSortIcon) {
                nameSortIcon.textContent = nameSortOrder ? "▴" : "▾";
            }
            if (dateSortIcon) {
                dateSortIcon.textContent = dateSortOrder ? "▴" : "▾";
            }
            if (sizeSortIcon) {
                sizeSortIcon.textContent = sizeSortOrder ? "▴" : "▾";
            }
            if (typeSortIcon) {
                typeSortIcon.textContent = typeSortOrder ? "▴" : "▾";
            }
        }

        let nameSortOrder = true;
        let dateSortOrder = true;
        let sizeSortOrder = true;
        let typeSortOrder = true;

        updateSortIcons();

        function updateTime() {
            var now = new Date();
            var day = now.getDate();
            var month = now.getMonth() + 1;
            var year = now.getFullYear();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();

            // var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var formattedDateTime = days[now.getDay()] + " " + day + " " + getMonthName(month) + " " + year + " \n " + hours + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
            var datetimeElement = document.getElementById("datetime");
            if (datetimeElement) {
                datetimeElement.textContent = formattedDateTime;
            }
        }

        function getMonthName(month) {
            // var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return months[month - 1];
        }

        updateTime();
        setInterval(updateTime, 1000);


        window.onload = function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
            } else {
                document.body.classList.add('dark-mode');
            }
            updatePathColor();
        };

        function toggleMode() {
            const body = document.body;
            const toggleButton = document.querySelector(".toogle");

            // Toggle kelas untuk light dan dark mode
            body.classList.toggle('light-mode');
            body.classList.toggle('dark-mode');

            // Tentukan mode saat ini dan simpan di localStorage
            const currentMode = body.classList.contains('light-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', currentMode);

            // Perbarui teks tombol berdasarkan mode
            toggleButton.textContent = currentMode === 'light' ? 'Dark 🌙' : 'Light ☀️';

            updatePathColor();
        }

        // Inisialisasi tema saat halaman dimuat
        document.addEventListener("DOMContentLoaded", () => {
            const savedTheme = localStorage.getItem('theme');
            const body = document.body;
            const toggleButton = document.querySelector(".toogle");

            // Atur tema berdasarkan preferensi yang tersimpan
            if (savedTheme === 'light') {
                body.classList.add('light-mode');
                body.classList.remove('dark-mode');
                toggleButton.textContent = 'Dark 🌙';
            } else {
                body.classList.add('dark-mode');
                body.classList.remove('light-mode');
                toggleButton.textContent = 'Light ☀️';
            }
        });



        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("loading").style.display = "none";
            document.querySelector(".container").style.display = "block";
        });

    </script>
</head>

<body>
    <div id="loading">
        <div class="loader"></div>
        <h1>Loading...</h1>
    </div>
    <div class="container">
        <h1>File Explorer</h1>
        <header>
            <div class="info">
                <p id="datetime"></p>
            </div>
            <div class="button">
                <button onclick="toggleMode()" class="toogle" type="button">Toggle Light/Dark Mode</button>
                <button onclick="goBack()" class="toogle" type="button">Back</button>
            </div>
        </header>

        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="searchFiles()" placeholder="Search for files...">
        </div>
        <table class="file-table" id="fileTable">
            <thead>
                <tr>
                    <th onclick="sortByName()">Name <span id="nameSortIcon" class="sort-icon">▴</span></th>
                    <th onclick="sortByDate()">Date modified <span id="dateSortIcon" class="sort-icon">▴</span></th>
                    <th onclick="sortByType()">Type <span id="typeSortIcon" class="sort-icon">▴</span></th>
                    <th onclick="sortBySize()">Size <span id="sizeSortIcon" class="sort-icon">▴</span></th>
                </tr>
            </thead>
            <tbody>
                <?php
                function humanFileSize($size, $unit = "")
                {
                    if ((!$unit && $size >= 1 << 30) || $unit == "GB")
                        return number_format($size / (1 << 30), 2) . " GB";
                    if ((!$unit && $size >= 1 << 20) || $unit == "MB")
                        return number_format($size / (1 << 20), 2) . " MB";
                    if ((!$unit && $size >= 1 << 10) || $unit == "KB")
                        return number_format($size / (1 << 10), 2) . " KB";
                    return number_format($size) . " bytes";
                }

                function getFolderSize($dir)
                {
                    $totalSize = 0;
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
                        if ($file->isFile()) {
                            $totalSize += $file->getSize();
                        }
                    }
                    return $totalSize;
                }
                $currentDir = isset($_GET['dir']) ? $_GET['dir'] : './';
                $files = array_diff(scandir($currentDir), array('.', '..'));

                foreach ($files as $file) {
                    $filePath = $currentDir . '/' . $file;
                    $fileSize = is_dir($filePath) ? humanFileSize(getFolderSize($filePath)) : humanFileSize(filesize($filePath));
                    $fileDate = date("F d Y H:i:s.", filemtime($filePath));
                    $fileType = filetype($filePath);

                    echo "<tr>";
                    if (is_dir($filePath)) {
                        echo "<td class='folder-icon'><a href='?dir=" . urlencode($filePath) . "'>$file</a></td>";
                        echo "<td>$fileDate</td>";
                        echo "<td>Folder</td>";
                        echo "<td class='grey-text'>$fileSize</td>";
                    } else {
                        echo "<td class='file-icon'><a href='" . htmlspecialchars($filePath) . "' target='_blank'>$file</a></td>";
                        echo "<td>$fileDate</td>";
                        echo "<td>$fileType</td>";
                        echo "<td>$fileSize</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <footer class="footer">
        <p>&copy; 2024 <?php echo gethostname(); ?>. All rights reserved.</p>
        <a href="https://github.com/lukman754/apache-autoindex-theme" target="_blank">
            Created by <span class="github-icon"><i class="fab fa-github"></i></span> Lukman754 & <span
                class="github-icon"><i class="fab fa-github"></i></span> Xnuvers007
        </a>

    </footer>
</body>

</html>
