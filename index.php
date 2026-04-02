<?php
session_start();

$auth_username = 'Xnuvers007';
$auth_password = 'Xnuvers007';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

if (!isset($_SESSION['sudah_login']) || $_SESSION['sudah_login'] !== true) {
    $error_msg = "";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input_user = $_POST['u'] ?? '';
        $input_pass = $_POST['p'] ?? '';
        
        if ($input_user === $auth_username && $input_pass === $auth_password) {
            $_SESSION['sudah_login'] = true;
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $error_msg = "Username atau Password salah!";
        }
    }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Area</title>
    <style>
        body { background-color: #1e1e1e; color: #d4d4d4; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #252526; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); width: 100%; max-width: 320px; text-align: center; }
        h2 { margin-top: 0; color: #fff; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #333; border: 1px solid #444; color: #fff; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; }
        button:hover { background: #0056b3; }
        .error { color: #ff4d4d; margin-bottom: 15px; font-size: 14px; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔒 Restricted File
            <br />
            Username : Xnuvers007 | Password : Xnuvers007
        </h2>
        <?php if($error_msg): ?><div class="error"><?= $error_msg ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required autocomplete="off">
            <input type="password" name="p" placeholder="Password" required autocomplete="off">
            <button type="submit">MASUK</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}
    
ob_start();

header('X-Frame-Options: SAMEORIGIN');

function logActivity($message) {
    $logFile = 'activity.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function isAllowedFile($file, $allowedExtensions)
{
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $allowedExtensions);
}

function isPathInsideBase($path, $baseDir)
{
    $realBaseDir = realpath($baseDir);
    $realPath = realpath($path);

    if ($realBaseDir === false || $realPath === false) {
        return false;
    }

    $realBaseDir = rtrim($realBaseDir, DIRECTORY_SEPARATOR);
    $realPath = rtrim($realPath, DIRECTORY_SEPARATOR);

    return $realPath === $realBaseDir || strpos($realPath, $realBaseDir . DIRECTORY_SEPARATOR) === 0;
}

function addPathToZip(ZipArchive $zip, $path, $localName)
{
    if (is_file($path)) {
        $zip->addFile($path, $localName);
        return;
    }

    if (!is_dir($path)) {
        return;
    }

    $zip->addEmptyDir($localName);

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = $localName . '/' . substr($item->getPathname(), strlen($path) + 1);

        if ($item->isDir()) {
            $zip->addEmptyDir($relativePath);
        } else {
            $zip->addFile($item->getPathname(), $relativePath);
        }
    }
}

function trashDirectory()
{
    return __DIR__ . '/.trash';
}

function trashManifestPath()
{
    return trashDirectory() . '/manifest.json';
}

function ensureTrashDirectory()
{
    if (!is_dir(trashDirectory())) {
        mkdir(trashDirectory(), 0777, true);
    }
}

function loadTrashManifest()
{
    ensureTrashDirectory();
    $manifestPath = trashManifestPath();

    if (!file_exists($manifestPath)) {
        return [];
    }

    $data = json_decode(file_get_contents($manifestPath), true);
    return is_array($data) ? $data : [];
}

function saveTrashManifest(array $manifest)
{
    ensureTrashDirectory();
    file_put_contents(trashManifestPath(), json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function generateBreadcrumb($currentDir)
{
    if (!is_string($currentDir)) {
        $currentDir = './';
    }

    $currentDir = realpath($currentDir) ?: $currentDir;
    $breadcrumbHtml = '<nav class="breadcrumb"><a href="?dir=./">Home</a>';
    
    $path = str_replace('\\', '/', $currentDir);
    $baseDir = str_replace('\\', '/', realpath('./'));
    
    if (strpos($path, $baseDir) === 0) {
        $relativePath = substr($path, strlen($baseDir));
        $relativePath = ltrim($relativePath, '/');
        
        if ($relativePath !== '') {
            $segments = array_values(array_filter(explode('/', $relativePath), 'strlen'));
            $builtPath = './';
            
            foreach ($segments as $segment) {
                $newPath = $builtPath . $segment;
                $encodedPath = urlencode($newPath);
                $breadcrumbHtml .= ' <span class="separator">/</span> ';
                $breadcrumbHtml .= '<a href="?dir=' . $encodedPath . '">' . htmlspecialchars($segment) . '</a>';
                $builtPath = $newPath . '/';
            }
        }
    }
    
    $breadcrumbHtml .= '</nav>';
    return $breadcrumbHtml;
}

function normalizeRelativePath($path)
{
    $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    return trim($path, DIRECTORY_SEPARATOR);
}

function uniqueTrashName($originalPath)
{
    return date('Ymd_His') . '_' . substr(sha1($originalPath . microtime(true)), 0, 10);
}

function movePathToTrash($path)
{
    ensureTrashDirectory();

    $realPath = realpath($path);
    if ($realPath === false) {
        return ['ok' => false, 'message' => 'Invalid file or folder.'];
    }

    $trashId = uniqueTrashName($realPath);
    $trashPath = trashDirectory() . '/' . $trashId;
    $manifest = loadTrashManifest();

    if (!rename($realPath, $trashPath)) {
        return ['ok' => false, 'message' => 'Failed to move item to trash.'];
    }

    $manifest[$trashId] = [
        'original_path' => $realPath,
        'trashed_at' => date('Y-m-d H:i:s'),
        'name' => basename($realPath),
        'type' => is_dir($trashPath) ? 'Folder' : 'File'
    ];

    saveTrashManifest($manifest);

    return ['ok' => true, 'message' => 'Item moved to trash successfully!', 'id' => $trashId];
}

function restoreTrashItem($trashId)
{
    $manifest = loadTrashManifest();
    if (!isset($manifest[$trashId])) {
        return ['ok' => false, 'message' => 'Trash item not found.'];
    }

    $trashPath = trashDirectory() . '/' . $trashId;
    if (!file_exists($trashPath)) {
        unset($manifest[$trashId]);
        saveTrashManifest($manifest);
        return ['ok' => false, 'message' => 'Trash file missing.'];
    }

    $originalPath = $manifest[$trashId]['original_path'];
    $originalDir = dirname($originalPath);

    if (!is_dir($originalDir)) {
        mkdir($originalDir, 0777, true);
    }

    $targetPath = $originalPath;
    if (file_exists($targetPath)) {
        $targetPath = $originalDir . '/' . pathinfo($originalPath, PATHINFO_FILENAME) . '_restored_' . time();
        if (pathinfo($originalPath, PATHINFO_EXTENSION) !== '') {
            $targetPath .= '.' . pathinfo($originalPath, PATHINFO_EXTENSION);
        }
    }

    if (!rename($trashPath, $targetPath)) {
        return ['ok' => false, 'message' => 'Failed to restore item.'];
    }

    unset($manifest[$trashId]);
    saveTrashManifest($manifest);

    return ['ok' => true, 'message' => 'Item restored successfully!'];
}

function emptyTrashItem($trashId)
{
    $manifest = loadTrashManifest();
    $trashPath = trashDirectory() . '/' . $trashId;

    if (file_exists($trashPath)) {
        if (is_dir($trashPath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($trashPath, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    rmdir($item->getPathname());
                } else {
                    unlink($item->getPathname());
                }
            }
            rmdir($trashPath);
        } else {
            unlink($trashPath);
        }
    }

    unset($manifest[$trashId]);
    saveTrashManifest($manifest);

    return ['ok' => true, 'message' => 'Trash item permanently deleted.'];
}

function scanDuplicateFiles($baseDir)
{
    $baseDirReal = realpath($baseDir);
    if ($baseDirReal === false) {
        return ['ok' => false, 'message' => 'Invalid directory.'];
    }

    $groups = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDirReal, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $path = $file->getPathname();
        $key = $file->getSize() . '|' . md5_file($path);
        if (!isset($groups[$key])) {
            $groups[$key] = [];
        }

        $groups[$key][] = $path;
    }

    $duplicates = [];
    foreach ($groups as $files) {
        if (count($files) > 1) {
            $duplicates[] = $files;
        }
    }

    return ['ok' => true, 'duplicates' => $duplicates, 'count' => count($duplicates)];
}

function searchFilesByContent($baseDir, $query)
{
    $baseDirReal = realpath($baseDir);
    if ($baseDirReal === false) {
        return ['ok' => false, 'message' => 'Invalid directory.'];
    }

    $query = trim((string) $query);
    if ($query === '') {
        return ['ok' => false, 'message' => 'Search query cannot be empty.'];
    }

    $matches = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDirReal, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getSize() > 2097152) {
            continue;
        }

        $path = $file->getPathname();
        $content = @file_get_contents($path);
        if ($content !== false && stripos($content, $query) !== false) {
            $matches[] = $path;
        }
    }

    return ['ok' => true, 'matches' => $matches, 'count' => count($matches)];
}

function copyDirectory($source, $dest)
{
    if (!is_dir($source)) {
        return false;
    }

    if (!is_dir($dest)) {
        @mkdir($dest, 0777, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($source) + 1);
        $targetPath = $dest . DIRECTORY_SEPARATOR . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($targetPath)) {
                @mkdir($targetPath, 0777, true);
            }
        } else {
            @copy($item->getPathname(), $targetPath);
        }
    }

    return true;
}

function sendJsonResponse(array $data, int $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

function sanitizeZipFilename($name)
{
    $name = trim((string) $name);
    $name = preg_replace('/[^A-Za-z0-9 _-]+/', '', $name);
    $name = preg_replace('/\s+/', '_', $name);
    $name = trim($name, '._-');

    if ($name === '') {
        $name = 'selected_items_' . date('Ymd_His');
    }

    if (substr(strtolower($name), -4) !== '.zip') {
        $name .= '.zip';
    }

    return $name;
}

function normalizeZipExtensionFilter($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return [];
    }

    $parts = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
    $extensions = [];

    foreach ($parts as $part) {
        $part = ltrim(trim($part), '.');
        if ($part !== '') {
            $extensions[] = $part;
        }
    }

    return array_values(array_unique($extensions));
}

function matchesZipFilter($path, $filterType, array $extensions)
{
    if ($filterType === 'folders') {
        return is_dir($path);
    }

    if ($filterType === 'files') {
        return is_file($path);
    }

    if ($filterType === 'extension') {
        if (!is_file($path) || count($extensions) === 0) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, $extensions, true);
    }

    return true;
}

function collectZipEntries(
    ZipArchive $zip,
    $path,
    $localName,
    $includeRecursive,
    $filterType,
    array $extensions,
    array &$stats
)
{
    if (is_file($path)) {
        if (!matchesZipFilter($path, $filterType, $extensions)) {
            $stats['skipped']++;
            return;
        }

        if ($zip->addFile($path, $localName)) {
            $stats['entries']++;
            $stats['bytes'] += filesize($path) ?: 0;
        }

        return;
    }

    if (!is_dir($path)) {
        $stats['skipped']++;
        return;
    }

    if (!$includeRecursive) {
        if (matchesZipFilter($path, $filterType, $extensions)) {
            if ($filterType !== 'files' && $zip->addEmptyDir($localName)) {
                $stats['entries']++;
            }
        } else {
            $stats['skipped']++;
        }

        return;
    }

    if ($filterType !== 'files' && $zip->addEmptyDir($localName)) {
        $stats['entries']++;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = $localName . '/' . substr($item->getPathname(), strlen($path) + 1);

        if ($item->isDir()) {
            if ($filterType !== 'files') {
                if ($filterType === 'all' || $filterType === 'folders') {
                    if ($zip->addEmptyDir($relativePath)) {
                        $stats['entries']++;
                    }
                }
            }
            continue;
        }

        if (!matchesZipFilter($item->getPathname(), $filterType, $extensions)) {
            $stats['skipped']++;
            continue;
        }

        if ($zip->addFile($item->getPathname(), $relativePath)) {
            $stats['entries']++;
            $stats['bytes'] += $item->getSize();
        }
    }
}

$allowedExtensions = [
    'html', 'css', 'js', 'env', 'php', 'txt', 'json', 'xml', 'env', 'gitignore', 'md',
    'yml', 'yaml', 'ini', 'conf', 'log', 'htaccess', 'htpasswd', 'csv', 'tsv', 'sql',
    'c', 'cpp', 'h', 'java', 'py', 'rb', 'sh', 'bat', 'pl', 'go', 'rs', 'swift', 'ts',
    'phtml', 'shtml', 'xhtml', 'jsp', 'asp', 'aspx', 'jspx', 'cfm', 'cfml',
    'scss', 'less', 'sass', 'vue', 'jsx', 'tsx', 'dart', 'lua', 'r', 'm', 'erl', 'hs',
    'groovy', 'kt', 'kts', 'sql', 'ps1', 'psm1', 'vbs', 'vb', 'asm', 'makefile', 'dockerfile'
];

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $file = $_GET['file'] ?? '';

    if ($action === 'read' && is_file($file)) {
        if (!isAllowedFile($file, $allowedExtensions)) {
            logActivity("Read File Attempted: $file - Not Allowed");
            echo "This file type is not allowed to be edited.";
            exit;
        }
        echo file_get_contents($file);
        logActivity("Read File: $file");
        exit;
    }

    if ($action === 'save' && is_file($file)) {
        if (!isAllowedFile($file, $allowedExtensions)) {
            echo "This file type is not allowed to be edited.";
            logActivity("Save File Attempted: $file - Not Allowed");
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents($file, $data['content']);
        echo "File saved successfully!";
        logActivity("Saved File: $file");
        exit;
    }

    if ($action === 'rename' && is_file($file)) {
        $newName = $_GET['newName'] ?? '';
        $newPath = dirname($file) . '/' . $newName;
        if (rename($file, $newPath)) {
            echo "File renamed successfully!";
            logActivity("Renamed File: $file to $newPath");
        } else {
            echo "Failed to rename file.";
            logActivity("Failed to Rename File: $file to $newPath");
        }
        exit;
    }

    if ($action === 'listFiles') {
        $currentDir = isset($_GET['dir']) ? $_GET['dir'] : './';

        if (is_dir($currentDir)) {
            $files = array_diff(scandir($currentDir), array('.', '..'));
            logActivity("Listed Files in Directory: $currentDir");
        } else {
            echo json_encode(['error' => 'Invalid directory']);
            logActivity("List Files Attempted: $currentDir - Invalid Directory");
            exit;
        }

        $fileList = [];
        foreach ($files as $file) {
            $filePath = $currentDir . '/' . $file;
            $fileList[] = [
                'name' => $file,
                'date' => date("F d Y H:i:s.", filemtime($filePath)),
                'type' => is_dir($filePath) ? 'Folder' : 'File',
                'size' => is_dir($filePath) ? humanFileSize(getFolderSize($filePath)) : humanFileSize(filesize($filePath))
            ];
        }
        echo json_encode($fileList);
        exit;
    }

    if ($action === 'delete') {
        if (is_file($file)) {
            $result = movePathToTrash($file);
            echo $result['message'];
            if ($result['ok']) {
                logActivity("Moved File to Trash: $file");
            } else {
                logActivity("Failed to Move File to Trash: $file");
            }
        } elseif (is_dir($file)) {
            $result = movePathToTrash($file);
            echo $result['message'];
            if ($result['ok']) {
                logActivity("Moved Folder to Trash: $file");
            } else {
                logActivity("Failed to Move Folder to Trash: $file");
            }
        } else {
            echo "Invalid file or folder.";
            logActivity("Delete Attempted: $file - Invalid File/Folder");
        }
        exit;
    }

    if ($action === 'restoreTrash') {
        $trashId = $_GET['trashId'] ?? '';
        $result = restoreTrashItem($trashId);
        echo $result['message'];
        logActivity(($result['ok'] ? 'Restored Trash Item: ' : 'Failed to Restore Trash Item: ') . $trashId);
        exit;
    }

    if ($action === 'purgeTrash') {
        $trashId = $_GET['trashId'] ?? '';
        $result = emptyTrashItem($trashId);
        echo $result['message'];
        logActivity('Purged Trash Item: ' . $trashId);
        exit;
    }

    if ($action === 'findDuplicates') {
        $dir = $_GET['dir'] ?? './';
        $result = scanDuplicateFiles($dir);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
        exit;
    }

    if ($action === 'searchContent') {
        $dir = $_GET['dir'] ?? './';
        $query = $_GET['q'] ?? '';
        $result = searchFilesByContent($dir, $query);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
        exit;
    }

    if ($action === 'moveFile') {
        $source = $_GET['source'] ?? '';
        $destination = $_GET['destination'] ?? '';
        $mode = $_GET['mode'] ?? 'move'; // 'move' or 'copy'

        if (!file_exists($source)) {
            sendJsonResponse(['ok' => false, 'message' => 'Source file/folder not found.'], 400);
        }

        if (!is_dir($destination)) {
            sendJsonResponse(['ok' => false, 'message' => 'Destination folder not found.'], 400);
        }

        // Prevent dragging to itself
        if (realpath($source) === realpath($destination)) {
            sendJsonResponse(['ok' => false, 'message' => 'Cannot move/copy to the same location.'], 400);
        }

        $targetPath = rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . basename($source);

        // Handle naming conflicts
        $counter = 1;
        $originalTargetPath = $targetPath;
        while (file_exists($targetPath)) {
            if (is_file($source)) {
                $info = pathinfo($originalTargetPath);
                $targetPath = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $counter . '.' . ($info['extension'] ?? '');
            } else {
                $targetPath = $originalTargetPath . '_' . $counter;
            }
            $counter++;
        }

        if ($mode === 'copy') {
            if (is_file($source)) {
                if (@copy($source, $targetPath)) {
                    logActivity("Copied File: $source to $targetPath");
                    sendJsonResponse(['ok' => true, 'message' => 'File copied successfully!']);
                } else {
                    sendJsonResponse(['ok' => false, 'message' => 'Failed to copy file.'], 500);
                }
            } else {
                // Recursive copy for directories
                if (copyDirectory($source, $targetPath)) {
                    logActivity("Copied Folder: $source to $targetPath");
                    sendJsonResponse(['ok' => true, 'message' => 'Folder copied successfully!']);
                } else {
                    sendJsonResponse(['ok' => false, 'message' => 'Failed to copy folder.'], 500);
                }
            }
        } else { // move
            if (@rename($source, $targetPath)) {
                logActivity("Moved: $source to $targetPath");
                sendJsonResponse(['ok' => true, 'message' => 'Item moved successfully!']);
            } else {
                sendJsonResponse(['ok' => false, 'message' => 'Failed to move item.'], 500);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_selected'])) {
    $currentDir = $_POST['current_dir'] ?? './';
    $selectedItems = $_POST['selected_items'] ?? [];
    $zipFilename = sanitizeZipFilename($_POST['zip_filename'] ?? '');
    $includeMode = $_POST['include_mode'] ?? 'selected';
    $filterType = $_POST['filter_type'] ?? 'all';
    $extensionFilter = normalizeZipExtensionFilter($_POST['filter_extensions'] ?? '');

    if (!is_dir($currentDir)) {
        sendJsonResponse(['ok' => false, 'message' => 'Invalid directory.'], 400);
    }

    if (!is_array($selectedItems) || count($selectedItems) === 0) {
        sendJsonResponse(['ok' => false, 'message' => 'Pilih minimal satu folder/file.'], 400);
    }

    if (!in_array($includeMode, ['selected', 'recursive'], true)) {
        $includeMode = 'selected';
    }

    if (!in_array($filterType, ['all', 'files', 'folders', 'extension'], true)) {
        $filterType = 'all';
    }

    if ($filterType === 'extension' && count($extensionFilter) === 0) {
        sendJsonResponse(['ok' => false, 'message' => 'Masukkan ekstensi file, misalnya php,js,css.'], 400);
    }

    $baseDirReal = realpath($currentDir);
    if ($baseDirReal === false) {
        sendJsonResponse(['ok' => false, 'message' => 'Gagal membaca direktori saat ini.'], 400);
    }

    $tempBase = tempnam(sys_get_temp_dir(), 'zip_');
    $zipPath = $tempBase . '.zip';
    unlink($tempBase);

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
        sendJsonResponse(['ok' => false, 'message' => 'Failed to create zip file.'], 500);
    }

    $stats = [
        'entries' => 0,
        'bytes' => 0,
        'skipped' => 0,
    ];

    $addedNames = [];
    foreach ($selectedItems as $selectedItem) {
        $selectedPath = realpath($selectedItem);
        if ($selectedPath === false) {
            $stats['skipped']++;
            continue;
        }

        if (!isPathInsideBase($selectedPath, $baseDirReal)) {
            $stats['skipped']++;
            continue;
        }

        $localName = ltrim(substr($selectedPath, strlen($baseDirReal)), DIRECTORY_SEPARATOR);
        if ($localName === '') {
            $localName = basename($selectedPath);
        }

        if (isset($addedNames[$localName])) {
            continue;
        }

        $beforeEntries = $stats['entries'];
        collectZipEntries($zip, $selectedPath, $localName, $includeMode === 'recursive', $filterType, $extensionFilter, $stats);
        if ($stats['entries'] > $beforeEntries) {
            $addedNames[$localName] = true;
        }
    }

    $zip->close();

    if ($stats['entries'] === 0 || !file_exists($zipPath)) {
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
        sendJsonResponse(['ok' => false, 'message' => 'Tidak ada item yang masuk ke ZIP. Cek filter atau pilihan folder kosong.'], 400);
    }

    logActivity('Downloaded ZIP for selected items in ' . $currentDir . ' | entries=' . $stats['entries'] . ' | bytes=' . $stats['bytes']);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
    header('Content-Length: ' . filesize($zipPath));
    header('X-Zip-Item-Count: ' . $stats['entries']);
    header('X-Zip-Total-Bytes: ' . $stats['bytes']);
    header('X-Zip-Filename: ' . $zipFilename);
    readfile($zipPath);
    unlink($zipPath);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadedFiles'])) {
    $uploadDir = isset($_GET['dir']) ? $_GET['dir'] : './';
    $uploadedFiles = $_FILES['uploadedFiles'];

    $successCount = 0;
    $errorCount = 0;

    foreach ($uploadedFiles['name'] as $key => $fileName) {
        $fileTmpName = $uploadedFiles['tmp_name'][$key];
        $filePath = $uploadDir . '/' . basename($fileName);

        if (move_uploaded_file($fileTmpName, $filePath)) {
            $successCount++;
            logActivity("Uploaded File: $filePath");
        } else {
            $errorCount++;
            logActivity("Failed to Upload File: $filePath");
        }
    }

    if ($successCount > 0) {
        echo "<script>alert('$successCount file(s) uploaded successfully!'); window.location.href = '?dir=" . urlencode($uploadDir) . "';</script>";
        logActivity("Upload Summary: $successCount Success, $errorCount Failed in $uploadDir");
    }

    if ($errorCount > 0) {
        echo "<script>alert('$errorCount file(s) failed to upload.');</script>";
        logActivity("Upload Summary: $successCount Success, $errorCount Failed in $uploadDir");
    }
}
?>

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

        .breadcrumb {
            display: flex;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 15px;
            font-size: 14px;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: #57a3ff;
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }

        .breadcrumb a:hover {
            background-color: #2a2d2e;
            color: #7eb3ff;
        }

        .breadcrumb .separator {
            color: #888;
            margin: 0 5px;
        }

        .light-mode .breadcrumb a {
            color: #0066cc;
        }

        .light-mode .breadcrumb a:hover {
            background-color: #e0e0e0;
            color: #0052a3;
        }

        .light-mode .breadcrumb .separator {
            color: #666;
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
            width: 4%;
            text-align: center;
        }

        .file-table th:nth-child(2),
        .file-table td:nth-child(2) {
            width: 28%;
        }

        .file-table th:nth-child(3),
        .file-table td:nth-child(3) {
            width: 22%;
        }

        .file-table th:nth-child(4),
        .file-table td:nth-child(4) {
            width: 12%;
        }

        .file-table th:nth-child(5),
        .file-table td:nth-child(5) {
            width: 10%;
        }

        .file-table th:nth-child(6),
        .file-table td:nth-child(6),
        .file-table th:nth-child(7),
        .file-table td:nth-child(7),
        .file-table th:nth-child(8),
        .file-table td:nth-child(8) {
            width: 8%;
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

        .pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    text-decoration: none;
    color: #007bff;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

.pagination a:hover {
    background-color: #007bff;
    color: #fff;
}

.pagination a[style*="color: #fcd53f"] {
    background-color: #fcd53f;
    color: #000;
    font-weight: bold;
}

        .zip-toolbar {
            margin-bottom: 12px;
            padding: 12px;
            border: 1px solid #2d2d2d;
            border-radius: 8px;
            background: #252526;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .zip-toolbar input,
        .zip-toolbar select {
            background: #1e1e1e;
            color: #d4d4d4;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 8px 10px;
        }

        .zip-toolbar .zip-field {
            min-width: 170px;
        }

        .zip-toolbar .zip-small-field {
            min-width: 140px;
        }

        .zip-toolbar .zip-status {
            color: #a0a0a0;
            font-size: 12px;
        }

        .zip-progress-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .zip-progress-card {
            background: #252526;
            color: #d4d4d4;
            border-radius: 10px;
            padding: 24px 28px;
            min-width: 280px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .zip-spinner {
            width: 42px;
            height: 42px;
            margin: 0 auto 12px;
            border: 4px solid rgba(255, 255, 255, 0.18);
            border-top-color: #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .search-advanced {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .search-advanced input,
        .search-advanced select {
            width: 100%;
            box-sizing: border-box;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #444;
            background: #1e1e1e;
            color: #d4d4d4;
        }

        .upload-progress-wrap {
            display: none;
            margin-top: 12px;
            background: #1e1e1e;
            border: 1px solid #444;
            border-radius: 6px;
            overflow: hidden;
        }

        .upload-progress-bar {
            width: 0%;
            height: 12px;
            background: linear-gradient(90deg, #007bff, #28a745);
            transition: width 0.2s ease;
        }

        .upload-progress-text {
            font-size: 12px;
            padding: 8px 10px;
            color: #d4d4d4;
        }

        .trash-panel {
            margin-top: 20px;
            background: #252526;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 14px;
        }

        .trash-panel h3,
        .duplicate-panel h3 {
            margin-top: 0;
        }

        .trash-item,
        .duplicate-group {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        .trash-item small,
        .duplicate-group small {
            color: #a0a0a0;
            display: block;
            margin-top: 4px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.72);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .modal-card {
            width: min(900px, 92vw);
            max-height: 84vh;
            overflow: auto;
            background: #252526;
            color: #d4d4d4;
            border-radius: 10px;
            padding: 18px;
            border: 1px solid #333;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            margin-bottom: 12px;
        }

        .modal-close {
            background: #444;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .context-menu {
            position: fixed;
            background: #252526;
            border: 1px solid #444;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            z-index: 10001;
            min-width: 180px;
            display: none;
        }

        .context-menu.show {
            display: block;
        }

        .context-item {
            padding: 10px 16px;
            cursor: pointer;
            color: #d4d4d4;
            font-size: 13px;
            transition: background-color 0.15s;
            user-select: none;
        }

        .context-item:hover {
            background-color: #2a2d2e;
            color: #7eb3ff;
        }

        .context-item:active {
            background-color: #1e1e1e;
        }

        .light-mode .context-menu {
            background: #f5f5f5;
            border-color: #ccc;
        }

        .light-mode .context-item {
            color: #333;
        }

        .light-mode .context-item:hover {
            background-color: #e8e8e8;
            color: #0066cc;
        }

    </style>
    <script>
        function goBack() {
            const currentDir = window.location.search ? new URLSearchParams(window.location.search).get('dir') : './';
            const parentDir = currentDir.substring(0, currentDir.lastIndexOf('/')) || './';
            window.location.href = '?dir=' + encodeURIComponent(parentDir);
        }


        function applyAdvancedSearch() {
            const nameQuery = (document.getElementById('searchName')?.value || document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const typeQuery = document.getElementById('searchType')?.value || 'all';
            const extensionQuery = (document.getElementById('searchExtension')?.value || '').toLowerCase().trim().replace(/^\./, '');
            const sizeMin = parseFloat(document.getElementById('searchSizeMin')?.value || '');
            const sizeMax = parseFloat(document.getElementById('searchSizeMax')?.value || '');
            const dateFrom = document.getElementById('searchDateFrom')?.value ? new Date(document.getElementById('searchDateFrom').value + 'T00:00:00') : null;
            const dateTo = document.getElementById('searchDateTo')?.value ? new Date(document.getElementById('searchDateTo').value + 'T23:59:59') : null;
            const rows = document.querySelectorAll('#fileTable tbody tr');

            rows.forEach(row => {
                const nameCell = row.getElementsByTagName('td')[1];
                const dateCell = row.getElementsByTagName('td')[2];
                const typeCell = row.getElementsByTagName('td')[3];
                const sizeCell = row.getElementsByTagName('td')[4];

                if (!nameCell || !dateCell || !typeCell || !sizeCell) {
                    return;
                }

                const nameText = nameCell.textContent.toLowerCase();
                const typeText = typeCell.textContent.toLowerCase();
                const sizeValue = parseSize(sizeCell.textContent);
                const dateValue = new Date(dateCell.textContent.replace(/\.$/, ''));
                const extensionMatch = nameText.includes('.') ? nameText.split('.').pop() : '';

                let visible = true;
                if (nameQuery && !nameText.includes(nameQuery)) visible = false;
                if (visible && typeQuery !== 'all' && typeText !== typeQuery) visible = false;
                if (visible && extensionQuery && extensionMatch !== extensionQuery) visible = false;
                if (visible && !Number.isNaN(sizeMin) && sizeValue < sizeMin) visible = false;
                if (visible && !Number.isNaN(sizeMax) && sizeValue > sizeMax) visible = false;
                if (visible && dateFrom && !Number.isNaN(dateValue.getTime()) && dateValue < dateFrom) visible = false;
                if (visible && dateTo && !Number.isNaN(dateValue.getTime()) && dateValue > dateTo) visible = false;

                row.style.display = visible ? '' : 'none';
            });
        }

        function clearAdvancedSearch() {
            ['searchInput', 'searchName', 'searchExtension', 'searchSizeMin', 'searchSizeMax', 'searchDateFrom', 'searchDateTo', 'searchContent'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = '';
                }
            });

            const searchType = document.getElementById('searchType');
            if (searchType) {
                searchType.value = 'all';
            }

            applyAdvancedSearch();
        }

        async function searchContentFiles() {
            const query = (document.getElementById('searchContent')?.value || '').trim();
            if (!query) {
                alert('Masukkan kata kunci isi file.');
                return;
            }

            showZipProgress('Mencari isi file...');
            try {
                const currentDir = window.location.search ? new URLSearchParams(window.location.search).get('dir') : './';
                const response = await fetch(`?action=searchContent&dir=${encodeURIComponent(currentDir)}&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (!data.ok) {
                    throw new Error(data.message || 'Gagal mencari isi file.');
                }

                if (!data.matches || data.matches.length === 0) {
                    showDuplicateModal('<div>Tidak ada file yang mengandung kata tersebut.</div>');
                    return;
                }

                const html = '<div class="duplicate-group"><strong>Content Search Results</strong><small>' + data.count + ' file ditemukan</small><ul>' + data.matches.map(item => `<li>${item}</li>`).join('') + '</ul></div>';
                showDuplicateModal(html);
            } catch (error) {
                alert(error.message || 'Gagal mencari isi file.');
            } finally {
                hideZipProgress();
            }
        }

        function searchFiles() {
            const nameInput = document.getElementById('searchInput');
            const nameQuery = (nameInput ? nameInput.value : '').toLowerCase().trim();
            const nameFilter = (document.getElementById('searchName')?.value || '').toLowerCase().trim();

            if (nameInput) {
                document.getElementById('searchName').value = nameQuery || nameFilter;
            }

            applyAdvancedSearch();

            const rows = document.querySelectorAll('#fileTable tbody tr');
            rows.forEach(row => {
                const nameCell = row.getElementsByTagName('td')[1];
                if (!nameCell || row.style.display === 'none') {
                    return;
                }

                const link = nameCell.getElementsByTagName('a')[0];
                if (link && nameQuery) {
                    const originalText = link.textContent || link.innerText;
                    const highlightedText = originalText.replace(new RegExp(nameQuery, 'gi'), match => `<span class='highlight'>${match}</span>`);
                    link.innerHTML = highlightedText;
                }
            });
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

                    let xValue = columnIndex === 4 ? parseSize(x.textContent) : x.textContent.toLowerCase();
                    let yValue = columnIndex === 4 ? parseSize(y.textContent) : y.textContent.toLowerCase();

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
            sortTable(1, nameSortOrder);
            nameSortOrder = !nameSortOrder;
            updateSortIcons();
        }

        function sortByDate() {
            sortTable(2, dateSortOrder);
            dateSortOrder = !dateSortOrder;
            updateSortIcons();
        }

        function sortBySize() {
            sortTable(4, sizeSortOrder);
            sizeSortOrder = !sizeSortOrder;
            updateSortIcons();
        }

        function sortByType() {
            sortTable(3, typeSortOrder);
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

        function updatePathColor() {
            const pathColorElement = document.querySelector('.path-color');
            if (pathColorElement) {
                const isLightMode = document.body.classList.contains('light-mode');
                pathColorElement.style.color = isLightMode ? 'red' : 'yellow';
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

        let currentFilePath = '';

function openEditor(filePath) {
    const allowedExtensions = [
    'html', 'css', 'js', 'env', 'php', 'txt', 'json', 'xml', 'env', 'gitignore', 'md',
    'yml', 'yaml', 'ini', 'conf', 'log', 'htaccess', 'htpasswd', 'csv', 'tsv', 'sql',
    'c', 'cpp', 'h', 'java', 'py', 'rb', 'sh', 'bat', 'pl', 'go', 'rs', 'swift', 'ts',
    'phtml', 'shtml', 'xhtml', 'jsp', 'asp', 'aspx', 'jspx', 'cfm', 'cfml',
    'scss', 'less', 'sass', 'vue', 'jsx', 'tsx', 'dart', 'lua', 'r', 'm', 'erl', 'hs',
    'groovy', 'kt', 'kts', 'sql', 'ps1', 'psm1', 'vbs', 'vb', 'asm', 'makefile', 'dockerfile'
    ];

    const fileExtension = filePath.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(fileExtension)) {
        alert('This file type is not allowed to be edited.');
        return;
    }

    currentFilePath = filePath;

    const editorModal = document.getElementById('textEditorModal');

    fetch(`?action=read&file=${encodeURIComponent(filePath)}`)
        .then(response => response.text())
        .then(content => {
            document.getElementById('editorContent').value = content;
            document.getElementById('textEditorModal').style.display = 'block';

            editorModal.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(error => alert('Failed to open file: ' + error));
}

function saveFile() {
    const content = document.getElementById('editorContent').value;

    fetch(`?action=save&file=${encodeURIComponent(currentFilePath)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content })
    })
        .then(response => response.text())
        .then(result => alert(result))
        .catch(error => alert('Failed to save file: ' + error));
}

function renameFile() {
    const newName = prompt('Enter new file name (with Extension):');
    if (!newName) return;

    fetch(`?action=rename&file=${encodeURIComponent(currentFilePath)}&newName=${encodeURIComponent(newName)}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            closeEditor();
            location.reload();
        })
        .catch(error => alert('Failed to rename file: ' + error));
}

function replaceText() {
    const searchText = prompt('Enter text to search:');
    const replaceText = prompt('Enter replacement text:');
    if (!searchText || !replaceText) return;

    const editor = document.getElementById('editorContent');
    editor.value = editor.value.split(searchText).join(replaceText);
}

function viewFile() {
    if (!currentFilePath) {
        alert('No file selected to view.');
        return;
    }
    window.open(currentFilePath, '_blank');
}

function closeEditor() {
    document.getElementById('textEditorModal').style.display = 'none';
}

function deleteFile(filePath) {
    if (confirm('Are you sure you want to delete this file/folder?')) {
        fetch(`?action=delete&file=${encodeURIComponent(filePath)}`)
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload();
            })
            .catch(error => alert('Failed to delete file/folder: ' + error));
    }
}

function selectAllFiles(checked) {
    document.querySelectorAll("input[name='selected_items[]']").forEach(input => {
        input.checked = checked;
    });

    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = checked;
    }

    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll("input[name='selected_items[]']:checked").length;
    const counter = document.getElementById('zipSelectedCount');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const allCheckboxes = document.querySelectorAll("input[name='selected_items[]']");

    if (counter) {
        counter.textContent = selectedCount + ' item dipilih';
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedCount === allCheckboxes.length;
    }
}

function toggleExtensionField() {
    const filterType = document.getElementById('filterType');
    const extensionWrap = document.getElementById('extensionFilterWrap');
    const extensionInput = document.getElementById('filterExtensions');

    if (!filterType || !extensionWrap || !extensionInput) {
        return;
    }

    const isExtensionFilter = filterType.value === 'extension';
    extensionWrap.style.display = isExtensionFilter ? 'flex' : 'none';

    if (!isExtensionFilter) {
        extensionInput.value = '';
    }
}

function formatBytes(bytes) {
    const value = Number(bytes || 0);
    if (value >= 1073741824) return (value / 1073741824).toFixed(2) + ' GB';
    if (value >= 1048576) return (value / 1048576).toFixed(2) + ' MB';
    if (value >= 1024) return (value / 1024).toFixed(2) + ' KB';
    return value + ' bytes';
}

function showZipProgress(message) {
    const overlay = document.getElementById('zipProgressOverlay');
    const text = document.getElementById('zipProgressText');

    if (overlay) {
        overlay.style.display = 'flex';
    }
    if (text) {
        text.textContent = message || 'Membuat ZIP...';
    }
}

function hideZipProgress() {
    const overlay = document.getElementById('zipProgressOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function showDuplicateModal(content) {
    const modal = document.getElementById('duplicateModal');
    const modalContent = document.getElementById('duplicateModalContent');
    if (modalContent) {
        modalContent.innerHTML = content;
    }
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeDuplicateModal() {
    const modal = document.getElementById('duplicateModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

let contextMenuTarget = null;

function showContextMenu(event, filePath) {
    event.preventDefault();
    contextMenuTarget = filePath;
    
    const contextMenu = document.getElementById('contextMenu');
    contextMenu.classList.add('show');
    contextMenu.style.left = event.pageX + 'px';
    contextMenu.style.top = event.pageY + 'px';
}

function hideContextMenu() {
    const contextMenu = document.getElementById('contextMenu');
    contextMenu.classList.remove('show');
    contextMenuTarget = null;
}

function contextAction(action) {
    if (!contextMenuTarget) return;
    
    hideContextMenu();
    
    switch (action) {
        case 'open':
            window.location.href = '?dir=' + encodeURIComponent(contextMenuTarget);
            break;
        case 'rename':
            const newName = prompt('Enter new name:', contextMenuTarget.split('/').pop());
            if (newName) {
                fetch(`?action=rename&file=${encodeURIComponent(contextMenuTarget)}&newName=${encodeURIComponent(newName)}`)
                    .then(() => location.reload())
                    .catch(err => alert('Rename failed: ' + err));
            }
            break;
        case 'delete':
            if (confirm('Move to trash?')) {
                fetch(`?action=delete&file=${encodeURIComponent(contextMenuTarget)}`)
                    .then(() => location.reload())
                    .catch(err => alert('Delete failed: ' + err));
            }
            break;
        case 'copyPath':
            navigator.clipboard.writeText(contextMenuTarget).then(() => {
                alert('Path copied to clipboard!');
            }).catch(err => alert('Failed to copy: ' + err));
            break;
        case 'properties':
            // Simple properties dialog
            alert('Path: ' + contextMenuTarget);
            break;
    }
}

let draggedFilePath = null;

function startDrag(event, filePath) {
    draggedFilePath = filePath;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.dropEffect = 'move';
    event.dataTransfer.setData('text/plain', filePath);
    
    // Add visual feedback
    if (event.target.closest('tr')) {
        event.target.closest('tr').style.opacity = '0.5';
    }
}

function allowDrop(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = event.ctrlKey ? 'copy' : 'move';
    
    // Highlight the drop zone
    if (event.target.closest('tbody')) {
        event.target.closest('tbody').style.backgroundColor = 'rgba(88, 205, 124, 0.2)';
    }
}

function leaveDrop(event) {
    // Remove highlight
    if (event.target.closest('tbody')) {
        event.target.closest('tbody').style.backgroundColor = 'transparent';
    }
}

async function handleDrop(event, destinationPath) {
    event.preventDefault();
    event.stopPropagation();
    
    if (!draggedFilePath) return;
    
    // Remove highlight
    if (event.target.closest('tbody')) {
        event.target.closest('tbody').style.backgroundColor = 'transparent';
    }
    
    // Restore opacity
    const rows = document.querySelectorAll('#fileTable tbody tr');
    rows.forEach(row => row.style.opacity = '1');
    
    const mode = event.ctrlKey ? 'copy' : 'move';
    
    try {
        const response = await fetch(`?action=moveFile&source=${encodeURIComponent(draggedFilePath)}&destination=${encodeURIComponent(destinationPath)}&mode=${mode}`);
        const data = await response.json();
        
        if (data.ok) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to ' + mode + ' file'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
    
    draggedFilePath = null;
}

async function findDuplicates() {
    showZipProgress('Mencari file duplikat...');
    try {
        const currentDir = window.location.search ? new URLSearchParams(window.location.search).get('dir') : './';
        const response = await fetch(`?action=findDuplicates&dir=${encodeURIComponent(currentDir)}`);
        const data = await response.json();

        if (!data.ok) {
            throw new Error(data.message || 'Gagal mencari duplikat.');
        }

        if (!data.duplicates || data.duplicates.length === 0) {
            showDuplicateModal('<div>Tidak ada file duplikat yang ditemukan di folder ini.</div>');
            return;
        }

        const html = data.duplicates.map((group, index) => {
            const items = group.map(item => `<li>${item}</li>`).join('');
            return `<div class="duplicate-group"><strong>Group ${index + 1}</strong><small>${group.length} file dengan isi identik</small><ul>${items}</ul></div>`;
        }).join('');

        showDuplicateModal(html);
    } catch (error) {
        alert(error.message || 'Gagal mencari duplikat.');
    } finally {
        hideZipProgress();
    }
}

function restoreTrashItem(trashId) {
    fetch(`?action=restoreTrash&trashId=${encodeURIComponent(trashId)}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            location.reload();
        })
        .catch(error => alert('Failed to restore trash item: ' + error));
}

function purgeTrashItem(trashId) {
    if (!confirm('Delete permanently this trash item?')) {
        return;
    }

    fetch(`?action=purgeTrash&trashId=${encodeURIComponent(trashId)}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            location.reload();
        })
        .catch(error => alert('Failed to purge trash item: ' + error));
}

async function handleZipDownload(event) {
    event.preventDefault();

    const form = event.target;
    const selectedCount = document.querySelectorAll("input[name='selected_items[]']:checked").length;
    const filterType = document.getElementById('filterType');
    const extensionInput = document.getElementById('filterExtensions');

    if (selectedCount === 0) {
        alert('Pilih minimal satu folder/file.');
        return;
    }

    if (filterType && filterType.value === 'extension' && extensionInput && !extensionInput.value.trim()) {
        alert('Masukkan ekstensi file, misalnya php,js,css.');
        return;
    }

    showZipProgress('Membuat ZIP, tunggu sebentar...');

    try {
        const response = await fetch(window.location.href.split('#')[0], {
            method: 'POST',
            body: new FormData(form)
        });

        const contentType = response.headers.get('Content-Type') || '';

        if (!response.ok || contentType.includes('application/json')) {
            const data = await response.json();
            throw new Error(data.message || 'Gagal membuat ZIP.');
        }

        const blob = await response.blob();
        const downloadName = response.headers.get('X-Zip-Filename') || 'selected_items.zip';
        const itemCount = response.headers.get('X-Zip-Item-Count') || '0';
        const totalBytes = response.headers.get('X-Zip-Total-Bytes') || '0';

        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = downloadName;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(downloadUrl);

        alert('ZIP selesai: ' + itemCount + ' item, total ' + formatBytes(totalBytes) + '.');
    } catch (error) {
        alert(error.message || 'Gagal membuat ZIP.');
    } finally {
        hideZipProgress();
    }
}

document.addEventListener("DOMContentLoaded", function () {
            const zipForm = document.getElementById('downloadZipForm');
            const filterType = document.getElementById('filterType');

            if (zipForm) {
                zipForm.addEventListener('submit', handleZipDownload);
            }

            if (filterType) {
                filterType.addEventListener('change', toggleExtensionField);
                toggleExtensionField();
            }

            document.querySelectorAll("input[name='selected_items[]']").forEach(input => {
                input.addEventListener('change', updateSelectedCount);
            });

            updateSelectedCount();

            document.querySelectorAll(".bookmark-btn").forEach(button => {
        button.addEventListener("click", (event) => {
            event.stopPropagation(); // Mencegah event click pada elemen induk
            const folderPath = button.getAttribute("data-path");
            bookmarkFolder(folderPath);
        });
    });

    // Event listener untuk tombol Delete
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", (event) => {
            event.stopPropagation(); // Mencegah event click pada elemen induk
            const filePath = button.getAttribute("data-path");
            deleteFile(filePath);
        });
    });


            document.getElementById("loading").style.display = "none";
            document.querySelector(".container").style.display = "block";

            const rows = document.querySelectorAll("#fileTable tbody tr");
            rows.forEach(row => {
                const link = row.querySelector("a");
                if (link) {
                    row.style.cursor = "pointer";
                    row.addEventListener("click", () => {
                        window.location.href = link.href;
                    });
                }
                const checkbox = row.querySelector("input[type='checkbox']");
                if (checkbox) {
                    checkbox.addEventListener("click", (event) => {
                        event.stopPropagation();
                    });
                }
                const editButton = row.querySelector("button[onclick^='openEditor']");
                if (editButton) {
                    editButton.addEventListener("click", (event) => {
                        event.stopPropagation();
                    });
                }
                
                // Add context menu
                const fileNameCell = row.querySelector("td:nth-child(2)");
                const linkInCell = fileNameCell ? fileNameCell.querySelector("a") : null;
                if (linkInCell) {
                    const filePath = new URLSearchParams(linkInCell.href.split('?')[1]).get('dir') || linkInCell.textContent.trim();
                    row.addEventListener("contextmenu", (event) => {
                        showContextMenu(event, filePath);
                    });
                }
            });

            // Hide context menu on click elsewhere
            document.addEventListener("click", hideContextMenu);
        });
        
    </script>
</head>

<body>
    <div id="loading">
        <div class="loader"></div>
        <h1>Loading...</h1>
    </div>
    <div id="zipProgressOverlay" class="zip-progress-overlay">
        <div class="zip-progress-card">
            <div class="zip-spinner"></div>
            <div id="zipProgressText">Membuat ZIP...</div>
        </div>
    </div>
    <div class="container">
        <h2 style="margin-bottom: 20px; text-align: center;">
            <a href="index.php" style="text-decoration: none;">
                <span class="path-color">File Manager (htdocs)</span>
            </a>
        </h2>
        <header>
            <div class="info">
                <p id="datetime"></p>
            </div>
            <div class="button">
                <button onclick="toggleMode()" class="toogle" type="button">Toggle Light/Dark Mode</button>
                <button onclick="goBack()" class="toogle" type="button">Back</button>
                <a href="?logout=true" style="background-color: #dc3545; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Logout</a>
            </div>
        </header>

        <?php 
        $currentDirForBreadcrumb = isset($_GET['dir']) ? $_GET['dir'] : './';
        echo generateBreadcrumb($currentDirForBreadcrumb);
        ?>

        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="searchFiles()" placeholder="Search for files...">
            <div class="search-advanced">
                <input type="text" id="searchName" placeholder="Nama file/folder">
                <select id="searchType">
                    <option value="all">Semua tipe</option>
                    <option value="file">File</option>
                    <option value="folder">Folder</option>
                </select>
                <input type="text" id="searchExtension" placeholder="Ekstensi, mis. php">
                <input type="number" id="searchSizeMin" placeholder="Ukuran min (bytes)">
                <input type="number" id="searchSizeMax" placeholder="Ukuran max (bytes)">
                <input type="date" id="searchDateFrom">
                <input type="date" id="searchDateTo">
                <input type="text" id="searchContent" placeholder="Isi file mengandung...">
                <button type="button" onclick="applyAdvancedSearch()" style="background-color: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Filter</button>
                <button type="button" onclick="searchContentFiles()" style="background-color: #6f42c1; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Search Content</button>
                <button type="button" onclick="clearAdvancedSearch()" style="background-color: #444; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Reset</button>
            </div>
        </div>
        <form id="downloadZipForm" method="post" class="zip-toolbar">
            <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars(isset($_GET['dir']) ? $_GET['dir'] : './', ENT_QUOTES, 'UTF-8'); ?>">
            <input class="zip-field" type="text" name="zip_filename" id="zipFilename" value="selected_items_<?php echo date('Ymd_His'); ?>" placeholder="Nama file ZIP">
            <select class="zip-small-field" name="include_mode" id="includeMode">
                <option value="selected">Hanya item yang dipilih</option>
                <option value="recursive">Include subfolder</option>
            </select>
            <select class="zip-small-field" name="filter_type" id="filterType">
                <option value="all">Semua</option>
                <option value="files">Hanya file</option>
                <option value="folders">Hanya folder</option>
                <option value="extension">Ekstensi tertentu</option>
            </select>
            <span id="extensionFilterWrap" style="display:none; gap:10px; align-items:center;">
                <input class="zip-small-field" type="text" name="filter_extensions" id="filterExtensions" placeholder="php,js,css">
            </span>
            <button type="submit" name="download_selected" style="background-color: #007bff; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Download ZIP</button>
            <button type="button" onclick="selectAllFiles(true)" style="background-color: #444; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Select All</button>
            <button type="button" onclick="selectAllFiles(false)" style="background-color: #444; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Unselect All</button>
            <button type="button" onclick="findDuplicates()" style="background-color: #6f42c1; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Find Duplicates</button>
            <span id="zipSelectedCount" class="zip-status">0 item dipilih</span>
        </form>
        <br />
        <div id="dropZone" style="border: 2px dashed #007bff; padding: 20px; text-align: center; color: #007bff; margin-bottom: 20px;">
            Drag and drop files here to upload
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="uploadedFiles[]" multiple style="display: none;">
                <button type="button" id="browseButton" style="margin-top: 10px; background-color: #007bff; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Browse Files</button>
                <button type="submit" style="margin-top: 10px; background-color: #28a745; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Upload</button>
                <button type="button" id="cancelUploadButton" style="margin-top: 10px; background-color: #dc3545; color: #fff; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display:none;">Cancel Upload</button>
            </form>
            <div class="upload-progress-wrap" id="uploadProgressWrap">
                <div class="upload-progress-bar" id="uploadProgressBar"></div>
                <div class="upload-progress-text" id="uploadProgressText">0%</div>
            </div>
        </div>
        <div class="bookmarks">
            <h3>Bookmarks</h3>
            <ul id="bookmarkList"></ul>
        </div>
        <div class="trash-panel">
            <h3>Recycle Bin</h3>
            <div style="font-size: 12px; color: #a0a0a0; margin-bottom: 10px;">Deleted items are moved here first. Restore or purge them below.</div>
            <?php
            $trashManifest = loadTrashManifest();
            if (count($trashManifest) === 0):
            ?>
                <div style="color: #a0a0a0; font-size: 12px;">Recycle bin is empty.</div>
            <?php else: ?>
                <?php foreach (array_reverse($trashManifest, true) as $trashId => $trashItem): ?>
                    <div class="trash-item">
                        <strong><?php echo htmlspecialchars($trashItem['name'] ?? $trashId, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <small>Original: <?php echo htmlspecialchars($trashItem['original_path'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small>
                        <small>Trashed at: <?php echo htmlspecialchars($trashItem['trashed_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small>
                        <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" onclick="restoreTrashItem('<?php echo htmlspecialchars($trashId, ENT_QUOTES, 'UTF-8'); ?>')" style="background-color: #28a745; color: #fff; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer;">Restore</button>
                            <button type="button" onclick="purgeTrashItem('<?php echo htmlspecialchars($trashId, ENT_QUOTES, 'UTF-8'); ?>')" style="background-color: #dc3545; color: #fff; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer;">Delete Permanently</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <table class="file-table" id="fileTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCheckbox" onclick="selectAllFiles(this.checked)"></th>
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
                    static $cache = [];
                    if (isset($cache[$dir])) {
                        return $cache[$dir];
                    }
                
                    $totalSize = 0;
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
                        if ($file->isFile()) {
                            $totalSize += $file->getSize();
                        }
                    }
                
                    $cache[$dir] = $totalSize;
                    return $totalSize;
                }

                $currentDir = isset($_GET['dir']) ? $_GET['dir'] : './';

                // Periksa apakah $currentDir adalah direktori
                if (is_dir($currentDir)) {
                    $files = array_diff(scandir($currentDir), array('.', '..'));
                } elseif (is_file($currentDir)) {
                    // Jika $currentDir adalah file, buka file tersebut
                    header('Content-Type: ' . mime_content_type($currentDir));
                    header('Content-Disposition: inline; filename="' . basename($currentDir) . '"');
                    readfile($currentDir);
                    exit;
                } else {
                    // Jika $currentDir tidak valid, tampilkan pesan error
                    die("Invalid directory or file.");
                }
                
                $files = array_diff(scandir($currentDir), array('.', '..'));

                $filesPerPage = 15; // atur page disini mau muncul berapa
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $totalFiles = count($files);
                $totalPages = ceil($totalFiles / $filesPerPage);
                $startIndex = ($page - 1) * $filesPerPage;
                $files = array_slice($files, $startIndex, $filesPerPage);
                
                foreach ($files as $file) {
                    $filePath = $currentDir . '/' . $file;
                    $fileSize = is_dir($filePath) ? humanFileSize(getFolderSize($filePath)) : humanFileSize(filesize($filePath));
                    $fileDate = date("F d Y H:i:s.", filemtime($filePath));
                    $fileType = filetype($filePath);
                
                    echo "<tr>";
                    echo "<td><input type='checkbox' form='downloadZipForm' name='selected_items[]' value='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' onclick='event.stopPropagation()'></td>";
                    if (is_dir($filePath)) {
                        echo "<td class='folder-icon'><a href='?dir=" . urlencode($filePath) . "'>$file</a></td>";
                        echo "<td>$fileDate</td>";
                        echo "<td>Folder</td>";
                        echo "<td class='grey-text'>$fileSize</td>";
                        echo "<td><button class='bookmark-btn' data-path='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' style='background-color: #ffc107; color: #000; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;'>Bookmark</button></td>";
                        echo "<td><button class='delete-btn' data-path='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' style='background-color: #ff4d4d; color: #fff; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;'>Delete</button></td>";
                    } else {
                        echo "<td class='file-icon'><a href='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . "</a></td>";
                        echo "<td>$fileDate</td>";
                        echo "<td>$fileType</td>";
                        echo "<td>$fileSize</td>";
                        echo "<td><button class='bookmark-btn' data-path='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' style='background-color: #ffc107; color: #000; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;'>Bookmark</button></td>";
                        echo "<td><button class='delete-btn' data-path='" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "' style='background-color: #ff4d4d; color: #fff; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;'>Delete</button></td>";
                                    
                        if (isAllowedFile($filePath, $allowedExtensions)) {
                            echo "<td><button onclick=\"openEditor('" . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . "')\" style='background-color: #007bff; color: #fff; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;'>Edit</button></td>";
                        } else {
                            echo "<td></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
                <?php echo "<a href='activity.log' target='_blank' style='background-color: #007bff; color: #fff; padding: 5px 10px; border: none; border-radius: 4px; text-decoration: none;'>View Log</a>"; ?>
                <br />
                
                <div class="pagination" style="text-align: center; margin-top: 20px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?dir=<?php echo urlencode($currentDir); ?>&page=<?php echo $i; ?>" 
                           style="margin: 0 5px; text-decoration: none; color: <?php echo $i === $page ? '#fcd53f' : '#007bff'; ?>;">
                           <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </tbody>
        </table>
    </div>
    
    <div id="textEditorModal" style="display: none;">
    <div style="background-color: #252526; padding: 20px; border-radius: 8px; width: 80%; margin: 50px auto; color: #d4d4d4;">
        <h2>Text Editor</h2>
        <textarea id="editorContent" style="width: 100%; height: 300px; background-color: #1e1e1e; color: #d4d4d4; border: 1px solid #444; padding: 10px; border-radius: 4px; font-family: 'Courier New', Courier, monospace; resize: none; box-sizing: border-box;"></textarea>
        <div style="margin-top: 10px; display: flex; justify-content: space-between;">
            <button onclick="saveFile()" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Save</button>
            <button onclick="renameFile()" style="background-color: #fcd53f; color: #000; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Rename</button>
            <button onclick="replaceText()" style="background-color: #ff5722; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Replace</button>
            <button onclick="viewFile()" style="background-color: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">View</button>
            <button onclick="closeEditor()" style="background-color: #444; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Exit</button>
        </div>
    </div>
</div>

    <footer class="footer">
        <p>&copy; <?php echo htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars(gethostname(), ENT_QUOTES, 'UTF-8'); ?>. All rights reserved.</p>
        <a href="https://github.com/lukman754/apache-autoindex-theme" target="_blank" rel="noopener noreferrer">
            Created by <span class="github-icon"><i class="fab fa-github"></i></span> Lukman754 & <span
                class="github-icon"><i class="fab fa-github"></i></span> Xnuvers007
        </a>
    </footer>
    <div id="duplicateModal" class="modal-overlay">
        <div class="modal-card duplicate-panel">
            <div class="modal-header">
                <h3 style="margin:0;">Duplicate Finder</h3>
                <button type="button" class="modal-close" onclick="closeDuplicateModal()">Close</button>
            </div>
            <div id="duplicateModalContent">Click Find Duplicates to scan the current folder.</div>
        </div>
    </div>

    <div id="contextMenu" class="context-menu">
        <div class="context-item" onclick="contextAction('open')">Open / Download</div>
        <div class="context-item" onclick="contextAction('rename')">Rename</div>
        <div class="context-item" onclick="contextAction('delete')">Delete to Trash</div>
        <div class="context-item" onclick="contextAction('copyPath')">Copy Path</div>
        <div class="context-item" onclick="contextAction('properties')">Properties</div>
    </div>

    <script>
function bookmarkFolder(folderPath) {
    let bookmarks = JSON.parse(localStorage.getItem('bookmarks')) || [];
    if (bookmarks.includes(folderPath)) {
        // Jika folder sudah di-bookmark, hapus dari daftar
        bookmarks = bookmarks.filter(bookmark => bookmark !== folderPath);
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
        alert('Bookmark removed!');
    } else {
        // Jika folder belum di-bookmark, tambahkan ke daftar
        bookmarks.push(folderPath);
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
        alert('Folder/File bookmarked!');
    }
    updateBookmarkList(); // Perbarui daftar bookmark
}

function updateBookmarkList() {
    const bookmarks = JSON.parse(localStorage.getItem('bookmarks')) || [];
    const bookmarkList = document.getElementById('bookmarkList');
    bookmarkList.innerHTML = ''; // Kosongkan daftar bookmark

    bookmarks.forEach(folder => {
        const li = document.createElement('li');
        li.innerHTML = `
            <a href="?dir=${encodeURIComponent(folder)}">${folder}</a>
            <button onclick="bookmarkFolder('${folder}')" style="margin-left: 10px; background-color: #ff4d4d; color: #fff; border: none; border-radius: 4px; padding: 5px 10px; cursor: pointer;">Remove</button>
        `;
        bookmarkList.appendChild(li);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    updateBookmarkList();
    const bookmarks = JSON.parse(localStorage.getItem('bookmarks')) || [];
    const bookmarkList = document.getElementById('bookmarkList');
    bookmarks.forEach(folder => {
        const li = document.createElement('li');
        li.innerHTML = `<a href="?dir=${encodeURIComponent(folder)}">${folder}</a>`;
        bookmarkList.appendChild(li);
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const browseButton = document.getElementById('browseButton');
    const cancelUploadButton = document.getElementById('cancelUploadButton');
    const uploadProgressWrap = document.getElementById('uploadProgressWrap');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const uploadProgressText = document.getElementById('uploadProgressText');
    let uploadXhr = null;

    function setUploadProgress(percent, text) {
        if (uploadProgressWrap) {
            uploadProgressWrap.style.display = 'block';
        }
        if (uploadProgressBar) {
            uploadProgressBar.style.width = percent + '%';
        }
        if (uploadProgressText) {
            uploadProgressText.textContent = text || percent + '%';
        }
    }

    function resetUploadProgress() {
        if (uploadProgressWrap) {
            uploadProgressWrap.style.display = 'none';
        }
        if (uploadProgressBar) {
            uploadProgressBar.style.width = '0%';
        }
        if (uploadProgressText) {
            uploadProgressText.textContent = '0%';
        }
        if (cancelUploadButton) {
            cancelUploadButton.style.display = 'none';
        }
    }

    function uploadFiles() {
        if (!fileInput.files || fileInput.files.length === 0) {
            return;
        }

        const formData = new FormData(uploadForm);
        uploadXhr = new XMLHttpRequest();
        setUploadProgress(0, '0%');
        if (cancelUploadButton) {
            cancelUploadButton.style.display = 'inline-block';
        }

        uploadXhr.upload.onprogress = (event) => {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                setUploadProgress(percent, 'Uploading ' + percent + '%');
            }
        };

        uploadXhr.onload = () => {
            resetUploadProgress();
            location.reload();
        };

        uploadXhr.onerror = () => {
            resetUploadProgress();
            alert('Upload gagal.');
        };

        uploadXhr.onabort = () => {
            resetUploadProgress();
            alert('Upload dibatalkan.');
        };

        uploadXhr.open('POST', window.location.href, true);
        uploadXhr.send(formData);
    }

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.style.backgroundColor = '#e0e0e0';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.backgroundColor = '';
    });

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.style.backgroundColor = '';

        fileInput.files = event.dataTransfer.files;
        uploadFiles();
    });

    browseButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        uploadFiles();
    });

    uploadForm.addEventListener('submit', (event) => {
        event.preventDefault();
        uploadFiles();
    });

    if (cancelUploadButton) {
        cancelUploadButton.addEventListener('click', () => {
            if (uploadXhr) {
                uploadXhr.abort();
            }
        });
    }
});
</script>
</body>
</html>

<?php
ob_end_flush();
?>
