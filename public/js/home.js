//Initialisation des variables
const christmas = new Date("2021-12-25");
const today = new Date();
//const day = today.getDay();
const day = String(today).split(" ")[2];


// Eléments
const content = document.getElementById("content");
const landscape = document.getElementById("landscape");
const message = document.getElementById("message");
const popup = document.getElementById("popup");
const popupText = document.getElementById("popupText");
const partTitle = document.getElementById("partTitle");

var forbidden = [];

const wait = Math.ceil((christmas-today)/(1000*60*60*24));


//Initialisation des fonctions
function checkLandscape(){
    if(window.innerWidth>window.innerHeight){ //Format paysage
        content.style = "display: block;";
        landscape.style = "display: none;";
    }
    else {
        landscape.style = "display: block;";
        content.style = "display: none;";
    }
}

function manageText(part){
    text = texts[part-1].split("\n");
    managed = "";
    for (var i=0;i!=text.length;i++){
        managed = managed+text[i]+"<br>"
    }
    return managed
}

function displayMessage(type,text){
    message.style = "visibility: visible; opacity: 1;"
    if (type=="error"){
        message.style = "background-color: var(--red);";
    } else {
        message.style = "background-color: var(--lightblue);";
    }
    message.innerHTML = text;
    setTimeout(() => {  
        message.style = "visibility: hidden; opacity: 0;" 
    }, 800);
}



function openPopup(box){
    var part = box.split("x")[1];
    if (forbidden.includes(box)){
        displayMessage("error","Ne grillez pas les étapes !");
    } else {
        var text;
        partTitle.innerHTML = `Partie ${part} - ${titles[part-1]}`;
        popupText.innerHTML = manageText(part);
        popup.style = "display: block;";
        document.getElementById("close").style = "display: block;"
    }
}
function closePopup(){
    popup.style = "display: none;"
}

//Initialisation de la page
message.style = "visibility: hidden;"
popup.style = "display: none;"
document.title = `J-${wait}`;
document.getElementById("title").innerHTML = `J-${wait}`;
checkLandscape();
for (let i=24;i!=day;i--){
    document.getElementById(`box${i}`).style = "cursor: not-allowed;";
    forbidden.push(`box${i}`);
}


//Arrière-plan
window.addEventListener("onload",checkLandscape);
window.addEventListener("resize",checkLandscape);
var intervalID = window.setInterval(checkLandscape, 500);