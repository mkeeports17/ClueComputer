<?php 
include 'config.php';
include 'Guess.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear'])){
    session_unset();
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="left" style="flex: 1;">
    <h1>Welcome to Clue Computer!</h1>
    <p>I am a computer designed to calculate the logic behind the game of clue. Tell me each guess made 
        in the game (by you and your opponents) and I will crack the case for you as quickly as possible.</p>
    <h2>Enter the next guess made:</h2><br>
    <form class="formbox" method="POST" action="server.php">
        <label>Suspect: <select name="suspect" id="suspectSelect" required>
            <option value=""></option>
            <?php foreach ($_SESSION['suspects'] as $s){
                echo "<option value='$s'>$s</option>";
            }?>
        </select></label><br><br>
        <label>Weapon: <select name="weapon" id="weaponSelect" required>
            <option value=""></option>
            <?php foreach ($_SESSION['weapons'] as $w){
                echo "<option value='$w'>$w</option>";
            }?>
        </select></label><br><br>
        <label>Room: <select name="room" id="roomSelect" required>
            <option value=""></option>
            <?php foreach ($_SESSION['rooms'] as $r){
                echo "<option value='$r'>$r</option>";
            }?>
        </select></label><br><br>
        <label>Player that guessed: <input type="number" name="player" id="playerInput"required></label><br><br>
        <label>Player that showed card <br>(0 if no one did): <input type="number" name="showPlayer" id="showPlayerInput"></label><br><br>
        
        <div id="extraField">
            <label>Which card was shown to you? <select name="shownCard" id="shownCardSelect">
                <option value=""></option>
            </select></label><br><br>
        </div>
        <input class="button-74" type="submit" value="Submit">
    </form>
    <br><br><a href="setup.php">
    <button class="button-74">Setup Page</button></a>
    <br>
    </div>

    <script>
        const suspect = document.getElementById("suspectSelect");
    const weapon = document.getElementById("weaponSelect");
    const room = document.getElementById("roomSelect");
    const playerInput = document.getElementById("playerInput");
    const showPlayerInput = document.getElementById("showPlayerInput");
    const extraField = document.getElementById("extraField");
    const shownCardSelect = document.getElementById("shownCardSelect");

    function updateShownCardOptions() {
        const s = suspect.value;
        const w = weapon.value;
        const r = room.value;

        // Don't update unless all 3 have values
        if (!s || !w || !r) return;

        // Reset and update dropdown
        shownCardSelect.innerHTML = "<option value=''></option>";
        shownCardSelect.innerHTML += `<option value="${s}">${s}</option>`;
        shownCardSelect.innerHTML += `<option value="${w}">${w}</option>`;
        shownCardSelect.innerHTML += `<option value="${r}">${r}</option>`;
    }

    function checkConditions() {
        const playerVal = parseInt(playerInput.value, 10);
        const showPlayerVal = parseInt(showPlayerInput.value, 10);

        const shouldShow = playerVal === 1 && showPlayerVal > 1;

        extraField.style.display = shouldShow ? 'block' : 'none';

        if (shouldShow) {
            updateShownCardOptions();
        }
    }

    // Attach events
    playerInput.addEventListener('input', checkConditions);
    showPlayerInput.addEventListener('input', checkConditions);

    // When the user changes any guess, re-update the shown card dropdown IF extraField is visible
    suspect.addEventListener('change', () => {
        if (extraField.style.display === 'block') updateShownCardOptions();
    });

    weapon.addEventListener('change', () => {
        if (extraField.style.display === 'block') updateShownCardOptions();
    });

    room.addEventListener('change', () => {
        if (extraField.style.display === 'block') updateShownCardOptions();
    });
    </script>


    <div class="right" style="flex: 2;">
    <h1>Master Table</h1>
    <table border="1">
        <thead>
        <tr>
            <th></th>
            <th>Envelope</th>
            <?php for ($i = 0; $i < count($_SESSION['players']); $i++) {
                echo "<th>" . $_SESSION['players'][$i] . "</th>";
            }?>
        </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($_SESSION['fullList']); $i++) {
                echo "<tr>";
                echo "<th>" . $_SESSION['fullList'][$i] . "</th>";
                for ($j = 0; $j <= count($_SESSION['players']); $j++) {
                    echo "<td>" . $_SESSION['table'][$i][$j] . "</td>";
                }
                echo "</tr>";
            }?>
        </tbody>
    </table>
    </div>
  </div>
</body>
</html>
