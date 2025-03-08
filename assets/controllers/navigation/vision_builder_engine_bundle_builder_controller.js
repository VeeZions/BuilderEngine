import { Controller } from '@hotwired/stimulus';
import { buildNavigationData, setNavigationDragAndDrop } from '@vision/builder-engine-utils-navigation';

stimulusFetch: 'lazy'

export default class extends Controller {

    static targets = [
        'stages',
        'stage',
        'tab',
        'child',
        'add',
        'save',
        'marker',
        'trash',
        'trashStage'
    ];

    navigation = buildNavigationData();

    connect() {
        setNavigationDragAndDrop();
        this.addTarget.onclick = () => {
            const html = `<div data-saas--navigation--builder-target="stage" class="w-full flex flex-col items-start justify-start p-2 border border-gray-300 rounded-md mt-4 relative bg-gray-100"><div data-saas--navigation--builder-target="tabs" class="w-full flex flex-col items-start justify-start droppable pb-4 min-h-[8px]"></div><span data-saas--navigation--builder-target="trashStage" class="absolute z-10 -right-2 -top-2 bg-white rounded shadow p-1 cursor-pointer"><span class="size-4 min-w-4 min-h-4 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="text-red-400 hover:text-red-500 w-full h-full"><path fill="currentColor" d="M20.5,6H16V4.5c-0.0012817-1.380188-1.119812-2.4987183-2.5-2.5h-3C9.119812,2.0012817,8.0012817,3.119812,8,4.5V6H3.5C3.223877,6,3,6.223877,3,6.5S3.223877,7,3.5,7H5v12.5c0.0012817,1.380188,1.119812,2.4987183,2.5,2.5h9c1.380188-0.0012817,2.4987183-1.119812,2.5-2.5V7h1.5C20.776123,7,21,6.776123,21,6.5S20.776123,6,20.5,6z M9,4.5C9.0009155,3.671936,9.671936,3.0009155,10.5,3h3c0.828064,0.0009155,1.4990845,0.671936,1.5,1.5V6H9V4.5z M18,19.5c-0.0009155,0.828064-0.671936,1.4990845-1.5,1.5h-9c-0.828064-0.0009155-1.4990845-0.671936-1.5-1.5V7h12V19.5z M9.5,18h0.0006104C9.7765503,17.9998169,10.0001831,17.776001,10,17.5v-7c0-0.276123-0.223877-0.5-0.5-0.5S9,10.223877,9,10.5v7.0005493C9.0001831,17.7765503,9.223999,18.0001831,9.5,18z M14.5,18h0.0006104C14.7765503,17.9998169,15.0001831,17.776001,15,17.5v-7c0-0.276123-0.223877-0.5-0.5-0.5S14,10.223877,14,10.5v7.0005493C14.0001831,17.7765503,14.223999,18.0001831,14.5,18z"></path></svg></span></span></div>`;
            this.stagesTarget.insertAdjacentHTML('beforeend', html);
        }
        this.saveTarget.onclick = () => {
            this.navigation = buildNavigationData();
            const final = {name: this.navigation.navigation.name, stages: []};
            const stages = this.navigation.navigation.stages;
            for (let i = 0; i < stages.length; i++) {
                if (stages[i].length > 0) {
                    final.stages.push(stages[i]);
                }
            }
            const input = document.querySelector('#navigation_data');
            const form = document.querySelector('form[name="navigation"]');
            input.value = JSON.stringify(final);
            form.submit();
        }
    }

    trashStageTargetConnected(item) {
        item.onclick = () => {
            item.parentElement.remove();
            this. navigation = buildNavigationData();
        }
    }

    trashTargetConnected(item) {
        item.onclick = () => {
            item.parentElement.remove();
            this. navigation = buildNavigationData();
        }
    }

    stageTargetConnected() {
        setNavigationDragAndDrop();
        this. navigation = buildNavigationData();
    }

    stageTargetDisconnected() {
        this. navigation = buildNavigationData();
    }

    tabTargetConnected() {
        setNavigationDragAndDrop();
    }

    tabTargetDisconnected() {
        this. navigation = buildNavigationData();
    }

    childTargetConnected() {
        setNavigationDragAndDrop();
    }

    childTargetDisconnected() {
        this. navigation = buildNavigationData();
    }

    markerTargetDisconnected() {
        this.navigation = buildNavigationData();
    }
}
