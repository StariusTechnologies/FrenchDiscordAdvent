// Variables initialization
var christmas = new Date("2022-01-01");
var today = new Date();
//var day = today.getDay();
var day = Number(String(today).split(' ')[2]);

var SECOND = 1000;
var MINUTE = 60 * SECOND;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR


// Elements
var content = document.getElementById('content');
var landscape = document.getElementById('landscape');
var message = document.getElementById('message');
var popup = document.getElementById('popup');
var popupText = document.getElementById('popupText');
var partTitle = document.getElementById('partTitle');

var forbidden = [];

var waitTime = Math.ceil((christmas - today) / DAY);


// Functions initialization
function checkLandscape() {
    if (window.innerWidth>window.innerHeight) { // Landscape
        content.style.display = 'block';
        landscape.style.display = 'none';
    } else { // Portrait
        landscape.style.display = 'block';
        content.style.display = 'none';
    }
}

function manageText(part) {
    var text = texts[part - 1].split("\n");
    var managed = '';

    for (var i = 0; i < text.length; i++) {
        managed = managed + text[i] + '<br />';
    }

    return managed;
}

function displayMessage(type,text){
    message.style.visibility = 'visible';
    message.style.opacity = '1';

    if (type === 'error') {
        message.style.backgroundColor = 'var(--red)';
    } else {
        message.style.backgroundColor = 'var(--lightblue)';
    }

    message.innerHTML = text;

    setTimeout(() => {
        message.style.visibility = 'hidden';
        message.style.opacity = '0';
    }, 800);
}

function openPopup(box) {
    var part = box.split("x")[1];

    if (forbidden.includes(box)) {
        displayMessage('error','Ne grillez pas les étapes !');
    } else {
        partTitle.innerHTML = 'Partie ' + part + ' - ' + titles[part-1];
        popupText.innerHTML = manageText(part);
        popup.style.display = 'block';
        document.getElementById('close').style.display = 'block';
    }
}

function closePopup() {
    popup.style.display = 'none';
}

// Page initialization
message.style.visibility = 'hidden';
popup.style.display = 'none';
document.title = 'J-' + waitTime;
document.getElementById('title').innerHTML = 'J-' + waitTime;

checkLandscape();

for (let i = 31; i > day; i--) {
    document.getElementById('box' + i).style.cursor = 'not-allowed';
    forbidden.push('box' + i);
}


// Background
window.addEventListener('onload',checkLandscape);
window.addEventListener('resize',checkLandscape);
window.setInterval(checkLandscape, 500);