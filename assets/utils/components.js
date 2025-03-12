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

export const timestamp = () => {
    const d = new Date;
    return d.getFullYear()
        + ('0' + (d.getMonth()+1)).slice(-2)
        + ('0' + d.getDate()).slice(-2)
        + ('0' + d.getHours()).slice(-2)
        + ('0' + d.getMinutes()).slice(-2)
        + ('0' + d.getSeconds()).slice(-2);
}

export const humanizeSize = (octets) => {
    octets = Math.abs(parseInt(octets, 10));
    let def = [
        [1, 'octets'],
        [1024, 'ko'],
        [1024 * 1024, 'Mo'],
        [1024 * 1024 * 1024, 'Go'],
        [1024 * 1024 * 1024 * 1024, 'To']
    ];
    for(let i= 0; i < def.length; i++) {
        if (octets<def[i][0]) {
            return (octets / def[i-1][0]).toFixed(2) + ' ' + def[i-1][1];
        }
    }
}
