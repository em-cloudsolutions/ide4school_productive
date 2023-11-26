/*=========================================================================================
    File Name: app-user-view.js
    Description: User View page
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function () {
  const suspendUser = document.querySelector('.suspend-user');

  // Suspend User javascript
  if (suspendUser) {
    suspendUser.onclick = function () {
      Swal.fire({
        title: 'Benutzer löschen?',
        text: "Bist du sicher?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, Benutzer löschen!',
        cancelButtonText: 'Abbrechen',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: 'Benutzer gelöscht!',
            text: 'Die Aktion wurde erfolgreich abgeschlossen!',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Abgebrochen',
            text: 'Der Benutzer wurde nicht gelöscht!',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
      });
    };
  }

  //? Billing page have multiple buttons
  // Cancel Subscription alert
  const cancelSubscription = document.querySelectorAll('.cancel-subscription');

  // Alert With Functional Confirm Button
  if (cancelSubscription) {
    cancelSubscription.forEach(cancelBtn => {
      cancelBtn.onclick = function () {
        Swal.fire({
          text: 'Bist du sicher dass du den Benutzer löschen möchtest?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ja',
          customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ms-1'
          },
          buttonsStyling: false
        }).then(function (result) {
          if (result.value) {
            Swal.fire({
              icon: 'success',
              title: 'Gelöscht',
              text: 'Der Benutzer wurde erfolgreich gelöscht!',
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
              title: 'Abgebrochen',
              text: 'Benutzer wurde nicht gelöscht!',
              icon: 'error',
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          }
        });
      };
    });
  }
})();
