<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$new_html_project = '{"identifier":"new","project_type":"html","locale":"en","name":"Neues Projekt","user_id":null,"components":[{"id":"e732f181-933f-4324-844a-c05cedd9c56c","name":"index","extension":"html","content":""},{"id":"b06d109f-71e4-4227-8bce-fb67a9599381","name":"styles","extension":"css","content":""}],"image_list":[], "to_review":false}';
$new_python_project = '{"identifier":"new","project_type":"python","name":"Neues Projekt","locale":null,"components":[{"extension":"py","name":"main","content":"","default":true}],"image_list":[], "to_review":false}';

?>
<script src="app-assets/js/scripts/pages/page-pricing.js"></script>
<script>
    function setupEnviroment(ide_short){
        if(ide_short == "python"){
            localStorage.setItem("project", decodeURIComponent(`<?=$new_python_project?>`));
            window.location.href = "/ide4school-ce";
        } else if(ide_short == "website"){
            localStorage.setItem("project", decodeURIComponent(`<?=$new_html_project?>`));
            window.location.href = "/ide4school-ce";
        }
    }
</script>


<!-- pricing modal  -->
<div class="modal fade" id="createEnviromentModal" tabindex="-1" aria-labelledby="createEnviromentModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-sm-5 mx-50 pb-5">
                                <div id="pricing-plan">
                                    <!-- title text and switch button -->
                                    <div class="text-center">
                                        <h1 id="pricingModalTitle">Entwicklungsumgebung auswählen</h1>
                                        <p class="mb-3">
                                            Welche Art von Projekt möchtest du programmieren?
                                        </p>
                                        
                                    </div>
                                    <!--/ title text and switch button -->

                                    <!-- pricing plan cards -->
                                    <div class="row pricing-card">
                                        <!-- basic plan -->
                                        <div class="col-12 col-lg-4">
                                            <div class="card basic-pricing border text-center shadow-none">
                                                <div class="card-body">
                                                    <img src="app-assets/images/setupEnviroment/python.png" width="20%" class="mb-2 mt-5" alt="svg img" />
                                                    <h3>Python</h3>
                                                    
                                                    <p class="card-text">Entwicklung erster Programme in Python.</p>
                                                    
                                                    <button OnClick="setupEnviroment('python')" class="btn w-100 btn-primary mt-2">Loslegen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ basic plan -->

                                        <!-- standard plan -->
                                        <div class="col-12 col-lg-4">
                                            <div class="card standard-pricing border-primary text-center shadow-none">
                                                <div class="card-body">
                                                    <h3>Wie funktioniert es?</h3>
                                                    <br />
                                                    <br />
                                                    <h6>1. Schritt: Wählen der Entwicklungsumgebung</h6>
                                                    <p class="card-text">Suche dir aus, ob du lieber ein Python Programm oder eine Webseite programmieren möchtest.</p>
                                                    <h6>2. Schritt: Programmieren</h6>
                                                    <p class="card-text">Programmiere nach Herzenslust an deinem Programm oder deiner Webseite und versuch neue Dinge.</p>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ standard plan -->

                                        <!-- basic plan -->
                                        <div class="col-12 col-lg-4">
                                            <div class="card basic-pricing border text-center shadow-none">
                                                <div class="card-body">
                                                    <img src="app-assets/images/setupEnviroment/html5.png" width="20%" class="mb-2 mt-5" alt="svg img" />
                                                    <h3>HTML / CSS</h3>
                                                    
                                                    <p class="card-text">Für die Entwicklung einer Webseite</p>
                                                    
                                                    <button OnClick="setupEnviroment('website')" class="btn w-100 btn-primary mt-2">Loslegen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ basic plan -->
                                        <span style="text-align: center;">
                                        <h6>3. Schritt: Projekt ggf. speichern?</h6>
                                                    <p class="card-text">Grundsätzlich werden die hier entwickelten Programme <b>NICHT</b> gespeichert. Möchtest du ein Projekt anlegen um deinen Code zu speichern, klicke bitte <a href="projects"><u>hier</u></a>.
                                                    <br />
                                                    Im Notfall ist es dennoch möglich, deinen hier programmierten Code in einem neuen Projekt zu speichern. Klicke dazu einfach auf "Speichern" und du findest deinen Code unter deinen eingenen Projekten wieder.    
                                                    </p>
                                                    </span>
                                    </div>
                                    <!--/ pricing plan cards -->

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / pricing modal  -->