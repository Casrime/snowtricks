import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    bottom() {
        window.scrollTo(0, document.body.scrollHeight);
    }
    top() {
        window.scrollTo(0, 0);
    }
}
