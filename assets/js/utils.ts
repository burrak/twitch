export const getURLSearchParams = (): URLSearchParams => new URLSearchParams(window.location.search)

export const redirectToURLSearchParams = (urlSearchParams?: URLSearchParams): void => {
  window.location.href = urlSearchParams ? `${window.location.pathname}?${urlSearchParams.toString()}` : window.location.pathname
}

export const getFormattedDate = (date: Date): string => new Intl.DateTimeFormat('sv', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
}).format(date)
