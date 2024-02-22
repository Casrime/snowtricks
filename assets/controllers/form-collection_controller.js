import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer"]

    static values = {
        index    : Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        const item = document.createElement('li');
        item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);
        const deleteButton = document.createElement('span');
        deleteButton.innerHTML = '<a href="#" class="remove"><i class="bi bi-x-circle"></i>\n</a>';
        item.appendChild(deleteButton);
        deleteButton.addEventListener('click', (e) => {
            e.preventDefault();
            item.remove();
        });
        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }
}
