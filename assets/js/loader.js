window.addEventListener('DOMContentLoaded', () => {
  const overlay = document.querySelector('div#overlay')
  const loader = document.querySelector('div#loader')

  if (overlay && loader) {
    for (const form of document.querySelectorAll('form')) {
      if (!form.classList.contains('login-form')) {
        form.addEventListener('submit', showLoader)
      }

      if (['template_grid', 'notification_grid'].includes(form.name)) {
        form.addEventListener('change', showLoader)

        for (const pageLink of form.querySelectorAll('a.page-link-js')) {
          pageLink.addEventListener('click', showLoader)
        }

        for (const sortLink of form.querySelectorAll('div.sort-js')) {
          sortLink.addEventListener('click', showLoader)
        }
      }
    }

    for (const link of document.querySelectorAll('a.show-loader')) {
      link.addEventListener('click', showLoader)
    }

    for (const button of document.querySelectorAll('button.show-loader')) {
      button.addEventListener('click', showLoader)
    }
  }
})

export const showLoader = () => {
  const overlay = document.querySelector('div#overlay')
  const loader = document.querySelector('div#loader')

  if (overlay && loader) {
    overlay.classList.remove('d-none')
    loader.classList.remove('d-none')
  }
}

export const hideLoader = () => {
  const overlay = document.querySelector('div#overlay')
  const loader = document.querySelector('div#loader')

  if (overlay && loader) {
    overlay.classList.add('d-none')
    loader.classList.add('d-none')
  }
}
