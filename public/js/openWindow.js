const adventWindows = document.getElementsByClassName('box');
const forbiddenWindows = forbidden; // from home.js
const windowCount = 31;
let windowState = [];
//const baseUrl = 'avent.frenchdiscord.com';
const baseUrl = 'localhost/avent/index.php';

function addEventOpenWindow() {
    for (let i = 0; i < adventWindows.length; i++) {
        if (!forbidden.includes(adventWindows[i]) && windowState[i] === 'close') {
            adventWindows[i].addEventListener(
                'click', () => {
                    //get available reward
                    //pick reward type (random token qtt, special reward like patreon 1month/nitro/big token qtt) 1 chance sur 50 ?
                    //pick concret reward
                    //TODO Lily will add animation
                    //ajax openWindow- set db entry window x opened - give reward somehow - if special reward decrement available speReward on db
                }
            );
        }
    }
}

window.addEventListener(
    'load', () => {
        ajaxGet(`index.php?action=getWindowState`, (response) => {
            response = JSON.parse(response);

            if (response && response === 'false') {
                document.location.href = baseUrl;    
            }

            if (response.length < 1) {
                windowState = new Array(windowCount).fill('close');
            } else {
                for (let i = 0; i < windowCount; i++) {
                    windowState[i] = response[i] ? 'open' : 'close';
                }
            }

            addEventOpenWindow();
        });
    }
);
