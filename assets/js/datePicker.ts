import flatpickr from 'flatpickr'
import { getFormattedDate, getURLSearchParams, redirectToURLSearchParams } from './utils'

window.addEventListener('DOMContentLoaded', () => {
  const gridDatePickers = document.querySelectorAll<HTMLSelectElement>('.grid-date-picker')
  const gridDatePickersFake = document.querySelectorAll<HTMLSelectElement>('.grid-date-picker-fake')
  const gridDatePickersCustom = document.querySelectorAll<HTMLInputElement>('.grid-date-picker-custom')

  for (const [index, gridDatePicker] of gridDatePickers.entries()) {
    const urlSearchParams = getURLSearchParams()
    const { name, options, form } = gridDatePicker
    const [from, to] = urlSearchParams.getAll(name)

    flatpickr(gridDatePickersCustom.item(index), {
      mode: 'range',
      dateFormat: 'Y-m-d',
      defaultDate: from && to ? [from, to] : ['', ''],
      onClose: dates => {
        if (dates.length === 2) {
          const [dateFrom, dateTo] = dates

          urlSearchParams.set(name, `${getFormattedDate(dateFrom)} 00:00:00`)
          urlSearchParams.append(name, `${getFormattedDate(dateTo)} 23:59:59`)

          redirectToURLSearchParams(urlSearchParams)
        }
      }
    })

    gridDatePicker.addEventListener('change', () => {
      const { selectedIndex } = gridDatePicker
      gridDatePickersFake.item(index).selectedIndex = selectedIndex

      if (selectedIndex === options.length - 1) {
        gridDatePickersCustom.item(index).classList.remove('d-none')
      } else {
        gridDatePickersCustom.item(index).classList.add('d-none')
        form && form.submit()
      }
    })
  }

  for (const datePicker of document.querySelectorAll<HTMLInputElement>('input[type=datetime-local]')) {
    flatpickr(datePicker, {
      dateFormat: 'Y-m-d H:i:S',
      minuteIncrement: 1,
      enableTime: true,
      time_24hr: true,
    })
  }
})
