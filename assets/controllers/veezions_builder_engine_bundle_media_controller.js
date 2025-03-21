import { Controller } from '@hotwired/stimulus';
import { resizeGridItems, humanizeSize } from '@veezions/builder-engine-utils-components';
import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'upload',
        'input',
        'preview',
        'items',
        'deleteBtn',
        'loadMore',
        'info',
        'container',
        'modal',
        'close',
        'save',
        'searchForm',
        'searchBtn'
    ];

    itemsPerLoad = null;
    maxFileSize = 0;

    connect() {
        this.itemsPerLoad = parseInt(this.element.dataset.perload);
        this.maxFileSize = parseInt(this.element.dataset.maxfilesize);
        this.setCounter();
    }

    searchFormTargetConnected() {
        this.searchBtnTarget.disabled = false;
        this.searchFormTarget.onsubmit = (e) => {
            e.preventDefault();

            const htmlTag = document.querySelector('html');
            htmlTag.setAttribute('aria-busy', 'true');

            const form = new FormData(this.searchFormTarget);

            fetch(this.searchFormTarget.action, {
                method: 'POST',
                body: form,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then(r => r.json())
                .then(json => {
                    const html = json.html;
                    this.containerTarget.innerHTML = '';
                    this.containerTarget.insertAdjacentHTML('beforeend', html);
                    htmlTag.removeAttribute('aria-busy');
                })
                .catch(e => {
                    console.error(e);
                    htmlTag.removeAttribute('aria-busy');
                });
        }
    }

    uploadTargetConnected(btn) {
        btn.onclick = () => {
            const htmlTag = document.querySelector('html');
            htmlTag.setAttribute('aria-busy', 'true');
        }
    }

    inputTargetConnected(input) {

        const container = this.element.querySelector('#vbe-library-page-container');

        if (container) {
            const drop = new Dropzone("#"+container.id, {
                url: "#",
                disablePreviews: true,
            });
            drop.on("addedfile", file => {
                container.classList.remove('vbe-library-page-drag-on');
                this.chooseFile(file);
            });
            drop.on("dragover", (e) => {
                container.classList.add('vbe-library-page-drag-on');
            })
            drop.on("dragleave", (e) => {
                container.classList.remove('vbe-library-page-drag-on');
            })
        }

        input.oninput = (e) => {
            const files = e.currentTarget.files;
            if (files.length > 0) {
                this.chooseFile(files[0]);
            }
        }
    }

    chooseFile(file) {

        this.previewTarget.innerHTML = `<span>${file.name}</span><i><b>${humanizeSize(file.size)}</b></i>`;
        this.previewTarget.title = file.name;
        this.previewTarget.classList.add('vbe-library-page-loaded');

        if (file.size >= this.maxFileSize) {
            this.previewTarget.classList.add('vbe-error');
            this.uploadTarget.disabled = true;
        } else {
            this.previewTarget.classList.remove('vbe-error');
            this.uploadTarget.disabled = false;
        }
    }

    deleteBtnTargetConnected(btn) {
        btn.onclick = () => {
            this.deleteItems()
        }
    }

    setCounter() {

        if (this.hasInfoTarget) {
            let itemsCount = 0;

            if (this.hasItemsTarget) {
                itemsCount = this.itemsTargets.length;
            }

            const total = parseInt(this.infoTarget.dataset.total);
            const html = `${itemsCount}/<b>${total}</b>`;
        }
    }

    itemsTargetConnected(item) {

        resizeGridItems();
        item.querySelector('input[type="checkbox"]').oninput = (e) => {

            const checkbox = e.currentTarget;
            const allCheckbox = document.querySelectorAll('input[name="media_items[]"] ');
            let disabled = true;

            for (let i = 0; i < allCheckbox.length; i++) {
                if (allCheckbox[i].checked === true) {
                    disabled = false;
                    break;
                }
            }

            this.deleteBtnTarget.disabled = disabled;
        };
    }

    editTargetConnected(item) {
        item.onclick = () => {

            const htmlTag = document.querySelector('html');
            htmlTag.setAttribute('aria-busy', 'true');

            const id = parseInt(item.dataset.id);
            const form = new FormData();
            form.append('id', id);

            fetch('/saas/xhr/media/modal', {
                method: 'POST',
                body: form,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then(r => r.json())
                .then(json => {
                    const html = json.html;
                    this.containerTarget.insertAdjacentHTML('afterbegin', html);
                    htmlTag.removeAttribute('aria-busy');
                })
                .catch(e => {
                    console.error(e);
                    htmlTag.removeAttribute('aria-busy');
                });
        }
    }

    deleteItems() {

        const htmlTag = document.querySelector('html');
        htmlTag.setAttribute('aria-busy', 'true');

        const toDeleteElement = document.querySelectorAll('input[name="media_items[]"]:checked');
        const ids = [];
        toDeleteElement.forEach((elmt) => {
            ids.push(parseInt(elmt.value));
        });

        const form = new FormData();
        form.append('ids', JSON.stringify(ids));

        fetch('/saas/xhr/media/delete', {
            method: 'POST',
            body: form,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        }).then(r => r.json())
            .then(json => {
                const html = json.html;
                this.containerTarget.innerHTML = '';
                this.containerTarget.insertAdjacentHTML('beforeend', html);
                this.setCounter();
                htmlTag.removeAttribute('aria-busy');
            })
            .catch(e => {
                console.error(e);
                htmlTag.removeAttribute('aria-busy');
            });
    }

    loadMoreTargetConnected() {
        this.loadMoreTarget.onclick = () => {
            if (this.hasItemsTarget) {

                const htmlTag = document.querySelector('html');
                htmlTag.setAttribute('aria-busy', 'true');

                const height = this.containerTarget.offsetTop + this.containerTarget.scrollHeight + 116;
                const count = this.itemsTargets.length + this.itemsPerLoad;
                const form = new FormData();
                form.append('count', count);

                fetch('/saas/xhr/media/more', {
                    method: 'POST',
                    body: form,
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                }).then(r => r.json())
                    .then(json => {
                        const html = json.html;
                        this.containerTarget.innerHTML = '';
                        this.containerTarget.insertAdjacentHTML('beforeend', html);
                        this.setCounter();
                        htmlTag.removeAttribute('aria-busy');
                        setTimeout(() => {
                            window.scrollTo(0, height);
                        }, 10)
                    })
                    .catch(e => {
                        console.error(e);
                        htmlTag.removeAttribute('aria-busy');
                    });
            }
        }
    }

    closeTargetConnected() {
        this.closeTarget.onclick = () => {
            this.modalTarget.remove();
        }
    }

    modalTargetConnected(modal) {
        modal.querySelector('[name="media_modal_title"]').oninput = (e) => {
            const value = e.currentTarget.value;
            this.saveTarget.disabled = value.length === 0;
        };
    }

    saveTargetConnected() {
        this.saveTarget.onclick = () => {

            const htmlTag = document.querySelector('html');
            htmlTag.setAttribute('aria-busy', 'true');

            const id = document.querySelector('input[name="media_modal_id"]').value;
            const title = document.querySelector('input[name="media_modal_title"]').value;
            const legend = document.querySelector('input[name="media_modal_legend"]').value;

            const form = new FormData();
            form.append('id', parseInt(id));
            form.append('title', title);
            if (legend.length > 0) {
                form.append('legend', legend);
            }

            fetch('/saas/xhr/media/save', {
                method: 'POST',
                body: form,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then(r => r.json())
                .then(json => {
                    const html = json.html;
                    this.containerTarget.innerHTML = '';
                    this.containerTarget.insertAdjacentHTML('beforeend', html);
                    htmlTag.removeAttribute('aria-busy');
                })
                .catch(e => {
                    console.error(e);
                    htmlTag.removeAttribute('aria-busy');
                });
        }
    }
}
