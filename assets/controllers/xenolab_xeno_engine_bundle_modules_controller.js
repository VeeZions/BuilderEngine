import { Controller } from '@hotwired/stimulus';
import {tinymceCss} from "@xenolab/xeno-engine-utils-modules";

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'text',
        'contact-form',
        'gallery',
        'header',
        'media',
        'slider',
        'title',
        'video'
    ];

    connect() {

    }

    textTargetConnected(mod) {
        const tinies = this.element.querySelectorAll('.tinymce');
        for (let i = 0; i < tinies.length; i++) {
            tinymce.init({
                selector: '#' + tinies[i].id,
                license_key: 'gpl',
                menubar: false,
                statusbar: false,
                link_title: false,
                link_target_list: false,
                toolbar_mode: 'scrolling',
                language: this.textTarget.dataset.language === 'fr' ? 'fr_FR' : false,
                plugins: 'charmap image link lists media autoresize',
                toolbar: 'bold italic strikethrough | link | numlist bullist | align | blocks',
                content_style: tinymceCss(12),
                noneditable_class: 'mceNonEditable',
                extended_valid_elements : 'svg[*]',
                object_resizing: false,
                paste_data_images: false,
                height: 110,
                max_height: 400,
                autoresize_bottom_margin: 0,
                autoresize_overflow_padding: 0,
                setup: (editor) => {
                    editor.on("keyup", function (e) {
                        tinymce.triggerSave();
                    });
                },
            });
            tinymce.triggerSave;
        }
    }

    contactFormTargetConnected(mod) {
        console.log(mod)
    }

    galleryTargetConnected(mod) {
        console.log(mod)
    }

    headerTargetConnected(mod) {
        console.log(mod)
    }

    mediaTargetConnected(mod) {
        console.log(mod)
    }

    sliderTargetConnected(mod) {
        console.log(mod)
    }

    titleTargetConnected(mod) {
        console.log(mod)
    }

    videoTargetConnected(mod) {
        console.log(mod)
    }

}
