window.addEventListener('DOMContentLoaded', () => {
    const processImage = (input: HTMLInputElement, image: HTMLImageElement) => {
        input.addEventListener('change', () => {
            const { files } = input

            if (files) {
                const { type } = files[0]

                if (type.startsWith('image/')) {
                    image.src = URL.createObjectURL(files[0])
                    image.classList.remove('d-none')
                } else {
                    image.classList.add('d-none')
                }
            }
        })
    }

    const processCreate = () => {
        const div = document.querySelector<HTMLElement>('#create-file-modal')
        const open = document.querySelector<HTMLButtonElement>('#create-file-modal-open')
        const save = document.querySelector<HTMLButtonElement>('#create-file-modal-save')
        const input = document.querySelector<HTMLInputElement>('#create_file_file')
        const image = document.querySelector<HTMLImageElement>('#create-file-modal-preview')
        const select = document.querySelector<HTMLSelectElement>('#create_file_type')
        const useName = document.querySelector<HTMLDivElement>('.create-use-name-in-url')
        //
        if (div && open && input && save && image && select && useName) {
            // @ts-ignore
            const modal = new bootstrap.Modal(div)

            select.addEventListener('change', () => {
                select.value === '1' ? useName.classList.remove('d-none') : useName.classList.add('d-none')
            })

            div.dataset.hasErrors === 'true' && modal.show()
            open.addEventListener('click', () => modal.show())
            save.addEventListener('click', () => save.form && save.form.submit())

            processImage(input, image)
        }
    }

    const processUpdate = () => {
        const div = document.querySelector<HTMLElement>('#update-file-modal')
        const opens = document.querySelectorAll<HTMLButtonElement>('.update-file-modal-open')
        const save = document.querySelector<HTMLButtonElement>('#update-file-modal-save')
        const form = document.querySelector<HTMLFormElement>('form[name=update_file]')
        const input = document.querySelector<HTMLInputElement>('#update_file_file')
        const inputId = document.querySelector<HTMLInputElement>('#update_file_id')
        const image = document.querySelector<HTMLImageElement>('#update-file-modal-preview')
        const select = document.querySelector<HTMLSelectElement>('#update_file_type')
        const useName = document.querySelector<HTMLDivElement>('.update-use-name-in-url')

        if (div && opens && form && input && inputId && save && image && select && useName) {
            // @ts-ignore
            const modal = new bootstrap.Modal(div)
            let itemId = form.dataset.id
            div.dataset.hasErrors === 'true' && modal.show()

            select.addEventListener('change', () => {
                select.value === '1' ? useName.classList.remove('d-none') : useName.classList.add('d-none')
            })

            for (const updateOpen of opens) {
                updateOpen.addEventListener('click', () => {
                    modal.show()
                    itemId = updateOpen.dataset.id
                })
            }

            save.addEventListener('click', () => {
                if (itemId) {
                    inputId.value = itemId
                    save.form && save.form.submit()
                }
            })

            processImage(input, image)
        }
    }

    processCreate()
    processUpdate()
})
