import JSONFormatter from 'json-formatter-js'

window.addEventListener('DOMContentLoaded', () => {
  for (const element of document.querySelectorAll<HTMLElement>('.json-view')) {
    element.appendChild(new JSONFormatter(JSON.parse(element.dataset.json as string), 0).render())
  }
})
