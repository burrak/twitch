const NotificationForm = (function () {
  const showClientFileArea = function (clientId_file_area, clientId_text_area) {
    clientId_text_area.classList.add('invisible')
    clientId_text_area.classList.remove('visible')

    clientId_file_area.classList.remove('invisible')
    clientId_file_area.classList.add('visible')
  }

  const showClientTextArea = function (clientId_file_area, clientId_text_area) {
    clientId_text_area.classList.remove('invisible')
    clientId_text_area.classList.add('visible')

    clientId_file_area.classList.remove('visible')
    clientId_file_area.classList.add('invisible')
  }

  return {
    init: function () {
      const forms = document.querySelectorAll('.needs-validation')
      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })

      const toggle_clients_file = document.getElementById('notification_useFile')
      const clientId_text_area = document.querySelector('.clientIdTextArea')
      const clientId_file_area = document.querySelector('.clientIdFileArea')

      if (toggle_clients_file) {
        if (toggle_clients_file.checked) {
          showClientFileArea(clientId_file_area, clientId_text_area)
        } else {
          showClientTextArea(clientId_file_area, clientId_text_area)
        }

        if (toggle_clients_file) {
          toggle_clients_file.addEventListener('change', function () {
            const checked = toggle_clients_file.checked
            if (checked) {
              showClientFileArea(clientId_file_area, clientId_text_area)
            } else {
              showClientTextArea(clientId_file_area, clientId_text_area)
            }
          })
        }
      }
    }
  }
})()

NotificationForm.init()
