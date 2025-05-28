import { Controller } from '@hotwired/stimulus';
import { buildNavigationData } from '@veezions/builder-engine-utils-navigation';

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'tab',
        'entities',
        'entity',
        'add',
        'create',
        'externals'
    ];

    navigation = buildNavigationData();
    externalLabel = document.querySelector('[name="navigation_external_label"]');
    externalLink = document.querySelector('[name="navigation_external_link"]');
    externalBlank = document.querySelector('[name="navigation_external_blank"]');

    connect() {
        this.setCurrentTab();
        if (this.hasAddTarget) {
            this.addTarget.onclick = (e) => {
                this.addEntities()
            }
        }
        if (this.hasCreateTarget) {
            this.createTarget.onclick = () => {
                this.addExternalLink();
                this.createTarget.disabled = true;
            }
        }
        if (this.externalLabel && this.externalLink && this.externalBlank && this.hasExternalsTarget) {
            this.externalLabel.oninput = () => {
                this.createTarget.disabled = !this.checkExternalLinkValue() || this.externalLabel.value.length === 0;
            }
            this.externalLink.oninput = () => {
                this.createTarget.disabled = !this.checkExternalLinkValue() || this.externalLabel.value.length === 0;
            }
        }
    }

    checkExternalLinkValue() {
        const check = this.externalLink.value.length > 0
            && (this.externalLink.value.startsWith('http')
            || this.externalLink.value.startsWith('/')
            || this.externalLink.value.startsWith('#'));

        if (check === true) {
            this.externalLink.classList.remove('error');
        } else {
            this.externalLink.classList.add('error');
        }

        return check;
    }

    addExternalLink() {

        const data = {
            id: null,
            type: 'external',
            label: this.externalLabel.value,
            route: null,
            query: [],
            link: this.externalLink.value,
            blank: this.externalBlank.checked
        }

        const existing = this.element.querySelectorAll('[name="navigation_entity[externals][]"]');
        const html = `<li class="w-full flex items-center justify-start mb-2" data-info='${JSON.stringify(data)}' data-type="external" data-saas--navigation--entities-target="entity"><div class="w-full"><label class="erp-checkbox flex items-center justify-start cursor-pointer w-full"><input type="checkbox" name="navigation_entity[externals][]" checked value="${existing.length + 1}"><span></span><p style="width: calc(100% - 28px);" class="text-sm ml-2 truncate">${data.label}</p></label></div></li>`;

        this.externalsTarget.insertAdjacentHTML('beforeend', html);

        this.externalLabel.value = '';
        this.externalLink.value = '';
        this.externalBlank.checked = false;
    }

    setCurrentTab() {
        if (this.hasTabTarget) {
            for (const i in this.tabTargets) {
                this.tabTargets[i].onclick = (e) => {
                    for (const tabIndex in this.tabTargets) {
                        this.tabTargets[tabIndex].classList.remove('selected');
                        this.entitiesTargets[tabIndex].classList.remove('selected');
                    }
                    e.currentTarget.classList.add('selected');
                    for (const entityIndex in this.entitiesTargets) {
                        if (this.entitiesTargets[entityIndex].dataset.entity === e.currentTarget.dataset.entity) {
                            this.entitiesTargets[entityIndex].classList.add('selected');
                            break;
                        }
                    }
                }
            }
        }
    }

    addEntities() {
        if (this.hasEntityTarget) {
            let selected = [];
            for (const entityIndex in this.entityTargets) {
                const input = this.entityTargets[entityIndex].querySelector('input[type="checkbox"]');
                if (input.checked) {
                    const json = JSON.parse(this.entityTargets[entityIndex].dataset.info);
                    json.children = [];
                    selected.push(json);
                    input.checked = false;
                }
            }
            const stages = document.querySelectorAll('[data-saas--navigation--builder-target="stage"]');
            if (stages.length > 0) {
                const lastStage = stages[stages.length - 1];
                const tabsContainer = lastStage.querySelector('[data-saas--navigation--builder-target="tabs"]');
                const form = new FormData;
                form.append('type', this.navigation.type);
                form.append('new', JSON.stringify(selected));
                form.append('block', 'tab');
                fetch('/saas/xhr/navigation/new', {
                    method: 'POST',
                    body: form,
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                }).then(r => r.json())
                    .then(json => {
                        const html = json.html;
                        tabsContainer.insertAdjacentHTML('beforeend', html);
                        buildNavigationData();
                    })
                    .catch(e => {
                        console.error(e);
                    });
            }
        }
    }
}
