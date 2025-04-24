<?php

if(!isset($_SESSION['suspects'])){
    $_SESSION['suspects'] = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White'];
    $_SESSION['weapons'] = ['Wrench', 'Candlestick', 'Dagger', 'Pistol', 'Leadpipe', 'Rope'];
    $_SESSION['rooms'] = ['Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    html, body {
      margin: 10;
      padding: 10;
      height: 100%;
      font-family:Arial, Helvetica, sans-serif;
    }
    .container {
      display: flex;         /* horizontal layout */
      height: 100vh;         /* full viewport height */
      width: 100vw;          /* full viewport width */
    }
    .left, .right {
      flex: 1;               /* split evenly */
      padding: 20px;
      box-sizing: border-box;
    }
    .left {
      background-color: #deae69;
      border-right: 2px solid black;
    }
    .right {
      background-color: #deae69;
    }
    #extraField {
        display: none;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td{
        padding: 20px 10px;
        text-align:center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div>
    <h1>Welcome to Clue Computer!</h1>
    <p>I am a computer designed to calculate the logic behind the game of clue. Tell me each guess made 
        in the game (by you and your opponents) and I will crack the case for you as quickly as possible.</p>
    <h2>Enter setup data before the game begins:</h2><br>
    <form method="POST" action="server.php">

        <label>How many players are there? <input type="number" name="playerCount" id="playerCountInput"required></label><br><br>
        
        <label>How many cards do you have? <input type="number" id="cardCount"></label><br><br>
        <div id="cardInputs"></div>
        
        <input type="submit" value="Submit">
    </form>
    <br>
    </div>

    <script>
        const cardCountInput = document.getElementById('cardCount');
        const cardInputsDiv = document.getElementById('cardInputs');

        // Combine suspects + weapons + rooms into one <option> list
        const cardOptions = `<?php
            $options = '';
            foreach ($_SESSION['suspects'] as $s) {
                $options .= "<option value='$s'>$s</option><br>";
            }
            foreach ($_SESSION['weapons'] as $w) {
                $options .= "<option value='$w'>$w</option><br>";
            }
            foreach ($_SESSION['rooms'] as $r) {
                $options .= "<option value='$r'>$r</option><br>";
            }
            echo $options;
        ?>`;

        cardCountInput.addEventListener('input', () => {
            const count = parseInt(cardCountInput.value) || 0;

            // Clear existing inputs
            cardInputsDiv.innerHTML = '';

            // Create new select inputs
            for (let i = 1; i <= count; i++) {
                const label = document.createElement('label');
                label.className = 'card-input';
                label.innerHTML = `Card ${i}: <select name="card${i}">${cardOptions}</select><br><br>`;
                cardInputsDiv.appendChild(label);
            }
        });
    </script>
  </div>
</body>
</html>