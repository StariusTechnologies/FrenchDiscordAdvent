var Modal = {
    element: null,
    contentBlock: null,
    shadow: null,
    rewardSrc: null,
    isOpen: false,
    closeHandler: null,

    init: function (element, contentBlock, shadow) {
        this.element = element;
        this.contentBlock = contentBlock;
        this.shadow = shadow;
        this.rewardSrc = document.getElementById('rewardTag').innerHTML;

        this.shadow.addEventListener(
            'click',
            function() {
                Modal.close();
            }
        );
    },

    empty: function () {
        var children = Array.from(this.contentBlock.children);

        for (var i = 0; i < children.length; i++) {
            this.contentBlock.removeChild(children[i]);
        }
    },

    populate: function (title, text, reward = null) {
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

        this.contentBlock.appendChild(button);
        this.contentBlock.appendChild(heading);
        this.contentBlock.appendChild(contents);

        if (reward) {
            var separator = document.createElement('hr');
            var rewardImg = document.createElement('img');
            var rewardLabel = document.createElement('p');
            rewardLabel.classList.add('rewardLabel');

            rewardImg.src = this.rewardSrc;
            rewardLabel.innerText = '✨ ' + reward['amount'] + ' ' + reward['label'] + ' ✨';

            this.contentBlock.appendChild(separator);
            this.contentBlock.appendChild(rewardImg);
            this.contentBlock.appendChild(rewardLabel);
        }
    },

    open: function (data) {
        if (!data['story']) {
            return;
        }

        var title = data['story']['title'];
        var text = data['story']['content'];

        if (title && text) {
            if (data['reward']) {
                this.populate(title, text, data['reward']);
            } else {
                this.populate(title, text);
            }
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