<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Project</title>
	<style>
		body {
			background-color: #1e1e1e;
			color: #d4d4d4;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.search-container {
			margin-bottom: 10px;
			display: flex;
			justify-content: space-between;
		}

		.search-container input[type="text"] {
			width: 92%;
			padding: 8px;
			box-sizing: border-box;
			border: 1px solid #2d2d2d;
			border-radius: 4px;
			background-color: #252526;
			color: #d4d4d4;
		}

		.search-container button {
			padding: 8px;
			background-color: #2d2d2d;
			border: 1px solid #2d2d2d;
			border-radius: 4px;
			color: #d4d4d4;
			cursor: pointer;
		}

		.file-table {
			width: 100%;
			border-collapse: collapse;
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
		}

		.file-table tr:hover {
			background-color: #2a2d2e;
		}

		.folder-icon::before {
			content: "📁";
			margin-right: 5px;
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
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					} else {
						tr[i].style.display = "none";
					}
				}
			}
		}

		let sortOrder = true; // true for ascending, false for descending

		function sortTable() {
			var table, rows, switching, i, x, y, shouldSwitch;
			table = document.getElementById("fileTable");
			switching = true;

			while (switching) {
				switching = false;
				rows = table.rows;

				for (i = 1; i < (rows.length - 1); i++) {
					shouldSwitch = false;
					x = rows[i].getElementsByTagName("TD")[0];
					y = rows[i + 1].getElementsByTagName("TD")[0];

					if (sortOrder) {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
							shouldSwitch = true;
							break;
						}
					} else {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
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

			sortOrder = !sortOrder;
		}
	</script>
</head>

<body>
	<div class="search-container">
		<input type="text" id="searchInput" onkeyup="searchFiles()" placeholder="Search for files...">
		<button onclick="sortTable()">Filter Name</button>
	</div>
	<table class="file-table" id="fileTable">
		<thead>
			<tr>
				<th>Name</th>
				<th>Date modified</th>
				<th>Type</th>
				<th>Size</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$dir = './';
			$files = scandir($dir);

			function countFiles($dir)
			{
				$file_count = 0;
				$files = scandir($dir);
				foreach ($files as $file) {
					if ($file != '.' && $file != '..' && !is_dir($dir . '/' . $file)) {
						$file_count++;
					}
				}
				return $file_count;
			}

			function getDirSize($dir)
			{
				$size = 0;
				foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
					$size += $file->getSize();
				}
				return $size;
			}

			$folders = [];

			foreach ($files as $file) {
				if ($file != '.' && $file != '..' && is_dir($file)) {
					$folders[] = $file;
				}
			}

			usort($folders, function ($a, $b) use ($dir) {
				return filemtime($dir . $b) - filemtime($dir . $a);
			});

			foreach ($folders as $file) {
				$file_count = countFiles($dir . $file);
				$dir_size = getDirSize($dir . $file);
				$last_modified = date("d/m/Y H:i", filemtime($dir . $file));

				$size_display = ($dir_size < 1024 * 1024) ? round($dir_size / 1024, 2) . " KB" : round($dir_size / (1024 * 1024), 2) . " MB";

				echo "<tr>
                    <td><span class='folder-icon'></span><a href='$file'>$file</a></td>
                    <td>$last_modified</td>
                    <td>File folder</td>
                    <td>$size_display</td>
                </tr>";
			}
			?>
		</tbody>
	</table>
</body>

</html>