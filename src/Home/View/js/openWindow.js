var calendarWindows = document.getElementsByClassName('box');
var windowCount = 31;
var windowState = [];
const availableSpecialReward = [];

function addEventOpenWindow() {
    for (let i = 0; i < calendarWindows.length; i++) {
        if (!forbidden.includes(calendarWindows[i]) && windowState[i] === 'close') {
            calendarWindows[i].addEventListener(
                'click',
                () => {
                    ajaxGet(`getReward?window=${adventWindows[i].textContent}`, (response) => {
                        response = JSON.parse(response);

                        if (!response || response.length < 1 || response === 'false') {
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

            if (!response || response.length < 1 || response === 'false') {
                document.location.href = baseUrl;
            }

            if (response.length < 1) {
                windowState = Array(windowCount).fill('close');
            } else {
                for (let i = 0; i < windowCount; i++) {
                    windowState[i] = response[i] ? 'open' : 'close';
                }
            }

            addEventOpenWindow();
        });
    }
);
