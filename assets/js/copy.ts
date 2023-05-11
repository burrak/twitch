window.addEventListener('DOMContentLoaded', () => {
  for (const button of document.querySelectorAll<HTMLButtonElement>('.copy-url-button')) {
    button.addEventListener('click', () => {
      const input = button.querySelector<HTMLInputElement>('.copy-url-input')

      if (input) {
        input.select()
        input.setSelectionRange(0, 99_999)
        navigator.clipboard.writeText(input.value).then(() => {
          button.innerHTML = button.innerHTML.replace('Copy URL', 'URL copied!')

          setTimeout(() => button.innerHTML = button.innerHTML.replace('URL copied!', 'Copy URL'), 2_000)
        })
      }
    })
  }
})
