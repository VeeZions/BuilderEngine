import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'content',
    ];

    initialize() {
        document.addEventListener('turbo:load', () => this.connect());
    }

    connect() {
        this.loadCSS();
    }

    loadCSS(path) {
        const loaded = document.querySelector('#quill-css');
        if (!loaded) {
            const link = document.createElement('link');
            link.id = 'quill-css';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/quill@latest/dist/quill.snow.css';
            document.head.appendChild(link);
        }
    }

    contentTargetConnected(elmt) {

        this.editorContainer = document.createElement('div');
        this.editorContainer.className = 'vbe-form-theme-quill-editor'
        elmt.parentElement.insertBefore(this.editorContainer, elmt);

        const options = {
            modules: {
                toolbar: true,
            },
            theme: 'snow'
        };
        this.quill = new Quill(this.editorContainer, options);

        this.quill.on('text-change', () => {
            elmt.value = this.quill.root.innerHTML;
        });

        if (elmt.value) {
            this.quill.root.innerHTML = elmt.value;
        }
    }
}
