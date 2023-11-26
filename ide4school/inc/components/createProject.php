<!-- create app modal -->

<link rel="stylesheet" type="text/css" href="app-assets/css/pages/modal-create-app.css">
<script src="app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>
<script src="app-assets/js/scripts/pages/modal-create-app.js"></script>
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-wizard.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/modal-create-app.css">
    <!-- END: Page CSS-->


<div class="modal fade" id="createAppModal" tabindex="-1" aria-labelledby="createAppTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pb-3 px-sm-3">
                                <h1 class="text-center mb-1" id="createAppTitle">Neues Projekt erstellen</h1>
                                <p class="text-center mb-2">Bitte gebe jetzt nähere Informationen zu deinem Projekt an.</p>

                                <div class="bs-stepper vertical wizard-modern create-app-wizard">
                                    <div class="bs-stepper-header" role="tablist">
                                        <div class="step" data-target="#create-app-details" role="tab" id="create-app-details-trigger">
                                            <button type="button" class="step-trigger py-75">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="book" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Details</span>
                                                    <span class="bs-stepper-subtitle">Namen & Beschreibung angeben</span>
                                                </span>
                                            </button>
                                        </div>
                                        
                                        <div class="step" data-target="#create-app-database" role="tab" id="create-app-database-trigger">
                                            <button type="button" class="step-trigger py-75">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="command" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Kategorie & Sichtbarkeit</span>
                                                    <span class="bs-stepper-subtitle">Grundlegende Einstellungen</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="step" data-target="#create-app-submit" role="tab" id="create-app-submit-trigger">
                                            <button type="button" class="step-trigger py-75">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="check" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Fertigstellen</span>
                                                    <span class="bs-stepper-subtitle">Erstelle dein Projekt</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- content -->
                                    <div class="bs-stepper-content shadow-none">
                                        <div id="create-app-details" class="content" role="tabpanel" aria-labelledby="create-app-details-trigger">
                                            <h5>Name des Projektes:</h5>
                                            <input class="form-control" id="project_name" type="text" placeholder="Mein Python Projekt" />
                                            <br /><br />
                                            <h5>Beschreibung:</h5>
                                            <textarea class="form-control" id="project_description" placeholder="Beschreibe dein Projekt"></textarea>
                                            <div class="d-flex justify-content-between mt-2">
                                                <button class="btn btn-outline-secondary btn-prev" disabled>
                                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                                </button>
                                                <button class="btn btn-primary btn-next">
                                                    <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                       
                                        <div id="create-app-database" class="content" role="tabpanel" aria-labelledby="create-app-database-trigger">
                                        <h5>Kategorie:</h5>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item border-0 px-0">
                                                    <label for="createAppCrm" class="d-flex cursor-pointer">
                                                        <span class="avatar avatar-tag bg-light-info me-1">
                                                            <img src="app-assets/images/setupEnviroment/python.png" height="25px" alt="Python">
                                                        </span>
                                                        <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                                            <span class="me-1">
                                                                <span class="h5 d-block fw-bolder">Python Projekt</span>
                                                                <span>Ein einfacher Einstieg.</span>
                                                            </span>
                                                            <span>
                                                                <input class="form-check-input" value="python" name="project_category" id="createAppCrm" type="radio" name="categoryRadio" checked />
                                                            </span>
                                                        </span>
                                                    </label>
                                                </li>
                                                <li class="list-group-item border-0 px-0">
                                                    <label for="createAppEcommerce" class="d-flex cursor-pointer">
                                                        <span class="avatar avatar-tag bg-light-info me-1">
                                                        <img src="app-assets/images/setupEnviroment/html5.png" height="25px" alt="Website">
                                                        </span>
                                                        <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                                            <span class="me-1">
                                                                <span class="h5 d-block fw-bolder">Website Projekt</span>
                                                                <span>Deine eigene Webseite.</span>
                                                            </span>
                                                            <span>
                                                                <input class="form-check-input" value="website" name="project_category" id="createAppEcommerce" type="radio" name="categoryRadio" />
                                                            </span>
                                                        </span>
                                                    </label>
                                                </li>
                                               
                                            </ul><br /><br />
                                            <h5>Sichtbarkeit deines Projektes:</h5>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item border-0 px-0">
                                                    <label for="createAppFirebase" class="d-flex cursor-pointer">
                                                        <span class="avatar avatar-tag bg-light-danger me-1">
                                                            <i data-feather="lock" class="font-medium-3"></i>
                                                        </span>
                                                        <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                                            <span class="me-1">
                                                                <span class="h5 d-block fw-bolder">Privat</span>
                                                                <span>Niemand außer dir wird das Projekt sehen können.</span>
                                                            </span>
                                                            <span>
                                                                <input class="form-check-input" value="private" name="project_visibility" id="createAppFirebase" type="radio" name="databaseRadio" />
                                                            </span>
                                                        </span>
                                                    </label>
                                                </li>
                                                <li class="list-group-item border-0 px-0">
                                                    <label for="createAppDynamoDB" class="d-flex cursor-pointer">
                                                        <span class="avatar avatar-tag bg-light-warning me-1">
                                                            <i data-feather="eye" class="font-medium-3"></i>
                                                        </span>
                                                        <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                                            <span class="me-1">
                                                                <span class="h5 d-block fw-bolder">Für alle einsehbar</span>
                                                                <span>Dies ist beispielsweise für Hilfestellungen oder Unterrichtsaufgaben sinnvoll.</span>
                                                            </span>
                                                            <span>
                                                                <input class="form-check-input" value="public" name="project_visibility" id="createAppDynamoDB" type="radio" name="databaseRadio" checked />
                                                            </span>
                                                        </span>
                                                    </label>
                                                </li>
                                                
                                            </ul>
                                            <div class="d-flex justify-content-between mt-2">
                                                <button class="btn btn-primary btn-prev">
                                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                                </button>
                                                <button class="btn btn-primary btn-next">
                                                    <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div id="create-app-submit" class="content text-center" role="tabpanel" aria-labelledby="create-app-submit-trigger">
                                            <input type="hidden" name="createProject" value="createProject" id="createProject">
                                            <h3>Fertigstellen 🥳</h3>
                                            <p>Die Vorbereitungen sind nun abgeschlossen.<br />Du kannst gleich mit der Programmierung beginnen.</p>
                                            <img src="app-assets/images/illustration/pricing-Illustration.svg" height="218" alt="illustration" />
                                            <div class="d-flex justify-content-between mt-3">
                                                <button class="btn btn-primary btn-prev" id="last_prev_button">
                                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                                </button>
                                                <button class="btn btn-success btn-submit" id="submit_project">
                                                    <span class="align-middle d-sm-inline-block d-none" id="submit_button_text">Projekt erstellen</span>
                                                    <i data-feather="check" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / create app modal -->

