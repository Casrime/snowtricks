import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    async show(e) {
        const seeMediasBlock = document.getElementById('see-medias-block');
        seeMediasBlock.style.display = 'none';
        const mediasImagesAndVideos = document.getElementById('medias_images_and_videos');
        mediasImagesAndVideos.classList.remove('hidden');
        e.preventDefault();
    }
}
