// Can open: colours
// Today's bubble: beeg!
var CALENDAR_WINDOW_COLOURS = {
    cantOpen: {
        background: '#CCCCCC',
        stroke: '#666666',
        text: '#888888',
    },
    canOpen: {
        background: 'white',
        stroke: '#AAAAAA',
        text: '#000000',
    },
};
var CALENDAR_WINDOW_RADIUS = {
    active: 100,
    inactive: 40,
};
var ANIMATION_DURATION = 0.5;
var FRENCH_COLOURS = ['#7eb301', '#fdcd00', '#0198e9', '#e40001', '#5d0073'];

var CalendarWindow = {
    x: 0,
    y: 0,
    vx: 0,
    vy: 0,
    fullRadius: CALENDAR_WINDOW_RADIUS.inactive,
    radius: CALENDAR_WINDOW_RADIUS.inactive,
    colours: CALENDAR_WINDOW_COLOURS.cantOpen,
    opacity: 1,
    text: '',
    canOpen: false,
    fps: 60,
    appearing: false,
    popping: false,
    appeared: false,
    frenchColoursIndex: 0,
    animating: false,
    animationFrame: 0,
    animationFrameEnd: null,

    clone: function () {
        var clonedObject = Object.assign({}, CalendarWindow);

        clonedObject.colours = Object.assign({}, CalendarWindow.colours);

        return clonedObject;
    },

    isMouseOver: function (mousePosition) {
        var x = mousePosition.x - this.x;
        var y = mousePosition.y - this.y;

        return Math.abs(Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2))) < this.radius;
    },

    setCanOpen: function (canOpen) {
        var today = window.day || new Date().getDate();
        var thisDay = parseInt(this.text);
        var active = !isNaN(thisDay) && thisDay === today;

        this.colours = Object.assign({}, CALENDAR_WINDOW_COLOURS[canOpen ? 'canOpen' : 'cantOpen']);
        this.fullRadius = CALENDAR_WINDOW_RADIUS[active ? 'active' : 'inactive'];
        this.radius = CALENDAR_WINDOW_RADIUS[active ? 'active' : 'inactive'];
        this.canOpen = !!canOpen;

        this.bumpColour();

        return this;
    },

    bumpColour: function () {
        if (this.canOpen) {
            this.colours.stroke = FRENCH_COLOURS[this.frenchColoursIndex % FRENCH_COLOURS.length];
            this.frenchColoursIndex++;
        } else {
            this.colours.stroke = CALENDAR_WINDOW_COLOURS.cantOpen.stroke;
        }

        return this;
    },

    animate: function (animation, fps) {
        if (fps) {
            this.fps = fps;
        }

        this.animating = true;
        this.animationFrame = 0;
        this.animationFrameEnd = this.fps * ANIMATION_DURATION;

        ['appearing', 'popping'].forEach(function (key) {
            this[key] = key === animation;
        }.bind(this));
    },

    endAnimation: function () {
        this.animating = false;
        this.animationFrame = 0;

        ['appearing', 'popping'].forEach(function (key) {
            this[key] = false;
        }.bind(this));
    },

    draw: function (context, mousePosition, image) {
        if (this.animating) {
            context.save();
            this.handleAnimation(context);
        }

        this.setContextStyle(context);

        if (this.isMouseOver(mousePosition)) {
            this.addGlow(context);
        }

        this.drawBubble(context);
        this.drawImage(context, image);
        this.drawNumber(context);
        this.calculateVelocity(context);

        if (this.animating) {
            context.restore();
        }
    },

    handleAnimation: function () {
        if (this.appearing) {
            this.animateAppearing();
        } else if (this.popping) {
            this.animatePopping();
        }
    },

    animateAppearing: function () {
        var radiusFactor = this.fullRadius / this.animationFrameEnd;
        var opacityFactor = 1 / this.animationFrameEnd;

        this.radius = this.animationFrame * radiusFactor;
        this.opacity = this.animationFrame * opacityFactor;

        this.animationFrame++;

        if (this.animationFrame >= this.animationFrameEnd) {
            this.radius = this.fullRadius;
            this.appeared = true;
            this.opacity = 1;
            this.endAnimation();
        }
    },

    animatePopping: function () {
        var radiusFunction = function (number) {
            var result = (Math.log10(number + 0.01) + 2) / 2;

            return result < 0 ? 0 : result;
        };

        var opacityFunction = function (number) {
            var result = (Math.log10(-1 * Math.pow(number, 3) + 0.03) + 3.52) / 2;

            return isNaN(result) || result < 0 ? 0 : result;
        };

        var frameProgression = this.animationFrame / this.animationFrameEnd;

        var radiusFactor = radiusFunction(frameProgression);
        var addedRadius = this.fullRadius * radiusFactor;

        this.opacity = opacityFunction(frameProgression);
        this.radius = this.fullRadius + addedRadius;

        this.animationFrame++;

        if (this.animationFrame >= this.animationFrameEnd) {
            this.opacity = 0;
            this.endAnimation();
        }
    },

    setContextStyle: function (context) {
        context.globalAlpha = this.opacity;

        context.save();

        context.fillStyle = this.colours.background;
        context.strokeStyle = this.colours.stroke;
        context.lineWidth = 4;
    },

    addGlow: function (context) {
        context.shadowBlur = 10;
        context.shadowColor = this.colours.background;
    },

    drawBubble: function (context) {
        context.beginPath();
        context.arc(this.x, this.y, this.radius, 0, Math.PI * 2, true);
        context.closePath();

        context.fill();
        context.stroke();

        context.beginPath();
        context.arc(
            this.x - (this.radius / 1.5),
            this.y - (this.radius / 1.5),
            this.radius / 2, 0,
            Math.PI * 2,
            true
        );
        context.closePath();
        context.fill();

        context.restore();
    },

    drawImage: function (context, image) {
        context.drawImage(
            image,
            this.x - this.radius / 1.38,
            this.y - this.radius / 1.38,
            this.radius * 1.5,
            this.radius * 1.5
        );
    },

    drawNumber: function (context) {
        context.font = this.radius / 2 + 'px Roboto'
        context.fillStyle = this.colours.text;
        context.textAlign = 'center';
        context.fillText(
            this.text,
            this.x - (this.radius / 1.5),
            (this.y + (this.radius / 1.5) / 4) - (this.radius / 1.5)
        );

        context.restore();
    },

    calculateVelocity: function (context) {
        var tooFarLeft = this.x <= this.fullRadius;
        var tooFarRight = this.x >= context.canvas.width - this.fullRadius;
        var tooFarUp = this.y <= this.fullRadius + context.lineWidth;
        var tooFarDown = this.y >= context.canvas.height - this.fullRadius - context.lineWidth;

        if (tooFarLeft && tooFarRight) {
            this.vx = 0;
        } else if (tooFarLeft || tooFarRight) {
            this.vx *= -1;
            this.bumpColour();
        }

        if (tooFarUp && tooFarDown) {
            this.vy = 0;
        } else if (tooFarUp || tooFarDown) {
            this.vy *= -1;
            this.bumpColour();
        }

        this.x += this.vx;
        this.y += this.vy;
    },

    clickHandler: function (event) {
        var mousePosition = {
            x: event.clientX,
            y: event.clientY,
        }

        return this.isMouseOver(mousePosition);
    },
};

var Calendar = {
    canvas: null,
    windows: [],
    requestAnimationFrame: null,
    mousePosition: {x: 0, y: 0},
    active: new Date().getDate(),
    imagesLoaded: false,
    fpsLoaded: false,
    fpsCount: 0,
    fps: 0,
    loaded: false,
    loadingAngle: 0,
    activeImagesToLoad: null,
    inactiveImagesToLoad: null,
    debug: false,
    calendarWindowActiveImages: [],
    calendarWindowInactiveImages: [],
    saveFPSInterval: null,
    loadFPSTimeout: null,

    init: function (element) {
        this.reset();
        this.setupCanvas(element);

        this.activeImagesToLoad = window.calendarWindowActiveImages.filter(() => true);
        this.inactiveImagesToLoad = window.calendarWindowInactiveImages.filter(() => true);
        this.loadImages();

        this.saveFPSInterval = setInterval(this.saveFPS.bind(this), 1000);
        this.loadFPSTimeout = setTimeout(function () {
            this.fpsLoaded = true;

            if (this.imagesLoaded) {
                this.loaded = true;
            }
        }.bind(this), 3000);

        this.requestAnimationFrame = window.requestAnimationFrame(this.draw.bind(this));

        this.initCalendarWindows();
    },

    reset: function() {
        if (this.requestAnimationFrame) {
            window.cancelAnimationFrame(this.requestAnimationFrame);
        }

        if (this.saveFPSInterval) {
            clearInterval(this.saveFPSInterval);
        }

        if (this.loadFPSTimeout) {
            clearTimeout(this.loadFPSTimeout);
        }

        this.active = window.day;
        this.windows = [];
        this.imagesLoaded = false;
        this.fpsLoaded = false;
        this.fpsCount = 0;
        this.fps = 0;
        this.loaded = false;
        this.loadingAngle = 0;
        this.debug = typeof debug === 'undefined' ? false : debug;
    },

    initCalendarWindows: function () {
        var context = this.setupCanvas();

        this.windows = [];

        for (var i = 0; i < 31; i++) {
            const day = 31 - i;

            if (this.active === day) {
                continue;
            }

            this.windows.push(this.createCalendarWindow(context, day.toString(), this.active > day));
        }

        this.windows.push(this.createCalendarWindow(context, (this.active).toString(), true));
    },

    saveFPS: function () {
        this.fps = this.fpsCount;
        this.fpsCount = 0;
    },

    toggleDebug: function () {
        this.debug = !this.debug;
    },

    setupCanvas: function (element) {
        if (element) {
            this.canvas = element;
        }

        this.canvas.width = this.canvas.offsetWidth;
        this.canvas.height = this.canvas.offsetHeight;

        this.clear();

        return this.canvas.getContext('2d');
    },

    loadImages: function () {
        var loadHandler = function (event, active) {
            if (active) {
                this.activeImagesToLoad.splice(this.activeImagesToLoad.indexOf(event.target.src), 1);
            } else {
                this.inactiveImagesToLoad.splice(this.inactiveImagesToLoad.indexOf(event.target.src), 1);
            }

            if (this.activeImagesToLoad.length < 1 && this.inactiveImagesToLoad.length < 1) {
                this.imagesLoaded = true;

                if (this.fpsLoaded) {
                    this.loaded = true;
                }
            }

            if (active) {
                this.calendarWindowActiveImages.push(event.target);
            } else {
                this.calendarWindowInactiveImages.push(event.target);
            }
        }.bind(this);

        var imagePath, image;

        for (imagePath of this.activeImagesToLoad) {
            image = new Image();

            image.onload = function (event) {
                loadHandler(event, true);
            };
            image.src = imagePath;
        }

        for (imagePath of this.inactiveImagesToLoad) {
            image = new Image();

            image.onload = function (event) {
                loadHandler(event, false);
            };
            image.src = imagePath;
        }
    },

    createCalendarWindow: function (context, text, canOpen) {
        var calendarWindow = CalendarWindow.clone();

        calendarWindow.text = text;
        calendarWindow.setCanOpen(canOpen);

        calendarWindow.vx = this.getRandomSpeed();
        calendarWindow.vy = this.getRandomSpeed();

        calendarWindow.x = Math.random() * (this.canvas.offsetWidth - calendarWindow.fullRadius * 2) + calendarWindow.fullRadius;
        calendarWindow.y = Math.random() * (this.canvas.offsetHeight - calendarWindow.fullRadius * 2) + calendarWindow.fullRadius;

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

    calendarWindowImage: function* (active) {
        var index = 0;
        var length = active ? this.calendarWindowActiveImages.length : this.calendarWindowInactiveImages.length;

        while (true) {
            if (active) {
                yield this.calendarWindowActiveImages[index];
            } else {
                yield this.calendarWindowInactiveImages[index];
            }

            index++;

            if (index === length) {
                index = 0;
            }
        }
    },

    draw: function () {
        var context = this.canvas.getContext('2d');

        this.clear();
        this.fpsCount++;

        if (this.debug) {
            this.drawFPS(context);
        }

        if (this.loaded) {
            var calendarWindowActiveImageIterator = this.calendarWindowImage(true);
            var calendarWindowInactiveImageIterator = this.calendarWindowImage(false);

            for (var i = 0; i < this.windows.length; i++) {
                var calendarWindow = this.windows[i];
                var image = calendarWindow.canOpen
                    ? calendarWindowActiveImageIterator.next().value
                    : calendarWindowInactiveImageIterator.next().value;

                calendarWindow.draw(context, this.mousePosition, image);

                if (!calendarWindow.animating && !calendarWindow.appeared) {
                    calendarWindow.animate('appearing', this.fps);
                }
            }
        } else {
            this.drawLoading(context);
        }

        this.requestAnimationFrame = window.requestAnimationFrame(this.draw.bind(this));
    },

    drawFPS: function (context) {
        context.save();

        context.font = '16px Roboto'
        context.fillStyle = 'white';
        context.strokeStyle = 'black';
        context.strokeWidth = '2px';
        context.textAlign = 'center';
        context.fillText((this.fps ?? 'null') + ' FPS', this.canvas.width - 50, 30);
        context.stroke();

        context.restore();
    },

    drawLoading: function (context) {
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
    },

    getWindowInstanceFromDay: function (day) {
        return this.windows.find(function (calendarWindow) {
            return parseInt(calendarWindow.text) === parseInt(day);
        });
    },

    openWindow: function (day) {
        ajaxGet('get-reward&day=' + day, function (response) {
            response = JSON.parse(response);

            if (!response) {
                return;
            }

            if (response.status < 1) {
                if (response.story) {
                    var calendarWindow = this.getWindowInstanceFromDay(day);

                    Modal.closeHandler = function () {
                        this.getWindowInstanceFromDay(day).animate('appearing', this.fps);
                    }.bind(this);

                    calendarWindow.setCanOpen(calendarWindow.canOpen);
                    Modal.open(response);
                } else {
                    setTimeout(function () {
                        this.getWindowInstanceFromDay(day).animate('appearing', this.fps);
                    }.bind(this), ANIMATION_DURATION * 1.5);
                }
            } else {
                Modal.open(
                    'Error',
                    'An error occurred. This is not your fault, don\'t worry. Please refresh the page, it should fix the problem.'
                );
            }
        }.bind(this));
    },

    mouseMoveHandler: function (event) {
        if (this.canvas) {
            this.mousePosition.x = event.clientX - this.canvas.offsetLeft;
            this.mousePosition.y = event.clientY - this.canvas.offsetTop;
        }
    },

    clickHandler: function (event) {
        if (!Modal.isOpen) {
            var clickedDays = this.windows.filter(function (calendarWindow) {
                return calendarWindow.clickHandler.bind(calendarWindow)(event);
            }).map(function (calendarWindow) {
                return parseInt(calendarWindow.text);
            });

            if (clickedDays.length > 0) {
                var windowToOpen = clickedDays.sort()[0];

                if (clickedDays.indexOf(this.active) > -1) {
                    windowToOpen = this.active;
                }

                this.openWindow(windowToOpen);
                var calendarWindow = this.getWindowInstanceFromDay(windowToOpen);

                calendarWindow.animate('popping', this.fps);
            }
        }
    },
};

document.addEventListener('mousemove', Calendar.mouseMoveHandler.bind(Calendar));
document.addEventListener('click', Calendar.clickHandler.bind(Calendar));
