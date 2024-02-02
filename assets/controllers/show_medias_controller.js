import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    async show(e) {
        const seeMediasBlock = document.getElementById('see-medias-block');
        seeMediasBlock.style.display = 'none';
        const dNone = document.querySelectorAll('.d-none');
        dNone.forEach((element) => {
            element.classList.remove('d-none');
        });
        e.preventDefault();
    }
}
