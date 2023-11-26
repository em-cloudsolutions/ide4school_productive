<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}?>
<script src="app-assets/js/scripts/pages/page-pricing.js"></script>
<script>
    function setupEnviroment(ide_code){
        document.getElementById("ide_code").value = ide_code;
        document.getElementById("setupEnviromentForm").submit();
    }
</script>

<form action="ide" method="post" id="setupEnviromentForm">
            <input name="ide_code" type="text" hidden id="ide_code">
            <input name="setupEnviroment" type="text" hidden id="setupEnviroment">
        </form>

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
                                                    <img src="app-assets/images/setupEnviroment/html5.png" width="20%" class="mb-2 mt-5" alt="svg img" />
                                                    <h3>HTML</h3>
                                                    <h5>Einfacher Editor</h5>
                                                    <p class="card-text">Für die Entwicklung einer Webseite</p>
                                                    
                                                    <button OnClick="setupEnviroment('1')" class="btn w-100 btn-primary mt-2">Loslegen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ basic plan -->

                                        <!-- standard plan -->
                                        <div class="col-12 col-lg-4">
                                            <div class="card standard-pricing border-primary text-center shadow-none">
                                                <div class="card-body">
                                                    
                                                    <img src="app-assets/images/setupEnviroment/python.png" width="25%" class="mb-1" alt="svg img" />
                                                    <h3>Python</h3>
                                                    <p class="card-text">Optimal für Schulaufgaben und kleinere Projekte</p>
                                                   
                                                    <button OnClick="setupEnviroment('2')" class="btn w-100 btn-primary mt-2">Loslegen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ standard plan -->

                                        <!-- enterprise plan -->
                                        <div class="col-12 col-lg-4">
                                            <div class="card enterprise-pricing border text-center shadow-none">
                                                <div class="card-body">
                                                    <img src="app-assets/images/setupEnviroment/html5.png" width="39%" class="mb-2" alt="svg img" />
                                                    <h3>HTML</h3>
                                                    <h5>Erweiterter Editor</h5>
                                                    <p class="card-text">Java, Ruby, C, C++, uvm.</p>
                                                   
                                                    <button OnClick="setupEnviroment('3')" class="btn w-100 btn-primary mt-2">Loslegen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ enterprise plan -->
                                    </div>
                                    <!--/ pricing plan cards -->

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / pricing modal  -->