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
			margin: 0;
			padding: 0;
		}

		.container {
			width: 90%;
			margin: 0 auto;
			padding: 20px;
		}

		.path-container {
			margin-bottom: 10px;
			font-size: 14px;
			color: #a0a0a0;
		}

		.search-container {
			margin-bottom: 10px;
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			gap: 10px;
		}

		.search-container input[type="text"] {
			flex: 1 1 100%;
			padding: 8px;
			box-sizing: border-box;
			border: 1px solid #2d2d2d;
			border-radius: 4px;
			background-color: #252526;
			color: #d4d4d4;
		}

		.search-container button {
			flex: 1 1 28%;
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
			overflow-x: auto;
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

		.grey-text {
			color: #a0a0a0;
			font-size: 12px;
		}

		@media (max-width: 768px) {

			.search-container input[type="text"],
			.search-container button {
				flex: 1 1 100%;
			}
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

		let nameSortOrder = true;
		let dateSortOrder = true;

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
		}

		function sortByName() {
			sortTable(0, nameSortOrder);
			nameSortOrder = !nameSortOrder;
		}

		function sortByDate() {
			sortTable(1, dateSortOrder);
			dateSortOrder = !dateSortOrder;
		}
	</script>
</head>

<body>
	<div class="container">
		<div class="path-container">
			<?php
			$dir = './';
			echo "Current Path: <span style='color: #fcd53f;'>" . realpath($dir) . "</span>";
			?>
		</div>
		<div class="search-container">
			<input type="text" id="searchInput" onkeyup="searchFiles()" placeholder="Search for files...">
			<button onclick="sortByName()">Filter Name</button>
			<button onclick="sortByDate()">Filter Date modified</button>
		</div>
		<table class="file-table" id="fileTable">
			<thead>
				<tr>
					<th>Name</th>
					<th class="grey-text">Date modified</th>
					<th class="grey-text">Type</th>
					<th class="grey-text">Size</th>
				</tr>
			</thead>
			<tbody>
				<?php
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

					if ($dir_size >= 1024 * 1024 * 1000) {
						$size_display = round($dir_size / (1024 * 1024 * 1024), 2) . " GB";
					} elseif ($dir_size >= 1024 * 1024) {
						$size_display = round($dir_size / (1024 * 1024), 2) . " MB";
					} else {
						$size_display = round($dir_size / 1024, 2) . " KB";
					}

					echo "<tr>
                        <td><span class='folder-icon'></span><a href='$file'>$file</a></td>
                        <td class='grey-text'>$last_modified</td>
                        <td class='grey-text'>File folder</td>
                        <td class='grey-text'>$size_display</td>
                    </tr>";
				}
				?>
			</tbody>
		</table>
	</div>
</body>
</html>
