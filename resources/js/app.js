import Alpine from 'alpinejs'
import { marked } from 'marked'
import 'flowbite'

// Configure marked options
marked.setOptions({
    breaks: true,
    gfm: true
})

window.Alpine = Alpine
window.marked = marked

Alpine.start()
