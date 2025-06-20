import '@veezions/builder-engine-jquery';
import '@veezions/builder-engine-jquery-ui';
import { resizeGridItems, resizeMediaProvider } from '@veezions/builder-engine-utils-components';
import '../css/internal.css';
import '../css/reset.css';
import '../css/form_theme.css';

// Opera 8.0+
const isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
// Firefox 1.0+
const isFirefox = typeof InstallTrigger !== 'undefined';
// Safari 3.0+ "[object HTMLElementConstructor]"
const isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && window['safari'].pushNotification));
// Internet Explorer 6-11
const isIE = /*@cc_on!@*/false || !!document.documentMode;
// Edge 20+
const isEdge = !isIE && !!window.StyleMedia;
// Chrome 1 - 79
const isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
// Edge (based on chromium) detection
const isEdgeChromium = isChrome && (navigator.userAgent.indexOf("Edg") != -1);
// Blink engine detection
const isBlink = (isChrome || isOpera) && !!window.CSS;

window.mobileAndTabletCheck = function() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};

function addContextDeviceToHtmlTag() {
    if (mobileAndTabletCheck()) {
        document.querySelector('html').classList.add('is-mobile');
        document.querySelector('html').classList.remove('is-desktop');
    } else {
        document.querySelector('html').classList.add('is-desktop');
        document.querySelector('html').classList.remove('is-mobile');
    }
}

addContextDeviceToHtmlTag();
resizeWebMobileNavigation();
detectIncompatibilities();

window.onclick = (e) => {
    if (!e.target.closest('[data-autoclose="true"]')) {
        const a = document.querySelectorAll('[data-autohide="true"]');
        a.forEach((e) => {
            e.classList.add('hidden');
            if (e.closest('.opened')) {
                e.closest('.opened').classList.remove('opened');
            }
        });
    }
    const saasMessage = document.querySelector('#message_form');
    const saasMessageFileInput = document.querySelector('#upload-file-to-chat');
    if (saasMessage && !saasMessage.classList.contains('update-chat') && saasMessageFileInput && !e.target.closest('#message_form') && !e.target.closest('.tox-tinymce-aux')) {
        saasMessage.classList.add('border-gray-300', 'inactive');
        saasMessage.classList.remove('border-cyan-600', 'active');
        saasMessageFileInput.disabled = true;
        saasMessageFileInput.parentElement.classList.remove('hover:border-cyan-700', 'hover:bg-cyan-700', 'hover:text-white', 'text-cyan-500', 'cursor-pointer', 'shadow');
        saasMessageFileInput.parentElement.classList.add('text-gray-300');
    }
}

window.onresize = (e) => {
    addContextDeviceToHtmlTag();
    resizeWebMobileNavigation();
    resizeGridItems();
    resizeMediaProvider();
}

function resizeWebMobileNavigation() {
    const mobileLogoContainer = document.querySelector('#mobile-logo-container');
    const navContainer = document.querySelector('#mobile-navigation');
    const subNavs = document.querySelectorAll('.mobile-sub-navigation');
    if (mobileLogoContainer && navContainer) {
        const logoHeight = mobileLogoContainer.scrollHeight;
        const navWidth = navContainer.scrollWidth;
        subNavs.forEach((nav) => {
            nav.style.width = navWidth + 'px';
            nav.style.height =  (window.innerHeight - logoHeight - 1) + 'px';
            nav.style.top = logoHeight + 1 + 'px';
        });
    }
}

function detectIncompatibilities() {
    if (isFirefox || isIE) {
        document.querySelector('html').setAttribute('data-obsoleteBrowser', 'yes')
    }
}
