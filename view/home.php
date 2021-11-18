<?php 
$user = $data['user'];
?>

<nav>
    <ul>
        <a class="discord-identity" href="index.php?logout=true">
            <img src="public/images/discord_logo.svg" alt="Discord_logo_white">
            <?php $extention = strpos($user->avatar, 'a_') === 0 ? '.gif' : '.png'; ?>
            <img class="discord-avatar" src="https://cdn.discordapp.com/avatars/<?=$user->id?>/<?=$user->avatar . $extention?>?size=128" alt="Avatar">
            <span><?=$user->username?> - Switch accounts ?</span>
        </a>
        <a href="https://frenchdiscord.com"><img src="public/images/french_logo.svg"><span>French shop</span></a>
        <a href="https://patreon.com/frenchdiscord"><img src="public/images/patreon_logo.png"></a>
        <h1 id="title"></h1>
    </ul>
</nav>
<!--<iframe src="https://ptb.discord.com/widget?id=254463427949494292&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>-->
<div id="message"></div>

<div id="landscape">
    <p>Merci de passer en mode paysage pour une meilleure expérience</p>
    
    <i class="fas fa-sync-alt"></i>
</div>

<section id="content">
    <img id="background" src="public/images/background.jpg" alt="">

    <ul>
        <li onclick="openPopup('box1')" id="box1" class="box">1</li>
        <li onclick="openPopup('box2')" id="box2" class="box">2</li>
        <li onclick="openPopup('box3')" id="box3" class="box">3</li>
        <li onclick="openPopup('box4')" id="box4" class="box">4</li>
        <li onclick="openPopup('box5')" id="box5" class="box">5</li>
        <li onclick="openPopup('box6')" id="box6" class="box">6</li>
        <li onclick="openPopup('box7')" id="box7" class="box">7</li>
        <li onclick="openPopup('box8')" id="box8" class="box">8</li>
        <li onclick="openPopup('box9')" id="box9" class="box">9</li>
        <li onclick="openPopup('box10')" id="box10" class="box">10</li>
        <li onclick="openPopup('box11')" id="box11" class="box">11</li>
        <li onclick="openPopup('box12')" id="box12" class="box">12</li>
        <li onclick="openPopup('box13')" id="box13" class="box">13</li>
        <li onclick="openPopup('box14')" id="box14" class="box">14</li>
        <li onclick="openPopup('box15')" id="box15" class="box">15</li>
        <li onclick="openPopup('box16')" id="box16" class="box">16</li>
        <li onclick="openPopup('box17')" id="box17" class="box">17</li>
        <li onclick="openPopup('box18')" id="box18" class="box">18</li>
        <li onclick="openPopup('box19')" id="box19" class="box">19</li>
        <li onclick="openPopup('box20')" id="box20" class="box">20</li>
        <li onclick="openPopup('box21')" id="box21" class="box">21</li>
        <li onclick="openPopup('box22')" id="box22" class="box">22</li>
        <li onclick="openPopup('box23')" id="box23" class="box">23</li>
        <li onclick="openPopup('box24')" id="box24" class="box">24</li>
    </ul>
</section>

<div id="popup">
    <i onclick="closePopup()" id="close" class="fas fa-times"></i>

    <h2 id="partTitle"></h2>

    <p id="popupText"></p>
</div>