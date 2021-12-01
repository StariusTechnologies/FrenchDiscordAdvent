var Modal = {
    element: null,
    isOpen: false,
    closeHandler: null,

    init: function (element) {
        this.element = element;
    },

    empty: function () {
        var children = Array.from(this.element.children);

        for (var i = 0; i < children.length; i++) {
            this.element.removeChild(children[i]);
        }
    },

    populate: function (title, text) {
        var button = document.createElement('button');
        var heading = document.createElement('h2');
        var contents = document.createElement('p');

        button.addEventListener('click', this.close.bind(this));
        button.setAttribute('aria-label', 'Close');
        button.classList.add('close');
        button.innerText = '×'

        heading.innerText = title;
        contents.innerText = text;

        this.empty();

        this.element.appendChild(button);
        this.element.appendChild(heading);
        this.element.appendChild(contents);
    },

    open: function (title, text) {
        if (title && text) {
            this.populate(title, text);
        }

        this.element.style.display = 'flex';
        this.isOpen = true;
    },

    close: function () {
        this.element.style.display = 'none';

        if (this.closeHandler) {
            this.closeHandler();
        }

        setTimeout(function () {
            this.isOpen = false;
        }.bind(this), 100);
    },
}