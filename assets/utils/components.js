export const autoHideAll = (el = null, n = false) => {
    const a = document.querySelectorAll('[data-autohide="true"]');
    a.forEach((e) => {
        if (n === false) {
            e.classList.add('hidden');
            if (e.closest('.opened')) {
                e.closest('.opened').classList.remove('opened');
            }
        } else if (e!==el.querySelector('[data-autohide="true"]')) {
            e.classList.add('hidden');
            if (e.closest('.opened')) {
                e.closest('.opened').classList.remove('opened');
            }
        }
    });
}

export const resizeGridItems = () => {
    const items = document.querySelectorAll('.media-grid-item');
    for (let i = 0; i < items.length; i++) {
        items[i].style.height = items[i].scrollWidth + 'px';
    }
}

export const resizeMediaProvider = () => {
    const providers = document.querySelectorAll('[data-components--inputs--mediaProvider-target="container"]');
    for (let i = 0; i < providers.length; i++) {
        providers[i].style.height = providers[i].scrollWidth*0.6 + 'px';
    }
}
