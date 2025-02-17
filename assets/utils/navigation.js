export const buildNavigationData = () => {

    const wrapper = document.querySelector('[data-controller="saas--navigation--entities"]');
    if (wrapper instanceof HTMLElement) {

        const navigation = {name: wrapper.dataset.name, stages: []};
        const type = wrapper.dataset.type;
        const stages = document.querySelectorAll('[data-saas--navigation--builder-target="stage"]');

        for (let i = 0; i < stages.length; i++) {
            const stage = [];
            const tabs = stages[i].querySelectorAll('[data-saas--navigation--builder-target="tab"]');

            for (let j = 0; j < tabs.length; j++) {
                const tab = tabs[j];

                if (tab instanceof HTMLElement) {
                    const t = JSON.parse(tabs[j].dataset.info);

                    if (type === 'header') {
                        t.children = [];
                        const children = tabs[j].querySelectorAll('[data-saas--navigation--builder-target="child"]');

                        for (let k = 0; k < children.length; k++) {
                            const child = children[k];

                            if (child instanceof HTMLElement) {
                                const json = JSON.parse(child.dataset.info);
                                if (typeof json.children !== 'undefined') {
                                    delete json.children;
                                }
                                t.children.push(json);
                            }
                        }
                    }
                    stage.push(t);
                }
            }
            navigation.stages.push(stage);
        }
        return {type, navigation};
    }
    return null;
}

export const setNavigationDragAndDrop = () => {

    $('.droppable').sortable({
        placeholder: 'navigation-placeholder',
        group: 'droppable',
        handle: '.dragger',
        connectWith: ['.droppable', '.sub-droppable'],
        update: (event, ui) => {
            const selector = '[data-saas--navigation--builder-target="marker"]';
            const container = '[data-saas--navigation--builder-target="container"]';
            const marker = document.querySelector(selector);
            const input = '<input type="hidden" data-saas--navigation--builder-target="marker">';
            if (marker, container) {
                const isChild = $(ui.item).attr('data-saas--navigation--builder-target') === 'child';
                if (isChild) {
                    $(ui.item).attr('data-saas--navigation--builder-target', 'tab');
                    const childrenContainer = '<div data-saas--navigation--builder-target="children" class="w-full flex flex-col items-start justify-start sub-droppable pl-4 min-h-[8px]"></div>';
                    $(ui.item).append(childrenContainer);
                }
                marker.remove();
                $(container).append(input);
            }
        }
    }).disableSelection();

    $('.sub-droppable').sortable({
        placeholder: 'navigation-placeholder',
        group: 'sub-droppable',
        handle: '.dragger',
        connectWith: ['.droppable', '.sub-droppable'],
        update: (event, ui) => {
            const selector = '[data-saas--navigation--builder-target="marker"]';
            const marker = document.querySelector(selector);
            const container = '[data-saas--navigation--builder-target="container"]';
            const input = '<input type="hidden" data-saas--navigation--builder-target="marker">';
            if (marker, container) {
                const isChild = $(ui.item).attr('data-saas--navigation--builder-target') === 'child';
                if (!isChild) {
                    $(ui.item).attr('data-saas--navigation--builder-target', 'child');
                    const childrenContainer = $(ui.item).find('[data-saas--navigation--builder-target="children"]');
                    if (childrenContainer) {
                        childrenContainer.remove();
                    }
                }
                marker.remove();
                $(container).append(input);
            }
        }
    }).disableSelection();
}
