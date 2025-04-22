<?php
session_start();
//setup form was entered, so (re)start the game
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playerCount'])) {
    session_unset();

    $_SESSION['suspects'] = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White'];
    $_SESSION['weapons'] = ['Wrench', 'Candlestick', 'Dagger', 'Pistol', 'Leadpipe', 'Rope'];
    $_SESSION['rooms'] = ['Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];
    $_SESSION['fullList'] = array_merge($_SESSION['suspects'], $_SESSION['weapons'], $_SESSION['rooms']);
    //$_SESSION['fullList'] = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White', 'Wrench', 'Candlestick', 'Dagger', 'Pistol', 
    //'Leadpipe', 'Rope', 'Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];

    $playerCount = htmlspecialchars($_POST['playerCount']);
    for ($i = 1; $i <= $playerCount; $i++) {
        $_SESSION['players'][] = "Player $i";
    }

    $playerCards =[];
    if(isset($_POST['card1'])){ $playerCards[] = htmlspecialchars($_POST['card1']); }
    if(isset($_POST['card2'])){ $playerCards[] = htmlspecialchars($_POST['card2']); }
    if(isset($_POST['card3'])){ $playerCards[] = htmlspecialchars($_POST['card3']); }
    if(isset($_POST['card4'])){ $playerCards[] = htmlspecialchars($_POST['card4']); }
    if(isset($_POST['card5'])){ $playerCards[] = htmlspecialchars($_POST['card5']); }
    if(isset($_POST['card6'])){ $playerCards[] = htmlspecialchars($_POST['card6']); }
    $_SESSION['playerCards'] = $playerCards;
    $_SESSION['table'] = [['-','X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-']];
    foreach($playerCards as $pc){
        eliminateHorizontally(getRow($pc),1);
    }

    header("Location: game.php");
    exit;
}

//next guess was entered
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['suspect'])) {
    try{
        $p = (int) $_POST['player'];
        $s = $_POST['suspect'];
        $w = $_POST['weapon'];
        $r = $_POST['room'];
        $sp = (int) $_POST['showPlayer'];
        if(isset($_POST['shownCard']) && !empty($_POST['shownCard'])){
            $sc = $_POST['shownCard'];
            // echo "$sc was shown<br>";
            eliminateHorizontally(getRow($sc), $sp);
        }
        // $thisGuess = new Guess($s, $w, $r, 2);
        // $_SESSION['Guesses'][] = $thisGuess;
        header("Location: game.php");
        exit;
        // echo "$p guessed $s with $w in $r and $sp showed a card.";
    } catch(Exception $e){
        echo $e;
    }
    
}



function eliminateHorizontally($row, $col){
    for ($i = 0; $i < count($_SESSION['table'][$row]); $i++) {
        $_SESSION['table'][$row][$i] = 'X';
    }
    $_SESSION['table'][$row][$col] = 'O';
}
function getRow($card){
    return (array_search($card, $_SESSION['fullList']));
}