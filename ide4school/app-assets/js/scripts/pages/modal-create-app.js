$(function () {
  ('use strict');
  var modernVerticalWizard = document.querySelector('.create-app-wizard'),
    createAppModal = document.getElementById('createAppModal'),
    assetsPath = 'app-assets/',
    creditCard = $('.create-app-card-mask'),
    expiryDateMask = $('.create-app-expiry-date-mask'),
    cvvMask = $('.create-app-cvv-code-mask');

  if ($('body').attr('data-framework') === 'laravel') {
    assetsPath = $('body').attr('data-asset-path');
  }

  // --- create app  ----- //
  if (typeof modernVerticalWizard !== undefined && modernVerticalWizard !== null) {
    var modernVerticalStepper = new Stepper(modernVerticalWizard, {
      linear: false
    });

    $(modernVerticalWizard)
      .find('.btn-next')
      .on('click', function () {
        modernVerticalStepper.next();
      });
    $(modernVerticalWizard)
      .find('.btn-prev')
      .on('click', function () {
        modernVerticalStepper.previous();
      });

    $(modernVerticalWizard)
      .find('.btn-submit')
      .on('click', function () {
        var formData = {
          project_name: document.getElementById('project_name').value,
          project_description: document.getElementById('project_description').value,
          project_visibility: document.querySelector('input[name="project_visibility"]:checked').value,
          project_category: document.querySelector('input[name="project_category"]:checked').value,
          createProject: document.getElementById('createProject').value,
        }
          var xhr = new XMLHttpRequest();
              xhr.open('POST', 'projects', true);
              xhr.setRequestHeader('Content-Type', 'application/json');
              xhr.onreadystatechange = function() {
              if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                  var response = JSON.parse(this.responseText);
                  if (response.success) {
                  window.location.href = response.redirect;
                  } 
              }
              };

              xhr.send(JSON.stringify(formData));
              document.getElementById('submit_project').disabled = true;
              document.getElementById('last_prev_button').disabled = true;
              document.getElementById('submit_button_text').innerHTML = 'Projekt wird erstellt. Bitte warten.';
              $("svg.feather.feather-check").replaceWith(feather.icons.loader.toSvg());
      });

    // reset wizard on modal hide
    createAppModal.addEventListener('hide.bs.modal', function (event) {
      modernVerticalStepper.to(1);
    });
  }

  // Credit Card
  if (creditCard.length) {
    creditCard.each(function () {
      new Cleave($(this), {
        creditCard: true,
        onCreditCardTypeChanged: function (type) {
          if (type != '' && type != 'unknown') {
            document.querySelector('.credit-app-card-type').innerHTML =
              '<img src="' + assetsPath + 'images/icons/payments/' + type + '-cc.png" height="24"/>';
          } else {
            document.querySelector('.credit-app-card-type').innerHTML = '';
          }
        }
      });
    });
  }

  // Expiry Date Mask
  if (expiryDateMask.length) {
    expiryDateMask.each(function () {
      new Cleave($(this), {
        date: true,
        delimiter: '/',
        datePattern: ['m', 'y']
      });
    });
  }

  // CVV
  if (cvvMask.length) {
    cvvMask.each(function () {
      new Cleave($(this), {
        numeral: true,
        numeralPositiveOnly: true
      });
    });
  }

  // --- / create app ----- //
});
