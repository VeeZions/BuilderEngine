import { Controller } from '@hotwired/stimulus';
import {
    defaultElementConfig,
    createElementFromHTML,
    setPageBuilderDragAndDrop,
    rebuildPageJson,
    getDataFromModule
} from '@veezions/builder-engine-utils-modules';

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'hidden',
        'addRow',
        'addSection',
        'addBlock',
        'addModule',
        'workspace',
        'loader',
        'row',
        'section',
        'block',
        'module',
        'rows',
        'sections',
        'blocks',
        'modules',
        'editElement',
        'deleteElement'
    ];

    data = null;
    availableTranslations = null;
    route = null;

    connect() {
        this.data = JSON.parse(this.hiddenTarget.value);
        this.availableTranslations = JSON.parse(this.element.dataset.translations);
        this.route = this.element.closest('[data-route]').dataset.route;
        if (this.hasWorkspaceTarget) {
            this.getData('page', null, {'data': JSON.stringify(this.data)}, (html) => {
                this.workspaceTarget.insertAdjacentHTML('beforeend', html);
                setPageBuilderDragAndDrop((event, ui) => this.updateData());
            });
        }
    }

    addRowTargetConnected(row) {
        row.onclick = (e) => {
            this.getNewElement('row', e.currentTarget, defaultElementConfig('row'));
        }
    }

    addSectionTargetConnected(section) {
        section.onclick = (ev) => {

            const sectionModal = document.querySelector('#erp-builder-new-section-modal');
            const sectionClose = document.querySelector('#erp-builder-new-section-modal-close');
            const templates = document.querySelectorAll('.erp-builder-section-config');

            if (sectionModal) {
                sectionModal.classList.remove('hidden');
            }

            if (sectionClose) {
                sectionClose.onclick = () => {
                    this.closeNewSectionModal();
                }
            }

            const elmt = ev.currentTarget;

            for (let i = 0; i < templates.length; i++) {
                templates[i].onclick = (e) => {

                    const blocks = JSON.parse(e.currentTarget.dataset.config);
                    const section = defaultElementConfig('section');

                    for (let j = 0; j < blocks.length; j++) {

                        const block = defaultElementConfig('block');
                        block.format = blocks[j];
                        section.children.push(block);
                    }

                    this.closeNewSectionModal();
                    this.getNewElement('section', elmt, section);
                }
            }
        }

        const isFullWidth = document.querySelector('input[name="isFullwidth"]');
        if (isFullWidth) {
            isFullWidth.oninput = () => {
                this.updateData();
            }
        }
    }

    closeNewSectionModal() {
        const sectionModal = document.querySelector('#erp-builder-new-section-modal');
        if (sectionModal) {
            sectionModal.classList.add('hidden');
        }
    }

    closeNewModuleModal() {
        const moduleModal = document.querySelector('#erp-builder-new-module-modal');
        if (moduleModal) {
            moduleModal.classList.add('hidden');
        }
    }

    addModuleTargetConnected() {
        for (const key in this.addModuleTargets) {
            this.addModuleTargets[key].onclick = (e) => {
                const moduleModal = document.querySelector('#erp-builder-new-module-modal');
                const moduleClose = document.querySelector('#erp-builder-new-module-modal-close');
                const modules = document.querySelectorAll('.erp-builder-new-module-modal-selector');

                if (moduleModal) {
                    moduleModal.classList.remove('hidden');
                }

                if (moduleClose) {
                    moduleClose.onclick = () => {
                        this.closeNewModuleModal();
                    }
                }

                const elmt = e.currentTarget;

                for (let i = 0; i < modules.length; i++) {
                    modules[i].onclick = (ev) => {
                        const module = defaultElementConfig('module');
                        module.contentType = ev.currentTarget.dataset.module;
                        this.closeNewModuleModal();
                        this.getNewElement('module', elmt, module);
                    }
                }
            }
        }
    }

    getNewElement(type, elmt, config) {
        this.getData(
            type,
            elmt,
            {'data': JSON.stringify(config)},
            (html, el) => {
                el.parentElement
                    .parentElement
                    .querySelector('.ui-sortable')
                    .insertAdjacentHTML('beforeend', html);
                this.updateData();
            }
        );
    }

    editElementTargetConnected(item) {
        item.onclick = (e) => {
            const editable = item.dataset.editable === 'true';
            this.loaderTarget.classList.add('display');
            const elmt = item.parentElement.closest('[data-saas--builder-target]');
            const json = JSON.parse(elmt.dataset.config);
            const type = elmt.getAttribute('data-saas--builder-target');
            const form = new FormData;
            form.append('data', elmt.dataset.config);
            form.append('type', type);
            form.append('editable', editable);
            fetch('/saas/xhr/builder/info', {
                method: 'POST',
                body: form,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
                .then(r => r.json())
                .then(json => {
                    this.displaySettingsModal(json.html);
                    this.loaderTarget.classList.remove('display');
                })
                .catch(e => {
                    console.error(e);
                    this.loaderTarget.classList.remove('display');
                });
        }
        const closeSettingsModal = document.querySelector('#erp-builder-settings-modal-close');
        if (closeSettingsModal) {
            closeSettingsModal.onclick = () => {
                this.closeSettingsModal();
            }
        }
    }

    closeSettingsModal() {
        const modal = document.querySelector('#erp-builder-settings-modal');
        const wrapper = document.querySelector('#erp-builder-settings-modal-wrapper');
        wrapper.innerHTML = '';
        modal.classList.add('hidden');
    }

    displaySettingsModal(html) {
        const modal = document.querySelector('#erp-builder-settings-modal');
        const wrapper = document.querySelector('#erp-builder-settings-modal-wrapper');

        if (modal && wrapper) {

            wrapper.innerHTML = '';
            wrapper.insertAdjacentHTML('beforeend', html);
            modal.classList.remove('hidden');
            const button = modal.querySelector('#erp-builder-modal-settings-btn button[type="button"]');

            if (button) {
                button.onclick = (e) => {

                    const inputTags = modal.querySelectorAll('input');
                    const textareaTags = modal.querySelectorAll('textarea');
                    const inputs = [];
                    const data = {};

                    for (let i = 0; i < inputTags.length; i++) {
                        inputs.push(inputTags[i]);
                    }

                    for (let i = 0; i < textareaTags.length; i++) {
                        inputs.push(textareaTags[i]);
                    }

                    for (let i = 0; i < inputs.length; i++) {

                        if (inputs[i].name === 'builder-responsive_order') {
                            data[inputs[i].name.replace('builder-', '')] = inputs[i].value.trim().length > 0
                                ? parseInt(inputs[i].value.trim())
                                : null;
                        } else {
                            data[inputs[i].name.replace('builder-', '')] = inputs[i].value.trim().length > 0
                                ? inputs[i].value.trim()
                                : null;
                        }
                    }

                    const element = document.querySelector('#' + data.id);
                    if (element) {
                        const json = JSON.parse(element.dataset.config);
                        json.bg = data['bg-size'] !== null
                        || data['bg-position'] !== null
                        || data['bg-repeat'] !== null
                        || data['bg-color'] !== null
                        || data['bg-image'] !== null
                            ? {
                                'image': ['0', null].includes(data['bg-image']) ? null : parseInt(data['bg-image']),
                                'size': data['bg-size'],
                                'position': data['bg-position'],
                                'repeat': data['bg-repeat'],
                                'color': data['bg-color'] === null ? 'transparent' : data['bg-color'].trim()
                            }
                            : null
                        json.stimulusController = data['stimulus_controller'];
                        json.stimulusTarget = data['stimulus_target'];
                        json.class = data['classes'];
                        json.stimulusTarget = data['stimulus_target'];
                        if (['module', 'block'].includes(json.type)) {
                            json.responsiveOrder = data['responsive_order'];
                        }
                        if (json.type === 'module') {
                            json.data = getDataFromModule(json.contentType, data, this.availableTranslations)
                        }
                        element.dataset.config = JSON.stringify(json);
                        this.updateData();
                        this.closeSettingsModal();
                    }
                }
            }
        }
    }



    deleteElementTargetConnected(elmt) {
        elmt.onclick = (e) => {
            const el = e.currentTarget;
            const parent = el.closest('[data-saas--builder-target="' + el.dataset.type + '"]');
            if (parent) {
                parent.remove();
                this.updateData();
            }
        }
    }

    duplicateElementTargetConnected(elmt) {
        elmt.onclick = (e) => {
            const el = e.currentTarget;
            const parent = el.closest('[data-saas--builder-target="' + el.dataset.type + '"]');
            if (parent) {
                const newer = createElementFromHTML(parent.outerHTML);
                const children = parent.parentElement.children;
                parent.parentElement.insertBefore(newer, children[children.length - 2]);
                this.updateData();
            }
        }
    }

    updateData() {
        this.data = rebuildPageJson(this.element);
        this.hiddenTarget.value = JSON.stringify(this.data);
        setPageBuilderDragAndDrop((event, ui) => this.updateData());
    }

    getData(type, elmt, fields = {}, callback = null) {

        this.loaderTarget.classList.add('display');
        const form = new FormData;

        for (const i in fields) {
            form.append(i, fields[i])
        }

        fetch('/saas/xhr/builder/' + type, {
            method: 'POST',
            body: form,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
            .then(r => r.json())
            .then(json => {
                if (callback !== null && json.html !== null) {
                    callback(json.html, elmt);
                } else {
                    console.log(json.html)
                }
                this.loaderTarget.classList.remove('display');
            })
            .catch(e => {
                console.error(e);
                this.loaderTarget.classList.remove('display');
            });
    }
}
