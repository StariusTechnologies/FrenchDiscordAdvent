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

                    ajaxGet(`get-reward?dayNumber=${calendarWindow.innerText}`, (response) => {
                        response = JSON.parse(response);

                        if (!response) {
                            //TODO do something I guess / document.location.href = baseUrl;
                        }
                    });
                    // TODO Lily will add animation
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
