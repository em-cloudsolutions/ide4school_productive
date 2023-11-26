<?php
// Aktuelle Version
$currentVersion = file_get_contents('version');

// GitHub API-URL
$githubApiUrl = 'https://api.github.com/repos/<username>/<repository>/releases/latest';

// Herunterladen des Releases
$release = json_decode(file_get_contents($githubApiUrl), true);
$latestVersion = $release['tag_name'];
$zipUrl = $release['zipball_url'];

if (version_compare($latestVersion, $currentVersion, '>')) {
    // ZIP-File herunterladen und entpacken
    $zipFile = file_get_contents($zipUrl);
    file_put_contents('update.zip', $zipFile);
    $zip = zip_open('update.zip');
    while ($zipEntry = zip_read($zip)) {
        $entryName = zip_entry_name($zipEntry);
        $entrySize = zip_entry_filesize($zipEntry);
        $entryContent = zip_entry_read($zipEntry, $entrySize);
        // Datenbank-Updates
        if (strpos($entryName, 'sql_data.sql') !== false) {
            $sql = $entryContent;
            // Führe SQL-Statements aus
            // Hier den Code für die Ausführung der SQL-Statements einfügen
            include('core/internal/database_config.php');
            $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if ($db->connect_errno) {
                die('Failed to connect to MySQL: ' . $db->connect_error);
            }
            if ($db->multi_query($sql)) {
                do {
                    // Leere den Ergebnissatz
                    if ($result = $db->store_result()) {
                        $result->free();
                    }
                    // Prüfe auf weitere Ergebnissätze
                    if ($db->more_results()) {
                        // Wenn ja, rufe den nächste Ergebnissatz ab
                        $db->next_result();
                    }
                } while ($db->more_results());
            }
            if ($db->errno) {
                die('Failed to execute SQL statements: ' . $db->error);
            }
            $db->close();
        } else {
            // Kopiere Dateien außerhalb des "Files/"-Verzeichnisses
            $localFile = $entryName;
            if (strpos($entryName, 'files/') !== 0) {
                if (!file_exists($localFile)) {
                    // Neue Datei hinzufügen
                    file_put_contents($localFile, $entryContent);
                } else {
                    $localFileContent = file_get_contents($localFile);
                    if ($entryContent !== $localFileContent) {
                        // Aktualisierte Datei kopieren
                        file_put_contents($localFile, $entryContent);
                    }
                }
            }
        }
    }

    // Lösche nicht mehr enthaltene Dateien
    $localFiles = glob('./*');
    $zipFiles = array_map(function($entry) {
        return './' . basename($entry);
    }, zip_entry_names($zip));
    $filesToDelete = array_diff($localFiles, $zipFiles);
    foreach ($filesToDelete as $fileToDelete) {
        if (is_file($fileToDelete) && strpos($fileToDelete, 'files/') !== 0) {
            unlink($fileToDelete);
        } elseif (is_dir($fileToDelete) && strpos($fileToDelete, 'files/') !== 0) {
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
    zip_close($zip);
    unlink('update.zip');
}
?>
    
