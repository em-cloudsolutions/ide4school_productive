
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title><?php if(isset($file_name)) {echo $file_name;} else { echo 'Neue Datei';}?> - ide4school - Entwicklungsumgebung für HTML</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/w3.css">
	<link rel="stylesheet" href="css/codemirror.css">
	<link rel="stylesheet" href="css/tit-fontello.css">
	
	<script src="js/codemirror/codemirror.js"></script>
	<script src="js/codemirror/addon/edit/closetag.js"></script>
	<script src="js/codemirror/addon/edit/closebrackets.js"></script>
	<script src="js/codemirror/addon/fold/xml-fold.js"></script>
	<script src="js/codemirror/mode/xml/xml.js"></script>
	<script src="js/codemirror/mode/javascript/javascript.js"></script>
	<script src="js/codemirror/mode/css/css.js"></script>
	<script src="js/codemirror/mode/htmlmixed/htmlmixed.js"></script>

	<script>

		function $Id(id) {return document.getElementById(id)};
		function $Tag(tag) {return document.getElementsByTagName(tag)};

		if (window.addEventListener) {
			window.addEventListener("resize", browserResize);
		} else if (window.attachEvent) {
			window.attachEvent("onresize", browserResize);
		}

		//window.onbeforeunload = function(e) { if (e) {e.returnValue = "You have unsaved changes."}; return "Are you sure?"; }

		function browserResize() {
			if (window.screen.availWidth <= 768) {
				restack(window.innerHeight > window.innerWidth);
			}
			showFrameSize();    
		}

	</script>

<style>
* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
body {
  color:#000000;
  margin:0px;
  font-size:100%;
}
.trytopnav {
  height:40px;
  overflow:hidden;
  min-width:380px;
  position:absolute;
  width:100%;
  top:99px;
top:130px;
  background-color:#E7E9EB;
}
.topnav-icons {
  margin-right:8px;
}
.trytopnav a {
  color:#999999;
}
.w3-bar .w3-bar-item:hover {
  color:#757575 !important;
}
a.w3schoolslink {
  padding:0 !important;
  display:inline !important;
}
a.w3schoolslink:hover,a.w3schoolslink:active {
  text-decoration:underline !important;
  background-color:transparent !important;
}
#dragbar{
  position:absolute;
  cursor: col-resize;
  z-index:3;
  padding:5px;
}
#shield {
  display:none;
  top:0;
  left:0;
  width:100%;
  position:absolute;
  height:100%;
  z-index:4;
}
#framesize {
  font-family: 'Montserrat', 'Source Sans Pro', sans-serif;
  font-size: 14px;
}
#container {
  background-color:#E7E9EB;
  width:100%;
  overflow:auto;
  position:absolute;
  top:144px;
top:175px;
  bottom:0;
  height:auto;
}
#textareacontainer, #iframecontainer {
  float:left;
  height:100%;
  width:50%;
}
#textarea, #iframe {
  height:100%;
  width:100%;
  padding-bottom:10px;
  padding-top:1px;
}
#textarea {
  padding-left:10px;
  padding-right:5px;  
}
#iframe {
  padding-left:5px;
  padding-right:10px;  
}
#textareawrapper {
  width:100%;
  height:100%;
  background-color: #ffffff;
  position:relative;
  box-shadow:0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}
#iframewrapper {
  width:100%;
  height:100%;
  -webkit-overflow-scrolling: touch;
  background-color: #ffffff;
  box-shadow:0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}
#textareaCode {
  background-color: #ffffff;
  font-family: consolas,Menlo,"courier new",monospace;
  font-size:15px;
  height:100%;
  width:100%;
  padding:8px;
  resize: none;
  border:none;
  line-height:normal;    
}
.CodeMirror.cm-s-default {
  line-height:normal;
  padding: 4px;
  height:100%;
  width:100%;
}
#iframeResult, #iframeSource {
  background-color: #ffffff;
  height:100%;
  width:100%;  
}
#stackV {background-color:#999999;}
#stackV:hover {background-color:#BBBBBB !important;}
#stackV.horizontal {background-color:transparent;}
#stackV.horizontal:hover {background-color:#BBBBBB !important;}
#stackH.horizontal {background-color:#999999;}
#stackH.horizontal:hover {background-color:#999999 !important;}
#textareacontainer.horizontal,#iframecontainer.horizontal{
  height:50%;
  float:none;
  width:100%;
}
#textarea.horizontal{
  height:100%;
  padding-left:10px;
  padding-right:10px;
}
#iframe.horizontal{
  height:100%;
  padding-right:10px;
  padding-bottom:10px;
  padding-left:10px;  
}
#container.horizontal{
  min-height:200px;
  margin-left:0;
}
#tryitLeaderboard {
  overflow:hidden;
  text-align:center;
  margin-top:5px;
  height:90px;
}
.w3-dropdown-content {width:350px}

#breadcrumb ul {
  font-family:'Montserrat', 'Source Sans Pro', sans-serif;
  list-style: none;
  display: inline-table;
  padding-inline-start: 1px;
  font-size: 12px;
  margin-block-start: 6px;
  margin-block-end: 6px;	
}
#breadcrumb li {
  display: inline;
}
#breadcrumb a {
  float: left;
  background: #E7E9EB;
  padding: 3px 10px 3px 20px;
  position: relative;
  margin: 0 5px 0 0; 
  text-decoration: none;
  color: #555;
}
#breadcrumb a:after {
  content: "";  
  border-top: 12px solid transparent;
  border-bottom: 12px solid transparent;
  border-left: 12px solid #E7E9EB;
  position: absolute; 
  right: -12px;
  top: 0;
  z-index: 1;
}
#breadcrumb a:before {
  content: "";  
  border-top: 12px solid transparent;
  border-bottom: 12px solid transparent;
  border-left: 12px solid #fff;
  position: absolute; 
  left: 0; 
  top: 0;
}
#breadcrumb ul li:first-child a:before {
  display: none; 
}
#breadcrumb ul:last-child li{
  padding-right: 5px;
}
#breadcrumb ul li a:hover {
  background: #04AA6D;
  color:#fff;
}
#breadcrumb ul li a:hover:after {
  border-left-color: #04AA6D;
  color:#fff;
}
#breadcrumb li:last-child {
  display: inline-block!important;
  margin-top: 3px!important;
}
#runbtn {
  background-color:#04AA6D;
  color:white;
  font-family: 'Source Sans Pro', sans-serif;
  font-size:18px;
  padding:6px 25px;
  margin-top:4px;
  border-radius:5px;
  word-spacing:10px;
}
#runbtn:hover {
  background-color: #059862 !important;
  color:white!important;
}
#getwebsitebtn {
  background-color:#04AA6D;
  font-family: 'Source Sans Pro', sans-serif;  
  color: white;
  font-size: 18px;
  padding:6px 15px;
  margin-top:4px;
  margin-right: 10px;
  display: block;
  float: right;
  border-radius: 5px;
}
#getwebsitebtn:hover {
  background-color: #059862 !important;
  color:white!important;
}

#tryhome {
  display:none;
}

@media screen and (max-width: 727px) {
  .trytopnav {top:70px;}
  #container {top:116px;}
  #breadcrumb {display:none;}
  #tryhome  {display:block;}
}
@media screen and (max-width: 467px) {
  .trytopnav {top:60px;}
  #container {top:106px;}
  .w3-dropdown-content {width:100%}
}
@media only screen and (max-device-width: 768px) {
  #iframewrapper {overflow: auto;}
  #container     {min-width:310px;}
  .stack         {display:none;}
  #tryhome       {display:block;}
  .trytopnav     {min-width:310px;}  
}
#iframewrapper {
	
}

.trytopnav {
  height:48px!important;
}
.fa {
  padding: 10px 10px!important;
}
a.topnav-icons, a.topnav-icons.fa-home, a.topnav-icons.fa-menu {
    font-size: 28px!important;
}




/*OLD
.darktheme #breadcrumb li {
  color:#fff;
}
.darktheme #breadcrumb a {
  background:#616161;
  color: #ddd;
}	
.darktheme #breadcrumb a:after {
  border-left: 12px solid #616161;
}
.darktheme #breadcrumb a:before {
  border-left: 12px solid rgb(40, 44, 52);
}
.darktheme .currentcrumb {
  color:#ddd;
}

body.darktheme {
  background-color:rgb(40, 44, 52);
}
body.darktheme #tryitLeaderboard{
  background-color:rgb(40, 44, 52);
}
body.darktheme .trytopnav{
  background-color:#616161;
  color:#dddddd;
}
body.darktheme #container {
  background-color:#616161;
}
body.darktheme .trytopnav a {
  color:#dddddd;
}
body.darktheme ::-webkit-scrollbar {width:12px;height:3px;}
body.darktheme ::-webkit-scrollbar-track-piece {background-color:#000;}
body.darktheme ::-webkit-scrollbar-thumb {height:50px;background-color: #616161;}
body.darktheme ::-webkit-scrollbar-thumb:hover {background-color: #aaaaaa;}
*/

.darktheme #breadcrumb li {
  color:#fff;
}
.darktheme #breadcrumb a {
  background:#616161;
  background-color:#38444d;  
  color: #ddd;
}	
.darktheme #breadcrumb a:after {
  border-left: 12px solid #616161;
  border-left: 12px solid #38444d; 
}
.darktheme #breadcrumb a:before {
  border-left: 12px solid rgb(40, 44, 52);
  border-left: 12px solid #1d2a35;  
}
.darktheme .currentcrumb {
  color:#ddd;
}

body.darktheme {
  background-color:rgb(40, 44, 52);
  background-color:#1d2a35;
}
body.darktheme #tryitLeaderboard{
  background-color:rgb(40, 44, 52);
  background-color:#1d2a35;  
}
body.darktheme .trytopnav{
  background-color:#616161;
  background-color:#38444d;
  color:#dddddd;
}
body.darktheme #container {
  background-color:#616161;
  background-color:#38444d;
}
body.darktheme .trytopnav a {
  color:#dddddd;
}
body.darktheme ::-webkit-scrollbar {width:12px;height:3px;}
body.darktheme ::-webkit-scrollbar-track-piece {background-color:#000;}
body.darktheme ::-webkit-scrollbar-thumb {height:50px;background-color: #616161; background-color:#38444d;}
body.darktheme ::-webkit-scrollbar-thumb:hover {background-color: #aaaaaa;background-color: #4b5b68}



</style>

	<style>
		* {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		body {
			background-color: #f1f1f1;
			color: #000000;
			margin: 0px;
		}

		ul {
			list-style-type: none;
			margin: 0;
			padding: 0px 10px;
			overflow: hidden;
		}
		li {
			float: left;
			margin: 2px;
		}
		li a {
			display: inline-block;
		}
		li.dropdown {
			display: inline-block;
		}

		.dropdown-content {
			display: none;
			position: absolute;
			top: 44px;
			min-width: 160px;
			width: 40%;
			box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.5);
			background-color: hsl(0, 0%, 95%);
			z-index: 10;
		}
		.dropdown-content p {
			color: black;
			padding: 2px 16px;
			font-size: 12px;
		}
		
		.switch {
			position: relative;
			display: inline-block;
			margin: 0 4px;
		}
		.switch input {
			display: none;
		}
		.slider {
			box-sizing: border-box;
			position: static;
			width: 50px;
			height: 40px;
			margin: 0px 4px;
			background: white;
			border: solid lightgrey;
			border-radius: 20%;
			border-width: 4px 20px 4px 4px;
			display: inline-block;
			vertical-align: -14px;
			cursor: pointer;
			transition: border 0.3s;
		} 
		/*.slider:hover { 		} */
		input:checked + .slider { 
			border-color: hsl(122, 39.4%, 49.2%);/*w3-green*/
			border-width: 4px 4px 4px 20px;
		} 

		.w3-bar .w3-bar-item:hover {
			color: #757575 !important;
		}
		.w3-bar .w3-bar-item {
			margin: 2px;
			height: calc(100% - (2px + 2px));
			padding: 2px 12px;
		}

		.dropdown {
			display: inline;
			z-index: 2;
		}

		.CodeMirror.cm-s-default {
			line-height: normal;
			padding: 4px;
			height: 100%;
			width: 100%;
		}

		#container {
			margin: 15px 10px;
			position: absolute;
			height: calc(100% - 10px - 44px);
			width: calc(100% - 20px);
			top: 44px; bottom: 0px; left: 0; right: 0;
		}
		#textareacontainer, #dragbar, #iframecontainer {
			float: left;
			height: 100%;
			width: calc(50% - 6px);
			box-shadow: 0px 3px 5px -1px rgb(182, 181, 181);
		}
		#dragbar {
			width: 12px;
			box-shadow: none;
			cursor: col-resize;
		}
		#filename {
			border: 1px solid hsl(130, 100%, 30%);
			background: white;
			width: 300px;
			padding: 7px 4px;
			text-align: left;
		}
		#shield {
			display: none;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 4;
		}
		#framesize span {
			font-family: Consolas, monospace;
		}
		#iframeResult {
			background-color: #ffffff;
			border: none;
			height: 100%;
			width: 100%;  
		}

		@media screen and (max-width: 1260px) {
			#container {
				top: 240px;
				height: calc(100% - 10px - 88px);
			}
		}
		@media screen and (max-width: 450px) {
			#container {
				top: 240px;
				height: calc(100% - 10px - 160px);
			}
		}
		@media only screen and (max-device-width: 768px) {
			#container     {min-width: 320px;}
		}

		[class*="tit-icon-"] {
			/*font: normal normal normal 18px/1 tit-fontello;*/
			text-rendering: auto;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			padding: 8px 10px;
		}

		@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
		}
		/* this MUST be the last rule*/
		.show {display: block;}
		
		
	</style>
	<!--[if lt IE 8]>
	<style>
		#textareacontainer, #iframecontainer {width: 48%;}
		#container {height: 500px;}
		#textarea, #iframe {width: 90%;height: 450px;}
		#textareaCode, #iframeResult {height: 450px;}
	</style>
	<![endif]-->
</head>
<body>

<div style="margin-bottom: 10px;">
<ul class="w3-light-grey">
	

	<li><button id="runbtn" class="w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="saveFile()" title="Datei speichern">&laquo; Speichern</button></li>
	
	<li style="float: right"><span class="w3-right w3-bar-item" style="padding: 9px 0;display: block;" id="framesize"></span></li>
  </ul>
</div>
<div id="shield"></div>

<div id="container">

	<div id="textareacontainer">
		<textarea autocomplete="on" id="textareaCode" wrap="logical" spellcheck="false" style="width: 940px; height: 873px;">
		<?php
		if(isset($file_content)) {
			$folder_path == trim($filepath, $file_name);
		}
		else {
			$folder_path = $db->getUserDir($_SESSION["user_id"])."/";
		}
		?>
<?php if(isset($file_content)) {echo $file_content;} else { echo `<!DOCTYPE html>
<html>
    <head>
        <!-- Pfad für Bilder, Videos und andere eingebundene Dateien - NICHT ÄNDERN ODER LÖSCHEN! -->
        <base href="' . $folder_path . '/">


        <!-- Weitere Angaben (ab hier einsetzbar) -->

        <meta charset="utf-8">
        <title>DEIN SEITENTITEL HIER EINSETZEN</title>

        <!-- Der wird NUR im Browsertitel angezeigt und dient NICHT als Überschrift! -->
        
    </head>
    <body>
    <!-- Hier kommt der Inhalt der Seite hin -->       























  <!-- Ab hier kommt kein Inhalt mehr hin -->
    </body>
</html>`; }?>
</textarea>
	</div>
	<div id="dragbar">  </div>
	<div id="iframecontainer">
		<iframe id="iframeResult">
		</iframe>
	</div>
</div>

<script>
	var framecontentedit = true;
	submitTryit()

	function submitTryit(n) {
		if (window.editor) {
			window.editor.save();
		}
		var text = $Id("textareaCode").value;
		if (window.editor) {
			var text = window.editor.getDoc().getValue("\n");
		}
		text = text.replace(/\n\n\n/g,"\n\n"); // normalize newlines (??!!)
		var ifr = $Id("iframeResult");

		var ifrw = (ifr.contentWindow) ? ifr.contentWindow : (ifr.contentDocument.document) ? ifr.contentDocument.document : ifr.contentDocument;
		ifrw.document.open();
		ifrw.document.write(text);  
		ifrw.document.close();
		//23.02.2016: contentEditable is set to true, to fix text-selection (bug) in firefox.
		//(and back to false to prevent the content from being editable)
		//(To reproduce the error: Select text in the result window with, and without, the contentEditable statements below.)  
		if (ifrw.document.body && !ifrw.document.body.isContentEditable) {
			ifrw.document.body.contentEditable = true;
			ifrw.document.body.contentEditable = false;
		}
		ifrw.document.body.contentEditable = framecontentedit;

	}
	function reEdited() {
		var text = frameHTML();
		$Id("textareaCode").value = text;
		window.editor.getDoc().setValue(text);
	}

	function showFrameSize() {
		$Id("framesize").innerHTML = "Angezeigte Größe: <span>" + $Id("iframecontainer")["clientWidth"] + " x " + $Id("iframecontainer")["clientHeight"] + "</span>";
	}

	var layout = "horizontal";
	var leftwidthperc = 50 ; var leftheightperc = 50 ;

	if ((window.screen.availWidth <= 768 && window.innerHeight > window.innerWidth) ) {restack();}

	function restack() {
		var l = $Id("textareacontainer");
		var c = $Id("dragbar");
		var r = $Id("iframecontainer");
		if (layout == "vertical") {
			l.style["height"] = c.style["height"] = r.style["height"] = "100%";
			l.style["width"] = "calc(" + leftwidthperc + "% - 6px)";
			c.style["width"] = "12px";
			c.style["cursor"] = "col-resize";
			r.style["width"] = "calc(" + (100 - leftwidthperc) + "% - 6px)";
			layout = "horizontal"
		} else {
			l.style["width"] = c.style["width"] = r.style["width"] = "100%";
			l.style["height"] = "calc(" + leftheightperc + "% - 6px)";
			c.style["height"] = "12px";
			c.style["cursor"] = "row-resize";
			r.style["height"] = "calc(" + (100 - leftheightperc) + "% - 6px)";
			layout = "vertical"		
		}
		showFrameSize();
	}

	dragBalance($Id(("dragbar")));

	function dragBalance(balancer) {
		if (window.addEventListener) {
			balancer.addEventListener("mousedown", function(e) {dragstart(e);});
			balancer.addEventListener("touchstart", function(e) {dragstart(e);});
			window.addEventListener("mousemove", function(e) {dragmove(e);});
			window.addEventListener("touchmove", function(e) {dragmove(e);});
			window.addEventListener("mouseup", dragend);
			window.addEventListener("touchend", dragend);
		}

		var dragging = false;
		var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
		function dragstart(e) {
			e.preventDefault();
			e = e || window.event;
			// get the mouse cursor position at startup:
			pos3 = e.clientX;
			pos4 = e.clientY;
			dragging = true;
		}
		function dragmove(e) {
			var perc;
			if (dragging) {
				// show overlay to avoid interfering of mouse moving with textarea
				$Id("shield").style.display = "block";        
				e = e || window.event;
				// calculate the new cursor position:
				pos1 = pos3 - e.clientX;
				pos2 = pos4 - e.clientY;
				pos3 = e.clientX;
				pos4 = e.clientY;
				// set the element's new size:
				if (layout == "vertical") {
					var pos = pos2;
					var axe1 = "clientHeight";
					var axe2 = "height";
					perc = (balancer.previousElementSibling[axe1] + (balancer[axe1] / 2) - pos) * 100 / balancer.parentElement[axe1];
					leftheightperc = perc;
				} else {
					var pos = pos1;
					var axe1 = "clientWidth";
					var axe2 = "width";
					perc = (balancer.previousElementSibling[axe1] + (balancer[axe1] / 2) - pos) * 100 / balancer.parentElement[axe1];
					leftwidthperc = perc;
				}
				if (perc > 5 && perc < 95) {
					balancer.previousElementSibling.style[axe2] = "calc(" + (perc) + "% - " + (balancer[axe1] / 2) + "px)";
					balancer.nextElementSibling.style[axe2] = "calc(" + (100 - perc) + "% - " + (balancer[axe1] / 2) + "px)";
				}
				showFrameSize();
			}
		}
		function dragend() {
			$Id("shield").style.display = "none";
			dragging = false;
			if (window.editor) {
				window.editor.refresh();
			}
		}
	}

	function keypressed(e) {
		if (e.key != "ArrowLeft" && e.key != "ArrowRight" && e.key != "ArrowUp" && e.key != "ArrowDown") {submitTryit(1)};
	}
	function keypressedinframe(e) {
		if (e.key != "ArrowLeft" && e.key != "ArrowRight" && e.key != "ArrowUp" && e.key != "ArrowDown") {reEdited()};
		setTimeout(reEdited,100);
	}
	if (window.addEventListener) {
		window.addEventListener("load", showFrameSize);
		$Id("textareacontainer").addEventListener("keyup", function(e) {keypressed(e);});
	}
	frameWindow().addEventListener("keyup", keypressedinframe);
	/*
	function setFocusIframe() {frameWindow().focus();}
	$Id("iframeResult").contentWindow.addEventListener("mousedown", function(e) {setTimeout(setFocusIframe, 100);return false});
	*/
	function colorcoding() {  
		window.editor = CodeMirror.fromTextArea($Id("textareaCode"), {
			mode: "text/html",
			htmlMode: true,
			lineWrapping: false,
			smartIndent: true,
			indentUnit: 4,
			tabSize: 4,
			indentWithTabs: true,
			addModeClass: true,
			autoCloseTags: true,
			autoCloseBrackets: true
		});
		//window.editor.on("change", function () {window.editor.save();});
		//window.editor.on("change", function () {submitTryit(1)}); better avoid this due to "conflict" with contentEditable
	}
	colorcoding();

	function frameWindow(){
		var ifr = $Id("iframeResult");
		var ifrw = (ifr.contentWindow) ? ifr.contentWindow : (ifr.contentDocument.document) ? ifr.contentDocument.document : ifr.contentDocument;
		return ifrw;
	}
	function frameHTML() {
		var ifrw = frameWindow();
		ifrw.document.body.removeAttribute("contentEditable");// = false;
		var text = "<!DOCTYPE html>\n<html>\n" + ifrw.document.documentElement.innerHTML.replace(/^\n+|\n+$/g,'').trim() + "\n</html>";
		text = text.replace(/\n\n\n/g,"\n\n"); // normalize newlines (??!!)
		ifrw.document.body.contentEditable = framecontentedit;
		return text;
	}
	function loadFile() {
		var dir = "";//location.href.slice(0,location.href.lastIndexOf("/") + 1);
		var name = $Id("filename").value;
		//console.log(dir + name);
		frameWindow().location.href = dir + name;
		setTimeout(reEdited,500);
		setTimeout(submitTryit,1000);
	}
	function getName() {
		var name = $Id("filename").value;
		return name = name.slice(name.lastIndexOf("/") + 1);		
	}
	function downloadFile() {
		var text = frameHTML();
		//text = window.editor.getDoc().getValue("\n");
		var blob = new Blob([text], {type: "text/html;charset=utf-8"});
		saveAs(blob, getName());
	}
	function loadFromLocalStorage() {
		//Load saved Content
		var text = localStorage.getItem(getName());
		if (text != null) {
			$Id("textareaCode").value = text;
			window.editor.getDoc().setValue(text);
			submitTryit();
		}
	}
	function saveToLocalStorage() {
		if (typeof(Storage) !== "undefined") {
			var sHTML = frameHTML(); //Get content
			localStorage.setItem(getName(), sHTML);
			alert('Saved Successfully');
		} else {
			alert("No localStorage available")
		}
	}
	function viewSource() {
		var source = frameHTML();
		//now we need to escape the html special chars, javascript has escape
		//but this does not do what we want
		source = source.replace(/</g, "&lt;");
		//now we add <pre> tags to preserve whitespace
		source = "<pre>" + source + "</pre>";
		var sourceWindow = window.open('Nice Title','Source of page','');
		sourceWindow.document.write(source);
		sourceWindow.document.close(); //close the document for writing, not the window
	}
	function frameEditable() {
		$Id("checkedit").value = ~ $Id("checkedit").value;
		if ($Id("checkedit").value == 0) {
			framecontentedit = true;
			$Id("switchflag").innerHTML = "ON";
		} else {
			framecontentedit = false;
			$Id("switchflag").innerHTML = "OFF";
		}
		submitTryit();
		reEdited();
	}

</script>
<script src="js/FileSaver.js"></script>
<script>
	/* alert before leaving page
	window.addEventListener("beforeunload", function (e) {
		var confirmationMessage = 'It looks like you have been editing something. '
								+ 'If you leave before saving, your changes will be lost.';

		(e || window.event).returnValue = confirmationMessage; //Gecko + IE
		return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
	});
	*/
</script>

<?php if(isset($file_name) && isset($file_content)) {echo '
    <form action="ide" method="POST" id="saveFileForm">
        <textarea name="file_content" id="file_content" style="visibility: hidden;"></textarea>
        <input name="file_path" type="text" hidden id="file_path" value="'; if(isset($file_name) && isset($file_content)) { echo $filepath; } echo '">
        <input name="saveFile" type="text" hidden id="saveFile">
    </form>
    <script>
        function saveFile(){
            document.getElementById("file_content").value = document.getElementById("textareaCode").value;
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
            document.getElementById("file_content").value = document.getElementById("textareaCode").value;
            document.getElementById("file_name").value = prompt("Bitte den Namen der neuen Datei MIT Dateiendung eingeben. Diese wird anschließend in deinem Benutzerordner gespeichert.");
            if(document.getElementById("file_name").value == "") {
                document.getElementById("file_name").value = "Neue Datei vom "+current_timestamp+" Uhr.html";
            }
            document.getElementById("saveFileForm").submit();        
        }
    </script>';
    }
    ?>


</body>
</html>