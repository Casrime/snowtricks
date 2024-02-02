import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    async connect() {
        const comments = document.querySelector('#comments');
        let offset = comments.dataset.offset;
        console.log(offset);
        let trickId = comments.dataset.id;
        const response = await fetch(`/trick/${trickId}/load-more-comments?offset=${offset}`);
        const html = await response.text();
        const loadMoreButtonDiv = document.getElementById('load-more-button-div');

        const accordionButtons = document.querySelectorAll('.accordion-button');
        console.log(accordionButtons);
        accordionButtons.forEach(button => {
            button.classList.add('collapsed');
        });

        const accordionCollapses = document.querySelectorAll('.accordion-collapse');
        accordionCollapses.forEach(collapse => {
            collapse.classList.remove('show');
        });

        loadMoreButtonDiv.insertAdjacentHTML('beforebegin', html);

        offset = document.getElementsByClassName('accordion-item').length;
        //console.log(document.getElementsByClassName('accordion-item').length);
        comments.setAttribute('data-offset', offset.toString());

        const offsetLeft = (html.match(/<div class="accordion-item">/g) || []).length;

        if (offsetLeft < 4) {
            document.getElementById('load-more-button').remove();
        }
    }
}
