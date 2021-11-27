var CALENDAR_WINDOW_PROPERTIES = {
    inactive: {
        colours: {
            background: 'grey',
            stroke: '#666666',
            text: '#AAAAAA',
        },
        radius: 25,
    },
    active: {
        colours: {
            background: 'white',
            stroke: '#AAAAAA',
            text: '#000000',
        },
        radius: 100,
    },
};

var CalendarWindow = {
    x: 0,
    y: 0,
    vx: 0,
    vy: 0,
    radius: 25,
    colours: {
        background: 'grey',
        stroke: '#666666',
        text: '#AAAAAA',
    },
    text: '',
    active: false,

    isMouseOver: function (mousePosition) {
        var x = mousePosition.x - this.x;
        var y = mousePosition.y - this.y;

        return Math.abs(Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2))) < this.radius;
    },

    setActive: function (active) {
        this.colours = CALENDAR_WINDOW_PROPERTIES[active ? 'active' : 'inactive'].colours;
        this.radius = CALENDAR_WINDOW_PROPERTIES[active ? 'active' : 'inactive'].radius;
        this.active = !!active;

        return this;
    },

    draw: function (context, mousePosition, image) {
        context.save();

        context.fillStyle = this.colours.background;
        context.strokeStyle = this.colours.stroke;
        context.lineWidth = 4;

        if (this.isMouseOver(mousePosition)) {
            context.shadowBlur = 10;
            context.shadowColor = 'white';
        }

        context.beginPath();
        context.arc(this.x, this.y, this.radius, 0, Math.PI * 2, true);
        context.closePath();

        context.fill();
        context.stroke();

        context.restore()

        context.drawImage(
            image,
            this.x - this.radius / 1.38,
            this.y - this.radius / 1.38,
            this.radius * 1.5,
            this.radius * 1.5
        );

        context.restore();

        context.font = this.radius + 'px Roboto'
        context.fillStyle = this.colours.text;
        context.textAlign = 'center';
        context.fillText(this.text, this.x, this.y + this.radius / 48 * 17);

        context.restore();

        var tooFarLeft = this.x <= this.radius;
        var tooFarRight = this.x >= context.canvas.width - this.radius;
        var tooFarUp = this.y <= this.radius + context.lineWidth;
        var tooFarDown = this.y >= context.canvas.height - this.radius - context.lineWidth;

        if (tooFarLeft && tooFarRight) {
            this.vx = 0;
        } else if (tooFarLeft || tooFarRight) {
            this.vx *= -1;
        }

        if (tooFarUp && tooFarDown) {
            this.vy = 0;
        } else if (tooFarUp || tooFarDown) {
            this.vy *= -1;
        }

        this.x += this.vx;
        this.y += this.vy;
    },

    clickHandler(event) {
        var mousePosition = {
            x: event.clientX,
            y: event.clientY,
        }

        if (!this.isMouseOver(mousePosition)) {
            return;
        }

        alert('YOOOOOOO ' + this.text); // TODO replace with actual action
    },
};

var Calendar = {
    canvas: null,
    windows: [],
    requestAnimationFrame: null,
    mousePosition: {x: 0, y: 0},
    active: new Date().getDate(),
    loaded: false,
    loadingAngle: 0,
    imagesToLoad: null,
    calendarWindowImages: [],

    init: function (element) {
        if (this.requestAnimationFrame) {
            window.cancelAnimationFrame(this.requestAnimationFrame);
        }

        var context = this.setupCanvas(element);

        this.imagesToLoad = window.calendarWindowImages.filter(() => true);
        this.loadImages();
        this.requestAnimationFrame = window.requestAnimationFrame(this.draw.bind(this));

        this.windows = [];

        for (var i = 0; i < 31; i++) {
            if (this.active === 31 - i) {
                continue;
            }

            this.windows.push(this.createCalendarWindow(context, (31 - i).toString(), false));
        }

        this.windows.push(this.createCalendarWindow(context, (this.active).toString(), true));
    },

    setupCanvas: function (element) {
        this.canvas = element;

        this.canvas.width = this.canvas.offsetWidth;
        this.canvas.height = this.canvas.offsetHeight;

        this.clear();

        return this.canvas.getContext('2d');
    },

    loadImages: function () {
        var loadHandler = function (event) {
            this.imagesToLoad.splice(this.imagesToLoad.indexOf(event.target.src), 1);

            if (this.imagesToLoad.length < 1) {
                this.loaded = true;
            }

            this.calendarWindowImages.push(event.target);
        }.bind(this);

        for (var imagePath of this.imagesToLoad) {
            var image = new Image();

            image.onload = loadHandler;
            image.src = imagePath;
        }
    },

    createCalendarWindow: function (context, text, active) {
        var calendarWindow = Object.assign({}, CalendarWindow);

        calendarWindow.setActive(active);

        calendarWindow.vx = this.getRandomSpeed();
        calendarWindow.vy = this.getRandomSpeed();

        calendarWindow.x = Math.random() * (this.canvas.offsetWidth - calendarWindow.radius * 2) + calendarWindow.radius;
        calendarWindow.y = Math.random() * (this.canvas.offsetHeight - calendarWindow.radius * 2) + calendarWindow.radius;

        calendarWindow.text = text;

        return calendarWindow;
    },

    getRandomSpeed: function () {
        var minSpeed = 1;
        var speed;

        do {
            speed = Math.random() * 4 - 2;
        } while (Math.abs(speed) < minSpeed);

        return speed;
    },

    clear: function () {
        var context = this.canvas.getContext('2d');

        context.clearRect(0, 0, this.canvas.offsetWidth, this.canvas.offsetHeight);
    },

    calendarWindowImage: function* () {
        var index = 0;

        while (true) {
            yield this.calendarWindowImages[index];
            index++;

            if (index === this.calendarWindowImages.length) {
                index = 0;
            }
        }
    },

    draw: function () {
        var context = this.canvas.getContext('2d');

        this.clear();

        if (this.loaded) {
            var calendarWindowImageIterator = this.calendarWindowImage();

            for (var calendarWindow of this.windows) {
                calendarWindow.draw(context, this.mousePosition, calendarWindowImageIterator.next().value);
            }
        } else {
            context.save();

            context.strokeStyle = '#FFFFFF';
            context.lineWidth = 10;

            context.beginPath();
            context.arc(
                this.canvas.width / 2,
                this.canvas.height / 2,
                50,
                this.loadingAngle + Math.cos(this.loadingAngle * 45 * Math.PI / 180),
                Math.PI * 1.25 + this.loadingAngle + Math.sin(this.loadingAngle * 45 * Math.PI / 180)
            );

            context.stroke();
            this.loadingAngle += 0.1;

            context.restore();
        }

        this.requestAnimationFrame = window.requestAnimationFrame(this.draw.bind(this));
    },

    mouseMoveHandler: function (event) {
        this.mousePosition.x = event.clientX - this.canvas.offsetLeft;
        this.mousePosition.y = event.clientY - this.canvas.offsetTop;
    },

    clickHandler: function (event) {
        this.windows.forEach(calendarWindow => calendarWindow.clickHandler.bind(calendarWindow)(event));
    },
};

document.addEventListener('mousemove', Calendar.mouseMoveHandler.bind(Calendar));
document.addEventListener('click', Calendar.clickHandler.bind(Calendar));
