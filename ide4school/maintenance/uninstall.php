<?php
// ZIP-Archiv erstellen
$zipFile = 'ide4school-Backup-' . date("YmdHis") . '.zip';
$zip = new ZipArchive;
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $dir = 'files/';
    $it = new RecursiveDirectoryIterator($dir);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($dir));
        if ($file->isDir()){
            $zip->addEmptyDir($relativePath);
        } else {
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();
}

// ZIP-Archiv herunterladen
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="ide4school-Backup-' . date("YmdHis") . '.zip"');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);

// Datenbank löschen
// Führe SQL-Statements aus, um die Datenbank zu leeren oder zu löschen
include('core/internal/database_config.php');
$dbHost = DB_HOST; // Datenbank-Host
$dbUser = DB_USERNAME; // Datenbank-Benutzername
$dbPass = DB_PASSWORD; // Datenbank-Passwort
$dbName = DB_NAME; // Datenbank-Name

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Tabellen löschen
$tables = array();
$result = mysqli_query($conn, 'SHOW TABLES');
while($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

foreach($tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS $table");
}


// Alle Dateien und Ordner löschen
$filesToDelete = glob('./*');
foreach ($filesToDelete as $fileToDelete) {
    if (is_file($fileToDelete)) {
        unlink($fileToDelete);
    } elseif (is_dir($fileToDelete)) {
        // Falls Verzeichnis, rekursiv löschen
        $it = new RecursiveDirectoryIterator($fileToDelete, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                     RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($fileToDelete);
    }
}

// Uninstaller-Skript löschen
unlink(__FILE__);
?>
