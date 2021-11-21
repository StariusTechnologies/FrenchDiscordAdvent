var calendarWindows = document.getElementsByClassName('box');
var windowCount = 31;
var windowState = [];

function addEventOpenWindow() {
    for (let i = 0; i < calendarWindows.length; i++) {
        if (!forbidden.includes(calendarWindows[i]) && windowState[i] === 'close') {
            calendarWindows[i].addEventListener(
                'click',
                () => {
                    // get available reward
                    // pick reward type (random token qtt, special reward like Patreon 1 month/nitro/big token qtt) 1 chance sur 50 ?
                    // pick concrete reward
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

            if (response && response === 'false') {
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
