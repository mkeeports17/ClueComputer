<?php
session_start();
include 'Guess.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//setup form was entered, so (re)start the game
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playerCount'])) {
    session_unset();

    $_SESSION['suspects'] = ['Green', 'Mustard', 'Peacock', 'Plum', 'Scarlet', 'White'];
    $_SESSION['weapons'] = ['Wrench', 'Candlestick', 'Dagger', 'Pistol', 'Leadpipe', 'Rope'];
    $_SESSION['rooms'] = ['Bathroom', 'Office', 'Dining Room', 'Game Room', 'Garage', 'Bedroom', 'Living Room', 'Kitchen', 'Courtyard'];
    $_SESSION['fullList'] = array_merge($_SESSION['suspects'], $_SESSION['weapons'], $_SESSION['rooms']);

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
    

    // $_SESSION['table'] = [['-','X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    // ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    // ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    // ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    // ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-'], 
    // ['-', 'X','-','-','-','-','-'], ['-', 'X','-','-','-','-','-']];
    $_SESSION['table'] = [['-','X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], 
    ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], 
    ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], 
    ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], 
    ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], ['-', 'X','-','-','-'], 
    ['-', 'X','-','-','-'], ['-', 'X','-','-','-']];
    for($i=0; $i<count($_SESSION['fullList']), $i++;){
        $_SESSION['table'][$i] = [];
        $_SESSION['table'][$i][0] = '-';
        $_SESSION['table'][$i][1] = 'X';
        
        for($j=2; $j<=count($_SESSION['players']), $j++;){
            $_SESSION['table'][$i][] = '-';
        }
        // }catch(Exception $e){ $_SESSION['table'] = [['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], 
        //     ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], 
        //     ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], 
        //     ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], 
        //     ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E'], 
        //     ['E','X','E','E','E','E','E'], ['E','X','E','E','E','E','E']];
        // }
    }
    foreach($playerCards as $pc){
        eliminateHorizontally(getRow($pc),1);
    }

    foreach($_SESSION['table'] as $row){
        foreach($row as $cell){
            echo $cell . " ";
        }
        echo "<br>";
    }
    // header("Location: game.php");
    // exit;
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
            eliminateHorizontally(getRow($sc), $sp);
        }
        $thisGuess = new Guess($s, $w, $r, $sp);
        $_SESSION['Guesses'][] = $thisGuess;
        guessElims($p, $sp, $thisGuess);
        checkTable();
        checkGuesses();
        header("Location: game.php");
        exit;
    } catch(Exception $e){
        echo $e;
    }
}


//puts an O in any column where there are all X's
function checkTable(){
    //check one row at a time
    for($i=0; $i<count($_SESSION['table']); $i++){
        $allX = true;
        for($j=1; $j<count($_SESSION['table'][$i]); $j++){
            if($_SESSION['table'][$i][$j] != 'X'){
                $allX = false;
                break;
            }
        } if($allX){
            $_SESSION['table'][$i][0] = 'O';
        }
    }
}

function checkGuesses(){

}

//runs currentGuessElim for each player that said they didn't have any of the cards in Guess
function guessElims($currentPlayer, $showPlayer, $guess){
    for($i = $currentPlayer+1; $i != $showPlayer; $i++){
        if($i== count($_SESSION['players'])+1){ $i=0;}
        else if($i==$currentPlayer){break;}
        else {currentGuessElim($i, $guess);};
    }
}

//eliminates all 3 values in 1 guess from a player, if they said they didn't have any of these cards
function currentGuessElim($col, $guess){
    $_SESSION['table'][getRow($guess->getSuspect())][$col] = 'X';
    $_SESSION['table'][getRow($guess->getWeapon())][$col] = 'X';
    $_SESSION['table'][getRow($guess->getRoom())][$col] = 'X';
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