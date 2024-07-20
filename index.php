<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="description" content="file manager of htdocs or /var/www/html">
    <meta name="language" content="id">
    <meta name="author" content="Xnuvers007 & Lukman754">
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

        footer {
            text-align: center;
            font-size: 14px;
            color: springgreen;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid #2d2d2d;
            
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .path-container {
            margin-bottom: 20px;
            font-size: 14px;
            color: #a0a0a0;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
        }

        .search-container input[type="text"] {
            flex: 1 1 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #2d2d2d;
            border-radius: 4px;
            background-color: #252526;
            color: #d4d4d4;
        }

        .search-container button {
            flex: 1 1 28%;
            padding: 10px;
            background-color: #2d2d2d;
            border: 1px solid #2d2d2d;
            border-radius: 4px;
            color: #d4d4d4;
            cursor: pointer;
            position: relative;
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
        }

        .file-table th,
        .file-table td {
            padding: 12px;
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

        .toogle {
            /* background-color: #2d2d2d; */
            background-color: white;
            /* color: #d4d4d4; */
            color: black;
            font-color: black;
            border: 1px solid #2d2d2d;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            top: 5px;
            right: 10px;
        }

        .light-mode .toogle {
            background-color: #e0e0e0;
            color: #333;
            border: 1px solid #e0e0e0;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            top: 5px;
            right: 10px;
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
        function searchFiles() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("fileTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (filter) {
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            td.innerHTML = txtValue.replace(new RegExp(filter, "gi"), match => `<span class='highlight'>${match}</span>`);
                        } else {
                            tr[i].style.display = "none";
                        }
                    } else {
                        tr[i].style.display = "";
                        td.innerHTML = txtValue;
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
                nameSortIcon.textContent = nameSortOrder ? "🔼" : "🔽";
            }
            if (dateSortIcon) {
                dateSortIcon.textContent = dateSortOrder ? "🔼" : "🔽";
            }
            if (sizeSortIcon) {
                sizeSortIcon.textContent = sizeSortOrder ? "🔼" : "🔽";
            }
            if (typeSortIcon) {
                typeSortIcon.textContent = typeSortOrder ? "🔼" : "🔽";
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

        function updatePathColor() {
            const body = document.body;
            const pathElement = document.querySelector('.path-container span');
            if (body.classList.contains('dark-mode')) {
                pathElement.classList.add('path-color');
                pathElement.classList.remove('light-mode');
            } else {
                pathElement.classList.remove('path-color');
                pathElement.classList.add('light-mode');
            }
        }

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
            body.classList.toggle('light-mode');
            body.classList.toggle('dark-mode');
            const currentMode = body.classList.contains('light-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', currentMode);
            updatePathColor();
        }

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
        <button onclick="toggleMode()" class="toogle" type="button">Toggle Light/Dark Mode</button>
        <p id="datetime"></p>
        <div class="path-container">
            <?php
            $dir = './';
            echo "Current Path: <span class='path-color light-mode'>" . realpath($dir) . "</span>";
            ?>
        </div>
        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="searchFiles()" placeholder="Search for files...">
            <button onclick="sortByName()">Filter Name <span id="nameSortIcon" class="sort-icon">🔼</span></button>
            <button onclick="sortByDate()">Filter Date modified <span id="dateSortIcon"
                    class="sort-icon">🔼</span></button>
            <button onclick="sortBySize()">Filter Size <span id="sizeSortIcon" class="sort-icon">🔼</span></button>
            <button onclick="sortByType()">Filter Type <span id="typeSortIcon" class="sort-icon">🔼</span></button>
        </div>
        <table class="file-table" id="fileTable">
            <thead>
                <tr>
                    <th onclick="sortByName()">Name <span id="nameSortIcon" class="sort-icon">🔼</span></th>
                    <th onclick="sortByDate()">Date modified <span id="dateSortIcon" class="sort-icon">🔼</span></th>
                    <th onclick="sortByType()">Type <span id="typeSortIcon" class="sort-icon">🔼</span></th>
                    <th onclick="sortBySize()">Size <span id="sizeSortIcon" class="sort-icon">🔼</span></th>
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

                $dir = './';
                $files = array_diff(scandir($dir), array('.', '..'));
                usort($files, function ($a, $b) use ($dir) {
                    return filemtime($dir . $b) - filemtime($dir . $a);
                });

                foreach ($files as $file) {
                    $filePath = $dir . $file;
                    $fileSize = is_dir($filePath) ? humanFileSize(getFolderSize($filePath)) : humanFileSize(filesize($filePath));
                    $fileDate = date("F d Y H:i:s.", filemtime($filePath));
                    $fileType = filetype($filePath);

                    echo "<tr>";
                    if (is_dir($filePath)) {
                        echo "<td class='folder-icon'><a href='$filePath'>$file</a></td>";
                        echo "<td>$fileDate</td>";
                        echo "<td>Folder</td>";
                        echo "<td class='grey-text'>$fileSize</td>";
                    } else {
                        echo "<td class='file-icon'><a href='$filePath'>$file</a></td>";
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
        <strong>
            <p>
                &copy; 2024 <?php echo gethostname(); ?>. All rights reserved. <br /> <br />
                <a href="https://github.com/lukman754/apache-autoindex-theme" target="__blank">
                    CREATED BY LUKMAN754 & XNUVERS007
                </a>
            </p>
        </strong>
    </footer>
</body>

</html>
