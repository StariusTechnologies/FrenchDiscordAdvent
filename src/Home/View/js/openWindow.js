var calendarWindows = document.getElementsByClassName('box');
var windowCount = 31;
var windowState = [];
var availableSpecialReward = [];

function addEventOpenWindow(calendarWindows) {
    for (var i = 0; i < calendarWindows.length; i++) {
        if (!forbidden.includes(calendarWindows[i]) && windowState[i] === 'close') {
            calendarWindows[i].addEventListener(
                'click', (e) => {
                    calendarWindow = e.currentTarget;

                    ajaxGet(`get-reward?window=${calendarWindow.innerText}`, (response) => {
                        response = JSON.parse(response);

                        if (!response) {
                            document.location.href = baseUrl;
                        }
                    });
                    //get reward
                    // TODO Lily will add animation
                    // ajax openWindow- set db entry window x opened - give reward somehow - if special reward decrement available speReward on db
                }
            );
        }
    }
}

window.addEventListener(
    'load', () => {
        ajaxGet('get-opened-windows', (response) => {
            response = JSON.parse(response);

            if (!response) {
                document.location.href = baseUrl;
            }

            if (response.length < 1) {
                windowState = Array(windowCount).fill('close');
            } else {
                for (var i = 0; i < windowCount; i++) {
                    windowState[i] = response[i] ? 'open' : 'close';
                }
            }

            addEventOpenWindow(calendarWindows);
        });
    }
);
