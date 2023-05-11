import { hideLoader, showLoader } from './loader'

const TemplateForm = (function () {
  let form_input_url = null
  let file_validations = null
  let use_file = null
  let close_element = null
  let thumbnail_image = null
  let url = null
  let image_url = null

  const removeAllFeedback = function (element) {
    element.querySelectorAll('.invalid-feedback').forEach(e => e.parentNode.removeChild(e))
    element.querySelectorAll('.valid-feedback').forEach(e => e.parentNode.removeChild(e))
  }

  const loadFile = function (file, token) {
    const form_data = new FormData()
    form_data.append('custom_image[file]', file)
    form_data.append('custom_image[_token]', token)

    const xhttp = new XMLHttpRequest()
    xhttp.onreadystatechange = function () {
      if (this.readyState === 4) {
        hideLoader()

        if (this.status === 200) {
          const file_response = JSON.parse(this.responseText)
          if (file_response.errors) {
            removeAllFeedback(file_validations)
            file_response.errors.forEach((error) => {
              const error_div = document.createElement('div')
              error_div.classList.add('invalid-feedback', 'd-block')
              error_div.textContent = error
              file_validations.append(error_div)

              use_file.disabled = true
              image_url = null
            })
          } else {
            removeAllFeedback(file_validations)

            if (file_response.url) {
              image_url = file_response.url
              const valid_div = document.createElement('div')
              valid_div.classList.add('valid-feedback', 'd-block')
              valid_div.textContent = 'Looks good!'
              file_validations.append(valid_div)

              thumbnail_image.src = file_response.url
              use_file.disabled = false
            }
          }
        }
      }
    }

    xhttp.open('POST', url, true)
    xhttp.send(form_data)
  }

  return {
    init: function () {
      const fileModals = document.querySelectorAll('.modal-file')

      fileModals.forEach((modal) => {
        modal.addEventListener('show.bs.modal', function (event) {
          form_input_url = document.getElementById(event.target.dataset.inputId)
          file_validations = event.target.querySelector('#fileValidations')
          close_element = event.target.querySelector('#closeModal')
          thumbnail_image = event.target.querySelector('.main-thumbnail-image')

          if (form_input_url) {
            use_file = event.target.querySelector('#useFile')
            const load_file = event.target.querySelector('#loadFile')

            url = form_input_url.dataset.url
            const token = form_input_url.dataset.csrfToken

            let file = null
            load_file.addEventListener('change', function (event) {
              file = event.target.files[0]
              if (!file) {
                removeAllFeedback(file_validations)
                use_file.disabled = true
                thumbnail_image.src = ''
              } else {
                showLoader()
                loadFile(file, token)
              }
            })

            use_file.addEventListener('click', function () {
              if (file && image_url) {
                form_input_url.value = image_url
                close_element.click()
              }
            })
          }
        })
      })
    }
  }
})()

TemplateForm.init()
