import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    async connect() {
        const tricks = document.querySelector('#tricks');
        let offset = tricks.dataset.offset;
        const response = await fetch(`/load-more-tricks?offset=${offset}`);
        const html = await response.text();
        offset = (html.match(/<div class="card">/g) || []).length;
        tricks.setAttribute('data-offset', offset.toString());
        const loadMoreButtonDiv = document.getElementById('load-more-button-div');
        loadMoreButtonDiv.insertAdjacentHTML('beforebegin', html);

        if (offset < 15) {
            document.getElementById('load-more-button').remove();
        }
    }
}
