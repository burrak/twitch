window.addEventListener('DOMContentLoaded', () => {
  const links: NodeListOf<HTMLLinkElement> = document.querySelectorAll('form[method=get] a[data-href]')
  const multipleActions: HTMLElement | null = document.querySelector('#multiple-actions')
  const checkBox: HTMLInputElement | null = document.querySelector('input[type=checkbox][name=select]')
  const showFilterButton: HTMLInputElement | null = document.querySelector('#show-filter')
  const hideFilterButton: HTMLInputElement | null = document.querySelector('#hide-filter')
  const applyFilterButton: HTMLInputElement | null = document.querySelector('#apply-filter')
  const trashFilterButton: HTMLInputElement | null = document.querySelector('#trash-filter')
  const selectBox: HTMLSelectElement | null = document.querySelector('form[method=get] select[name=itemsPerPage]')
  const checkBoxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[type=checkbox][name=selected]')
  const inputs: NodeListOf<HTMLInputElement> = document.querySelectorAll('form[method=get] div.grid-filter')
  const checkedCheckBoxes = new Map<string, undefined>()
  const modals = document.querySelector<HTMLDivElement>('#grid-modal')
  const modalImages = document.querySelectorAll<HTMLImageElement>('.grid-modal-image')

  if (modals) {
    document.querySelector('.container-fluid')?.appendChild(modals)
  }

  if (modalImages) {
    for (const modalImage of modalImages) {
      const src = modalImage.dataset.src

      if (src) {
        modalImage.addEventListener('load', () => {
          modalImage.classList.remove('d-none')
        })

        modalImage.addEventListener('error', () => {
          modalImage.parentElement?.querySelector('.grid-modal-image-not-image')?.classList.remove('d-none')
        })

        modalImage.src = src
      } else {
        modalImage.classList.add('d-none')
      }
    }
  }

  const processLinks = (htmlInputElement: HTMLInputElement): void => {
    const key = htmlInputElement.dataset.key

    if (key) {
      if (htmlInputElement.checked) {
        checkedCheckBoxes.set(key, undefined)
      } else {
        checkedCheckBoxes.delete(key)
      }

      if (multipleActions) {
        if (checkedCheckBoxes.size) {
          multipleActions.classList.remove('d-none')
        } else {
          multipleActions.classList.add('d-none')
        }
      }
    }
  }

  if (selectBox) {
    selectBox.addEventListener('change', () => {
      selectBox.form && selectBox.form.dispatchEvent(new Event('submit')) && selectBox.form.submit()
    })
  }

  if (checkBox) {
    checkBox.addEventListener('change', () => {
      for (const innerCheckBox of checkBoxes) {
        innerCheckBox.checked = checkBox.checked
        processLinks(innerCheckBox)
      }
    })

    for (const innerCheckBox of checkBoxes) {
      innerCheckBox.addEventListener('change', () => processLinks(innerCheckBox))
    }

    for (const link of links) {
      link.addEventListener('click', event => {
        event.preventDefault()
        event.stopPropagation()
        event.stopImmediatePropagation()

        const href = link.dataset.href

        if (href) {
          window.location.href = href.replace('__REPLACE_ME__', Array.from(checkedCheckBoxes).map(([key]) => key).join(','))
        }
      })
    }
  }

  if (showFilterButton && applyFilterButton && hideFilterButton && trashFilterButton) {
    const showFilters = () => {
      showFilterButton.classList.add('d-none')
      hideFilterButton.classList.remove('d-none')
      applyFilterButton.classList.remove('d-none')
      trashFilterButton.classList.remove('d-none')

      for (const input of inputs) {
        input.classList.remove('d-none')
      }
    }

    const hideFilters = () => {
      showFilterButton.classList.remove('d-none')
      hideFilterButton.classList.add('d-none')
      applyFilterButton.classList.add('d-none')
      trashFilterButton.classList.add('d-none')

      for (const input of inputs) {
        input.classList.add('d-none')
      }
    }

    if (parseInt(localStorage.getItem('showFilters') ?? '0')) {
      showFilters()
    }

    showFilterButton.addEventListener('click', event => {
      event.preventDefault()
      event.stopPropagation()
      event.stopImmediatePropagation()
      localStorage.setItem('showFilters', '1')
      showFilters()
    })

    hideFilterButton.addEventListener('click', event => {
      event.preventDefault()
      event.stopPropagation()
      event.stopImmediatePropagation()
      localStorage.setItem('showFilters', '0')
      hideFilters()
    })
  }

  const inputGroups: NodeListOf<HTMLElement> | null = document.querySelectorAll('.range-inputs')
  let is_empty = true
  if (inputGroups) {
    inputGroups.forEach(inputGroup => {
      const inputs: NodeListOf<HTMLInputElement> | null = inputGroup.querySelectorAll('input')

      inputs.forEach((element) => {
        element.addEventListener('keyup', () => {
          is_empty = true
          inputs.forEach((input) => {
            if (input.value !== '') {
              is_empty = false
            }
          })
          if (is_empty) {
            inputs.forEach((element) => {
              element.removeAttribute('required')
            })
          } else {
            inputs.forEach((element) => {
              element.setAttribute('required', 'required')
            })
          }
        })
      })
    })
  }
})
