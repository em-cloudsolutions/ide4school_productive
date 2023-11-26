<?php
// projectpreview.php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

// SESSION CLASS MANAGMENT - END


// Prüfe, ob die ID und der Dateiname in der URL vorhanden sind
if (isset($_GET['ID']) && isset($_GET['file'])) {
  $id = $_GET['ID'];
  $fileName = $_GET['file'];
  
  // Datenbankabfrage, um den Ordnerpfad basierend auf der ID zu erhalten
  // Führe die Datenbankabfrage durch und erhalte den Ordnerpfad basierend auf der ID
  
  // Überprüfe den Zugriff auf den Ordner (z. B. durch .htaccess-Schutz)
  
  // Lade den Inhalt der HTML-Datei basierend auf dem Dateinamen
  $filePath = $ordnerPfad . '/' . $fileName;
  if (file_exists($filePath)) {
    $dateiInhalt = file_get_contents($filePath);
    
    // CSS- und JS-Dateien einbinden
    $cssFile = 'styles.css'; // Pfad zur CSS-Datei
    $jsFile = 'script.js'; // Pfad zur JS-Datei
    
    $cssContent = file_get_contents($cssFile);
    $jsContent = file_get_contents($jsFile);
    
    // Füge den CSS-Code in den HTML-Inhalt ein
    $dateiInhalt = str_replace('</head>', '<style>' . $cssContent . '</style></head>', $dateiInhalt);
    
    // Füge den JS-Code vor dem schließenden </body>-Tag ein
    $dateiInhalt = str_replace('</body>', '<script>' . $jsContent . '</script></body>', $dateiInhalt);
    
    // Bilder einbinden
    $imagePath = 'images/'; // Pfad zu den Bildern
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif']; // unterstützte Bildformate
    
    $dateiInhalt = preg_replace_callback('/<img[^>]+src="([^">]+)"/', function($matches) use ($imagePath, $imageExtensions) {
      $src = $matches[1];
      $extension = pathinfo($src, PATHINFO_EXTENSION);
      
      // Überprüfe, ob die Datei ein unterstütztes Bildformat hat
      if (in_array(strtolower($extension), $imageExtensions)) {
        $imageFile = $imagePath . basename($src);
        if (file_exists($imageFile)) {
          $data = file_get_contents($imageFile);
          $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
          
          return '<img src="' . $base64 . '"';
        }
      }
      
      return $matches[0];
    }, $dateiInhalt);
    
    echo $dateiInhalt;
  } else {
    echo "Datei nicht gefunden.";
  }
} else {
  echo "Ungültige URL-Parameter.";
}
?>

<html>
  <head>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      var links = document.querySelectorAll('a');
      var prefix = 'projectpreview.php?id=163&file=';
      
      for (var i = 0; i < links.length; i++) {
        var link = links[i];
        var href = link.getAttribute('href');
        
        // Modifiziere den Link-URL durch Hinzufügen des Präfixes
        link.setAttribute('href', prefix + href);
      }
    });
  </script>
  </head>
 </html>