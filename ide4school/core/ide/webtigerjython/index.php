<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="page-topic" content="TigerJython, Schule, Programmieren">
    <meta name="description" content="WebTigerJython ist eine kostenlose, webbasierte Entwicklungsumgebung für Python,
     welche sich besonders für Anfänger eignet.">
    <meta name="keywords" lang=“de“ content="Python, programmieren, turtlegrafik, lehrmittel">
    <meta name="keywords" lang=“en-us“ content="Python, coding, turtle, IDE">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title><?php if(isset($file_name)) {echo $file_name;} else { echo 'Neue Datei';}?> - ide4school - Entwicklungsumgebung für Python</title>


    <!-- Materialize -->
    <!-- Compiled and minified CSS, custom css file: see materialize.scss
        To generate:
        $ sass web/core/ide/webtigerjython/stylesheets/materialize/materialize.scss web/core/ide/webtigerjython/stylesheets/materialize/materialize.css
        $ uglifycss web/core/ide/webtigerjython/stylesheets/materialize/materialize.css > web/core/ide/webtigerjython/stylesheets/materialize/materialize.min.css
        use div element with class="materialize" to leverage materialize
        TODO theme to Materialize?-->
    <link rel="stylesheet" href="core/ide/webtigerjython/stylesheets/materialize/materialize.min.css">
    <!-- Google Icons TODO local or just use flaticons-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="core/ide/webtigerjython/stylesheets/content.css">
    <link rel="stylesheet" href="core/ide/webtigerjython/stylesheets/menu.css">
    <link rel="stylesheet" href="core/ide/webtigerjython/stylesheets/gpanel.css">
    <link rel="stylesheet" href="core/ide/webtigerjython/stylesheets/debugger.css">

</head>
<body>
<!-- Lodash cloneDeep-->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/lodash/lodash.js"></script>
<!-- Tobias Kohn's Parser -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/tigerjython-parser/tpyparser-opt.js"></script>
<!-- FloodFill https://github.com/binarymax/floodfill.js/ -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/floodfill/floodfill.min.js"></script>
<!-- jQuery -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/jquery/jquery.min.js"></script>
<!-- SweetAlert -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/sweetalert2/sweetalert2.all.min.js" charset="utf-8"></script>
<!-- Materialize -->
<!-- Compiled and minified JavaScript -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/materialize/materialize.min.js" charset="utf-8"></script>
<!-- SQLite -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/sqljs-all/sql-wasm-debug.js"></script>
<!-- Skulpt -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/skulpt/skulpt.min.js"></script>
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/skulpt/skulpt-stdlib.js"></script>
<!-- Ace Editor -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/ace/ace.js" charset="utf-8"></script>
<!-- FileSaver -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/save/fileSaver.min.js" charset="utf-8"></script>
<!-- Split -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/split/Split.js" charset="utf-8"></script>

<!-- WebTigerJython -->
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/webtigerjython/functions-compiled.js" charset="utf-8"></script>
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/webtigerjython/debuggerView-compiled.js" charset="utf-8"></script>

<!-- Access statistics -->

<!-- Menu bar -->
<ul>
    <li id="logo-img">
        <div class="logo-container">
            <img class="img" src="./favicon.ico">
        </div>
    </li>
    <li id="logo-name"><?php if(isset($file_name)) {echo $file_name;} else { echo 'Neue Datei';}?></li>

    <li onclick="runProgram()" data-translate="start-button-hover" title="Start">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/start.png">
        </div>
    </li>
    <li onclick="stopProgram()" data-translate="stop-button-hover" title="Stop">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/stop.png">
        </div>
    </li>
    <li onclick="saveFile()" data-translate="save-button-hover" title="Speichern">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/save.png">
        </div>
    </li>
    <li onclick="debugProgram()" data-translate="debug-button-hover" title="Debug" id="dbgStart" style="display: none;">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/debug-button.svg">
        </div>
    </li>
    <li onclick="debugProgram(true)" data-translate="debug-step-mode-button-hover" title="Debug in Step Mode"
        id="dbgStepStart" style="display: none;">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/debug-step-mode-button.svg">
        </div>
    </li>

    <li onclick="dbg.dbgStepBack()" data-translate="backward-button-hover" title="StepBackward" id="dbgStepBack"
        style="display: none;">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/left-button.svg">
        </div>
    </li>

    <li onclick="dbg.dbgStepFwd()" data-translate="forward-button-hover" title="StepForward" id="dbgStepFwd"
        style="display: none;">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/right-button.svg">
        </div>
    </li>

    <li onclick="dbg.dbgContinue()" data-translate="continue-button-hover" title="Continue" id="dbgContinue"
        style="display: none;">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/continue-button.svg">
        </div>
    </li>

    <li class="dropdown">
        <div class="button-container">
            <img class="img" src="core/ide/webtigerjython/img/menu.png">
        </div>
        <div class="dropdown-content">
            <div id="language-button" class="dropdown-container" onclick="openLanguageSwitcher()">
                <img id="language-image" class="option-img" src="core/ide/webtigerjython/img/langDE.png">
                <p id="language-text" data-translate="options-current-language">Deutsch</p>
                <div id="language-switch" style="display: none;">
                    <div class="dropdown-container-lang" onclick="setDE()">
                        <img class="option-img" src="core/ide/webtigerjython/img/langDE.png">
                        <p>Deutsch</p>
                    </div>
                    <div class="dropdown-container-lang" onclick="setFR()">
                        <img class="option-img" src="core/ide/webtigerjython/img/langFR.png">
                        <p>Français</p>
                    </div>
                    <div class="dropdown-container-lang" onclick="setIT()">
                        <img class="option-img" src="core/ide/webtigerjython/img/langIT.png">
                        <p>Italiano</p>
                    </div>
                    <div class="dropdown-container-lang" onclick="setEN()">
                        <img class="option-img" src="core/ide/webtigerjython/img/langEN.png">
                        <p>English</p>
                    </div>
                    <div class="dropdown-container-lang" onclick="setRU()">
                        <img class="option-img" src="core/ide/webtigerjython/img/langRU.png">
                        <p>Русский</p>
                    </div>
                </div>
            </div>
            <div id="fullscreen-button" class="dropdown-container" onclick="toggleFullScreen()">
                <img id="fullscreen-image" class="option-img" src="core/ide/webtigerjython/img/expand.png">
                <p id="fullscreen-text" data-translate="options-fullscreen">Vollbild</p>
            </div>
            <div id="python-version-button" class="dropdown-container">
                <img id="python-version-image" class="option-img" src="core/ide/webtigerjython/img/python.png">
                <p id="python-version-text">Python 3</p>
                <label class="toggle"> <input id="toggleswitch" type="checkbox"> <span class="roundbutton"></span>
                </label>
            </div>

            <div id="save-image-button" class="dropdown-container">
                <img class="option-img" src="core/ide/webtigerjython/img/photo.png">
                <p data-translate="options-image-save">Bild herunterladen</p>
            </div>
            <div id="save-button" class="dropdown-container">
                <img class="option-img" src="core/ide/webtigerjython/img/save.png">
                <p data-translate="options-program-save">Programm herunterladen</p>
</div>
        </div>
    </li>
</ul>

<!-- Editor, Canvas and Output -->
<div id="content" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
    <div id="editor" class="split split-horizontal"><?php if(isset($file_content)) {echo $file_content;}?></div>
    <div id="right-content" class="split split-horizontal">
        <div id="mycanvas"></div> <!-- for turtle graphics -->
        <div id="modal">
            <a data-translate="modal-close-button" title="Schliessen" class="close">X</a>
            <div id="mycanvas-modal"></div>
        </div>
        <div id="output"></div>
    </div>
</div>

<script type="text/javascript" src="core/ide/webtigerjython/javascripts/webtigerjython/dictionary-compiled.js" charset="utf-8"></script>
<script type="text/javascript" src="core/ide/webtigerjython/javascripts/webtigerjython/initialization-compiled.js" charset="utf-8"></script>



<script type="text/javascript">

    // Detect if Internet Explorer is used and display an error message
    // (We have to detect this here because IE is not able to run it inside an imported script)
    if (navigator.userAgent.indexOf('MSIE ') !== -1 || navigator.userAgent.indexOf('Trident/index.html') !== -1) {
        // MSIE -> IE 10 or older
        // Trident -> IE 11

        // Retrieve error message in the browser language
        let language = navigator.language.toLowerCase();
        let errorMsg = dictionary.en["error-msg-ie"];
        if (language.indexOf("de") !== -1) {
            errorMsg = dictionary.de["error-msg-ie"];
        } else if (language.indexOf("fr") !== -1) {
            errorMsg = dictionary.fr["error-msg-ie"];
        } else if (language.indexOf("it") !== -1) {
            errorMsg = dictionary.it["error-msg-ie"];
        }
        document.getElementsByTagName('body')[0].innerHTML = errorMsg;
    }

</script>


<?php if(isset($file_name) && isset($file_content)) {echo '
    <form action="ide" method="POST" id="saveFileForm">
        <textarea name="file_content" id="file_content" style="visibility: hidden;"></textarea>
        <input name="file_path" type="text" hidden id="file_path" value="'; if(isset($file_name) && isset($file_content)) { echo $filepath; } echo '">
        <input name="saveFile" type="text" hidden id="saveFile">
    </form>
    <script>
        function saveFile(){
            document.getElementById("file_content").value = editor.session.getValue();
            document.getElementById("saveFileForm").submit();        
        }
    </script>'
    ;}
    elseif(!isset($file_name) && !isset($file_content)) {
        $timestamp = time();
        $date = date("d_m_Y - H_i_s", $timestamp);
        echo '
        <form action="ide" method="POST" id="saveFileForm">
            <textarea name="file_content" id="file_content" style="visibility: hidden;"></textarea>
            <input name="file_name" type="text" hidden id="file_name">
            <input name="file_path" type="text" hidden id="file_path" value="' . $db->getUserDir($_SESSION["user_id"]) . '">
            <input name="saveNewFile" type="text" hidden id="saveNewFile">
    </form>    
    <script>
        function saveFile(){
            current_timestamp = "'.$date.'"
            document.getElementById("file_content").value = editor.session.getValue();
            document.getElementById("file_name").value = prompt("Bitte den Namen der neuen Datei MIT Dateiendung eingeben. Diese wird anschließend in deinem Benutzerordner gespeichert.");
            if(document.getElementById("file_name").value == "") {
                document.getElementById("file_name").value = "Neue Datei vom "+current_timestamp+" Uhr.py";
            }
            document.getElementById("saveFileForm").submit();        
        }
    </script>';
    }
    ?>

</body>
</html>
