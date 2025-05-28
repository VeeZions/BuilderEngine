export const defaultElementConfig = (type) => {

    let config = type !== 'module'
        ? {
            "bg": null,
            "id": null,
            "type": type,
            "class": "",
            "children": [],
            "stimulusTarget": null,
            "stimulusController": null
        }
        : {
            "bg": null,
            "id": null,
            "data": null,
            "type": type,
            'responsiveOrder': null,
            "class": "",
            "contentType": 'text',
            "stimulusTarget": null,
            "stimulusController": null,
        }

    if (type === 'row') {
        config.fullwidth = false;
    }

    if (type === 'block') {
        config.format = 100;
        config.responsiveOrder = null;
    }

    return config;
}

export const createElementFromHTML = (htmlString) => {
    const div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}

export const setPageBuilderDragAndDrop = (callback) => {

    $('.builder-rows').sortable({
        placeholder: 'builder-row-placeholder',
        group: 'builder-rows',
        connectWith: ['.builder-rows'],
        handle: '.dragger-row',
        update: function(event, ui) {
            $(ui.item).css('opacity', 1)
            callback(event, ui)
        },
        start: function(e, ui ){
            $(ui.placeholder).css('height', ui.helper.outerHeight())
            $(ui.placeholder).css('width', ui.helper.outerWidth())
            $(ui.item).css('opacity', 0.5)
        },
        stop: (e, ui ) => {
            $(ui.item).css('opacity', 1)
        }
    }).disableSelection();

    $('.builder-sections').sortable({
        placeholder: 'builder-section-placeholder',
        group: 'builder-sections',
        connectWith: ['.builder-sections'],
        handle: '.dragger-section',
        update: function(event, ui) {
            $(ui.item).css('opacity', 1)
            callback(event, ui)
        },
        start: function(e, ui ){
            $(ui.placeholder).css('height', ui.helper.outerHeight())
            $(ui.placeholder).css('width', ui.helper.outerWidth())
            $(ui.item).css('opacity', 0.5)
        },
        stop: (e, ui ) => {
            $(ui.item).css('opacity', 1)
        }
    }).disableSelection();

    $('[data-saas--builder-target="blocks"]').sortable({
        placeholder: 'builder-block-placeholder',
        handle: '.dragger-block',
        update: function(event, ui) {
            $(ui.item).css('opacity', 1)
            callback(event, ui)
        },
        start: function(e, ui ){
            $(ui.placeholder).css('height', ui.helper.outerHeight())
            $(ui.placeholder).css('width', ui.helper.outerWidth() - 16)
            $(ui.placeholder).css('margin-top', 8)
            $(ui.item).css('opacity', 0.5)
        },
        stop: (e, ui ) => {
            $(ui.item).css('opacity', 1)
        }
    }).disableSelection();

    $('.builder-modules').sortable({
        placeholder: 'builder-module-placeholder',
        group: 'builder-modules',
        connectWith: ['.builder-modules'],
        handle: '.dragger-module',
        update: function(event, ui) {
            $(ui.item).css('opacity', 1)
            callback(event, ui)
        },
        start: function(e, ui ){
            $(ui.placeholder).css('height', ui.helper.outerHeight())
            $(ui.placeholder).css('width', ui.helper.outerWidth())
            $(ui.item).css('opacity', 0.5)
        },
        stop: (e, ui ) => {
            $(ui.item).css('opacity', 1)
        }
    }).disableSelection();
}

export const rebuildPageJson = (container) => {

    const data = [];
    const rows = container.querySelectorAll('[data-saas--builder-target="row"]');

    for (let i = 0; i < rows.length; i++) {

        if (rows[i] instanceof HTMLElement) {

            const rI = i + 1;
            const rLabel = `row${rI}`;
            const row = JSON.parse(rows[i].dataset.config);
            const isFullWidth = rows[i].querySelector('input[name="isFullwidth"]');

            row.id = rLabel;
            row.fullwidth = isFullWidth.checked;
            rows[i].id = rLabel;
            rows[i].dataset.config = JSON.stringify(row);
            row.children = [];

            const sections = rows[i].querySelectorAll('[data-saas--builder-target="section"]');
            for (let j = 0; j < sections.length; j++) {

                if (sections[j] instanceof HTMLElement) {

                    const sI = j + 1;
                    const sLabel = `section${rI}-${sI}`;
                    const section = JSON.parse(sections[j].dataset.config);
                    section.id = sLabel;
                    sections[j].id = sLabel;
                    sections[j].dataset.config = JSON.stringify(section);
                    section.children = [];

                    const blocks = sections[j].querySelectorAll('[data-saas--builder-target="block"]');
                    for (let k = 0; k < blocks.length; k++) {

                        if (blocks[k] instanceof HTMLElement) {

                            const bI = k + 1;
                            const bLabel = `block${rI}-${sI}-${bI}`;
                            const block = JSON.parse(blocks[k].dataset.config);
                            block.id = bLabel;
                            blocks[k].id = bLabel;
                            blocks[k].dataset.config = JSON.stringify(block);
                            block.children = [];

                            const modules = blocks[k].querySelectorAll('[data-saas--builder-target="module"]');
                            for (let l = 0; l < modules.length; l++) {

                                if (modules[l] instanceof HTMLElement) {

                                    const mI = l + 1;
                                    const mLabel = `module${rI}-${sI}-${bI}-${mI}`;
                                    const module = JSON.parse(modules[l].dataset.config);

                                    module.id = mLabel;
                                    module.contentType = modules[l].dataset.module;
                                    modules[l].id = mLabel;
                                    modules[l].dataset.config = JSON.stringify(module);

                                    block.children.push(module);
                                }
                            }
                            section.children.push(block);
                        }
                    }
                    row.children.push(section);
                }
            }
            data.push(row);
        }
    }
    return data;
}

export const getDataFromModule = (type, data, translations) => {
    if (type === 'text') {
        const content = {};
        for (let i = 0; i < translations.length; i++) {
            content[translations[i]] = data['module_text_' + translations[i]];
        }
        return content;
    } else if (['image', 'video'].includes(type)) {
        return {'library': parseInt(data['module_' + type])}
    }
    return null;
}

export const tinymceCss = (fontSize = 14) => {
    const css = `<style>
        body { 
            font-family:"sans",Helvetica,Arial,sans-serif; 
            font-size:${fontSize}px; 
            line-height: 18px;
            color: #56565d; 
            background-color: transparent; 
            cursor: text; 
        } 
        p {
            margin: 0;
        } 
        img {
            width: 150px;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        } 
        .file-message-container img {
            
        }
        .file-message-container {
            display: flex;
            justify-content: flex-start;
            align-items: stretch;
            background-color: #fff;
            border-radius: 10px;
            border: 1px solid #e1e1e1;
            width: max-content;
            max-width: calc(100% - 20px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .file-message-container-default {
            
        }
        .file-message-container-image {
            
        }
        .file-message-container-left {
            display: flex; 
            justify-content: flex-start; 
            align-items: center; 
            flex-direction: column; 
            padding: 10px 20px; 
            background-color: dodgerblue; 
            border-top-left-radius: 10px; 
            border-bottom-left-radius: 10px;
        }
        .file-message-container-right {
            display: flex; 
            justify-content: flex-start; 
            align-items: flex-start; 
            flex-direction: column; 
            padding: 10px; 
            border-top-right-radius: 10px; 
            border-bottom-right-radius: 10px; 
            overflow: hidden;
        }
        .file-message-container-left-svg {
            width: 30px; 
            color: #fff;
        }
        .file-message-container-left-ext {
            font-weight: bold; 
            font-size: 14px; 
            color: #fff;
        }
        .file-message-container-right-title {
            font-weight: bold; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap; 
            width: 100%;
        }
        .file-message-container-right-size {
            font-style: italic; 
            font-size: 10px; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap; 
            width: 100%;
        }
    </style>`;
    return css.replace(/<[^>]*>/g, '');
}
